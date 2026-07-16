<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

csrf_verify($_POST['csrf_token'] ?? null ?? null);

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    exit(json_encode([
        'ok' => false,
        'error' => 'Unauthorized'
    ]));
}

$customer_id = $_SESSION['customer_id'];

$listing_id = (int) ($_POST['listing_id'] ?? 0);
$plan_id = (int) ($_POST['plan_id'] ?? 0);

if (!$listing_id || !$plan_id) {
    exit(json_encode([
        'ok' => false,
        'error' => 'Missing data'
    ]));
}

try {

    $pdo->beginTransaction();

    /*
    -----------------------------------
    Elanı yoxla
    -----------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id,title
        FROM listings
        WHERE id = ? AND customer_id = ?
        LIMIT 1
    ");

    $stmt->execute([$listing_id, $customer_id]);
    $listing = $stmt->fetch();

    if (!$listing) {
        throw new Exception('Elan tapılmadı');
    }

    /*
    -----------------------------------
    Planı yoxla
    -----------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id,duration_days,price
        FROM premium_plans
        WHERE id = ? AND status = 1
        LIMIT 1
    ");

    $stmt->execute([$plan_id]);
    $plan = $stmt->fetch();

    if (!$plan) {
        throw new Exception('Plan tapılmadı');
    }


    /*
    -----------------------------------
    Elanın aktivliyini yoxla
    -----------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT status
        FROM listings
        WHERE status != ?
        LIMIT 1
    ");

    $stmt->execute(['active']);

    if ($stmt->fetch()) {
        throw new Exception('Aktiv olmayan elanı premium edə bilməzsiniz.');
    }
    

    /*
    -----------------------------------
    Aktiv premium varmı
    -----------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM premium_listings
        WHERE listing_id = ?
        AND expires_at > NOW()
        LIMIT 1
    ");

    $stmt->execute([$listing_id]);

    if ($stmt->fetch()) {
        throw new Exception('Bu elan artıq premiumdur. Premium müddəti bitdikdən sonra yenidən elanı premium edə bilərsiniz.');
    }



    


    /*
    -----------------------------------
    Premium əlavə et
    -----------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO premium_listings
        (listing_id,plan_id,started_at,expires_at)
        VALUES
        (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY))
    ");

    $stmt->execute([
        $listing_id,
        $plan['id'],
        $plan['duration_days']
    ]);

    $pdo->commit();

    echo json_encode([
        'ok' => true,
        'message' => 'Elan uğurla premium edildi',
        'listing' => [
            'id' => $listing['id'],
            'title' => $listing['title']
        ],
        'premium' => [
            'duration_days' => $plan['duration_days'],
            'price' => $plan['price']
        ]
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ]);
}