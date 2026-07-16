<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}

$id = (int) ($_POST['package_id'] ?? 0);
$name = trim($_POST['name']);
$duration_days = (int)($_POST['duration_days']);
$base_price = (float)$_POST['base_price'];
$is_active = (int) ($_POST['is_active'] ?? 0);

if ($id <= 0) {
    exit('Paket tapılmadı.'); 
}



if (empty($name)) {
    exit('Zəhmət olmasa, paket adını ingiliscə qeyd edin.');
}

if (empty($duration_days)) {
    exit('Zəhmət olmasa, paket müddətini (gün) qeyd edin.');
}

if ($base_price === '') {
    exit('Zəhmət olmasa, paket dəyərini (AZN) qeyd edin.');
}



try {

    $statement = $pdo->prepare("
        UPDATE packages
        SET
            name = ?,
            duration_days = ?,
            base_price = ?,
            is_active = ?
        WHERE id = ?
    ");

    $statement->execute([
        $name,
        $duration_days,
        $base_price,
        $is_active,
        $id
    ]);

    echo 'success';

} catch (PDOException $e) {

    echo 'Server xətası baş verdi. (' . $e->getMessage() . ')';

}