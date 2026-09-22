<?php
declare(strict_types=1);

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/PaymentService.php";
require_once __DIR__ . "/PaymentProcessor.php";
require_once __DIR__ . "/../../services/payment/gateway/BirBankGateway.php";
require_once __DIR__ . "/../../inc/payment_config.php";

$payload = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($payload)) {
    $payload = $_GET;
}

try {
    if (!is_array($payload)) {
        throw new Exception('Invalid callback payload.');
    }

    $gateway = new BirBankGateway(...birbank_config());

    $paymentService = new PaymentService($pdo);

    $processor = new PaymentProcessor(
        $pdo,
        $paymentService,
        $gateway
    );

    $result = $processor->handleCallback($payload);

    // callback.php App/api/payment qovluğundadır; nəticə səhifəsi isə App/payment-result.php-dir.
    $app_root_url = rtrim(dirname(dirname(dirname($base_url))), '/');

    $target = $app_root_url
        . '/App/payment-result.php?payment_id='
        . (int) $result['payment_id'];

    header('Location: ' . $target, true, 303);
    exit;
} catch (Throwable $e) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Ödəniş emalı zamanı xəta baş verdi.';
}
