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

$customerId = (int)($_SESSION['customer_id'] ?? 0);
if ($customerId <= 0) {
  json_out(['ok' => false, 'error' => 'Sessiya tapılmadı.'], 401);
}

function normalize_msisdn(string $phone): string
{
  $digits = preg_replace('/\D+/', '', $phone);

  if (strpos($digits, '994') === 0) {
    return $digits;
  }

  if (strpos($digits, '0') === 0) {
    return '994' . substr($digits, 1);
  }

  if (strlen($digits) === 9) {
    return '994' . $digits;
  }

  return $digits;
}

function poctgoyercini_send_sms(string $toPhone, string $text): array
{
  $apiUrl      = defined('PG_API_URL') ? trim(PG_API_URL) : '';
  $publicKey   = defined('PG_PUBLIC_KEY') ? trim(PG_PUBLIC_KEY) : '';
  $privateKey  = defined('PG_PRIVATE_KEY') ? trim(PG_PRIVATE_KEY) : '';
  $originator  = defined('PG_SMS_ORIGINATOR') ? trim(PG_SMS_ORIGINATOR) : '';
  $encoding    = defined('PG_SMS_ENCODING') ? trim(PG_SMS_ENCODING) : 'LATIN';
  $purpose     = defined('PG_SMS_PURPOSE') ? trim(PG_SMS_PURPOSE) : 'INF';
  $reportLabel = defined('PG_SMS_REPORT_LABEL') ? trim(PG_SMS_REPORT_LABEL) : 'endout-otp';

  if ($apiUrl === '' || $publicKey === '' || $privateKey === '') {
    return [
      'ok' => false,
      'error' => 'SMS config tam deyil.'
    ];
  }

  $payload = [
    'Text' => $text,
    'Purpose' => $purpose,
    'Options' => [
      'Originator' => $originator,
      'SendTime' => null,
      'ExpireTime' => null,
      'Encoding' => $encoding,
      'SmsType' => 'SMS',
      'ReportLabel' => $reportLabel
    ],
    'Receivers' => [
      [
        'Receiver' => normalize_msisdn($toPhone)
      ]
    ]
  ];

  $url = "https://{$apiUrl}/gateway/api/sms/v1/message/send?publicKey=" . urlencode($publicKey);

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $privateKey,
      'Content-Type: application/json',
      'Accept: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
  ]);

  $raw = curl_exec($ch);
  $curlErr = curl_error($ch);
  $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($raw === false) {
    return [
      'ok' => false,
      'error' => 'cURL xətası: ' . $curlErr
    ];
  }

  $json = json_decode($raw, true);
  if (!is_array($json)) {
    return [
      'ok' => false,
      'error' => 'Provider JSON qaytarmadı.',
      'raw' => $raw,
      'http_code' => $httpCode
    ];
  }

  $status   = (int)($json['Status'] ?? 0);
  $desc     = (string)($json['Description'] ?? '');
  $result   = $json['Result'] ?? [];
  $accepted = $result['ReceiversAccepted'] ?? [];
  $rejected = $result['ReceiversRejected'] ?? [];

  if ($httpCode >= 200 && $httpCode < 300 && $status === 200 && !empty($accepted)) {
    $firstAccepted = $accepted[0] ?? [];

    return [
      'ok' => true,
      'provider_response' => $json,
      'message_id' => (string)($firstAccepted['id'] ?? ''),
      'receiver' => (string)($firstAccepted['Receiver'] ?? '')
    ];
  }

  $firstRejected = $rejected[0] ?? [];

  return [
    'ok' => false,
    'error' => (string)($firstRejected['ErrorMessage'] ?? ($desc !== '' ? $desc : 'SMS göndərilə bilmədi.')),
    'error_code' => (string)($firstRejected['ErrorCode'] ?? ''),
    'rejected_receiver' => (string)($firstRejected['Receiver'] ?? ''),
    'provider_response' => $json,
    'http_code' => $httpCode
  ];
}

// phone + rate-limit info
$stmt = $pdo->prepare("
  SELECT phone, phone_verified, phone_otp_sent_at
  FROM customers
  WHERE id = ?
  LIMIT 1
");
$stmt->execute([$customerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  json_out(['ok' => false, 'error' => 'İstifadəçi tapılmadı.'], 404);
}

if ((int)($row['phone_verified'] ?? 0) === 1) {
  json_out([
    'ok' => true,
    'message' => 'Telefon artıq təsdiqlənib.',
    'redirect' => 'dashboard'
  ]);
}

$phone = trim((string)($row['phone'] ?? ''));
if ($phone === '') {
  json_out(['ok' => false, 'error' => 'Telefon nömrəsi tapılmadı.'], 422);
}

// 60 saniyə rate limit
if (!empty($row['phone_otp_sent_at'])) {
  $sentTs = strtotime((string)$row['phone_otp_sent_at']);
  if ($sentTs) {
    $remain = 60 - (time() - $sentTs);
    if ($remain > 0) {
      json_out([
        'ok' => false,
        'rate_limited' => true,
        'remaining_seconds' => $remain,
        'message' => 'Kod artıq göndərilib.'
      ], 429);
    }
  }
}

// OTP
$otp = (string)random_int(1000, 9999);
$otpHash = password_hash($otp, PASSWORD_DEFAULT);
$expiresAt = date('Y-m-d H:i:s', time() + 5 * 60);
$sentAt = date('Y-m-d H:i:s');

// OTP-ni əvvəl DB-yə yazırıq
$upd = $pdo->prepare("
  UPDATE customers
  SET phone_otp_code = ?, phone_otp_expires_at = ?, phone_otp_sent_at = ?, updated_at = NOW()
  WHERE id = ?
  LIMIT 1
");
$upd->execute([$otpHash, $expiresAt, $sentAt, $customerId]);

$smsText = "EndOut təsdiq kodu: {$otp}. Kod 5 dəqiqə etibarlıdır.";
$sms = poctgoyercini_send_sms($phone, $smsText);

if (!$sms['ok']) {
  $rollback = $pdo->prepare("
    UPDATE customers
    SET phone_otp_code = NULL,
        phone_otp_expires_at = NULL,
        phone_otp_sent_at = NULL,
        updated_at = NOW()
    WHERE id = ?
    LIMIT 1
  ");
  $rollback->execute([$customerId]);

  json_out([
  'ok' => false,
  'error' => $sms['error'] ?? 'SMS göndərilə bilmədi.',
  'provider_error_code' => $sms['error_code'] ?? null,
  'provider_response' => $sms['provider_response'] ?? null
], 500);
}


if (!empty($sms['message_id'])) {
  $saveMsgId = $pdo->prepare("
    UPDATE customers
    SET phone_otp_message_id = ?, updated_at = NOW()
    WHERE id = ?
    LIMIT 1
  ");
  $saveMsgId->execute([$sms['message_id'], $customerId]);
}


// mask phone
$masked = $phone;
if (strlen($phone) >= 4) {
  $masked = str_repeat('*', max(0, strlen($phone) - 4)) . substr($phone, -4);
}

$resp = [
  'ok' => true,
  'message' => "Təsdiq kodu {$masked} nömrəsinə göndərildi.",
  'cooldown_seconds' => 90,
  'provider_message_id' => $sms['message_id'] ?? null
];

if (defined('SHOW_OTP_ON_FRONT') && SHOW_OTP_ON_FRONT) {
  $resp['test_otp'] = $otp;
}

json_out($resp);