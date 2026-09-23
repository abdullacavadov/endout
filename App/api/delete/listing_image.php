<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_login_api($pdo);
csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

$data = json_decode(file_get_contents("php://input"), true);
$id = (int) ($data['id'] ?? 0);

if ($id <= 0) {
    json_out(['ok' => false, 'error' => 'ID boşdur'], 422);
}

$stmt = $pdo->prepare("
    SELECT li.image_path
    FROM listing_images li
    JOIN listings l ON l.id = li.listing_id
    WHERE li.id = ? AND l.customer_id = ?
    LIMIT 1
");
$stmt->execute([$id, (int) $_SESSION['customer_id']]);

$image = $stmt->fetchColumn();

if (!$image) {
    json_out(['ok' => false, 'error' => 'Şəkil tapılmadı'], 404);
}

$stmt = $pdo->prepare("DELETE FROM listing_images WHERE id = ?");
$stmt->execute([$id]);

$path = __DIR__ . "/../../assets/img/uploads/listings/" . basename((string) $image);

if (is_file($path)) {
    @unlink($path);
}

json_out(['ok' => true]);
