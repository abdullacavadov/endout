<?php

require_once __DIR__ . "/../../inc/config.php";


/* market id tap */

$st = $pdo->prepare("SELECT id FROM markets WHERE customer_id = ?");
$st->execute([$_SESSION['customer_id']]);

$market = $st->fetch(PDO::FETCH_ASSOC);

$market_id = $market['id'] ?? null;

/* ölkələri gətir */

$stmt = $pdo->prepare("
SELECT c.id, c.name, c.iso2
FROM market_ad_countries mac
JOIN countries c ON c.id = mac.country_id
WHERE mac.market_id = ?
");

$stmt->execute([$market_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));