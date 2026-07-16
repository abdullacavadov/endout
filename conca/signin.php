<?php
require_once("inc/db.php");

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


include("inc/CSRF_Protect.php");

$csrf = new CSRF_Protect();
$error_message = '';

if (isset($_POST['login'])) {

    // CSRF token doğrulaması
    $csrf->verifyRequest();

    // Daxil edilən məlumatların təmizlənməsi
    $email = trim($_POST['email']);
    $password = $_POST['pass'];

    // İstifadəçini email və aktiv status üzrə axtarırıq
    $statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email = ? AND status = ?");
    $statement->execute([$email, 'Active']);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $error_message .= 'Bu email bazada mövcud deyil<br>';
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message .= 'Email formatı yanlışdır<br>';
        }
        // DB-dən alınan hash-lanmış şifrə ilə daxil edilən şifrəni müqayisə edirik
        if (!password_verify($password, $row['password'])) {
            $error_message .= 'Şifrə uyğun gəlmir<br>';
        } else {
            // Daxil edilən məlumatlar düzgündür, sessiyaya məlumatı yazırıq və yönləndiririk
            $_SESSION['user'] = $row;
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