<?php
declare(strict_types=1);

require_once __DIR__ . "/../_db.php";
require_once __DIR__ . "/../_helpers.php";

require_post();

$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
if (!is_array($body)) {
  json_out(['ok' => false, 'error' => 'Invalid JSON'], 422);
}

$codes = $body['country_codes'] ?? [];
$marketType = (string)($body['market_type'] ?? '');

if (!is_array($codes) || empty($codes)) {
  json_out(['ok' => true, 'countries' => 0, 'totalAudience' => 0]);
}

$codes = array_values(array_unique(array_filter(array_map(function($c){
  $c = strtoupper(trim((string)$c));
  return (strlen($c) === 2) ? $c : null;
}, $codes))));

if (empty($codes)) {
  json_out(['ok' => true, 'countries' => 0, 'totalAudience' => 0]);
}

try {
  $in = implode(",", array_fill(0, count($codes), "?"));

  $st = $pdo->prepare("
    SELECT iso2, country_audience AS audience
    FROM countries 
    WHERE iso2 IN ($in)
  ");
  $st->execute($codes);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  $totalAudience = 0;
  foreach ($rows as $r) {
    $totalAudience += (int)$r['audience'];
  }

  json_out([
    'ok' => true,
    'countries' => count($rows),
    'totalAudience' => $totalAudience
  ]);

} catch (Throwable $e) {
  json_out([
    'ok' => false,
    'error' => $e->getMessage()
  ], 500);
}
