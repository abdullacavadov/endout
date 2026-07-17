<?php
declare(strict_types=1);

require_once __DIR__ . '/PaymentGatewayInterface.php';

class TestGateway implements PaymentGatewayInterface
{
    public function createPayment(array $payment): array
    {
        $providerPaymentId = 'TEST_PAY_' . uniqid();

        return [
            'success' => true,

            // Provider tərəfindən verilən unikal ID
            'provider_payment_id' => $providerPaymentId,

            // Test səhifəsinə yönləndirmə
            'redirect_url' => '/payment/test.php?payment=' . urlencode($providerPaymentId),

            // Provider-in xam cavabı
            'raw_response' => [
                'provider' => 'test',
                'status' => 'initiated',
                'payment_id' => $providerPaymentId,
                'created_at' => date('c')
            ]
        ];
    }

    public function verifyPayment(array $payload): array
    {
        return [
            'success' => true,
            'status' => 'paid',
            'provider_payment_id' => $payload['provider_payment_id'] ?? null,
            'provider_reference' => 'TEST_TXN_' . uniqid(),
            'paid_at' => date('Y-m-d H:i:s'),
            'raw_response' => [
                'provider' => 'test',
                'callback' => $payload,
                'verified_at' => date('c')
            ]
        ];
    }

    public function refundPayment(array $payment): array
    {
        return [
            'success' => true,
            'status' => 'refunded',
            'provider_reference' => 'REFUND_' . uniqid(),
            'raw_response' => []
        ];
    }
}