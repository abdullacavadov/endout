<?php
require_once __DIR__ . "/../../inc/config.php"; 
require_once __DIR__ . "/../_helpers.php";

require_login($pdo);

$customerId = (int)($_SESSION['customer_id'] ?? 0);

// 1) Customer
$stmt = $pdo->prepare("SELECT id, full_name, phone, email FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$customer) {
  header("Location: /endout/App/login");
  exit;
}

// 2) Market
$stmt = $pdo->prepare("SELECT *,
                      m.name AS market_name,
                      m.type AS market_type,
                      m.id AS market_id,
                      ct.coefficient AS cust_coefficient 

                      FROM markets m
                      LEFT JOIN countries ct ON ct.id = m.country_id
                      WHERE customer_id = ? LIMIT 1");
$stmt->execute([$customerId]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$market) {
  header("Location: /endout/App/signup-market");
  exit;
}



// 3) Seçilmiş ölkələr + audience
$stmt = $pdo->prepare("
  SELECT *,
    c.id AS country_id,
    c.name AS country_name,
    COALESCE(c.country_audience, 0) AS country_audience
  FROM market_ad_countries mac
  JOIN countries c ON c.id = mac.country_id
  WHERE mac.market_id = ?
  ORDER BY c.name ASC
");
$stmt->execute([(int)$market['market_id']]);
$adCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4) Paketlər
$stmt = $pdo->prepare("
  SELECT id, name, slug, duration_days, base_price, currency, description
  FROM packages
  WHERE is_active = 1
  ORDER BY sort_order ASC, id ASC
");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5) Paket feature-ları
$stmt = $pdo->prepare("
  SELECT package_id, feature_key, value_type, feature_value
  FROM package_features
  WHERE is_active = 1
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6) Bütün ölkələr (add üçün) + audience
$stmt = $pdo->prepare("
  SELECT
    c.id AS country_id,
    c.name AS country_name,
    COALESCE(c.country_audience, 0) AS country_audience
  FROM countries c
  LEFT JOIN country_stats cs ON cs.country_id = c.id
  ORDER BY c.name ASC
");
$stmt->execute();
$allCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);



$packageFeatures = [];
foreach ($rows as $r) {
  $pid = (int)$r['package_id'];
  $k   = (string)$r['feature_key'];
  $t   = (string)$r['value_type'];
  $vRaw = (string)$r['feature_value'];

  switch ($t) {
    case 'bool':    $v = ((int)$vRaw) ? 1 : 0; break;
    case 'int':     $v = (int)$vRaw; break;
    case 'decimal': $v = (float)$vRaw; break;
    case 'json':
      $tmp = json_decode($vRaw, true);
      $v = (json_last_error() === JSON_ERROR_NONE) ? $tmp : $vRaw;
      break;
    default:        $v = $vRaw;
  }

  $packageFeatures[$pid][$k] = $v;
}

$jsPayload = [
  'customer' => [
    'id' => (int)$customer['id'],
    'full_name' => $customer['full_name'] ?? '',
    'phone' => $customer['phone'] ?? '',
    'email' => $customer['email'] ?? ''
  ],
  'market' => [
    'id' => (int)$market['market_id'],
    'name' => $market['market_name'] ?? '',
    'type' => $market['market_type'] ?? ''
  ],
  'adCountries' => array_map(fn($x) => [
    'country_id' => (int)$x['country_id'],
    'country_name' => (string)$x['country_name'],
    'country_audience' => (int)$x['country_audience']
  ], $adCountries),
  'packages' => array_map(function($p) use ($packageFeatures){
    $pid = (int)$p['id'];
    return [
      'id' => $pid,
      'name' => (string)$p['name'],
      'slug' => (string)$p['slug'],
      'duration_days' => (int)$p['duration_days'],
      'base_price' => (float)$p['base_price'],
      'currency' => (string)$p['currency'],
      'description' => (string)($p['description'] ?? ''),
      'features' => $packageFeatures[$pid] ?? []
    ];
  }, $packages),
];

// payload-a əlavə et
$jsPayload['allCountries'] = array_map(fn($x)=>[
  'country_id' => (int)$x['country_id'],
  'country_name' => (string)$x['country_name'],
  'country_audience' => (int)$x['country_audience']
], $allCountries);
