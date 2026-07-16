<?php
require_once __DIR__ . "/../../inc/config.php";

$mid = intval($_GET['mid']);

$stmt = $pdo->prepare("
SELECT id,name
FROM listing_categories_sub
WHERE mid_id = ?
");

$stmt->execute([$mid]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));