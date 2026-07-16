<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$role_name = trim($_POST['role_name'] ?? '');

$role_permissions = isset($_POST['role_permissions'])
    ? array_map('trim', $_POST['role_permissions'])
    : [];


if (empty($role_name)) {
    exit('Zəhmət olmasa, vəzifə adını doldurun.');
}

if (empty($role_permissions)) {
    exit('Zəhmət olmasa, vəzifə icazələrini seçin.');
}

$role_permissions = array_map(function ($item) {
    return "'" . $item . "'";
}, $role_permissions);

$permissions_string = implode(',', $role_permissions);





try {

    // Duplicate yoxlaması
    $check = $pdo->prepare("SELECT role_name FROM tbl_roles WHERE role_name = ?");
    $check->execute([$role_name]);

    if ($check->rowCount() > 0) {
        exit('Bu vəzifə artıq mövcuddur.');
    }

    // Insert
    $statement = $pdo->prepare("
        INSERT INTO tbl_roles (role_name, role_permissions)
        VALUES (?, ?)
    ");

    $statement->execute([$role_name, $permissions_string]);

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}