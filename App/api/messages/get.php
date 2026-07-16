<?php
require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$conversation_id = (int) $_GET['conversation_id'];
$user_id = $_SESSION['customer_id'];

// 🔐 Ownership check
$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.listing_id,
        l.id AS listing_id,
        l.title AS product_name,
        l.slug AS product_slug
    FROM conversations c
    JOIN listings l ON l.id = c.listing_id
    WHERE c.id = ?
    AND (c.user1_id = ? OR c.user2_id = ?)
");

$stmt->execute([$conversation_id, $user_id, $user_id]);

$conv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$conv) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Giriş icazəsi məhdudlaşdırıldı.'
    ]);
    exit;
}

// 📩 Mesajları gətir
$stmt = $pdo->prepare("
    SELECT 
        m.customer_id,
        m.message,
        m.created_at,
        u.full_name
    FROM messages m
    JOIN customers u ON u.id = m.customer_id
    WHERE m.conversation_id = ?
    ORDER BY m.created_at ASC
");
$stmt->execute([$conversation_id]);

$messages = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $messages[] = [
        'is_mine' => $row['customer_id'] == $user_id, // göndərilən və alınan ayrımı
        'customer_id' => $row['customer_id'],
        'message' => htmlspecialchars($row['message']),
        'time' => date('H:i', strtotime($row['created_at']))
    ];
}

// 👁️ mark as read
$pdo->prepare("
    UPDATE messages 
    SET is_read = 1
    WHERE conversation_id = ?
    AND customer_id != ?
")->execute([$conversation_id, $user_id]);

// 👤 other user
$stmt = $pdo->prepare("
    SELECT 
        CASE 
            WHEN user1_id = ? THEN user2_id
            ELSE user1_id
        END as other_user_id
    FROM conversations
    WHERE id = ?
");
$stmt->execute([$user_id, $conversation_id]);
$other_user_id = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT c.full_name, COALESCE(m.logo, 'default-logo.png') AS logo
    FROM customers c
    LEFT JOIN markets m ON c.id = m.customer_id
    WHERE c.id = ?
");
$stmt->execute([$other_user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$other_user = $row['full_name'];
$logo = $row['logo'];

echo json_encode([
    'status' => 'success',
    'product_name' => $conv['product_name'],
    'product_slug' => $conv['product_slug'],
    'listing_id' => $conv['listing_id'],
    'messages' => $messages,
    'other_user' => $other_user,
    'logo' => $logo
]);