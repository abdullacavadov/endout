<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

require_login_api($pdo);
csrf_verify($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null));

$customerId = (int) $_SESSION['customer_id'];
$listingId = (int) ($_POST['listing_id'] ?? 0);
$planId = (int) ($_POST['plan_id'] ?? 0);

if ($listingId <= 0 || $planId <= 0) {
    json_out(['ok' => false, 'error' => 'Missing data'], 422);
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT id, title, status
        FROM listings
        WHERE id = ? AND customer_id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([$listingId, $customerId]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        throw new RuntimeException('Elan tapılmadı.');
    }

    if ($listing['status'] !== 'active') {
        throw new RuntimeException('Yalnız aktiv elan premium edilə bilər.');
    }

    $stmt = $pdo->prepare("
        SELECT id, duration_days, price
        FROM premium_plans
        WHERE id = ? AND status = 1
        LIMIT 1
    ");
    $stmt->execute([$planId]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plan) {
        throw new RuntimeException('Plan tapılmadı.');
    }

    /*
     * Premium üçün ayrıca ödəniş/order axını olmadıqca ödənişli planın
     * bu endpoint-dən birbaşa aktivləşdirilməsi qadağandır.
     * Pulsuz planlar isə normal şəkildə aktivləşdirilə bilər.
     */
    if ((float) $plan['price'] > 0) {
        throw new RuntimeException(
            'Ödənişli premium plan payment axını ilə aktivləşdirilməlidir.'
        );
    }

    $stmt = $pdo->prepare("
        SELECT id
        FROM premium_listings
        WHERE listing_id = ?
          AND expires_at > NOW()
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([$listingId]);

    if ($stmt->fetch()) {
        throw new RuntimeException('Bu elan artıq premiumdur.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO premium_listings
            (listing_id, plan_id, started_at, expires_at)
        VALUES
            (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY))
    ");
    $stmt->execute([
        $listingId,
        (int) $plan['id'],
        (int) $plan['duration_days']
    ]);

    $pdo->commit();

    json_out([
        'ok' => true,
        'message' => 'Elan uğurla premium edildi',
        'listing' => [
            'id' => (int) $listing['id'],
            'title' => $listing['title']
        ],
        'premium' => [
            'duration_days' => (int) $plan['duration_days'],
            'price' => (float) $plan['price']
        ]
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('premium activation failed: ' . $e->getMessage());
    json_out([
        'ok' => false,
        'error' => $e->getMessage()
    ], 400);
}
