<?php
declare(strict_types=1);


$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');


/*
|--------------------------------------------------------------------------
| USER IP-ni və ölkə kodunu əldə et
|--------------------------------------------------------------------------
*/
function get_client_ip()
{

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]); // yalnız birinci IP
    }

    if (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }

    return null;
}


$user_ip = get_client_ip();

if ($user_ip === '127.0.0.1' || $user_ip === '::1') {
    $user_ip = '188.253.208.24'; // test
}

$response = file_get_contents("http://ip-api.com/json/{$user_ip}");
$data = json_decode($response, true);

$user_country_code = $data['countryCode'] ?? null;




/*
|--------------------------------------------------------------------------
| SESSION HARDENING
|--------------------------------------------------------------------------
*/
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

if (!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', '1');
}

/*
|--------------------------------------------------------------------------
| SECURITY HEADERS
|--------------------------------------------------------------------------
*/
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

/*
|--------------------------------------------------------------------------
| LOAD CORE FILES
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../api/_db.php';
require_once __DIR__ . '/../api/_guards.php';
require_once __DIR__ . '/../api/_csrf.php';
require_once __DIR__ . '/../api/_helpers.php';


/*
|--------------------------------------------------------------------------
| START SESSION
|--------------------------------------------------------------------------
*/
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
  json_out(['ok' => false, 'error' => 'DB bağlantısı tapılmadı. (_db.php)'], 500);
}



///////////////////////////////////////////////////////////////////////////////////

define('BASE_URL', $base_url);

// Test/Prod mode
define('APP_ENV', 'local'); // prod-da 'prod'
define('SHOW_OTP_ON_FRONT', true); // prod-da false et

//Google Captcha
define('SECRET_KEY', '6LeOX5osAAAAAPcUgr9zb1xBdTRkYHjBGnVMIhgi');
define('SITE_KEY', '6LeOX5osAAAAAPcUgr9zb1xBdTRkYHjBGnVMIhgi');


// SMTP SETTINGS (PHPMailer)
define('SMTP_HOST', 'mail.ofismall.az');
define('SMTP_PORT', 465);
define('SMTP_USER', 'a.javadov@ofismall.az');     // tam email
define('SMTP_PASS', 'Graf1993a'); // webmail hesabının şifrəsi
define('SMTP_FROM_EMAIL', 'a.javadov@ofismall.az');
define('SMTP_FROM_NAME', 'EndOut Support');
define('SMTP_SECURE', 'ssl'); // 465 üçün



// SMS OTP
define('PG_API_URL', 'api.poctgoyercini.com');
define('PG_PUBLIC_KEY', 'e6a2adb3268348b1');
define('PG_PRIVATE_KEY', 'eyJhbGciOiJBMjU2S1ciLCJlbmMiOiJBMjU2Q0JDLUhTNTEyIiwidHlwIjoiSldUIiwiY3R5IjoiSldUIn0.A4J2rnJIYf0NCdbsKyTbOdbl3bJFPp-jrXq3IfY_dkKJZVFYOucUOlapVbLYL8_ykQv-zFsJysLAr0Wdwt9IFu2Rba3DGWiV.Kez6_oSK1lH2bU6kY2hAoA.5OMCJjmt99Hj21DEUTSI_Ubycdu-cbnq-xs-OROEY4m3g3A6uGjVqeCLEtq3wctWNl4_5RIRzenM0EtiJejt71VX29o4WX8pjYB5_tWUBhHKTOh4_CkPNaN7exRSR0YvzeJiCkdoTI32OX4mN3pdD8mxqwp-Pk4UQymqfuzXbktxUQXgY4QpbdpwGN6Hmo-eeE_QdxdNggjhj3ueadizDxjuGegHX42dR6gzBZWjnnSjtL6u1nf2tb-ztnfQdKPQYSpNLAlCZqhKWRB__LwSUCwC8K0loxis_W6-HOkk4bum70VwzlCeYMA2gLMpfqZQL8zwvKKIr0HgdoD2ykL68Hq0Ux6n2gdKZdSr7VAiU_L5NswrenmpMfBmxDRtCgez49EBryCppPUxSPANt7nZpsad3B1TDv07vwEyQbe_gWLE4F1SnxhvtT0WPvnf6iGF_H2sjQ_k1X6vOe8jTKpujg.AJsYfDeeSgQqLMuv_BDeMEbcR1JZyBs5KLMVKGyPgK0');
define('PG_SMS_ENCODING', 'LATIN');
define('PG_SMS_REPORT_LABEL', 'endout-otp');
define('PG_SMS_PURPOSE', 'INF');
define('PG_SMS_ORIGINATOR', 'EndOut');

