<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ---------------- AUTH ---------------- */
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$customer_id = (int) $_SESSION['customer_id'];

/* ---------------- MARKET ---------------- */
$stmt = $pdo->prepare("SELECT id FROM markets WHERE customer_id = ? LIMIT 1");
$stmt->execute([$customer_id]);
$market_id = $stmt->fetchColumn();

if (!$market_id) {
    exit(json_encode(['error' => 'Market not found']));
}

/* ---------------- INPUT ---------------- */
$listing_type = (int) ($_POST['listing_type'] ?? 0);
$sale_mode = (int) ($_POST['sale_mode'] ?? 0);
$category_id = (int) ($_POST['category_id'] ?? 0);

$title = trim($_POST['title'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$old_price = (float) ($_POST['old_price'] ?? 0);
$currency = trim($_POST['currency'] ?? '');
$description = trim($_POST['description'] ?? '');
$countries = $_POST['countries'] ?? [];

/* ---------------- VALIDATION ---------------- */
if (!$title || !$listing_type || !$sale_mode || !$category_id || !$price || !$description || !$currency) {
    exit(json_encode(['error' => 'Bütün seçimləri doldurun']));
}

if (in_array($listing_type, [1, 2]) && !$old_price) {
    exit(json_encode(['error' => 'Köhnə qiymət daxil edilməyib']));
}

if ($listing_type == 2 && $sale_mode == 1) {
    exit(json_encode(['error' => 'Outlet yalnız pərakəndə ola bilər']));
}

if (in_array($listing_type, [1, 2]) && $old_price <= $price) {
    exit(json_encode(['error' => 'Köhnə qiymət böyük olmalıdır']));
}

/* IMAGE CHECK */
$hasImage = false;
if (!empty($_FILES['images']['tmp_name'])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($tmp && $_FILES['images']['error'][$i] === 0) {
            $hasImage = true;
            break;
        }
    }
}
if (!$hasImage) {
    exit(json_encode(['error' => 'Şəkil əlavə edilməyib']));
}

/* COUNTRIES CHECK */
if (empty($countries) || !is_array($countries)) {
    exit(json_encode(['error' => 'Ölkə seçilməyib']));
}

/* ---------------- FK VALIDATION ---------------- */

// category
$stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
if (!$stmt->fetch()) {
    exit(json_encode(['error' => 'Kateqoriya mövcud deyil']));
}

// countries
$stmt = $pdo->prepare("SELECT id FROM countries WHERE id = ?");
foreach ($countries as $c) {
    $stmt->execute([(int) $c]);
    if (!$stmt->fetch()) {
        exit(json_encode(['error' => 'Yanlış ölkə ID: ' . $c]));
    }
}

/* ---------------- SLUG ---------------- */
function generateUniqueSlug(PDO $pdo, string $title): string
{
    $baseSlug = slugify($title);

    $baseSlug = slugify($title);

    if (empty($baseSlug)) {
        $baseSlug = 'tender';
    }

    $slug = $baseSlug;
    $i = 1;

    while (true) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM listings
            WHERE slug = ?
            LIMIT 1
        ");

        $stmt->execute([$slug]);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

$slug = generateUniqueSlug($pdo, $title);



/* ---------------- TRANSACTION ---------------- */
$pdo->beginTransaction();

try {

    /* ---------- LISTING INSERT ---------- */
    $stmt = $pdo->prepare("
        INSERT INTO listings
        (customer_id, market_id, title, type_id, sale_mode_id, category_id, slug, price, old_price, currency, description, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?, 'moderation')
    ");

    $stmt->execute([
        $customer_id,
        $market_id,
        $title,
        $listing_type,
        $sale_mode,
        $category_id,
        $slug,
        $price,
        $old_price,
        $currency,
        $description
    ]);

    $listing_id = $pdo->lastInsertId();

    if (!$listing_id) {
        throw new Exception("Listing insert failed");
    }
} catch (PDOException $e) {
    die("LISTING ERROR: " . $e->getMessage());
}

try {

    /* ---------- IMAGE UPLOAD ---------- */
    $uploadDir = "../../assets/img/uploads/listings/";
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $images = $_FILES['images'];
    $sortOrders = $_POST['sort_order'] ?? [];

    $stmtImg = $pdo->prepare("
        INSERT INTO listing_images (listing_id, image_path, sort_order)
        VALUES (?,?,?)
    ");

    foreach ($images['tmp_name'] as $i => $tmp) {

        if (!$tmp || $images['error'][$i] !== 0)
            continue;

        $mime = mime_content_type($tmp);
        if (!in_array($mime, $allowed))
            continue;

        $fileName = uniqid() . ".webp";
        $path = $uploadDir . $fileName;

        if (!move_uploaded_file($tmp, $path)) {
            throw new Exception("Image upload failed");
        }

        $sort = (int) ($sortOrders[$i] ?? $i + 1);

        $stmtImg->execute([$listing_id, $fileName, $sort]);
    }

} catch (PDOException $e) {
    die("IMAGE ERROR: " . $e->getMessage());
}

try {

    /* ---------- VIDEO ---------- */
    if (!empty($_FILES['video_path']['tmp_name'])) {

        if ($_FILES['video_path']['size'] > 30 * 1024 * 1024) {
            throw new Exception("Video max 30MB");
        }

        $allowedVideo = ['video/mp4', 'video/webm', 'video/quicktime'];

        $tmp = $_FILES['video_path']['tmp_name'];
        $mime = mime_content_type($tmp);

        if (in_array($mime, $allowedVideo)) {

            $videoDir = "../../assets/video/uploads/listings/";
            if (!is_dir($videoDir))
                mkdir($videoDir, 0777, true);

            $fileName = uniqid() . ".mp4";
            $path = $videoDir . $fileName;

            if (!move_uploaded_file($tmp, $path)) {
                throw new Exception("Video upload failed");
            }

            $stmt = $pdo->prepare("
                INSERT INTO listing_videos (listing_id, video_path)
                VALUES (?,?)
            ");
            $stmt->execute([$listing_id, $fileName]);
        }
    }

    /* ---------- COUNTRIES ---------- */
    $stmtCountry = $pdo->prepare("
        INSERT INTO listing_countries (listing_id, country_id)
        VALUES (?,?)
    ");

    foreach ($countries as $c) {
        $stmtCountry->execute([$listing_id, (int) $c]);
    }

    /* ---------- DONE ---------- */
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'listing_id' => $listing_id
    ]);

} catch (Throwable $e) {


    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}