<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);
require_login($pdo);

if (!isset($pdo) || !($pdo instanceof PDO)) {
  json_out(['ok' => false, 'error' => 'DB bağlantısı tapılmadı.'], 500);
}

$customerId = (int)($_SESSION['customer_id'] ?? 0);
$marketId   = (int)($_POST['market_id'] ?? 0);
$name       = trim((string)($_POST['name'] ?? ''));

if ($marketId <= 0 || $name === '') {
  json_out(['ok' => false, 'error' => 'Məlumatlar natamamdır.'], 422);
}
if (mb_strlen($name) < 2 || mb_strlen($name) > 150) {
  json_out(['ok' => false, 'error' => 'Mağaza adı 2-150 simvol arası olmalıdır.'], 422);
}

try {
  // market bu user-ındır?
  $stmt = $pdo->prepare("SELECT id FROM markets WHERE id = ? AND customer_id = ? LIMIT 1");
  $stmt->execute([$marketId, $customerId]);
  if (!$stmt->fetchColumn()) {
    json_out(['ok' => false, 'error' => 'İcazə yoxdur.'], 403);
  }

  $upd = $pdo->prepare("UPDATE markets SET name = ?, updated_at = NOW() WHERE id = ? AND customer_id = ? LIMIT 1");
  $upd->execute([$name, $marketId, $customerId]);

  json_out(['ok' => true, 'message' => 'Yeniləndi.']);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server xətası.'], 500);
}