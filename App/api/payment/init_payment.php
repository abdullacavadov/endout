<?php
declare(strict_types=1);

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/PaymentService.php";
require_once __DIR__ . "/PaymentProcessor.php";
require_once __DIR__ . '/../../services/payment/gateway/TestGateway.php';
require_once __DIR__ . '/../../services/payment/gateway/BirBankGateway.php';
require_once __DIR__ . "/../_csrf.php";
require_once __DIR__ . "/../_helpers.php";
require_once __DIR__ . "/../../inc/payment_config.php";

header('Content-Type: application/json; charset=utf-8');

try {
    require_login($pdo);

    csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

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

    if (!in_array($order['status'], ['pending', 'failed'], true)) {
        throw new Exception("Order artıq emaldadır");
    }

    if ($order['status'] === 'failed') {
        $resetStmt = $pdo->prepare("
            UPDATE orders
            SET status = 'pending'
            WHERE id = ?
              AND customer_id = ?
              AND status = 'failed'
        ");

        $resetStmt->execute([
            $orderId,
            $customerId
        ]);

        $order['status'] = 'pending';
    }

    $provider = strtolower((string) ($input['provider'] ?? 'birbank'));

    if ($provider === 'test') {
        $gateway = new TestGateway();
    } elseif ($provider === 'birbank') {
        $gateway = new BirBankGateway(...birbank_config());
    } else {
        throw new Exception("Dəstəklənməyən payment provider.");
    }

    $paymentService = new PaymentService($pdo);

    $processor = new PaymentProcessor(
        $pdo,
        $paymentService,
        $gateway
    );

    $result = $processor->initPayment(
        $order,
        $customerId,
        $provider
    );

    echo json_encode(array_merge(
        ['ok' => true],
        $result
    ));
} catch (Throwable $e) {
    http_response_code(400);

    error_log('payment init failed: ' . $e->getMessage());

    echo json_encode([
        'ok' => false,
        'message' => $e instanceof Exception
            ? $e->getMessage()
            : 'Ödəniş başladılarkən xəta baş verdi.'
    ]);
}
