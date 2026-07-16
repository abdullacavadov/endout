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

// country məlumatını qaytaraq (ad + audience)
$stmt = $pdo->prepare("
  SELECT id AS country_id, name AS country_name, country_audience FROM countries WHERE id = ? LIMIT 1
");
$stmt->execute([$countryId]);
$country = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$country) json_out(['ok'=>false,'error'=>'Ölkə tapılmadı.'], 404);


// artıq varsa, insert etmə (təhlükəsiz)
$stmt = $pdo->prepare("SELECT 1 FROM market_ad_countries WHERE market_id=? AND country_id=? LIMIT 1");
$stmt->execute([(int)$market['id'], $countryId]);
if ($stmt->fetchColumn()) {
  json_out(['ok'=>true, 'country'=>[
    'country_id'=>(int)$country['country_id'],
    'country_name'=>(string)$country['country_name'],
    'country_audience'=>(int)$country['country_audience'],
  ], 'already'=>true]);
}

// Əgər seçilən ölkə artıq bu marketə məxsus deyilsə, əlavə edirik
if ((int)$country['country_id'] === $countryId) {
  // Ölkə məlumatı düzgündür, əlavə etməyə davam edirik
} else {
  json_out(['ok'=>false,'error'=>'Ölkə məlumatı yanlışdır.'], 422);
}

$stmt = $pdo->prepare("INSERT INTO market_ad_countries (market_id, country_id) VALUES (?, ?)");
$stmt->execute([(int)$market['id'], $countryId]);

json_out(['ok'=>true, 'country'=>[
  'country_id'=>(int)$country['country_id'],
  'country_name'=>(string)$country['country_name'],
  'country_audience'=>(int)$country['country_audience'],
]]);