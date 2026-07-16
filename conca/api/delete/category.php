<?php

require_once '../../inc/config.php';

$cid = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT);

if (!$cid) {
    exit('Yanlış kateqoriya ID-si.');
}

$statement = $pdo->prepare("SELECT icon FROM categories WHERE id = ?");
$statement->execute([$cid]);
$category = $statement->fetch(PDO::FETCH_ASSOC);

if ($category && !empty($category['icon'])) {
    $iconPath = dirname(__DIR__, 3) . '/App/assets/img/category/' . $category['icon'];
    if (file_exists($iconPath)) {
        unlink($iconPath);
    }
}


$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$cid]);

header("Location: ../../categories.php");
exit();