<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_login_api($pdo);

if (!isset($_GET['lid']) || !ctype_digit((string) $_GET['lid'])) {
    json_out(['ok' => false, 'error' => 'Invalid listing'], 422);
}

$lid = (int) $_GET['lid'];

$stmt = $pdo->prepare("
    SELECT *
    FROM listings
    WHERE id = ?
      AND customer_id = ?
    LIMIT 1
");
$stmt->execute([$lid, (int) $_SESSION['customer_id']]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    json_out(['ok' => false, 'error' => 'Not found'], 404);
}

$stmt = $pdo->prepare("
    SELECT id, image_path, sort_order
    FROM listing_images
    WHERE listing_id = ?
    ORDER BY sort_order ASC, id ASC
");
$stmt->execute([$lid]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT id, video_path
    FROM listing_videos
    WHERE listing_id = ?
    ORDER BY id ASC
");
$stmt->execute([$lid]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT country_id
    FROM listing_countries
    WHERE listing_id = ?
");
$stmt->execute([$lid]);
$countries = $stmt->fetchAll(PDO::FETCH_COLUMN);

json_out([
    'ok' => true,
    'listing' => $listing,
    'images' => $images,
    'videos' => $videos,
    'countries' => $countries
]);
