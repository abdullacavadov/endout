<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = trim($_POST['is_active'] ?? '');
$feature_type = trim($_POST['value_type'] ?? '');
$feature_key = trim($_POST['feature_key'] ?? '');
$category = trim($_POST['category'] ?? '');

if (!empty($feature_key)) {
    if (!preg_match('/^[a-z_]+$/', $feature_key)) {
        exit('Xüsusiyyət açarı yalnız latın qrafikalı kiçik hərflər və alt xətdən ibarət ola bilər.');
    }
} else {
    exit('Zəhmət olmasa, xüsusiyyət açarını (feature_key) qeyd edin.');
}

if (empty($title)) {
    exit('Zəhmət olmasa, xüsusiyyət başlığını (title) qeyd edin.');
}

if (empty($description)) {
    exit('Zəhmət olmasa, xüsusiyyət təsvirini (description) qeyd edin.');
}
if (empty($feature_type)) {
    exit('Zəhmət olmasa, dəyər növünü (feature_type) qeyd edin.');
}

if (empty($category)) {
    exit('Zəhmət olmasa, kateqoriyanı (category) seçin.');
}

try {
    // ən böyük sort_order dəyərini al
    $stmt = $pdo->prepare("SELECT MAX(sort_order) AS max_sort FROM feature_definitions");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_sort = ($row && $row['max_sort'] !== null) ? ((int)$row['max_sort'] + 1) : 1;
    
    // Duplicate yoxlaması
    $check = $pdo->prepare("SELECT feature_key FROM feature_definitions WHERE feature_key = ?");
    $check->execute([$feature_key]);

    if ($check->rowCount() > 0) {
        exit('Bu xüsusiyyət açarı (feature_key) artıq istifadə olunub.');
    }

    //insert
    $statement = $pdo->prepare("
        INSERT INTO feature_definitions (title, description, is_active, value_type, feature_key, category, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $statement->execute([$title, $description, $status, $feature_type, $feature_key, $category, $next_sort]);

    exit('success');
} catch (PDOException $e) {
    exit('Xəta baş verdi: ' . $e->getMessage());
}
