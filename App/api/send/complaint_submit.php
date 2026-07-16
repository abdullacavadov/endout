<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";
require_once __DIR__ . "/../_helpers.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

if (!empty($_POST['website'])) {
    echo json_encode(['success' => false, 'error' => 'Bot tapıldı']);
    exit; // bot detected
}


// $secretKey = SECRET_KEY;

// $captcha = $_POST['g-recaptcha-response'] ?? '';

// if (!$captcha) {
//     echo json_encode(['success' => false, 'error' => 'Captcha boşdur']);
//     exit;
// }

// // Google-a request
// $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}");
// $response = json_decode($verify);

// // CHECK
// if (!$response->success) {
//     echo json_encode(['success' => false, 'error' => 'Captcha uğursuzdur']);
//     exit;
// }



$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$listing_id = (int) ($_POST['listing_id'] ?? 0);

// VALIDATION (bunu skip etmək olmaz)
if (!$full_name || !$email || !$message || !$listing_id) {
    echo json_encode(['success' => false, 'error' => 'Bütün sahələri doldurun']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Email düzgün deyil']);
    exit;
}

// basic anti-spam (eyni ip 1 dəqiqədə 3 dəfə)
$ip = $_SERVER['REMOTE_ADDR'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM listing_complaints 
    WHERE ip_address=? AND created_at > (NOW() - INTERVAL 1 MINUTE)
");
$stmt->execute([$ip]);

if ($stmt->fetchColumn() >= 2) {
    echo json_encode(['success' => false, 'error' => 'Çox tez-tez göndərirsiniz']);
    exit;
}

// INSERT
$stmt = $pdo->prepare("
    INSERT INTO listing_complaints 
    (listing_id, full_name, email, message, ip_address, user_agent)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $listing_id,
    $full_name,
    $email,
    $message,
    $ip,
    $_SERVER['HTTP_USER_AGENT'] ?? null
]);

echo json_encode(['success' => true]);