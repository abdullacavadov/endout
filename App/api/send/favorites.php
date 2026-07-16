<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";



ensure_session();


if (empty($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'not_logged_in',
        'message' => 'Davam etmək üçün daxil olun'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$listingId = (int) ($data['listing_id'] ?? 0);
$customerId = $_SESSION['customer_id'];

if (!$listingId) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Yanlış sorğu'
    ]);
}

// yoxla var ya yox
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE customer_id = ? AND listing_id = ?");
$stmt->execute([$customerId, $listingId]);

$exists = $stmt->fetch();

if ($exists) {
    // sil
    $del = $pdo->prepare("DELETE FROM favorites WHERE id = ?");
    $del->execute([$exists['id']]);

    echo json_encode([
        'status' => 'removed',
        'message' => 'Elan SEÇİLMİŞLƏRDƏN silindi'
    ]);
} else {
    // əlavə et
    $ins = $pdo->prepare("INSERT INTO favorites (customer_id, listing_id) VALUES (?, ?)");
    $ins->execute([$customerId, $listingId]);

    echo json_encode([
        'status' => 'added',
        'message' => 'Elan SEÇİLMİŞLƏRƏ əlavə edildi'
    ]);
}