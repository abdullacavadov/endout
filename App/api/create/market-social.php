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

$customerId  = (int)($_SESSION['customer_id'] ?? 0);
$marketId    = (int)($_POST['market_id'] ?? 0);
$socialName  = trim((string)($_POST['social_name'] ?? ''));
$socialUrl   = trim((string)($_POST['social_url'] ?? ''));
$socialIcon  = trim((string)($_POST['social_icon'] ?? ''));

if ($marketId <= 0 || $socialName === '' || $socialUrl === '') {
  json_out(['ok' => false, 'error' => 'Məlumat natamamdır.'], 422);
}

if (mb_strlen($socialName) > 100) {
  json_out(['ok' => false, 'error' => 'Social adı çox uzundur.'], 422);
}
if (mb_strlen($socialUrl) > 255) {
  json_out(['ok' => false, 'error' => 'URL çox uzundur.'], 422);
}
if ($socialIcon !== '' && mb_strlen($socialIcon) > 150) {
  json_out(['ok' => false, 'error' => 'Icon çox uzundur.'], 422);
}

// Market ownership check
$stmt = $pdo->prepare("SELECT id FROM markets WHERE id = ? AND customer_id = ? LIMIT 1");
$stmt->execute([$marketId, $customerId]);
if (!$stmt->fetchColumn()) {
  json_out(['ok' => false, 'error' => 'İcazə yoxdur.'], 403);
}

// (İstəyə görə) eyni social təkrar olmasın:
$chk = $pdo->prepare("SELECT id FROM markets_socials WHERE market_id = ? AND social_name = ? LIMIT 1");
$chk->execute([$marketId, $socialName]);
if ($chk->fetchColumn()) {
  json_out(['ok' => false, 'error' => 'Bu sosial şəbəkə artıq əlavə edilib.'], 409);
}

try {
  $ins = $pdo->prepare("
    INSERT INTO markets_socials (market_id, social_name, social_url, social_icon)
    VALUES (?, ?, ?, ?)
  ");
  $ins->execute([$marketId, $socialName, $socialUrl, $socialIcon ?: null]);

  $newId = (int)$pdo->lastInsertId();

  json_out([
    'ok' => true,
    'social' => [
      'id' => $newId,
      'social_name' => $socialName,
      'social_url' => $socialUrl,
      'social_icon' => $socialIcon ?: 'fa-link'
    ]
  ]);
} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server xətası.'], 500);
}