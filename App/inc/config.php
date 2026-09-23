<?php
declare(strict_types=1);

$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function env_bool(string $key, bool $default = false): bool
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
}

/*
|---------------------------------------------------------------------------
| SESSION HARDENING
|---------------------------------------------------------------------------
*/
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
|---------------------------------------------------------------------------
| SECURITY HEADERS
|---------------------------------------------------------------------------
*/
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

/*
|---------------------------------------------------------------------------
| USER IP / COUNTRY
|---------------------------------------------------------------------------
| X-Forwarded-For yalnız tətbiq etibarlı reverse proxy arxasındadırsa
| istifadə olunur. Əks halda client tərəfindən saxtalaşdırıla bilər.
*/
function get_client_ip(): ?string
{
    if (
        env_bool('TRUST_PROXY', false)
        && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
    ) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    return is_string($ip) && filter_var($ip, FILTER_VALIDATE_IP)
        ? $ip
        : null;
}

$user_ip = get_client_ip();
$user_country_code = $_SESSION['_country_code'] ?? null;

if (
    $user_country_code === null
    && $user_ip !== null
    && filter_var($user_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
) {
    $geoUrl = 'https://ip-api.com/json/' . rawurlencode($user_ip) . '?fields=status,countryCode';

    $context = stream_context_create([
        'http' => [
            'timeout' => 2,
            'ignore_errors' => true,
        ],
    ]);

    $response = @file_get_contents($geoUrl, false, $context);
    $data = is_string($response) ? json_decode($response, true) : null;

    $user_country_code = is_array($data) && ($data['status'] ?? '') === 'success'
        ? ($data['countryCode'] ?? null)
        : null;

    $_SESSION['_country_code'] = $user_country_code;
}

/*
|---------------------------------------------------------------------------
| LOAD CORE FILES
|---------------------------------------------------------------------------
*/
require_once __DIR__ . '/../api/_db.php';
require_once __DIR__ . '/../api/_guards.php';
require_once __DIR__ . '/../api/_csrf.php';
require_once __DIR__ . '/../api/_helpers.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    json_out(['ok' => false, 'error' => 'DB bağlantısı tapılmadı.'], 500);
}

define('BASE_URL', $base_url);

define('APP_ENV', env_value('APP_ENV', 'prod'));
define('SHOW_OTP_ON_FRONT', env_bool('SHOW_OTP_ON_FRONT', false));

/*
|---------------------------------------------------------------------------
| GOOGLE reCAPTCHA
|---------------------------------------------------------------------------
*/
define('SECRET_KEY', env_value('RECAPTCHA_SECRET_KEY', ''));
define('SITE_KEY', env_value('RECAPTCHA_SITE_KEY', ''));

/*
|---------------------------------------------------------------------------
| SMTP
|---------------------------------------------------------------------------
*/
define('SMTP_HOST', env_value('SMTP_HOST', ''));
define('SMTP_PORT', (int) env_value('SMTP_PORT', '465'));
define('SMTP_USER', env_value('SMTP_USER', ''));
define('SMTP_PASS', env_value('SMTP_PASS', ''));
define('SMTP_FROM_EMAIL', env_value('SMTP_FROM_EMAIL', ''));
define('SMTP_FROM_NAME', env_value('SMTP_FROM_NAME', 'EndOut Support'));
define('SMTP_SECURE', env_value('SMTP_SECURE', 'ssl'));

/*
|---------------------------------------------------------------------------
| SMS OTP
|---------------------------------------------------------------------------
*/
define('PG_API_URL', env_value('PG_API_URL', ''));
define('PG_PUBLIC_KEY', env_value('PG_PUBLIC_KEY', ''));
define('PG_PRIVATE_KEY', env_value('PG_PRIVATE_KEY', ''));
define('PG_SMS_ENCODING', env_value('PG_SMS_ENCODING', 'LATIN'));
define('PG_SMS_REPORT_LABEL', env_value('PG_SMS_REPORT_LABEL', 'endout-otp'));
define('PG_SMS_PURPOSE', env_value('PG_SMS_PURPOSE', 'INF'));
define('PG_SMS_ORIGINATOR', env_value('PG_SMS_ORIGINATOR', 'EndOut'));
