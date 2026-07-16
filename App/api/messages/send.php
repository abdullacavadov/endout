<?php
require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json");

csrf_verify($_POST['csrf_token'] ?? null);

$user_id = $_SESSION['customer_id'];
$conversation_id = (int)$_POST['conversation_id'];
$message = trim($_POST['message']);

if (!$message) {
    echo json_encode(['status'=>'error']);
    exit;
}

// check access
$stmt = $pdo->prepare("
    SELECT id FROM conversations
    WHERE id = ?
    AND (user1_id = ? OR user2_id = ?)
");
$stmt->execute([$conversation_id, $user_id, $user_id]);

if (!$stmt->fetch()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied'
    ]);
    exit;
}

// insert message
$stmt = $pdo->prepare("
    INSERT INTO messages (conversation_id, customer_id, message)
    VALUES (?, ?, ?)
");

$stmt->execute([
    $conversation_id,
    $user_id,
    $message
]);

// update conversation
$pdo->prepare("
    UPDATE conversations 
    SET last_message_at = NOW()
    WHERE id = ?
")->execute([$conversation_id]);

echo json_encode(['status' => 'success']);