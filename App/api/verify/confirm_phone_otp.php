<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);

if (!isset($pdo) || !($pdo instanceof PDO)) {
  json_out(['ok' => false, 'error' => 'DB bağlantısı tapılmadı.'], 500);
}

require_login($pdo);
require_unverified($pdo);

$customerId = (int) ($_SESSION['customer_id'] ?? 0);
$otp = preg_replace('/\D+/', '', (string) ($_POST['otp'] ?? ''));

if (!preg_match('/^\d{4}$/', $otp)) {
  json_out(['ok' => false, 'error' => 'OTP 4 rəqəm olmalıdır.'], 422);
}

$stmt = $pdo->prepare("SELECT phone_verified, phone_otp_code, phone_otp_expires_at
                       FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$customerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row)
  json_out(['ok' => false, 'error' => 'İstifadəçi tapılmadı.'], 404);

if ((int) ($row['phone_verified'] ?? 0) === 1) {
  json_out(['ok' => true, 'message' => 'Telefon artıq təsdiqlənib.', 'redirect' => 'dashboard']);
}

$otpHash = (string) ($row['phone_otp_code'] ?? '');
$exp = (string) ($row['phone_otp_expires_at'] ?? '');

if ($otpHash === '' || $exp === '') {
  json_out(['ok' => false, 'error' => 'Aktiv OTP yoxdur. Əvvəlcə kod göndərin.'], 422);
}

$expTs = strtotime($exp);
if (!$expTs || time() > $expTs) {
  json_out(['ok' => false, 'error' => 'Kodun vaxtı bitib. Yenidən kod göndərin.'], 422);
}

if (!password_verify($otp, $otpHash)) {
  json_out(['ok' => false, 'error' => 'OTP yanlışdır.'], 422);
}

// təsdiqlə + statusu active et (səndə status işlənir)
$upd = $pdo->prepare("UPDATE customers
                      SET phone_verified = 1,
                          status = 'active',
                          phone_otp_code = NULL,
                          phone_otp_expires_at = NULL,
                          phone_otp_sent_at = NULL,
                          updated_at = NOW()
                      WHERE id = ? LIMIT 1");
$upd->execute([$customerId]);

// phone + rate-limit info
$stmt = $pdo->prepare("
  SELECT type
  FROM customers
  WHERE id = ?
  LIMIT 1
");
$stmt->execute([$customerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row['type'] === 'partner') {
  json_out([
    'ok' => true,
    'message' => 'Telefon uğurla təsdiqləndi.',
    'redirect' => 'dashboard'
  ]);
} else {
  json_out([
    'ok' => true,
    'message' => 'Telefon uğurla təsdiqləndi.',
    'redirect' => 'dashboard-user'
  ]);
}
