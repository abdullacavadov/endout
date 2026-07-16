<?php

require_once '../../inc/config.php';

$rid = $_GET['rid'];

$stmt = $pdo->prepare("DELETE FROM tbl_roles WHERE role_id = ?");
$stmt->execute([$rid]);

header("Location: ../../staff-roles.php");
exit();