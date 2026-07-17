<?php
declare(strict_types=1);

interface PaymentGatewayInterface
{
    /**
     * Yeni ödəniş yaradır.
     */
    public function createPayment(array $payment): array;

    /**
     * Callback/Webhook sonrası ödənişi yoxlayır.
     */
    public function verifyPayment(array $payload): array;

    /**
     * Refund əməliyyatı.
     */
    public function refundPayment(array $payment): array;
}