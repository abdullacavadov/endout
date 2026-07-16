<?php

require_once '../../inc/config.php';

$mid = $_GET['mid'];

$stmt = $pdo->prepare("DELETE FROM tbl_user WHERE id = ?");
$stmt->execute([$mid]);

header("Location: ../../staff-members.php");
exit();