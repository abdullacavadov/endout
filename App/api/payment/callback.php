<?php
require_once __DIR__ . "/../../inc/config.php";

$orderId = (int)$_GET['order_id'];

// order çək
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

// ⚠️ yenidən hesabla
$real = calculateTotal($pdo, $order['market_id'], $order['package_id'], 3, $order['currency']);

if (abs($real - $order['amount']) > 0.01) {
    // fraud
    $pdo->prepare("UPDATE orders SET status='fraud' WHERE id=?")->execute([$orderId]);
    die("Fraud detected");
}

// success
$pdo->prepare("UPDATE orders SET status='paid' WHERE id=?")->execute([$orderId]);