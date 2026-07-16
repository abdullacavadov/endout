<?php
require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json; charset=utf-8");

csrf_verify($_POST['csrf_token'] ?? null);

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login tələb olunur']);
    exit;
}

$message = trim($_POST['message']);
$listing_id = (int)$_POST['listing_id'];
$sender_id = $_SESSION['customer_id'];

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Mesaj boş ola bilməz']);
    exit;
}

// listing sahibi
$stmt = $pdo->prepare("SELECT customer_id FROM listings WHERE id = ?");
$stmt->execute([$listing_id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    echo json_encode(['status' => 'error', 'message' => 'Elan tapılmadı']);
    exit;
}

$receiver_id = (int)$listing['customer_id'];

// özünə mesaj qadağandır
if ($sender_id === $receiver_id) {
    echo json_encode(['status' => 'error', 'message' => 'Özünüzə mesaj göndərmək olmaz']);
    exit;
}

// conversation tap
$stmt = $pdo->prepare("
    SELECT id FROM conversations
    WHERE listing_id = ?
    AND (
        (user1_id = ? AND user2_id = ?)
        OR
        (user1_id = ? AND user2_id = ?)
    )
    LIMIT 1
");

$stmt->execute([$listing_id, $sender_id, $receiver_id, $receiver_id, $sender_id]);
$conversation = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conversation) {
    $conversation_id = $conversation['id'];
} else {
    $stmt = $pdo->prepare("
        INSERT INTO conversations (listing_id, user1_id, user2_id, last_message_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$listing_id, $sender_id, $receiver_id]);
    $conversation_id = $pdo->lastInsertId();
}

// mesaj əlavə et
$stmt = $pdo->prepare("
    INSERT INTO messages (conversation_id, customer_id, message)
    VALUES (?, ?, ?)
");
$stmt->execute([$conversation_id, $sender_id, $message]);

// conversation update
$stmt = $pdo->prepare("
    UPDATE conversations
    SET last_message_at = NOW()
    WHERE id = ?
");
$stmt->execute([$conversation_id]);

echo json_encode(['status' => 'success']);