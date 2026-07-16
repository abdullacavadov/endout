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

if (!function_exists('imagewebp')) {
  json_out(['ok' => false, 'error' => 'Server WebP convert dəstəkləmir (GD imagewebp yoxdur).'], 500);
}

$customerId = (int)($_SESSION['customer_id'] ?? 0);
$marketId   = (int)($_POST['market_id'] ?? 0);

$maxBytes = 10 * 1024 * 1024; // 10MB

if ($marketId <= 0) {
  json_out(['ok' => false, 'error' => 'Məlumat natamamdır.'], 422);
}

// PHP limitinə görə fayl gəlməyə bilər
if (!isset($_FILES['market_logo'])) {
  json_out([
    'ok' => false,
    'error' => 'Fayl göndərilmədi. (Limit aşıla bilər: upload_max_filesize/post_max_size)'
  ], 400);
}

$file = $_FILES['market_logo'];

if ($file['error'] !== UPLOAD_ERR_OK) {
  json_out(['ok' => false, 'error' => 'Upload xətası.'], 400);
}
if ($file['size'] > 10 * 1024 * 1024) {
  json_out(['ok' => false, 'error' => 'Fayl 10MB-dan böyük ola bilməz.'], 400);
}

// Market ownership check + köhnə logo
$stmt = $pdo->prepare("SELECT logo FROM markets WHERE id = ? AND customer_id = ? LIMIT 1");
$stmt->execute([$marketId, $customerId]);
$market = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$market) {
  json_out(['ok' => false, 'error' => 'İcazə yoxdur.'], 403);
}

// MIME yoxla
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
  'image/jpeg',
  'image/png',
  'image/webp',
];

if (!in_array($mime, $allowed, true)) {
  json_out(['ok' => false, 'error' => 'Yalnız JPG, PNG, WEBP icazəlidir.'], 400);
}

// GD image resource yarat
$src = null;
switch ($mime) {
  case 'image/jpeg':
    $src = @imagecreatefromjpeg($file['tmp_name']);
    break;
  case 'image/png':
    $src = @imagecreatefrompng($file['tmp_name']);
    break;
  case 'image/webp':
    if (!function_exists('imagecreatefromwebp')) {
      json_out(['ok' => false, 'error' => 'Server WebP oxuma dəstəkləmir.'], 500);
    }
    $src = @imagecreatefromwebp($file['tmp_name']);
    break;
}

if (!$src) {
  json_out(['ok' => false, 'error' => 'Şəkil oxuna bilmədi.'], 400);
}

/**
 * İstəyə görə resize (məs: max 600px)
 * İstəmirsənsə bu hissəni sil.
 */
$max = 800;
$w = imagesx($src);
$h = imagesy($src);

$dst = $src;
if ($w > $max || $h > $max) {
  $ratio = min($max / $w, $max / $h);
  $nw = (int)round($w * $ratio);
  $nh = (int)round($h * $ratio);

  $dst = imagecreatetruecolor($nw, $nh);

  // PNG/WebP transparency üçün
  imagealphablending($dst, false);
  imagesavealpha($dst, true);
  $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
  imagefilledrectangle($dst, 0, 0, $nw, $nh, $transparent);

  imagecopyresampled($dst, $src, 0,0,0,0, $nw,$nh, $w,$h);
  imagedestroy($src);
}

// Fayl adı: həmişə webp
$newFileName = 'market_' . $marketId . '_' . time() . '.webp';

$uploadDir  = __DIR__ . '/../../assets/img/market-logo/';
$uploadPath = $uploadDir . $newFileName;

// keyfiyyət: 80-90 yaxşıdır
$quality = 85;

if (!@imagewebp($dst, $uploadPath, $quality)) {
  imagedestroy($dst);
  json_out(['ok' => false, 'error' => 'WebP-ə çevirmə alınmadı.'], 500);
}
imagedestroy($dst);

// Köhnəni sil
if (!empty($market['logo'])) {
  $oldPath = $uploadDir . $market['logo'];
  if (is_file($oldPath)) {
    @unlink($oldPath);
  }
}

// DB update
$upd = $pdo->prepare("UPDATE markets SET logo = ?, updated_at = NOW() WHERE id = ? AND customer_id = ? LIMIT 1");
$upd->execute([$newFileName, $marketId, $customerId]);

json_out([
  'ok' => true,
  'new_logo_url' => 'assets/img/market-logo/' . $newFileName
]);