<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}

$pid = $_POST['pid'] ?? '';
$permission = trim($_POST['permission'] ?? '');
$name = trim($_POST['name'] ?? '');

if (empty($pid)) {
    exit('Bu icazə mövcud deyil.');
}

if (empty($permission)) {
    exit('Zəhmət olmasa, icazə sahəsini doldurun.');
}

if (empty($name)) {
    exit('Zəhmət olmasa, icazə adını doldurun.');
}


try {

    $st = $pdo->prepare("UPDATE tbl_permissions SET permission = ?, name = ? WHERE id = ?");
    $st->execute([$permission, $name, $pid]);

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    // echo $e->getMessage();

}