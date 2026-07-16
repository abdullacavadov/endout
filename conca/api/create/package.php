<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$name = trim($_POST['name'] ?? '');
$duration_days = (int)($_POST['duration_days'] ?? '');
$base_price = (float)($_POST['base_price'] ?? '');
$is_active = (int)($_POST['is_active'] ?? '');



if (empty($name)) {
    exit('Zəhmət olmasa, paket adını qeyd edin.');
}

if (empty($duration_days)) {
    exit('Zəhmət olmasa, müddəti qeyd edin.');
}

if (empty($base_price)) {
    exit('Zəhmət olmasa, baza qiymətini qeyd edin.');
}



try {
    // Duplicate yoxlaması
    $check = $pdo->prepare("SELECT name FROM packages WHERE name = ?");
    $check->execute([$name]);

    if ($check->rowCount() > 0) {
        exit('Bu paket adı artıq istifadə olunub.');
    }

    // Insert
    $statement = $pdo->prepare("
        INSERT INTO packages (name, duration_days, base_price, is_active)
        VALUES (?, ?, ?, ?)
    ");

    $statement->execute([$name, $duration_days, $base_price, $is_active]);


    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi. (' . $e->getMessage() . ')';

}