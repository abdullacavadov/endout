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

    /**
     * Payment prosesini başladır
     */
    public function initPayment(array $order, int $customerId, string $provider = 'test'): array
    {
        /*
        |--------------------------------------------------------------------------
        | Payment yarat
        |--------------------------------------------------------------------------
        */

        $paymentId = $this->paymentService->create([
            'order_id' => (int) $order['id'],
            'cust_id' => $customerId,
            'provider' => $provider,
            'provider_order_id' => (string) $order['id'],
            'amount' => $order['total'],
            'currency' => $order['currency']
        ]);


        /*
        |--------------------------------------------------------------------------
        | Gateway
        |--------------------------------------------------------------------------
        */

        $gatewayResponse = $this->gateway->createPayment([
            'payment_id' => $paymentId,
            'order_id' => (int) $order['id'],
            'amount' => $order['total'],
            'currency' => $order['currency']
        ]);


        if (!$gatewayResponse['success']) {
            throw new Exception('Gateway payment initialization failed.');
        }


        /*
        |--------------------------------------------------------------------------
        | Payment update
        |--------------------------------------------------------------------------
        */

        $this->paymentService->updateGatewayData(
            $paymentId,
            $gatewayResponse['provider_payment_id'],
            $gatewayResponse['raw_response']
        );




        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

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
    }



    /**
     * Gateway callback emalı
     */
    public function handleCallback(array $payload): array
    {
        /*
        |--------------------------------------------------------------------------
        | Gateway doğrulaması
        |--------------------------------------------------------------------------
        */

        $result = $this->gateway->verifyPayment($payload);

        if (!$result['success']) {
            throw new Exception('Payment verification failed.');
        }


        /*
        |--------------------------------------------------------------------------
        | Payment tap
        |--------------------------------------------------------------------------
        */

        $payment = $this->paymentService->findByProviderPaymentId(
            $result['provider_payment_id']
        );

        if (!$payment) {
            throw new Exception('Payment not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction əlavə et
        |--------------------------------------------------------------------------
        */

        $this->paymentService->addTransaction([
            'payment_id' => (int) $payment['id'],
            'provider_reference' => $result['provider_reference'] ?? null,
            'status' => $result['status'],
            'request_data' => $payload,
            'response_data' => $result['raw_response']
        ]);


        /*
        |--------------------------------------------------------------------------
        | Payment status yenilə
        |--------------------------------------------------------------------------
        */

        $this->paymentService->updateStatus(
            (int) $payment['id'],
            $result['status']
        );


        /*
        |--------------------------------------------------------------------------
        | Order status yenilə
        |--------------------------------------------------------------------------
        */

        $stmt = $this->pdo->prepare("
        UPDATE orders
        SET status = :status
        WHERE id = :id
    ");

        $stmt->execute([
            ':status' => $result['status'],
            ':id' => $payment['order_id']
        ]);


        return [
            'payment_id' => (int) $payment['id'],
            'order_id' => (int) $payment['order_id'],
            'status' => $result['status']
        ];
    }
}