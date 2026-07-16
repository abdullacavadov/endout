<?php
require_once __DIR__ . "/../../inc/config.php";

header("Content-Type: application/json");

$q = mb_strtolower(trim($_GET['q'] ?? ''));
$q = preg_replace('/[^\p{L}\p{N}\s]/u', '', $q);


if (mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT keyword 
    FROM tags 
    WHERE keyword LIKE ?
    LIMIT 10
");

$stmt->execute([$q . '%']);
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($tags);