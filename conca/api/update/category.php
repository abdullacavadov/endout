<?php

require_once("../../inc/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Sorğu metodu yanlışdır.');
}


$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$slug_path = trim($_POST['slug_path'] ?? '');

$parent_id = null;

if (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') {

    $parent_id = (int) $_POST['parent_id'];

    if ($parent_id === 0) {
        $parent_id = null;
    } else {
        if (!isset($_POST['parent_id']) || $_POST['parent_id'] === '') {
            exit('Zəhmət olmasa, üst kateqoriyanı seçin.');
        }
    }
}


if (empty($name)) {
    exit('Zəhmət olmasa, tam adı doldurun.');
}
if (empty($slug)) {
    exit('Zəhmət olmasa, slugu doldurun.');
}


try {

    // UPDATE
    $statement = $pdo->prepare("
        UPDATE categories
        SET name = ?, slug = ?, slug_path = ?, parent_id = ?
        WHERE id = ?
    ");

    $statement->execute([$name, $slug, $slug_path, $parent_id, $id]);

    echo 'success';

} catch (PDOException $e) {

    // Productionda bunu istifadəçiyə göstərmə
    echo 'Server xətası baş verdi.';

    // Debug üçün:
    echo $e->getMessage();

}