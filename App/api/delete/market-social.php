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

$customerId = (int) ($_SESSION['customer_id'] ?? 0);
$socialId = (int) ($_POST['social_id'] ?? 0);

if ($socialId <= 0) {
  json_out(['ok' => false, 'error' => 'Məlumat natamamdır.'], 422);
}

try {
  // 1) ownership check + market_id tap
  $stmt = $pdo->prepare("
    SELECT ms.id
    FROM markets_socials ms
    INNER JOIN markets m ON m.id = ms.market_id
    WHERE ms.id = ? AND m.customer_id = ?
    LIMIT 1
  ");
  $stmt->execute([$socialId, $customerId]);

  if (!$stmt->fetchColumn()) {
    json_out(['ok' => false, 'error' => 'Silinmədi və ya icazə yoxdur.'], 403);
  }

  // 2) delete
  $del = $pdo->prepare("DELETE FROM markets_socials WHERE id = ? LIMIT 1");
  $del->execute([$socialId]);

  if ($del->rowCount() < 1) {
    json_out(['ok' => false, 'error' => 'Silinmədi.'], 400);
  }

  json_out(['ok' => true]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server xətası.'], 500);
}