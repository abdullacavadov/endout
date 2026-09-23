<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/admin_auth.php';
require_admin_permission($pdo, 'reports');

$type = (string) ($_GET['type'] ?? '');
$filename = 'endout-' . preg_replace('/[^a-z0-9_-]/i', '-', $type) . '-' . date('Ymd-His') . '.csv';

switch ($type) {
    case 'customers':
        $stmt = $pdo->query("SELECT id, full_name, email, phone, type, status, phone_verified, email_verified, created_at FROM customers ORDER BY id DESC");
        break;
    case 'listings':
        $stmt = $pdo->query("SELECT id, customer_id, market_id, title, price, currency, status, views, created_at FROM listings ORDER BY id DESC");
        break;
    case 'payments':
        $stmt = $pdo->query("SELECT id, order_id, cust_id, provider, provider_payment_id, amount, currency, status, paid_at, created_at FROM payments ORDER BY id DESC");
        break;
    case 'subscriptions':
        $stmt = $pdo->query("SELECT id, cust_id, package_id, status, starts_at, ends_at, price_paid, currency, auto_renew, created_at FROM customer_subs ORDER BY id DESC");
        break;
    default:
        http_response_code(422); exit('Naməlum export növü.');
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$out = fopen('php://output', 'wb');
fwrite($out, "\xEF\xBB\xBF");
$first = true;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($first) { fputcsv($out, array_keys($row)); $first = false; }
    fputcsv($out, $row);
}
fclose($out);
admin_audit($pdo, 'report.exported', 'report', null, null, ['type'=>$type]);
exit;
