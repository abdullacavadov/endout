<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}

$id = (int)($_POST['country_id'] ?? 0);
$coefficient = (float)($_POST['coefficient'] ?? 0);
$country_audience = (int)($_POST['country_audience'] ?? 0);
$name = trim($_POST['name'] ?? '');
$iso2 = trim($_POST['iso2'] ?? '');
$area = trim($_POST['area'] ?? '');
$dial_code = $_POST['dial_code'];
$currency_name = $_POST['currency_name'];
$currency_code = $_POST['currency_code'];
$currency_symbol = $_POST['currency_symbol'];
$country_status = trim($_POST['country_status']);

if ($id <= 0) {
    exit('Ölkə tapılmadı.');
}


if (empty($name)) {
    exit('Zəhmət olmasa, tam adı doldurun.');
}
if (empty($iso2)) {
    exit('Zəhmət olmasa, ISO2`i daxil edin.');
}
if (empty($area)) {
    exit('Zəhmət olmasa, ölkənin aid olduğu regionu seçin.');
}
if (empty($country_audience)) {
    exit('Zəhmət olmasa, ölkənin əhali sayını qeyd edin.');
}
if (!isset($_POST['coefficient']) || $_POST['coefficient'] === '') {
    exit('Zəhmət olmasa, qiymət hesablaması üçün bu ölkəyə əmsal təyin edin.');
}
if (empty($currency_name)) {
    exit('Zəhmət olmasa, ölkənin pul vahidini daxil edin (ingiliscə).');
}



try {

    $statement = $pdo->prepare("
        UPDATE countries
        SET
            name = ?,
            iso2 = ?,
            area = ?,
            country_audience = ?,
            coefficient = ?,
            dial_code = ?,
            currency_name = ?,
            currency_code = ?,
            currency_symbol = ?,
            country_status = ?
        WHERE id = ?
    ");

    $statement->execute([
        $name,
        $iso2,
        $area,
        $country_audience,
        $coefficient,
        $dial_code,
        $currency_name,
        $currency_code,
        $currency_symbol,
        $country_status,
        $id
    ]);

    echo 'success';

} catch (PDOException $e) {

    echo 'Server xətası baş verdi.';

    // Debug zamanı açıq saxla
    // echo $e->getMessage();
}