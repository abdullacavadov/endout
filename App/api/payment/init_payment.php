<?php
declare(strict_types=1);
require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/PaymentService.php";
require_once __DIR__ . "/PaymentProcessor.php";
require_once __DIR__ . '/../../services/payment/gateway/TestGateway.php';
require_once __DIR__ . "/../_csrf.php";
require_once __DIR__ . "/../_helpers.php";

header('Content-Type: application/json');

try {
    //require_login($pdo);

    //$customerId = (int) $_SESSION['customer_id'];
    $customerId = 40; // Test üçün müvəqqəti olaraq istifadə olunur
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

    $result = $processor->initPayment(
        $order,
        $customerId,
        $input['provider'] ?? 'test'
    );

    echo json_encode(array_merge(
        ['ok' => true],
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