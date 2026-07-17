<?php
declare(strict_types=1);

require_once __DIR__ . "/../../inc/config.php";

require_once __DIR__ . "/PaymentService.php";
require_once __DIR__ . "/PaymentProcessor.php";

require_once __DIR__ . "/../../services/payment/gateway/TestGateway.php";

header('Content-Type: application/json');

try {

    /*
    |--------------------------------------------------------------------------
    | Callback payload
    |--------------------------------------------------------------------------
    */

    $payload = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (!is_array($payload)) {
        $payload = $_POST;
    }

    if (!is_array($payload)) {
        throw new Exception('Invalid callback payload.');
    }


    /*
    |--------------------------------------------------------------------------
    | Gateway
    |--------------------------------------------------------------------------
    */

    $gateway = new TestGateway();

    $paymentService = new PaymentService(
        $pdo,
        $gateway
    );

    $processor = new PaymentProcessor(
        $pdo,
        $paymentService,
        $gateway
    );


    /*
    |--------------------------------------------------------------------------
    | Callback emalı
    |--------------------------------------------------------------------------
    */

    $result = $processor->handleCallback($payload);


    echo json_encode(array_merge(
        [
            'ok' => true
        ],
        $result
    ));

} catch (Throwable $e) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}