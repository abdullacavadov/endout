<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$member_id = $_POST['member_id'] ?? null;
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = trim($_POST['role'] ?? '');


if (empty($full_name)) {
    exit('Zəhmət olmasa, tam adı doldurun.');
}
if (empty($email)) {
    exit('Zəhmət olmasa, emaili doldurun.');
}
if (empty($phone)) {
    exit('Zəhmət olmasa, telefon nömrəsini doldurun.');
}
if (empty($role)) {
    exit('Zəhmət olmasa, vəzifəni seçin.');
}




try {

    // UPDATE
    $statement = $pdo->prepare("
        UPDATE tbl_user
        SET full_name = ?, email = ?, phone = ?, role = ?
        WHERE id = ?
    ");

    $statement->execute([$full_name, $email, $phone, $role, $member_id]);

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}