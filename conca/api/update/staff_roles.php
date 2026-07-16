<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$role_id = $_POST['role_id'] ?? null;
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

    // UPDATE
    $statement = $pdo->prepare("
        UPDATE tbl_roles
        SET role_name = ?, role_permissions = ?
        WHERE role_id = ?
    ");

    $statement->execute([$role_name, $permissions_string, $role_id]);

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}