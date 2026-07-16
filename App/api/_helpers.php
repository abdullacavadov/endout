<?php


function json_out(array $arr, int $code = 200): void
{
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

function require_post(): void
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['ok' => false, 'error' => 'Invalid method'], 405);
  }
}

function normalize_phone(string $phone): string
{
  // Sadə normalize: boşluq, '-', '()' sil
  $p = preg_replace('/[^\d\+]/', '', $phone);
  // Əgər + yoxdursa və AZ mobil kimi gəlibsə, öz qaydana uyğun düzəldə bilərsən
  return $p ?? $phone;
}

function random_otp(int $len = 6): string
{
  $min = (int) pow(10, $len - 1);
  $max = (int) pow(10, $len) - 1;
  return (string) random_int($min, $max);
}

function make_token(int $bytes = 32): string
{
  return bin2hex(random_bytes($bytes));
}


function make_webp(string $srcTmpPath, string $dstAbsPath, int $quality = 80): void
{
  if (!function_exists('imagewebp')) {
    throw new RuntimeException("GD imagewebp aktiv deyil.");
  }

  $info = getimagesize($srcTmpPath);
  if (!$info || empty($info['mime'])) {
    throw new RuntimeException("Şəkil oxuna bilmədi.");
  }

  switch ($info['mime']) {
    case 'image/jpeg':
      $im = imagecreatefromjpeg($srcTmpPath);
      break;

    case 'image/png':
      $im = imagecreatefrompng($srcTmpPath);
      imagepalettetotruecolor($im);
      imagealphablending($im, true);
      imagesavealpha($im, true);
      break;

    case 'image/gif':
      $im = imagecreatefromgif($srcTmpPath);
      break;

    case 'image/webp':
      $im = imagecreatefromwebp($srcTmpPath);
      break;

    default:
      throw new RuntimeException("Format dəstəklənmir.");
  }

  if (!$im) {
    throw new RuntimeException("Şəkil çevrilə bilmədi.");
  }

  // Qovluğu yarat
  $dir = dirname($dstAbsPath);
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }

  if (!imagewebp($im, $dstAbsPath, $quality)) {
    imagedestroy($im);
    throw new RuntimeException("WebP yazmaq alınmadı.");
  }

  imagedestroy($im);
}



function generateSlugPath($name, $parent = null)
{
  $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

  if ($parent) {
    return $parent['slug_path'] . '/' . $slug;
  }

  return $slug;
}


function getRatesCached(): array
{
  $cacheFile = __DIR__ . "/../rates.json";
  $ttl = 60 * 60 * 6; // 6 saat

  if (file_exists($cacheFile)) {
    $data = json_decode(file_get_contents($cacheFile), true);
    if ($data && time() - $data['time'] < $ttl) {
      return $data['rates'];
    }
  }

  $xml = simplexml_load_file("https://www.cbar.az/currencies/" . date('d.m.Y') . ".xml");

  $rates = ['AZN' => 1];

  foreach ($xml->ValType as $type) {
    foreach ($type->Valute as $valute) {
      $code = (string) $valute['Code'];
      $rates[$code] = (float) $valute->Value / (float) $valute->Nominal;
    }
  }

  file_put_contents($cacheFile, json_encode([
    'time' => time(),
    'rates' => $rates
  ]));

  return $rates;
}