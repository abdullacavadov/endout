<?php
declare(strict_types=1);

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_csrf.php";
require_once __DIR__ . "/../_helpers.php";

header('Content-Type: application/json');

require_login($pdo);

csrf_verify($_POST['_csrf']);


$packageId = (int) $_POST['package_id'];
$months = (int) $_POST['duration'];
$currency = $_POST['currency'] ?? 'AZN';

$marketId = (int) ($_POST['market_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT id, country_id FROM markets 
    WHERE id = ? AND customer_id = ?
");
$stmt->execute([$marketId, $_SESSION['customer_id']]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$market) {
    throw new Exception("Unauthorized market");
}

$marketCountry = (int) $market['country_id'];



$allowed = ['AZN', 'USD', 'EUR', 'TRY', 'RUB', 'GBP', 'CHF', 'JPY', 'AED', 'CNY'];
if (!in_array($currency, $allowed)) {
    die(json_encode(['ok' => false]));
}


function getRates()
{
    $xml = simplexml_load_file("https://www.cbar.az/currencies/" . date('d.m.Y') . ".xml");

    $rates = [
        'AZN' => 1 // baza
    ];

    foreach ($xml->ValType as $type) {
        foreach ($type->Valute as $valute) {
            $code = (string) $valute['Code'];
            $nominal = (float) $valute->Nominal;
            $value = (float) $valute->Value;

            $rates[$code] = $value / $nominal;
        }
    }

    return $rates;
}

function convertCurrency($amount, $from, $to, $rates)
{
    if ($from === $to)
        return $amount;

    if (!isset($rates[$from]) || !isset($rates[$to])) {
        return null; // unsupported currency
    }

    // from → AZN
    $azn = $amount * $rates[$from];

    // AZN → to
    return $azn / $rates[$to];
}




function getDiscount($months)
{
    return match ($months) {
        3 => 0.05,
        6 => 0.10,
        12 => 0.15,
        default => 0
    };
}

$stmt = $pdo->query("SELECT id, name, country_audience FROM countries ORDER BY name");
$allCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);

function calculateTotal($pdo, $marketId, $packageId, $months, $targetCurrency, $marketCountry)
{
    $stmt = $pdo->prepare("SELECT base_price, currency FROM packages WHERE id=? AND is_active=1");
    $stmt->execute([$packageId]);
    $pkg = $stmt->fetch();

    if (!$pkg)
        throw new Exception("Invalid package");

    if ((float) $pkg['base_price'] <= 0) {

        $stmt = $pdo->prepare("
        SELECT id
        FROM countries
        WHERE id = ?
        AND country_audience > 0
        LIMIT 1
    ");

        $stmt->execute([$marketCountry]);

        $exists = $stmt->fetchColumn();

        if (!$exists) {

            return [
                'total' => null,
                'features' => [],
                'invalid' => true
            ];
        }
    }

    $stmt = $pdo->prepare("
        SELECT id, feature_key, feature_value
        FROM package_features
        WHERE package_id = ?
    ");
    $stmt->execute([$packageId]);
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT ct.coefficient
        FROM markets m
        JOIN countries ct ON ct.id = m.country_id
        WHERE m.id=?
    ");
    $stmt->execute([$marketId]);
    $market = $stmt->fetch();

    $coeff = (float) $market['coefficient'];

    $stmt = $pdo->prepare("
        SELECT SUM(c.country_audience) as total_audience
        FROM market_ad_countries mac
        JOIN countries c ON c.id = mac.country_id
        WHERE mac.market_id=?
    ");
    $stmt->execute([$marketId]);
    $row = $stmt->fetch();

    $sumAudience = (float) ($row['total_audience'] ?? 0);
    if ($sumAudience <= 0)
        throw new Exception("No audience");

    $rates = getRates();

    $baseUSD = convertCurrency($pkg['base_price'], $pkg['currency'], "USD", $rates);

    $userBase = $baseUSD * $coeff;
    $multiplier = $sumAudience / 10000000;

    $totalUSD = $userBase * $multiplier;

    $discount = getDiscount($months);

    $totalUSD *= $months * (1 - $discount);

    $final = convertCurrency($totalUSD, "USD", $targetCurrency, $rates);

    return [
        'total' => round($final, 2),
        'features' => $features
    ];
}


$stmt = $pdo->query("SELECT id FROM packages WHERE is_active=1");
$pkgIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

$packages = [];

foreach ($pkgIds as $pid) {
    $pkgData = calculateTotal($pdo, $marketId, $pid, $months, $currency, $marketCountry);


    $packages[$pid] = [
        'total' => $pkgData['total'],
        'features' => $pkgData['features'],
        'invalid' => $pkgData['invalid'] ?? false
    ];
}


try {
    $totalData = calculateTotal($pdo, $marketId, $packageId, $months, $currency, $marketCountry);

    echo json_encode([
        'ok' => true,
        'total' => $totalData['total'],
        'packages' => $packages,
        'features' => $totalData['features'],
        'countries' => [],
        'allCountries' => $allCountries,
        'marketCountry' => $marketCountry
    ]);

} catch (Exception $e) {
    echo json_encode(['ok' => false]);
}