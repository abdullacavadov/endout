<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../../inc/config.php";

require_post();
csrf_verify($_POST['_csrf'] ?? null);

try {
    if (empty($_SESSION['customer_id'])) {
        echo json_encode(["ok" => false, "error" => "Sessiya bitib. Yenidən daxil olun."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $customerId = (int)$_SESSION['customer_id'];

    $currentPassword = (string)($_POST['current_password'] ?? '');
    $newPassword     = (string)($_POST['new_password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($currentPassword === '') {
        echo json_encode(["ok" => false, "error" => "Cari şifrəni daxil edin."], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($newPassword === '') {
        echo json_encode(["ok" => false, "error" => "Yeni şifrəni daxil edin."], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (strlen($newPassword) < 8) {
        echo json_encode(["ok" => false, "error" => "Yeni şifrə ən az 8 simvol olmalıdır."], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($newPassword !== $confirmPassword) {
        echo json_encode(["ok" => false, "error" => "Yeni şifrə və təsdiq şifrəsi eyni deyil."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Mövcud hash-i götür
    $st = $pdo->prepare("SELECT password_hash FROM customers WHERE id = ? LIMIT 1");
    $st->execute([$customerId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["ok" => false, "error" => "İstifadəçi tapılmadı."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $hash = (string)$row['password_hash'];

    if (!password_verify($currentPassword, $hash)) {
        echo json_encode(["ok" => false, "error" => "Cari şifrə yanlışdır."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Eyni şifrə olmasın
    if (password_verify($newPassword, $hash)) {
        echo json_encode(["ok" => false, "error" => "Yeni şifrə cari şifrə ilə eyni ola bilməz."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $st = $pdo->prepare("UPDATE customers SET password_hash = ?, updated_at = NOW() WHERE id = ? LIMIT 1");
    $st->execute([$newHash, $customerId]);

    echo json_encode(["ok" => true, "message" => "Şifrə uğurla yeniləndi."], JSON_UNESCAPED_UNICODE);
    session_destroy();
} catch (Throwable $e) {
    echo json_encode(["ok" => false, "error" => "Server xətası: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}