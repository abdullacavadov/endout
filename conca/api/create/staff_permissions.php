<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}

$name = trim($_POST['permissions_name'] ?? '');
$permission = trim($_POST['permission'] ?? '');

if (empty($name)) {
    exit('Zəhmət olmasa, icazə adını doldurun.');
}

if (empty($permission)) {
    exit('Zəhmət olmasa, icazə sahəsini doldurun.');
}

try {

    // Duplicate yoxlaması
    $check = $pdo->prepare("SELECT id FROM tbl_permissions WHERE permission = ?");
    $check->execute([$permission]);

    if ($check->rowCount() > 0) {
        exit('Bu icazə artıq mövcuddur.');
    }

    // Insert
    $statement = $pdo->prepare("
        INSERT INTO tbl_permissions (name, permission)
        VALUES (?, ?)
    ");

    $statement->execute([$name, $permission]);

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}