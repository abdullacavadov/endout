<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$customer_id = (int) $_SESSION['customer_id'];
$tender_id = (int) ($_POST['id'] ?? 0);

if ($tender_id <= 0) {
    exit(json_encode(['error' => 'Invalid tender id']));
}

try {

    $pdo->beginTransaction();

    /* listing sahibini yoxla */
    $stmt = $pdo->prepare("
        SELECT id 
        FROM tenders 
        WHERE id=? AND customer_id=?
        LIMIT 1
    ");
    $stmt->execute([$tender_id, $customer_id]);

    if (!$stmt->fetch()) {
        throw new Exception("Tender not found");
    }

    /* listing sil */
    $stmt = $pdo->prepare("UPDATE tenders SET deleted_at=NOW(), status='deleted' WHERE id=?");
    $stmt->execute([$tender_id]);

    $pdo->commit();

    echo json_encode([
        'success' => true
    ]);
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}