<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";
require_once __DIR__ . "/../_mailer.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);
require_login($pdo);

if (!isset($pdo) || !($pdo instanceof PDO)) {
  json_out(['ok' => false, 'error' => 'DB bağlantısı tapılmadı.'], 500);
}

$customerId = (int)($_SESSION['customer_id'] ?? 0);
if ($customerId <= 0) json_out(['ok' => false, 'error' => 'Sessiya tapılmadı.'], 401);

// Emaili DB-dən götür
$stmt = $pdo->prepare("SELECT email, email_verified, email_otp_sent_at FROM customers WHERE id = ? LIMIT 1");
$stmt->execute([$customerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) json_out(['ok' => false, 'error' => 'İstifadəçi tapılmadı.'], 404);

$email = trim((string)($row['email'] ?? ''));
if ($email === '') json_out(['ok' => false, 'error' => 'Email əlavə edilməyib.'], 422);

if ((int)($row['email_verified'] ?? 0) === 1) {
  json_out(['ok' => true, 'message' => 'Email artıq təsdiqlənib.']);
}

// 60 saniyə rate limit
if (!empty($row['email_otp_sent_at'])) {
  $sentTs = strtotime((string)$row['email_otp_sent_at']);
  if ($sentTs) {
    $elapsed = time() - $sentTs;
    $remain = 60 - $elapsed;

    if ($remain > 0) {
      json_out([
        'ok' => false,
        'rate_limited' => true,
        'remaining_seconds' => $remain,
        'message' => "Kod artıq göndərilib."
      ]);
    }
  }
}

// OTP yarat
$otp = (string)random_int(100000, 999999);
$expiresAt = date('Y-m-d H:i:s', time() + 10 * 60);
$otpHash = password_hash($otp, PASSWORD_DEFAULT);
$sentAt = date('Y-m-d H:i:s');

// DB update
$upd = $pdo->prepare("
  UPDATE customers
  SET email_otp_code = ?, email_otp_expires_at = ?, email_otp_sent_at = ?
  WHERE id = ? LIMIT 1
");
$upd->execute([$otpHash, $expiresAt, $sentAt, $customerId]);

// Email məzmunu
$subject = "Endout - Email təsdiq kodu";
$html = "
  <div style='font-family:Arial,sans-serif;line-height:1.5'>
    <h3>Email təsdiqi</h3>
    <p>Sizin təsdiq kodunuz:</p>
    <div style='font-size:28px;font-weight:700;letter-spacing:4px;margin:12px 0'>{$otp}</div>
    <p>Kodun bitmə vaxtı: <b>{$expiresAt}</b></p>
    <p>Əgər bu əməliyyatı siz etməmisinizsə, bu mesajı nəzərə almayın.</p>
  </div>
";

$send = send_email_smtp($email, $email, $subject, $html);

if (!$send['ok']) {
  json_out(['ok' => false, 'error' => 'Email göndərilə bilmədi: ' . ($send['error'] ?? 'Xəta')], 500);
}

json_out([
  'ok' => true,
  'message' => 'Təsdiq kodu <b>' . htmlspecialchars($email) . '</b> ünvanına göndərildi.'
]);