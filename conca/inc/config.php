<?php
declare(strict_types=1);

ob_start();

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

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

// Production: detallı xətalar server loguna gedir, browser-ə çıxmır.
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Setting up the time zone
date_default_timezone_set($time_zone);


