<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = (int) ($data['id'] ?? 0);

if (!$id) {
    echo json_encode(["error" => "ID boşdur"]);
    exit;
}

/* şəkli tap */

$stmt = $pdo->prepare("
   SELECT li.image_path
FROM listing_images li
JOIN listings l ON l.id = li.listing_id
WHERE li.id=? AND l.customer_id=?
");

$stmt->execute([$id, $_SESSION['customer_id']]);

$image = $stmt->fetchColumn();

if (!$image) {
    echo json_encode(["error" => "Şəkil tapılmadı"]);
    exit;
}

/* DB-dən sil */

$stmt = $pdo->prepare("DELETE FROM listing_images WHERE id=?");
$stmt->execute([$id]);

/* faylı sil */

$path = __DIR__ . "/../../uploads/listings/" . $image;

if (file_exists($path)) {
    unlink($path);
}

echo json_encode(["success" => true]);