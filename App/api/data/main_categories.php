<?php
require_once __DIR__ . "/../../inc/config.php";

$stmt = $pdo->query("
SELECT id,name
FROM listing_categories_main
ORDER BY name
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));