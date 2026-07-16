<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);



$phone = trim((string) ($_POST['cust_phone'] ?? ''));
$password = (string) ($_POST['cust_password'] ?? '');
$remember = !empty($_POST['rememberme']);

if ($phone === '' || $password === '') {
  json_out(['ok' => false, 'error' => 'Mobil nömrə və şifrə boş ola bilməz.'], 422);
}

// whitespace təmizlə
$phone = preg_replace('/\s+/', '', $phone);

try {
  // yalnız aktiv istifadəçilər login ola bilsin deyə istəsən status filter qoyuruq
  $stmt = $pdo->prepare("
    SELECT id, full_name, password_hash, status, type
    FROM customers
    WHERE phone = :phone
    LIMIT 1
  ");
  $stmt->execute([':phone' => $phone]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    json_out(['ok' => false, 'error' => 'İstifadəçi tapılmadı.'], 401);
  }

  // status yoxlaması
  if (isset($user['status']) && (string) $user['status'] !== 'active') {
    json_out(['ok' => false, 'error' => 'Hesab aktiv deyil.'], 403);
  }

  if (!password_verify($password, (string) $user['password_hash'])) {
    json_out(['ok' => false, 'error' => 'Şifrə yanlışdır.'], 401);
  }

  session_regenerate_id(true);

  $_SESSION['customer_id'] = (int) $user['id'];
  $_SESSION['cust_type'] = $user['type'];


  // Remember me (təhlükəsiz token)
  if ($remember) {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);

    // token cədvəli varsa yazırıq (yoxdursa bu hissəni ya söndür, ya cədvəli yarat)
    $pdo->prepare("
      INSERT INTO customer_remember_tokens (customer_id, token_hash, expires_at, created_at)
      VALUES (:cid, :th, DATE_ADD(NOW(), INTERVAL 30 DAY), NOW())
    ")->execute([
          ':cid' => (int) $user['id'],
          ':th' => $tokenHash
        ]);

    setcookie(
      "remember_token",
      $token,
      [
        'expires' => time() + 86400 * 30,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
      ]
    );
  }

  json_out([
    'ok' => true,
    'redirect' => 'dashboard',
    'user' => [
      'id' => (int) $user['id'],
      'name' => (string) $user['full_name']
    ]
  ]);

} catch (Throwable $e) {
  json_out(['ok' => false, 'error' => 'Server xətası.'], 500);
}
