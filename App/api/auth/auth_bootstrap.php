<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
  // _db.php include edilməlidir
  return;
}

// artıq login olubsa çıx
if (!empty($_SESSION['user_id'])) {
  return;
}

// remember cookie yoxdursa çıx
$token = $_COOKIE['remember_token'] ?? '';
if ($token === '') return;

$tokenHash = hash('sha256', $token);

$stmt = $pdo->prepare("
  SELECT t.customer_id, c.full_name
  FROM customer_remember_tokens t
  JOIN customers c ON c.id = t.customer_id
  WHERE t.token_hash = :th AND t.expires_at > NOW()
  LIMIT 1
");
$stmt->execute([':th' => $tokenHash]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) return;

// session aç
session_regenerate_id(true);
$_SESSION['user_id'] = (int)$row['user_id'];
