<?php
declare(strict_types=1);

require_once __DIR__ . '/PaymentService.php';
require_once __DIR__ . '/../../services/payment/gateway/PaymentGatewayInterface.php';

class PaymentProcessor
{
    private PDO $pdo;
    private PaymentService $paymentService;
    private PaymentGatewayInterface $gateway;

    public function __construct(
        PDO $pdo,
        PaymentService $paymentService,
        PaymentGatewayInterface $gateway
    ) {
        $this->pdo = $pdo;
        $this->paymentService = $paymentService;
        $this->gateway = $gateway;
    }

    public function initPayment(
        array $order,
        int $customerId,
        string $provider = 'test'
    ): array {
        $paymentId = $this->paymentService->create([
            'order_id' => (int) $order['id'],
            'cust_id' => $customerId,
            'provider' => $provider,
            'provider_order_id' => (string) $order['id'],
            'amount' => $order['total'],
            'currency' => $order['currency']
        ]);

        try {
            $gatewayResponse = $this->gateway->createPayment([
                'payment_id' => $paymentId,
                'order_id' => (int) $order['id'],
                'amount' => $order['total'],
                'currency' => $order['currency']
            ]);

            if (!$gatewayResponse['success']) {
                throw new Exception('Gateway payment initialization failed.');
            }

            $this->paymentService->updateGatewayData(
                $paymentId,
                $gatewayResponse['provider_payment_id'],
                $gatewayResponse['raw_response']
            );

            $transactionId = $this->paymentService->addTransaction([
                'payment_id' => $paymentId,
                'provider_reference' => $gatewayResponse['provider_payment_id'],
                'status' => 'created',
                'request_data' => [
                    'order_id' => (int) $order['id'],
                    'amount' => $order['total'],
                    'currency' => $order['currency']
                ],
                'response_data' => $gatewayResponse['raw_response']
            ]);

            return [
                'payment_id' => $paymentId,
                'transaction_id' => $transactionId,
                'amount' => $order['total'],
                'currency' => $order['currency'],
                'redirect_url' => $gatewayResponse['redirect_url']
            ];
        } catch (Throwable $e) {
            $this->paymentService->updateStatus($paymentId, 'failed');
            throw $e;
        }
    }

    public function handleCallback(array $payload): array
    {
        $result = $this->gateway->verifyPayment($payload);

        if (!$result['success']) {
            throw new Exception('Payment verification failed.');
        }

        $providerPaymentId = (string) $result['provider_payment_id'];

        if ($providerPaymentId === '') {
            throw new Exception('Provider payment ID is missing.');
        }

        $this->pdo->beginTransaction();

        try {
            $payment = $this->paymentService->findByProviderPaymentId(
                $providerPaymentId,
                true
            );

            if (!$payment) {
                throw new Exception('Payment not found.');
            }

            $status = (string) $result['status'];

            $transactionStatus = match ($status) {
                'paid' => 'success',
                'failed', 'refunded' => 'failed',
                default => 'created'
            };

            $this->paymentService->addTransaction([
                'payment_id' => (int) $payment['id'],
                'provider_reference' => $result['provider_reference'] ?? null,
                'status' => $transactionStatus,
                'request_data' => $payload,
                'response_data' => $result['raw_response']
            ]);

            if ($status === 'paid' && $payment['status'] !== 'paid') {
                $context = $this->paymentService->getOrderContext(
                    (int) $payment['order_id']
                );

                if (!$context) {
                    throw new Exception('Order or package data not found.');
                }

                if ((int) $context['customer_id'] !== (int) $payment['cust_id']) {
                    throw new Exception('Payment customer mismatch.');
                }

                $months = (int) $context['months'];

                if (!in_array($months, [1, 3, 6, 12], true)) {
                    throw new Exception('Invalid subscription duration.');
                }

                if ((int) $context['package_is_active'] !== 1) {
                    throw new Exception('Purchased package is no longer active.');
                }

                $subscription = $this->paymentService
                    ->findLatestActiveSubscription((int) $payment['cust_id']);

                $startsAt = new DateTimeImmutable('now');
                $endsAt = $startsAt->modify('+' . $months . ' months');

                if ($subscription) {
                    $subscriptionEndsAt = new DateTimeImmutable(
                        (string) $subscription['ends_at']
                    );

                    if (
                        (int) $subscription['package_id'] ===
                        (int) $context['package_id']
                    ) {
                        $newEndsAt = $subscriptionEndsAt
                            ->modify('+' . $months . ' months');

                        $this->paymentService->extendSubscription(
                            (int) $subscription['id'],
                            $newEndsAt->format('Y-m-d H:i:s'),
                            (float) $payment['amount'],
                            (string) $payment['currency']
                        );

                        $subscriptionId = (int) $subscription['id'];
                    } else {
                        if ($subscriptionEndsAt > $startsAt) {
                            $startsAt = $subscriptionEndsAt;
                        }

                        $endsAt = $startsAt->modify('+' . $months . ' months');

                        $subscriptionId = $this->paymentService->createSubscription([
                            'cust_id' => (int) $payment['cust_id'],
                            'package_id' => (int) $context['package_id'],
                            'starts_at' => $startsAt->format('Y-m-d H:i:s'),
                            'ends_at' => $endsAt->format('Y-m-d H:i:s'),
                            'price_paid' => (float) $payment['amount'],
                            'currency' => (string) $payment['currency']
                        ]);
                    }
                } else {
                    $subscriptionId = $this->paymentService->createSubscription([
                        'cust_id' => (int) $payment['cust_id'],
                        'package_id' => (int) $context['package_id'],
                        'starts_at' => $startsAt->format('Y-m-d H:i:s'),
                        'ends_at' => $endsAt->format('Y-m-d H:i:s'),
                        'price_paid' => (float) $payment['amount'],
                        'currency' => (string) $payment['currency']
                    ]);
                }

                $this->paymentService->attachSubscription(
                    (int) $payment['id'],
                    $subscriptionId
                );

                $this->paymentService->updateStatus(
                    (int) $payment['id'],
                    'paid',
                    true
                );

                $stmt = $this->pdo->prepare("
                    UPDATE orders
                    SET status = 'paid'
                    WHERE id = :id
                ");

                $stmt->execute([':id' => (int) $payment['order_id']]);
            } elseif ($status === 'failed') {
                $this->paymentService->updateStatus(
                    (int) $payment['id'],
                    'failed'
                );

                $stmt = $this->pdo->prepare("
                    UPDATE orders
                    SET status = 'failed'
                    WHERE id = :id
                      AND status = 'pending'
                ");

                $stmt->execute([':id' => (int) $payment['order_id']]);
            } elseif ($status === 'refunded') {
                $this->paymentService->updateStatus(
                    (int) $payment['id'],
                    'refunded'
                );

                $stmt = $this->pdo->prepare("
                    UPDATE orders
                    SET status = 'cancelled'
                    WHERE id = :id
                ");

                $stmt->execute([':id' => (int) $payment['order_id']]);
            }

            $this->pdo->commit();

            return [
                'payment_id' => (int) $payment['id'],
                'order_id' => (int) $payment['order_id'],
                'status' => $status,
                'subscription_id' => $status === 'paid'
                    ? (int) ($subscriptionId ?? $payment['sub_id'] ?? 0)
                    : null
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
