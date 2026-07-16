<?php
$dbhost = 'localhost';
$dbname = 'endout';
$dbuser = 'root';
$dbpass = '';

try {
	$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
	echo "Connection error :" . $exception->getMessage();
}

?>