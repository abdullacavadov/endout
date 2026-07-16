<?php

require_once __DIR__ . "/../inc/config.php";

try {

    /* ==============================
       PREMIUM MÜDDƏTİ BİTƏNLƏR
    ============================== */

    $stmt = $pdo->prepare("
        SELECT id, listing_id, expires_at
        FROM premium_listings
        WHERE expires_at < NOW()
    ");

    $stmt->execute();
    $expiredPremiums = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($expiredPremiums) {

        $delete = $pdo->prepare("
            DELETE FROM premium_listings
            WHERE id = ?
        ");

        foreach ($expiredPremiums as $row) {
            $delete->execute([$row['id']]);
        }
    }


    /* ==============================
       ABUNƏLİYİ BİTƏNLƏR
    ============================== */

    $stmt2 = $pdo->prepare("
        SELECT id, cust_id, ends_at
        FROM customer_subs
        WHERE status = 'active'
        AND ends_at < NOW()
    ");

    $stmt2->execute();
    $expiredSubs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    if ($expiredSubs) {

        $update = $pdo->prepare("
            UPDATE customer_subs
            SET status = 'expired'
            WHERE id = ?
        ");

        foreach ($expiredSubs as $row) {
            $update->execute([$row['id']]);
        }
    }


    echo "Cron completed: " . date("Y-m-d H:i:s");

} catch (PDOException $e) {

    echo "Cron error: " . $e->getMessage();

}