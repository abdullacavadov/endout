<?php
require_once("inc/db.php");

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
$row = $result[0] ?? [];

$confirm_code = $row['confirm_code'] ?? '';
$logo = $row['logo'] ?? '';
$meta_title_home = $row['meta_title_home'] ?? 'EndOut';
$base_url = $row['base_url'] ?? '';
$time_zone = $row['time_zone'] ?? 'Asia/Baku';
$admin_url = $row['admin_url'] ?? '';
$google_client_id = $row['google_client_id'] ?? '';
$google_client_secret = $row['google_client_secret'] ?? '';

define("BASE_URL", $base_url);
define("ADMIN_URL", BASE_URL . $admin_url . "/");
define('CONFIRM_DELETE_KEY', $confirm_code);
define('GOOGLE_CLIENT_ID', $google_client_id);
define('GOOGLE_CLIENT_SECRET', $google_client_secret);

ini_set('display_errors', '0');
ini_set('log_errors', '1');

try {
    date_default_timezone_set($time_zone ?: 'Asia/Baku');
} catch (Throwable $e) {
    date_default_timezone_set('Asia/Baku');
    error_log('Invalid admin timezone: ' . $e->getMessage());
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include("inc/CSRF_Protect.php");

$csrf = new CSRF_Protect();
$error_message = '';

if (isset($_POST['login'])) {
    $csrf->verifyRequest();

    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['pass'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    /*
     * Rate-limit cədvəli migration tətbiq olunmayıbsa belə login səhifəsi
     * HTTP 500 verməməlidir. Migration tətbiq olunandan sonra limit aktivdir.
     */
    $attemptsTableAvailable = true;
    try {
        $rate = $pdo->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE success=0 AND created_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE) AND (email=? OR ip_address=?)");
        $rate->execute([$email, $ip]);

        if ((int)$rate->fetchColumn() >= 8) {
            $error_message = 'Çox sayda uğursuz giriş cəhdi. 15 dəqiqə sonra yenidən cəhd edin.';
        }
    } catch (Throwable $e) {
        $attemptsTableAvailable = false;
        error_log('Admin login rate-limit table unavailable: ' . $e->getMessage());
    }

    if ($error_message === '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Email və ya şifrə yanlışdır.';
    }

    if ($error_message === '') {
        $statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=? AND status='Active' LIMIT 1");
        $statement->execute([$email]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, (string)$admin['password'])) {
            if ($attemptsTableAvailable) {
                try {
                    $pdo->prepare("INSERT INTO admin_login_attempts(email,ip_address,success) VALUES(?,?,0)")
                        ->execute([$email, $ip]);
                } catch (Throwable $e) {
                    error_log('Admin login attempt logging failed: ' . $e->getMessage());
                }
            }
            $error_message = 'Email və ya şifrə yanlışdır.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = $admin;

            if ($attemptsTableAvailable) {
                try {
                    $pdo->prepare("INSERT INTO admin_login_attempts(email,ip_address,success) VALUES(?,?,1)")
                        ->execute([$email, $ip]);

                    $pdo->prepare("INSERT INTO admin_sessions(admin_id,session_id,ip_address,user_agent) VALUES(?,?,?,?)")
                        ->execute([
                            (int)$admin['id'],
                            session_id(),
                            $ip,
                            substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255)
                        ]);
                } catch (Throwable $e) {
                    error_log('Admin session logging failed: ' . $e->getMessage());
                }
            }

            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $meta_title_home; ?> | Admin</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="assets/img/logo/favicon.png" type="image/x-icon">

    <!-- global style sheet for all pages -->
    <link id="bootstrap-css" rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/conca.css">

</head>

<body>

    <div class="auth-wrapper auth-cover min-vh-100 d-flex align-items-center justify-content-center">
        <div class="col-xl-9 col-lg-7 col-md-9 col-11">
            <div class="row position-relative z-2 mx-0 shadow-xl rounded overflow-hidden card-bg">
                <div class="col-xxl-6 col-xl-5 col-lg-12 d-xl-block d-none px-0">
                    <div class="auth-cover-wrapper h-100 d-flex align-items-center">
                        <div class="auth-cover-content" style="padding: 0;">

                            <div class="auth-cover-image">
                                <img src="https://cdn.pixabay.com/photo/2023/08/07/13/44/tree-8175062_1280.jpg" alt=""
                                    style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-7">

                    <div class="row justify-content-center align-items-center h-100">
                        <div class="col-sm-10 col-12">
                            <div class="py-12 px-5">
                                <div class="mb-7">
                                    <div class="d-flex align-items-center justify-content-center mb-5">
                                        <img class="app-main-logo logo-black" width="120"
                                            src="<?= $base_url; ?>assets/img/logo/<?= $logo; ?>" alt="Logo">
                                        <img class="app-main-logo logo-white d-none" width="120"
                                            src="<?= $base_url; ?>assets/img/logo/<?= $logo; ?>" alt="Logo">
                                    </div>
                                </div>

                                <form action="" method="post">
                                    <?php $csrf->echoInputField(); ?>
                                    <?php
                                    if ((isset($error_message)) && ($error_message != '')):
                                        echo '<div class="alert alert-danger">' . $error_message . '</div>';
                                    endif;
                                    ?>


                                    <div class="mb-3">
                                        <label for="loginEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="loginEmail"
                                            placeholder="mali@example.com" name="email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label">Şifrə</label>
                                        <div class="input-group mb-3">
                                            <input type="password" class="form-control" placeholder="**********"
                                                id="loginPassword" name="pass">
                                            <span class="input-group-text password-toggle">
                                                <span class="close-eye password-eye">
                                                    <svg width="22" height="10" viewBox="0 0 22 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M21 1C21 1 17 7 11 7C5 7 1 1 1 1" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round" />
                                                        <path d="M14 6.5L15.5 9" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M19 4L21 6" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M1 6L3 4" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M8 6.5L6.5 9" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="open-eye password-eye d-none">
                                                    <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M20.544 7.04498C20.848 7.4713 21 7.68447 21 8C21 8.31553 20.848 8.52869 20.544 8.95501C19.1779 10.8706 15.6892 15 11 15C6.31078 15 2.8221 10.8706 1.45604 8.95502C1.15201 8.5287 1 8.31553 1 8C1 7.68447 1.15201 7.47131 1.45604 7.04499C2.8221 5.12944 6.31078 1 11 1C15.6892 1 19.1779 5.12944 20.544 7.04498Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path
                                                            d="M14 8C14 6.34315 12.6569 5 11 5C9.34315 5 8 6.34315 8 8C8 9.65685 9.34315 11 11 11C12.6569 11 14 9.65685 14 8Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                    </svg>
                                                </span>
                                            </span>

                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {

                                                    document.querySelectorAll(".password-toggle").forEach(toggle => {

                                                        toggle.addEventListener("click", function () {

                                                            const input = this.parentElement.querySelector("input");
                                                            const closeEye = this.querySelector(".close-eye");
                                                            const openEye = this.querySelector(".open-eye");

                                                            if (input.type === "password") {
                                                                input.type = "text";

                                                                closeEye.classList.add("d-none");
                                                                openEye.classList.remove("d-none");
                                                            } else {
                                                                input.type = "password";

                                                                closeEye.classList.remove("d-none");
                                                                openEye.classList.add("d-none");
                                                            }

                                                        });

                                                    });

                                                });
                                            </script>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="d-flex align-items-center justify-content-end flex-wrap">

                                            <p class="mt-2">
                                                <a href="#" class="text-hover-underline">Şifrənin bərpası</a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-center">
                                            <button type="submit" name="login"
                                                class="btn btn-primary w-100">Login</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- global js scripts for all pages -->
    <script src="assets/vendor/libs/jquery/jquery.html"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.html"></script>
    <script src="assets/js/bootstrap.html"></script>


    <!-- app js -->
    <script src="assets/js/conca-sidebar.js"></script>
    <script src="assets/js/conca.js"></script>

    <!-- page specific script -->

</body>


<!-- Mirrored from html.aqlova.com/conca-demo/conca/auth-login-cover.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 18 May 2026 11:02:18 GMT -->

</html>