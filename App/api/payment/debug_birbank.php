<?php
declare(strict_types=1);

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../../inc/payment_config.php";
require_once __DIR__ . "/../../services/payment/gateway/BirBankGateway.php";
require_once __DIR__ . "/../_helpers.php";

require_login($pdo);

$providerPaymentId = trim((string) ($_GET['id'] ?? ''));

if ($providerPaymentId === '') {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => false,
        'message' => 'Provider payment ID is required.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $gateway = new BirBankGateway(...birbank_config());

    $result = $gateway->verifyPayment([
        'ID' => $providerPaymentId
    ]);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'ok' => true,
        'provider_payment_id' => $providerPaymentId,
        'http_status' => $gateway->getLastHttpCode(),
        'raw_response' => $gateway->getLastRawResponse(),
        'parsed_response' => $result
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(502);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'ok' => false,
        'provider_payment_id' => $providerPaymentId,
        'http_status' => $gateway->getLastHttpCode() ?? 0,
        'raw_response' => $gateway->getLastRawResponse() ?? '',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
