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
        $paymentId = 0;

        try {
            $this->pdo->beginTransaction();

            /*
             * Order səviyyəsində lock: eyni order üçün paralel payment
             * yaradılmasının qarşısını alır.
             */
            $stmt = $this->pdo->prepare("
                SELECT id, customer_id, total, currency, status
                FROM orders
                WHERE id = ? AND customer_id = ?
                LIMIT 1
                FOR UPDATE
            ");
            $stmt->execute([(int) $order['id'], $customerId]);
            $lockedOrder = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lockedOrder) {
                throw new Exception('Order tapılmadı.');
            }

            if (!in_array($lockedOrder['status'], ['pending', 'failed'], true)) {
                throw new Exception('Order artıq emaldadır.');
            }

            $existingPayment = $this->paymentService->findActiveByOrder(
                (int) $lockedOrder['id']
            );

            if ($existingPayment) {
                if ($existingPayment['status'] === 'paid') {
                    throw new Exception('Order artıq ödənilib.');
                }

                throw new Exception('Bu order üçün artıq aktiv payment mövcuddur.');
            }

            $paymentId = $this->paymentService->create([
                'order_id' => (int) $lockedOrder['id'],
                'cust_id' => $customerId,
                'provider' => $provider,
                'provider_order_id' => (string) $lockedOrder['id'],
                'amount' => $lockedOrder['total'],
                'currency' => $lockedOrder['currency']
            ]);

            /*
             * Gateway çağırışı qısa transaction daxilində saxlanılır ki,
             * eyni order üçün ikinci payment yaradıla bilməsin.
             */
            $gatewayResponse = $this->gateway->createPayment([
                'payment_id' => $paymentId,
                'order_id' => (int) $lockedOrder['id'],
                'amount' => $lockedOrder['total'],
                'currency' => $lockedOrder['currency']
            ]);

            if (empty($gatewayResponse['success'])) {
                throw new Exception('Gateway payment initialization failed.');
            }

            if (empty($gatewayResponse['provider_payment_id'])) {
                throw new Exception('Provider payment ID is missing.');
            }

            $this->paymentService->updateGatewayData(
                $paymentId,
                (string) $gatewayResponse['provider_payment_id'],
                (array) ($gatewayResponse['raw_response'] ?? [])
            );

            $transactionId = $this->paymentService->addTransaction([
                'payment_id' => $paymentId,
                'provider_reference' => $gatewayResponse['provider_payment_id'],
                'status' => 'created',
                'request_data' => [
                    'order_id' => (int) $lockedOrder['id'],
                    'amount' => $lockedOrder['total'],
                    'currency' => $lockedOrder['currency']
                ],
                'response_data' => $gatewayResponse['raw_response'] ?? []
            ]);

            $this->pdo->commit();

            return [
                'payment_id' => $paymentId,
                'transaction_id' => $transactionId,
                'amount' => $lockedOrder['total'],
                'currency' => $lockedOrder['currency'],
                'redirect_url' => $gatewayResponse['redirect_url']
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            if ($paymentId > 0) {
                try {
                    $this->paymentService->updateStatus($paymentId, 'failed');
                } catch (Throwable $ignored) {
                    // Əsas xətanı gizlətmə.
                }
            }

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

                $localAmount = number_format((float) $payment['amount'], 2, '.', '');
                $providerAmount = $result['amount'] ?? null;
                $providerCurrency = $result['currency'] ?? null;

                /*
                 * Bəzi provider status cavablarında amount/currency ayrıca
                 * gəlməyə bilər. Bu halda payment yaradılarkən saxlanmış,
                 * redaktə edilməmiş biznes məlumatından yox, provider-in
                 * ilkin response-dakı order məlumatından istifadə edirik.
                 */
                if ($providerAmount === null || $providerCurrency === null) {
                    $storedResponse = json_decode(
                        (string) ($payment['raw_response'] ?? ''),
                        true
                    );

                    $storedOrder = is_array($storedResponse)
                        ? ($storedResponse['order'] ?? [])
                        : [];

                    if ($providerAmount === null) {
                        $providerAmount = $storedOrder['amount'] ?? null;
                    }

                    if ($providerCurrency === null) {
                        $providerCurrency = $storedOrder['currency'] ?? null;
                    }
                }

                if (
                    $providerAmount === null
                    || number_format((float) $providerAmount, 2, '.', '') !== $localAmount
                ) {
                    throw new Exception('Payment amount mismatch.');
                }

                $localCurrency = strtoupper((string) $payment['currency']);
                $providerCurrency = strtoupper((string) $providerCurrency);

                if ($providerCurrency === '' || $providerCurrency !== $localCurrency) {
                    throw new Exception('Payment currency mismatch.');
                }

                $months = (int) $context['months'];

                if (!in_array($months, [1, 3, 6, 12], true)) {
                    throw new Exception('Invalid subscription duration.');
                }

                if ((int) $context['package_is_active'] !== 1) {
                    throw new Exception('Purchased package is no longer active.');
                }

                $this->paymentService->lockCustomer(
                    (int) $payment['cust_id']
                );

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

                if (!empty($payment['sub_id'])) {
                    $this->paymentService->cancelSubscription(
                        (int) $payment['sub_id']
                    );
                }

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
