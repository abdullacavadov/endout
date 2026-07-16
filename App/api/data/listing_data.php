<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit(json_encode(['error'=>'Unauthorized']));
}

if (!isset($_GET['lid']) || !ctype_digit($_GET['lid'])) {
    exit(json_encode(['error'=>'Invalid listing']));
}

$lid = (int)$_GET['lid'];

$stmt = $pdo->prepare("
SELECT *
FROM listings
WHERE id = ?
AND customer_id = ?
LIMIT 1
");

$stmt->execute([$lid, $_SESSION['customer_id']]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    exit(json_encode(['error'=>'Not found']));
}

/* şəkillər */

$stmt = $pdo->prepare("
SELECT id,image
FROM listing_images
WHERE listing_id = ?
ORDER BY sort ASC
");

$stmt->execute([$lid]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* video */

$stmt = $pdo->prepare("
SELECT id,video
FROM listing_videos
WHERE listing_id = ?
");

$stmt->execute([$lid]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ölkələr */

$stmt = $pdo->prepare("
SELECT country_id
FROM listing_countries
WHERE listing_id = ?
");

$stmt->execute([$lid]);
$countries = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'listing'=>$listing,
    'images'=>$images,
    'videos'=>$videos,
    'countries'=>$countries
]);