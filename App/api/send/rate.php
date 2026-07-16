<?php

require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json; charset=utf-8");

ensure_session();

$customer_id = $_SESSION['customer_id'] ?? null;

$data = json_decode(file_get_contents("php://input"), true);

$listing_id = (int) ($data['listing_id'] ?? 0);
$rating = (int) ($data['rating'] ?? 0);


// 1. Login check
if (!$customer_id) {
    echo json_encode([
        'status' => 'not_logged_in',
        'message' => 'Qiymətləndirmək üçün daxil olun'
    ]);
    exit;
}

// 2. Listing check (bunu əlavə etməmisən — səhvdir)
$stmt = $pdo->prepare("SELECT id FROM listings WHERE id = ?");
$stmt->execute([$listing_id]);

if (!$stmt->fetch()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Elan tapılmadı'
    ]);
    exit;
}

// 3. Rating validation
if ($rating < 1 || $rating > 5) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Yanlış qiymətləndirmə'
    ]);
    exit;
}

// 4. Already voted?
$stmt = $pdo->prepare("
    SELECT id FROM listings_reviews 
    WHERE listing_id = ? AND customer_id = ?
    LIMIT 1
");
$stmt->execute([$listing_id, $customer_id]);

if ($stmt->fetch()) {
    echo json_encode([
        'status' => 'already_voted',
        'message' => 'Bu elanı artıq qiymətləndirmisiz'
    ]);
    exit;
}

// 5. Insert
$stmt = $pdo->prepare("
    INSERT INTO listings_reviews (listing_id, customer_id, rating, created_at)
    VALUES (?, ?, ?, NOW())
");

$stmt->execute([$listing_id, $customer_id, $rating]);


// 6. Yeni average + count (BU VACİBDİR UI üçün)
$stmt = $pdo->prepare("
    SELECT 
        ROUND(AVG(rating), 1) as avg_rating,
        COUNT(*) as total_reviews
    FROM listings_reviews
    WHERE listing_id = ?
");
$stmt->execute([$listing_id]);

$stats = $stmt->fetch(PDO::FETCH_ASSOC);


echo json_encode([
    'status' => 'success',
    'message' => 'Qiymətləndirmə əlavə edildi',
    'data' => [
        'avg' => $stats['avg_rating'],
        'count' => $stats['total_reviews']
    ]
]);
exit;