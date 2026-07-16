<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$customer_id = (int)$_SESSION['customer_id'];
$listing_id  = (int)($_POST['id'] ?? 0);

if ($listing_id <= 0) {
    exit(json_encode(['error' => 'Invalid listing id']));
}

try {

    $pdo->beginTransaction();

    /* listing sahibini yoxla */
    $stmt = $pdo->prepare("
        SELECT id 
        FROM listings 
        WHERE id=? AND customer_id=?
        LIMIT 1
    ");
    $stmt->execute([$listing_id, $customer_id]);

    if (!$stmt->fetch()) {
        throw new Exception("Listing not found");
    }

    /* şəkilləri götür */
    $stmt = $pdo->prepare("
        SELECT image_path 
        FROM listing_images 
        WHERE listing_id=?
    ");
    $stmt->execute([$listing_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    /* videonu götür */
    $stmt = $pdo->prepare("
        SELECT video_path 
        FROM listing_videos 
        WHERE listing_id=?
    ");
    $stmt->execute([$listing_id]);
    $videos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    /* faylları serverdən sil */
    foreach ($images as $img) {
        $path = __DIR__ . "/../../assets/img/uploads/listings/" . $img;
        if (file_exists($path)) unlink($path);
    }

    foreach ($videos as $vid) {
        $path = __DIR__ . "/../../assets/video/uploads/listings/" . $vid;
        if (file_exists($path)) unlink($path);
    }

    /* listing sil */
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id=?");
    $stmt->execute([$listing_id]);

    $pdo->commit();

    echo json_encode([
        'success' => true
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}