<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../../inc/config.php";

$data = json_decode(file_get_contents("php://input"), true);

$marketId = (int)($data['market_id'] ?? 0);
$field    = $data['field'] ?? '';
$value    = trim($data['value'] ?? '');

$allowed = ['phone_number', 'email', 'address', 'description', 'url', 'promocode', 'promodiscount'];

if (!$marketId || !in_array($field, $allowed)) {
    echo json_encode(['ok' => false, 'error' => 'Yanlış sorğu']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE markets SET {$field} = ? WHERE id = ?");
    $stmt->execute([$value, $marketId]);

    echo json_encode(['ok' => true]);

} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'error' => 'DB xətası']);
}