<?php

require_once '../../inc/config.php';

$pid = $_GET['pid'];

$stmt = $pdo->prepare("DELETE FROM tbl_permissions WHERE id = ?");
$stmt->execute([$pid]);

header("Location: ../../staff-permissions.php");
exit();