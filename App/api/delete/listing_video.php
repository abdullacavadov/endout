<?php

header('Content-Type: application/json');

require_once __DIR__ . "/../../inc/config.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = (int) ($data['id'] ?? 0);

if (!$id) {
    echo json_encode(["error" => "ID boşdur"]);
    exit;
}

/* videonu tap */

$stmt = $pdo->prepare("
SELECT video_path 
FROM listing_videos
WHERE id=?
");

$stmt->execute([$id]);

$video = $stmt->fetchColumn();

if (!$video) {
    echo json_encode(["error" => "Video tapılmadı"]);
    exit;
}

/* DB sil */

$stmt = $pdo->prepare("DELETE FROM listing_videos WHERE id=?");
$stmt->execute([$id]);

/* faylı sil */

$path = __DIR__ . "/../../assets/video/listings/" . $video;

if (file_exists($path)) {
    unlink($path);
}

echo json_encode(["success" => true]);