<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_post();
$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_csrf'] ?? null);
csrf_verify($csrf);

$full  = trim((string)($_POST['cust_full_name'] ?? ''));
$phone = normalize_phone((string)($_POST['cust_phone'] ?? ''));
$email = trim((string)($_POST['cust_email'] ?? ''));
$pass  = (string)($_POST['cust_password'] ?? '');
$rep   = (string)($_POST['cust_repasword'] ?? '');

if ($full === '' || $phone === '' || $email === '' || $pass === '') {
  json_out(['ok' => false, 'error' => 'Bütün xanaları doldurun.'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_out(['ok' => false, 'error' => 'Email düzgün deyil.'], 422);
}
if (strlen($pass) < 6) {
  json_out(['ok' => false, 'error' => 'Şifrə ən az 6 simvol olmalıdır.'], 422);
}
if ($pass !== $rep) {
  json_out(['ok' => false, 'error' => 'Şifrələr uyğun deyil.'], 422);
}

try {
  $pdo->beginTransaction();

  $st = $pdo->prepare("SELECT id, phone_verified, status FROM customers WHERE phone=? OR email=? LIMIT 1");
  $st->execute([$phone, $email]);
  $existing = $st->fetch(PDO::FETCH_ASSOC);

  if ($existing) {
    $customerId = (int)$existing['id'];

    if ((int)$existing['phone_verified'] === 1 || $existing['status'] === 'active') {
      $pdo->rollBack();
      json_out(['ok' => false, 'error' => 'Bu hesab artıq aktivdir. Daxil olun.'], 409);
    }

    // pending user-dirsə, məlumatları yenilə (istəsən)
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $up = $pdo->prepare("UPDATE customers SET full_name=?, phone=?, email=?, password_hash=?, status='pending', updated_at=NOW()
                         WHERE id=? LIMIT 1");
    $up->execute([$full, $phone, $email, $hash, $customerId]);

    $pdo->commit();
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $st = $pdo->prepare("INSERT INTO customers(full_name, phone, email, password_hash, status, type)
                         VALUES(?,?,?,?, 'pending', 'partner')");
    $st->execute([$full, $phone, $email, $hash]);
    $customerId = (int)$pdo->lastInsertId();

    $pdo->commit();
  }

  // login sessiya
  $_SESSION['customer_id'] = $customerId;
  $_SESSION['cust_type'] = 'partner';

  json_out(['ok' => true, 'redirect' => 'verify-phone']);

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  json_out(['ok' => false, 'error' => 'Server xətası.'], 500);
}