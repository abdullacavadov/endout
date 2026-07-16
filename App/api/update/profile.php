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

    $customerId = (int) $_SESSION['customer_id'];

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));

    if ($fullName === '') {
        echo json_encode(["ok" => false, "error" => "Ad və soyad boş ola bilməz."], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($phone === '') {
        echo json_encode(["ok" => false, "error" => "Telefon boş ola bilməz."], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["ok" => false, "error" => "E-poçt düzgün deyil."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Mövcud məlumatları oxu (dəyişiklik olduqda verified sıfırlamaq üçün)
    $st = $pdo->prepare("SELECT phone, email FROM customers WHERE id = ? LIMIT 1");
    $st->execute([$customerId]);
    $current = $st->fetch(PDO::FETCH_ASSOC);

    if (!$current) {
        echo json_encode(["ok" => false, "error" => "İstifadəçi tapılmadı."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Phone uniqueness (istəyə görə çıxara bilərsən)
    $st = $pdo->prepare("SELECT id FROM customers WHERE phone = ? AND id <> ? LIMIT 1");
    $st->execute([$phone, $customerId]);
    if ($st->fetch()) {
        echo json_encode(["ok" => false, "error" => "Bu telefon nömrəsi artıq istifadə olunur."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Email uniqueness (istəyə görə çıxara bilərsən)
    $st = $pdo->prepare("SELECT id FROM customers WHERE email = ? AND id <> ? LIMIT 1");
    $st->execute([$email, $customerId]);
    if ($st->fetch()) {
        echo json_encode(["ok" => false, "error" => "Bu e-poçt artıq istifadə olunur."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $phoneChanged = ($phone !== (string) $current['phone']);
    $emailChanged = ($email !== (string) $current['email']);

    // Dəyişibsə verified flag-ları sıfırla
    $phoneVerified = $phoneChanged ? 0 : null; // null => toxunma
    $emailVerified = $emailChanged ? 0 : null;

    // Dinamik UPDATE (yalnız lazım olan sahələr)
    $fields = ["full_name = ?", "phone = ?", "email = ?", "updated_at = NOW()"];
    $params = [$fullName, $phone, $email];

    if ($phoneVerified !== null) {
        $fields[] = "phone_verified = ?";
        $params[] = $phoneVerified;
    }
    if ($emailVerified !== null) {
        $fields[] = "email_verified = ?";
        $params[] = $emailVerified;
    }

    $params[] = $customerId;

    $sql = "UPDATE customers SET " . implode(", ", $fields) . " WHERE id = ? LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute($params);

    echo json_encode([
        "ok" => true,
        "needs_verification" => ($phoneChanged || $emailChanged)
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode(["ok" => false, "error" => "Server xətası: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}