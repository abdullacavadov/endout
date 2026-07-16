<?php
require_once __DIR__ . "/../../inc/config.php";

$stmt = $pdo->prepare("
SELECT id, name
FROM sale_modes
");

$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));