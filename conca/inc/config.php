<?php
error_reporting(E_ALL);

ob_start();
session_start();

require_once("db.php");

if (!isset($_SESSION['user'])) {
	header('location: signin.php');
	exit;
}

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$confirm_code = $row['confirm_code'];
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$meta_title_home = $row['meta_title_home'];
	$meta_keyword_home = $row['meta_keyword_home'];
	$meta_description_home = $row['meta_description_home'];
	$meta_img = $row['meta_img'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$contact_address = $row['contact_address'];
	$contact_map = $row['contact_map'];
	$base_url = $row['base_url'];
	$time_zone = $row['time_zone'];
	$admin_url = $row['admin_url'];
	$google_client_id = $row['google_client_id'];
	$google_client_secret = $row['google_client_secret'];
	$maintenance = $row['maintenance'];
}

// Defining base url
define("BASE_URL", $base_url);

// Getting admin url
define("ADMIN_URL", BASE_URL . "" . $admin_url . "/");

//Confirm delete code
define('CONFIRM_DELETE_KEY', $confirm_code);

//Google Login
define('GOOGLE_CLIENT_ID', $google_client_id);
define('GOOGLE_CLIENT_SECRET', $google_client_secret);

// Error Reporting Turn On
ini_set('error_reporting', E_ALL);

// Setting up the time zone
date_default_timezone_set($time_zone);


