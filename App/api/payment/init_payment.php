<?php
declare(strict_types=1);


require_once __DIR__ . "/../../inc/config.php";

require_once __DIR__ . "/PaymentService.php";

require_once __DIR__ . "/../_csrf.php";
require_once __DIR__ . "/../_helpers.php";


header('Content-Type: application/json');


try {


    require_login($pdo);


    $customerId = (int) $_SESSION['customer_id'];



    $input = json_decode(
        file_get_contents("php://input"),
        true
    );


    if (!is_array($input)) {
        throw new Exception("Invalid request");
    }



    $orderId = (int) ($input['order_id'] ?? 0);



    if (!$orderId) {
        throw new Exception("Order ID tələb olunur");
    }




    /*
    |--------------------------------------------------------------------------
    | Order yoxlanışı
    |--------------------------------------------------------------------------
    */


    $stmt = $pdo->prepare("
        SELECT
            id,
            customer_id,
            total,
            currency,
            status

        FROM orders

        WHERE id = ?

        LIMIT 1
    ");


    $stmt->execute([$orderId]);


    $order = $stmt->fetch(PDO::FETCH_ASSOC);



    if (!$order) {
        throw new Exception("Order tapılmadı");
    }



    if ((int) $order['customer_id'] !== $customerId) {
        throw new Exception("Unauthorized order");
    }



    if ($order['status'] !== 'pending') {
        throw new Exception("Order artıq emaldadır");
    }





    /*
    |--------------------------------------------------------------------------
    | Payment yarat
    |--------------------------------------------------------------------------
    */


    $paymentService = new PaymentService($pdo);



    $paymentId = $paymentService->create([

        'order_id' => $orderId,

        'cust_id' => $customerId,

        'provider' => $input['provider'] ?? 'test',

        'provider_order_id' => (string) $orderId,

        'amount' => $order['total'],

        'currency' => $order['currency']

    ]);





    /*
    |--------------------------------------------------------------------------
    | Transaction başlanğıcı
    |--------------------------------------------------------------------------
    */


    $transactionId = $paymentService->addTransaction([

        'payment_id' => $paymentId,

        'status' => 'created',

        'request_data' => [

            'order_id' => $orderId,

            'amount' => $order['total'],

            'currency' => $order['currency']

        ]

    ]);





    echo json_encode([

        'ok' => true,

        'payment_id' => $paymentId,

        'transaction_id' => $transactionId,

        'amount' => $order['total'],

        'currency' => $order['currency']

    ]);



} catch (Throwable $e) {


    http_response_code(400);


    echo json_encode([

        'ok' => false,

        'message' => $e->getMessage(),

        'file' => $e->getFile(),

        'line' => $e->getLine()

    ]);

}