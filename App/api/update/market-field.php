<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_login_api($pdo);
csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

$data = json_decode(file_get_contents("php://input"), true);

$marketId = (int) ($data['market_id'] ?? 0);
$field = (string) ($data['field'] ?? '');
$value = trim((string) ($data['value'] ?? ''));

$allowed = [
    'phone_number',
    'email',
    'address',
    'description',
    'url',
    'promocode',
    'promodiscount'
];

if ($marketId <= 0 || !in_array($field, $allowed, true)) {
    json_out(['ok' => false, 'error' => 'Yanlış sorğu'], 422);
}

$customerId = (int) $_SESSION['customer_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id
        FROM markets
        WHERE id = ? AND customer_id = ?
        LIMIT 1
    ");
    $stmt->execute([$marketId, $customerId]);

    if (!$stmt->fetchColumn()) {
        json_out(['ok' => false, 'error' => 'Bu bazara giriş icazəniz yoxdur.'], 403);
    }

    $stmt = $pdo->prepare("UPDATE markets SET {$field} = ? WHERE id = ? AND customer_id = ?");
    $stmt->execute([$value, $marketId, $customerId]);

    json_out(['ok' => true]);
} catch (Throwable $e) {
    error_log('market-field update failed: ' . $e->getMessage());
    json_out(['ok' => false, 'error' => 'Əməliyyat zamanı xəta baş verdi.'], 500);
}
