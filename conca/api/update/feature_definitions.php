<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title']);
$feature_key = trim($_POST['feature_key']);
$description = $_POST['description'] ?? '';
$value_type = trim($_POST['value_type']);
$category = trim($_POST['category']);
$is_active = (int) ($_POST['is_active'] ?? 0);

if ($id <= 0) {
    exit('Xüsusiyyət tapılmadı.');
}


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
    exit('Zəhmət olmasa, xüsusiyyət açıqlamasını (description) qeyd edin.');
}
if (empty($value_type)) {
    exit('Zəhmət olmasa, dəyər növünü (value_type) qeyd edin.');
}

if (empty($category)) {
    exit('Zəhmət olmasa, kateqoriyanı (category) seçin.');
}



try {

    $statement = $pdo->prepare("
        UPDATE feature_definitions
        SET
            title = ?,
            feature_key = ?,
            value_type = ?,
            description = ?,
            category = ?,
            is_active = ?
        WHERE id = ?
    ");

    $statement->execute([
        $title,
        $feature_key,
        $value_type,
        $description,
        $category,
        $is_active,
        $id
    ]);

    echo 'success';

} catch (PDOException $e) {

    echo 'Server xətası baş verdi. (' . $e->getMessage() . ')';

}