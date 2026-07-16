<?php

require_once '../../inc/config.php';

$cid = $_GET['cid'];

$stmt = $pdo->prepare("DELETE FROM countries WHERE id = ?");
$stmt->execute([$cid]);

header("Location: ../../countries.php");
exit();