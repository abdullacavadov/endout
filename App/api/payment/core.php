<?php
function getDiscount($months) {
    return match($months) {
        3 => 0.05,
        6 => 0.10,
        12 => 0.15,
        default => 0
    };
}

function calculateTotal($pdo, $marketId, $packageId, $months, $targetCurrency) {

    // 1. package
    $stmt = $pdo->prepare("SELECT base_price, currency FROM packages WHERE id=? AND is_active=1");
    $stmt->execute([$packageId]);
    $pkg = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pkg) throw new Exception("Invalid package");

    // 2. market coefficient
    $stmt = $pdo->prepare("
        SELECT COALESCE(ct.coefficient,1) as coeff
        FROM markets m
        LEFT JOIN countries ct ON ct.id = m.country_id
        WHERE m.id=?
    ");
    $stmt->execute([$marketId]);
    $market = $stmt->fetch();
    $coeff = max(1, (float)$market['coeff']);

    // 3. countries (STRICT)
    $stmt = $pdo->prepare("
        SELECT c.country_audience
        FROM market_ad_countries mac
        JOIN countries c ON c.id = mac.country_id
        WHERE mac.market_id=?
    ");
    $stmt->execute([$marketId]);
    $countries = $stmt->fetchAll();

    if (!$countries) throw new Exception("No countries");

    // 4. rates
    $rates = getRates();

    // package → AZN
    $baseAZN = convertCurrency($pkg['base_price'], $pkg['currency'], "AZN", $rates);

    $sumAZN = 0;

    foreach ($countries as $c) {
        $aud = (float)$c['country_audience'];
        $mult = $aud / 10000000;

        $sumAZN += ($baseAZN * $coeff * $mult);
    }

    // duration
    $factor = $months * (1 - getDiscount($months));
    $sumAZN *= $factor;

    // convert to target
    $total = convertCurrency($sumAZN, "AZN", $targetCurrency, $rates);

    return round($total, 2);
}