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
$socialId   = (int)($_POST['social_id'] ?? 0);
$socialUrl  = trim((string)($_POST['social_url'] ?? ''));

if ($socialId <= 0 || $socialUrl === '') {
  json_out(['ok' => false, 'error' => 'Məlumat natamamdır.'], 422);
}
if (mb_strlen($socialUrl) > 255) {
  json_out(['ok' => false, 'error' => 'URL çox uzundur.'], 422);
}

try {
  // Ownership check: social -> market -> customer
  $stmt = $pdo->prepare("
    SELECT ms.id
    FROM markets_socials ms
    INNER JOIN markets m ON m.id = ms.market_id
    WHERE ms.id = ? AND m.customer_id = ?
    LIMIT 1
  ");
  $stmt->execute([$socialId, $customerId]);

  if (!$stmt->fetchColumn()) {
    json_out(['ok' => false, 'error' => 'İcazə yoxdur.'], 403);
  }

  $upd = $pdo->prepare("UPDATE markets_socials SET social_url = ? WHERE id = ? LIMIT 1");
  $upd->execute([$socialUrl, $socialId]);

  json_out(['ok' => true, 'social_url' => $socialUrl]);

} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server xətası.'], 500);
}