<?php
require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json; charset=utf-8");

// 🔒 login check
if (empty($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'login_required'
    ]);
    exit;
}

$user_id = (int) $_SESSION['customer_id'];
$market_id = isset($_POST['market_id']) ? (int) $_POST['market_id'] : 0;

if ($market_id <= 0) {
    echo json_encode(['status' => 'error']);
    exit;
}

// 🔥 toggle (atomic)
$stmt = $pdo->prepare("
   INSERT INTO market_subs (customer_id, market_id, is_active)
VALUES (:user_id, :market_id, 1)
ON DUPLICATE KEY UPDATE
    is_active = IF(is_active = 1, 0, 1),
    unsubscribed_at = IF(is_active = 1, NOW(), NULL);
");
$stmt->execute([
    ':user_id' => $user_id,
    ':market_id' => $market_id
]);

// yeni statusu qaytar
$stmt = $pdo->prepare("
    SELECT is_active 
    FROM market_subs 
    WHERE customer_id = ? AND market_id = ?
");
$stmt->execute([$user_id, $market_id]);
$is_active = (bool) $stmt->fetchColumn();

echo json_encode([
    'status' => 'ok',
    'subscribed' => $is_active
]);