<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);
require_login($pdo);

if (!isset($pdo) || !($pdo instanceof PDO)) {
    json_out(['ok' => false, 'error' => 'DB bağlantısı tapılmadı.'], 500);
}

$customerId = (int) ($_SESSION['customer_id'] ?? 0);
$otp = trim((string) ($_POST['otp'] ?? ''));

if (!preg_match('/^\d{6}$/', $otp)) {
    json_out(['ok' => false, 'error' => 'OTP 6 rəqəm olmalıdır.'], 422);
}

$stmt = $pdo->prepare("SELECT email_verified, email_otp_code, email_otp_expires_at
                       FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$customerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row)
    json_out(['ok' => false, 'error' => 'İstifadəçi tapılmadı.'], 404);

if ((int) ($row['email_verified'] ?? 0) === 1) {
    json_out(['ok' => true, 'message' => 'Email artıq təsdiqlənib.', 'redirect' => './dashboard']);
}

$otpHash = (string) ($row['email_otp_code'] ?? '');
$exp = (string) ($row['email_otp_expires_at'] ?? '');

if ($otpHash === '' || $exp === '') {
    json_out(['ok' => false, 'error' => 'Aktiv email təsdiq kodu yoxdur. Əvvəlcə kod göndərin.'], 422);
}

$expTs = strtotime($exp);
if (!$expTs || time() > $expTs) {
    json_out(['ok' => false, 'error' => 'Kodun vaxtı bitib. Yenidən kod göndərin.'], 422);
}

if (!password_verify($otp, $otpHash)) {
    json_out(['ok' => false, 'error' => 'Kod yanlışdır.'], 422);
}

$upd = $pdo->prepare("UPDATE customers
                      SET email_verified = 1,
                          email_otp_code = NULL,
                          email_otp_expires_at = NULL
                      WHERE id = ? LIMIT 1");
$upd->execute([$customerId]);

json_out([
    'ok' => true,
    'message' => 'Email uğurla təsdiqləndi.',
    'redirect' => './dashboard'
]);