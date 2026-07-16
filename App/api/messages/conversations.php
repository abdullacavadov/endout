<?php
require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json; charset=utf-8");

$user_id = $_SESSION['customer_id'];

$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.listing_id,
        c.user1_id,
        c.user2_id,
        c.last_message_at,

        l.title,

        u.id as other_id,
        u.full_name

    FROM conversations c

    JOIN listings l ON l.id = c.listing_id

    JOIN customers u ON u.id = (
        CASE 
            WHEN c.user1_id = ? THEN c.user2_id
            ELSE c.user1_id
        END
    )

    WHERE c.user1_id = ? OR c.user2_id = ?

    ORDER BY c.last_message_at DESC
");

$stmt->execute([$user_id, $user_id, $user_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));