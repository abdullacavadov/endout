<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/admin_auth.php';
require_admin_permission($pdo, 'customers');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
admin_verify_csrf($_POST['csrf'] ?? null);

$id = (int) ($_POST['id'] ?? 0);
$status = (string) ($_POST['status'] ?? '');
if ($id <= 0 || !in_array($status, ['pending','active','blocked'], true)) {
    http_response_code(422); exit('Yanlış məlumat.');
}

$stmt = $pdo->prepare("SELECT id, status FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$old = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$old) { http_response_code(404); exit('Müştəri tapılmadı.'); }

$stmt = $pdo->prepare("UPDATE customers SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);
admin_audit($pdo, 'customer.status_changed', 'customer', $id, $old, ['status'=>$status]);

header('Location: ../customers.php?updated=1');
exit;
