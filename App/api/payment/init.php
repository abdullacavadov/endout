<?php
require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

require_login($pdo);
csrf_verify($_POST['_csrf'] ?? null);


$packageId = (int)$_POST['package_id'];
$months    = (int)$_POST['duration_months'];
$currency  = $_POST['currency'] ?? 'AZN';

// ⚠️ client_total IGNORE ET
$total = calculateTotal($pdo, $marketId, $packageId, $months, $currency);

// order yarat
$stmt = $pdo->prepare("
    INSERT INTO orders (customer_id, market_id, package_id, amount, currency, status)
    VALUES (?,?,?,?,?, 'pending')
");
$stmt->execute([$customerId, $marketId, $packageId, $total, $currency]);

$orderId = $pdo->lastInsertId();

// local gateway redirect
header("Location: /payment-gateway/pay?order_id=".$orderId);
exit;