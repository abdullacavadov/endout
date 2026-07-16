<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);

require_login($pdo);


$customerId = (int) $_SESSION['customer_id'];

$name = trim((string) ($_POST['market_name'] ?? ''));
$type = (string) ($_POST['market_type'] ?? '');

$countryCode = strtoupper(trim((string) ($_POST['market_country'] ?? ''))); // AZ
$cityId = (int) ($_POST['market_city'] ?? 0);
$address = trim((string) ($_POST['market_address'] ?? ''));
$phone = trim((string) ($_POST['market_phone'] ?? ''));
$email = trim((string) ($_POST['market_email'] ?? ''));
$marketDesc = trim((string) ($_POST['market_desc'] ?? ''));
$url = trim((string) ($_POST['market_url'] ?? ''));
$promocode = trim((string) ($_POST['market_promo_code'] ?? ''));
$promodiscount = trim((string) ($_POST['market_promo_discount'] ?? ''));

$tags = $_POST['market_tags'] ?? [];

if (!is_array($tags)) {
  $tags = [$tags];
}

// normalize + təmizlə
$tags = array_values(array_unique(array_filter(array_map(function ($t) {
  $t = mb_strtolower(trim((string) $t));
  $t = preg_replace('/[^\p{L}\p{N}\s]/u', '', $t); // təhlükəsizlik
  return $t ?: null;
}, $tags))));

function ensure_dir(string $dir): void
{
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }
}

/* Slug generator */
function slugify($text)
{
  $map = [
    'ə' => 'e',
    'ş' => 's',
    'ç' => 'c',
    'ü' => 'u',
    'ö' => 'o',
    'ğ' => 'g',
    'ı' => 'i'
  ];

  $text = mb_strtolower($text, 'UTF-8');
  $text = strtr($text, $map);
  $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

  return trim($text, '-');
}

$slug = slugify($name);

function ext_from_mime(string $mime): ?string
{
  $map = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
  ];
  return $map[$mime] ?? null;
}

// multiple select (codes)
$adCodes = $_POST['market_ad_view_countries'] ?? [];
if (!is_array($adCodes))
  $adCodes = [$adCodes];

$adCodes = array_values(array_unique(array_filter(array_map(function ($c) {
  $c = strtoupper(trim((string) $c));
  return (strlen($c) === 2) ? $c : null;
}, $adCodes))));

if ($name === '') {
  json_out(['ok' => false, 'field' => 'market_name', 'error' => 'Mağaza adı qeyd olunmayıb.'], 422);
}

if (count($tags) > 15) {
  json_out(['ok' => false, 'field' => 'market_tags', 'error' => 'Maksimum 20 teq əlavə edə bilərsiniz.'], 422);
}

if (count($tags) < 3) {
  json_out(['ok' => false, 'field' => 'market_tags', 'error' => 'Minimum 3 teq əlavə etməlisiniz.'], 422);
}



if ($phone === '') {
  json_out(['ok' => false, 'field' => 'market_phone', 'error' => 'Mağaza telefon nömrəsi qeyd olunmayıb.'], 422);
}
if (!preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
  json_out(['ok' => false, 'field' => 'market_phone', 'error' => 'Telefon nömrəsi yanlışdır.'], 422);
}
if ($email === '') {
  json_out(['ok' => false, 'field' => 'market_email', 'error' => 'E-poçt ünvanı qeyd olunmayıb.'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_out(['ok' => false, 'field' => 'market_email', 'error' => 'E-poçt ünvanı formatı yanlışdır.'], 422);
}
if ($type === '') {
  json_out(['ok' => false, 'field' => 'market_type', 'error' => 'Mağaza növü seçilməyib.'], 422);
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
  json_out(['ok' => false, 'field' => 'market_url', 'error' => 'URL ünvanı yanlışdır və ya boşdur. Nümunə: https://sayt.com'], 422);
}
if ($marketDesc === '') {
  json_out(['ok' => false, 'field' => 'market_desc', 'error' => 'Mağaza haqqında məlumat qeyd olunmayıb.'], 422);
}
if ($type === 'physical_market' && $countryCode === '') {
  json_out(['ok' => false, 'field' => 'market_country', 'error' => 'Mağaza ölkəsi seçilməyib.'], 422);
}
if ($type === 'physical_market' && $cityId === 0) {
  json_out(['ok' => false, 'field' => 'market_city', 'error' => 'Mağaza şəhəri seçilməyib.'], 422);
}
if ($type === 'physical_market' && $address === '') {
  json_out(['ok' => false, 'field' => 'market_address', 'error' => 'Mağaza ünvanı qeyd olunmayıb.'], 422);
}
if (empty($adCodes)) {
  json_out(['ok' => false, 'field' => 'market_ad_view_countries[]', 'error' => 'Elanları göstərmək istədiyiniz ölkələri seçin.'], 422);
}


$countryId = null;

try {

  // country code -> id
  $st = $pdo->prepare("SELECT id FROM countries WHERE iso2=? LIMIT 1");
  $st->execute([$countryCode]);
  $countryId = (int) $st->fetchColumn();
  if ($countryId <= 0)
    json_out(['ok' => false, 'field' => 'market_country', 'error' => 'Ölkə tapılmadı.'], 422);

  // city həmin ölkəyə aiddirmi?
  $st = $pdo->prepare("SELECT 1 FROM cities WHERE id=? AND country_id=? LIMIT 1");
  $st->execute([$cityId, $countryId]);
  if (!$st->fetchColumn()) {
    json_out(['ok' => false, 'field' => 'market_city', 'error' => 'Şəhər seçimi yanlışdır.'], 422);
  }



  $pdo->beginTransaction();

  // market upsert (1 customer = 1 market)
  $st = $pdo->prepare("SELECT id FROM markets WHERE customer_id=? LIMIT 1");
  $st->execute([$customerId]);
  $existingMarketId = (int) $st->fetchColumn();

  if ($existingMarketId > 0) {
    $marketId = $existingMarketId;
    $st = $pdo->prepare("
  UPDATE markets
  SET name=?, slug=?, type=?, country_id=?, city_id=?, address=?, description=?, phone_number=?, email=?, url=?, promocode=?, promodiscount=?
  WHERE id=? AND customer_id=?
  ");
    $st->execute([
      $name,
      $slug,
      $type,
      $countryId ?: null,
      $cityId > 0 ? $cityId : null,
      $address,
      $marketDesc,
      $phone,
      $email,
      $url,
      $promocode ?: null,
      $promodiscount !== '' ? $promodiscount : null,
      $marketId,
      $customerId
    ]);


    $pdo->prepare("DELETE FROM market_ad_countries WHERE market_id=?")->execute([$marketId]);

    // --- LOGO UPLOAD (optional) ---
    $logoPathToSave = null;

  } else {
    $st = $pdo->prepare("
  INSERT INTO markets(customer_id, name, slug, type, country_id, city_id, address, description, phone_number, email, url)
  VALUES(?,?,?,?,?,?,?,?,?,?,?)
");
    $st->execute([
      $customerId,
      $name,
      $slug,
      $type,
      $countryId ?: null,
      $cityId > 0 ? $cityId : null,
      $address,
      $marketDesc,
      $phone,
      $email,
      $url
    ]);
    $marketId = (int) $pdo->lastInsertId();

  }


  // --- LOGO UPLOAD (optional) ---
// marketId artıq var (insert və ya update sonrası)
  if (!empty($_FILES['market_logo']) && is_array($_FILES['market_logo'])) {
    $f = $_FILES['market_logo'];

    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {

      if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Logo yüklənmə xətası. Kod: " . (int) $f['error']);
      }

      $max = 10 * 1024 * 1024; // 10MB
      if (($f['size'] ?? 0) > $max) {
        json_out(['ok' => false, 'error' => 'Şəkil maksimum 10 MB ola bilər.'], 422);
      }

      $tmp = (string) $f['tmp_name'];

      // App/ qovluğu
      $baseAbs = realpath(__DIR__ . "/../.."); // App
      if (!$baseAbs) {
        throw new RuntimeException("App yolu tapılmadı.");
      }

      // Qovluq: App/assets/img/market-logo/
      $dirAbs = $baseAbs . "/assets/img/market-logo/";
      ensure_dir($dirAbs);

      // Fayl adı: marketId + random, və həmişə webp
      $fileName = "m{$marketId}/" . bin2hex(random_bytes(8)) . ".webp";
      $destAbs = $dirAbs . "/" . $fileName;

      // Çevirmə + yazma
      make_webp($tmp, $destAbs, 80);

      // DB-də saxlanacaq dəyər (qovluqsuz)
      $logoValue = $fileName;

      // markets.logo sütununa yaz
      $pdo->prepare("UPDATE markets SET logo=? WHERE id=? AND customer_id=?")
        ->execute([$logoValue, $marketId, $customerId]);

      $_SESSION['market_logo'] = $logoValue;
    }
  }

  // ad codes -> ids
  $in = implode(",", array_fill(0, count($adCodes), "?"));
  $st = $pdo->prepare("SELECT id, iso2 FROM countries WHERE iso2 IN ($in)");
  $st->execute($adCodes);
  $countryRows = $st->fetchAll(PDO::FETCH_ASSOC);

  if (count($countryRows) === 0) {
    $pdo->rollBack();
    json_out(['ok' => false, 'error' => 'Reklam ölkələri tapılmadı.'], 422);
  }

  $ins = $pdo->prepare("INSERT INTO market_ad_countries(market_id, country_id) VALUES(?,?)");
  foreach ($countryRows as $r) {
    $ins->execute([$marketId, (int) $r['id']]);
  }



  // tags - əvvəl mövcudları sil, sonra yenidən əlavə et (sadə üsul)
  $pdo->prepare("DELETE FROM market_tags WHERE market_id=?")->execute([$marketId]);

  if (!empty($tags)) {

    foreach ($tags as $tag) {
      if (mb_strlen($tag) > 50) {
        json_out(['ok' => false, 'field' => 'market_tags', 'error' => 'Teq çox uzundur. (max 50 hərf)'], 422);
      }
    }

    $insertTag = $pdo->prepare("
    INSERT INTO tags (keyword)
    VALUES (?)
    ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
  ");

    $insertRelation = $pdo->prepare("
    INSERT INTO market_tags (market_id, tag_id)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE tag_id=tag_id
  ");

    foreach ($tags as $tag) {

      $insertTag->execute([$tag]);
      $tagId = (int) $pdo->lastInsertId();

      $insertRelation->execute([$marketId, $tagId]);
    }
  }


  $pdo->commit();

  $st = $pdo->prepare("SELECT full_name FROM customers WHERE id=? LIMIT 1");
  $st->execute([$customerId]);
  $_SESSION['customer_name'] = (string) $st->fetchColumn();


  // ✅ Session update (market save edildikdən sonra)
  $_SESSION['market_id'] = $marketId;
  $_SESSION['market_name'] = $name;
  $_SESSION['market_slug'] = $slug;
  $_SESSION['market_type'] = $type;
  $_SESSION['market_desc'] = $marketDesc;
  $_SESSION['market_phone'] = $phone;
  $_SESSION['market_email'] = $email;
  $_SESSION['market_promo_code'] = $promocode ?: null;
  $_SESSION['market_promo_discount'] = $promodiscount !== '' ? (float) $promodiscount : null;


  // type-ə görə field-ləri də saxla (istəyə görə)
  $_SESSION['market_country'] = $countryCode ?: null;
  $_SESSION['market_city_id'] = $cityId > 0 ? $cityId : null;
  $_SESSION['market_address'] = $address ?: null;
  $_SESSION['market_url'] = $url ?: null;

  // reklam ölkələri (codes)
  $_SESSION['market_ad_view_countries'] = $adCodes;

  json_out(['ok' => true, 'redirect' => './payment']);


} catch (Throwable $e) {
  if ($pdo->inTransaction())
    $pdo->rollBack();
  json_out(['ok' => false, 'error' => $e->getMessage()], 500);
}
