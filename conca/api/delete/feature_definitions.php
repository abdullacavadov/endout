<?php

require_once '../../inc/config.php';

$fid = $_GET['fid'];

$stmt = $pdo->prepare("DELETE FROM feature_definitions WHERE id = ?");
$stmt->execute([$fid]);

header("Location: ../../feature-definitions.php");
exit();