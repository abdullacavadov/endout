<?php

require_once '../../inc/config.php';

$pid = $_GET['pid'];

$stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
$stmt->execute([$pid]);

header("Location: ../../packages.php");
exit();