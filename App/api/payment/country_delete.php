<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

require_login($pdo);
require_post();

csrf_verify($_POST['_csrf'] ?? null);

$customerId = (int)($_SESSION['customer_id'] ?? 0);
$countryId  = (int)($_POST['country_id'] ?? 0);

if ($countryId <= 0) json_out(['ok'=>false,'error'=>'country_id yanlışdır.'], 422);

// market tap
$stmt = $pdo->prepare("SELECT id FROM markets WHERE customer_id = ? LIMIT 1");
$stmt->execute([$customerId]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$market) json_out(['ok'=>false,'error'=>'Market tapılmadı.'], 404);

// yalnız həmin marketin ölkəsini sil
$stmt = $pdo->prepare("DELETE FROM market_ad_countries WHERE market_id = ? AND country_id = ? LIMIT 1");
$stmt->execute([(int)$market['id'], $countryId]);

json_out(['ok'=>true]);