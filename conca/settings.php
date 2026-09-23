<?php require_once("inc/config.php"); require_once("inc/admin_auth.php"); require_admin_permission($pdo, "settings"); ?>

<?php

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id = ?");
$statement->execute([1]);
$member = $statement->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="az">


<head>
    <?php require_once('inc/head.php'); ?>
</head>

<body>



    <div class="app-main">

        <!-- app wrapper start -->
        <div id="app-wrapper" class="app-wrapper d-flex flex-column align-items-stretch min-vh-100">

            <!-- app sidebar start -->
            <?php require_once('inc/sidebar.php'); ?>
            <!-- app sidebar end -->

            <!-- app header start -->
            <?php require_once('inc/navbar.php'); ?>
            <!-- app header end -->

            <!-- app content start -->
            <div class="app-content-wrapper py-20 pb-13">
                <div class="container ">
                    <div class="page-content">

                        <form id="settings">
                            <div class="row">
                                <div class="col-md-4">
                                    <div
                                        class="rounded-custom card-bg shadow-custom px-7 py-2 mb-6 position-sticky top-8" style="top: 20px">
                                        <div class="side-navigation">
                                            <nav id="account-side-navigation" class="flex-column align-items-stretch">
                                                <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4 border-bottom"
                                                    href="#basic_settings">
                                                    <span>
                                                        <i class="fa-solid fa-gears"></i>
                                                    </span>
                                                    Əsas parametrlər
                                                </a>
                                                <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4 border-bottom"
                                                    href="#meta_tags">
                                                    <span>
                                                        <i class="fa-solid fa-hashtag"></i>
                                                    </span>
                                                    Meta Tag
                                                </a>
                                                <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4 border-bottom"
                                                    href="#contact">
                                                    <span>
                                                        <i class="fa-solid fa-at"></i>
                                                    </span>
                                                    Əlaqə
                                                </a>
                                                <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4"
                                                    href="#logo">
                                                    <span>

                                                        <i class="fa-solid fa-image"></i>
                                                    </span>
                                                    Logo
                                                </a>

                                                <div class="pure-card-footer">
                                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                                        <a href="index.php" class="btn btn-custom-secondary">İmtina</a>
                                                        <button type="submit" class="btn btn-primary">
                                                            Yadda saxla
                                                        </button>
                                                    </div>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div data-bs-spy="scroll" data-bs-target="#account-side-navigation"
                                        data-bs-smooth-scroll="true" tabindex="0">
                                        <div id="basic_settings" class="pure-card rounded-custom card-bg shadow-custom">
                                            <form>
                                                <div class="pure-card-header">
                                                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                                                        <span class="text-primary d-flex align-items-center">
                                                            <i class="fa-solid fa-gears"></i>
                                                        </span>
                                                        Əsas parametrlər
                                                    </h3>
                                                </div>
                                                <div class="pure-card-body">
                                                    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 gy-6">
                                                        <div class="col">
                                                            <label for="base_url" class="form-label">Əsas url (BASE_URL)
                                                                <span class="text-danger fw-bold">*</span></label>
                                                            <input type="text" class="form-control" id="base_url"
                                                                placeholder="https://endout.org/app/" name="base_url"
                                                                value="<?= $base_url; ?>">
                                                        </div>
                                                        <div class="col">
                                                            <label for="admin_url" class="form-label">Admin URL
                                                                (ADMIN_URL) <span
                                                                    class="text-danger fw-bold">*</span></label>
                                                            <input type="text" class="form-control" id="admin_url"
                                                                placeholder="https://endout.org/admin/" name="admin_url"
                                                                value="<?= $admin_url; ?>">
                                                        </div>
                                                        <div class="col">
                                                            <label for="time_zone" class="form-label">Saat qurşağı <span
                                                                    class="text-danger fw-bold">*</span></label>
                                                            <select class="form-control" id="time_zone"
                                                                name="time_zone">
                                                                <option value="">-- Saat qurşağı --</option>
                                                                <?php include('inc/time_zone_options.php'); ?>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                            <label for="confirm_code" class="form-label">Təsdiq kodu
                                                                <span class="text-danger fw-bold">*</span></label>
                                                            <input type="tel" class="form-control" id="confirm_code"
                                                                placeholder="Təsdiq kodu: ****" name="confirm_code"
                                                                value="<?= $confirm_code; ?>">
                                                        </div>


                                                    </div>
                                                </div>

                                        </div>

                                        <div id="meta_tags" class="pure-card rounded-custom card-bg shadow-custom mt-6">
                                            <div class="pure-card-header">
                                                <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                                                    <span class="text-primary d-flex align-items-center">
                                                        <i class="fa-solid fa-hashtag"></i>
                                                    </span>
                                                    Meta Tags
                                                </h3>
                                            </div>
                                            <div class="pure-card-body">
                                                <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 gy-6">
                                                    <div class="col">
                                                        <label for="base_url" class="form-label">Əsas url (BASE_URL)
                                                            <span class="text-danger fw-bold">*</span></label>
                                                        <input type="text" class="form-control" id="base_url"
                                                            placeholder="https://endout.org/app/" name="base_url"
                                                            value="<?= $base_url; ?>">
                                                    </div>
                                                    <div class="col">
                                                        <label for="admin_url" class="form-label">Admin URL
                                                            (ADMIN_URL) <span
                                                                class="text-danger fw-bold">*</span></label>
                                                        <input type="text" class="form-control" id="admin_url"
                                                            placeholder="https://endout.org/admin/" name="admin_url"
                                                            value="<?= $admin_url; ?>">
                                                    </div>
                                                    <div class="col">
                                                        <label for="time_zone" class="form-label">Saat qurşağı <span
                                                                class="text-danger fw-bold">*</span></label>
                                                        <select class="form-control" id="time_zone" name="time_zone">
                                                            <option value="">-- Saat qurşağı --</option>
                                                            <?php include('inc/time_zone_options.php'); ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label for="confirm_code" class="form-label">Təsdiq kodu
                                                            <span class="text-danger fw-bold">*</span></label>
                                                        <input type="tel" class="form-control" id="confirm_code"
                                                            placeholder="Təsdiq kodu: ****" name="confirm_code"
                                                            value="<?= $confirm_code; ?>">
                                                    </div>


                                                </div>
                                            </div>

                                        </div>

                                        <div id="contact" class="pure-card rounded-custom card-bg shadow-custom mt-6">
                                            <div class="pure-card-header">
                                                <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                                                    <span class="text-primary d-flex align-items-center">
                                                        <i class="fa-solid fa-at"></i>
                                                    </span>
                                                    Əlaqə
                                                </h3>
                                            </div>
                                            <div class="pure-card-body">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="text-custom-paragraph fw-medium">
                                                                    Type
                                                                </th>
                                                                <th scope="col" class="text-custom-paragraph fw-medium">
                                                                    Device</th>
                                                                <th scope="col" class="text-custom-paragraph fw-medium">
                                                                    Location</th>
                                                                <th scope="col" class="text-custom-paragraph fw-medium">
                                                                    Recent activity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <img src="assets/img/icons/browser/chrome.html"
                                                                            alt="">
                                                                        <span class="fw-medium text-custom-body">Chrome
                                                                            on
                                                                            Windows</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <svg width="18" height="17" viewBox="0 0 18 17"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M15.4 1H2.6C1.71634 1 1 1.71634 1 2.6V10.6C1 11.4837 1.71634 12.2 2.6 12.2H15.4C16.2837 12.2 17 11.4837 17 10.6V2.6C17 1.71634 16.2837 1 15.4 1Z"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M5.79688 15.3999H12.1969"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M9 12.2V15.4" stroke="currentColor"
                                                                                stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                        </svg>
                                                                        <span class="fw-normal text-custom-body">Dell
                                                                            XPS
                                                                            15</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span class="fw-normal text-custom-body">New
                                                                            Mexico</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span
                                                                            class="fw-normal text-custom-body">Now</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <img src="assets/img/icons/browser/safari.html"
                                                                            alt="">
                                                                        <span class="fw-medium text-custom-body">Safari
                                                                            on
                                                                            MacOS</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <svg width="18" height="17" viewBox="0 0 18 17"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M15.4 1H2.6C1.71634 1 1 1.71634 1 2.6V10.6C1 11.4837 1.71634 12.2 2.6 12.2H15.4C16.2837 12.2 17 11.4837 17 10.6V2.6C17 1.71634 16.2837 1 15.4 1Z"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M5.79688 15.3999H12.1969"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M9 12.2V15.4" stroke="currentColor"
                                                                                stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                        </svg>
                                                                        <span class="fw-normal text-custom-body">MacBook
                                                                            Air
                                                                            2020</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span class="fw-normal text-custom-body">New
                                                                            York</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span class="fw-normal text-custom-body">
                                                                            2 days ago
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <img src="assets/img/icons/browser/safari.html"
                                                                            alt="">
                                                                        <span class="fw-medium text-custom-body">Safari
                                                                            on
                                                                            MacOS</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <svg width="18" height="17" viewBox="0 0 18 17"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M15.4 1H2.6C1.71634 1 1 1.71634 1 2.6V10.6C1 11.4837 1.71634 12.2 2.6 12.2H15.4C16.2837 12.2 17 11.4837 17 10.6V2.6C17 1.71634 16.2837 1 15.4 1Z"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M5.79688 15.3999H12.1969"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M9 12.2V15.4" stroke="currentColor"
                                                                                stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                        </svg>
                                                                        <span class="fw-normal text-custom-body">MacBook
                                                                            Pro
                                                                            M4</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span
                                                                            class="fw-normal text-custom-body">Dhaka</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span class="fw-normal text-custom-body">
                                                                            1 week ago
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <img src="assets/img/icons/browser/chrome.html"
                                                                            alt="">
                                                                        <span class="fw-medium text-custom-body">
                                                                            Chrome on Windows
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <svg width="18" height="17" viewBox="0 0 18 17"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M15.4 1H2.6C1.71634 1 1 1.71634 1 2.6V10.6C1 11.4837 1.71634 12.2 2.6 12.2H15.4C16.2837 12.2 17 11.4837 17 10.6V2.6C17 1.71634 16.2837 1 15.4 1Z"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M5.79688 15.3999H12.1969"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M9 12.2V15.4" stroke="currentColor"
                                                                                stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                        </svg>
                                                                        <span class="fw-normal text-custom-body">
                                                                            HP Pavilion 15
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span class="fw-normal text-custom-body">
                                                                            California
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex align-items-center gap-2 py-2 text-custom-body">
                                                                        <span class="fw-normal text-custom-body">
                                                                            2 weeks ago
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="logo" class="pure-card rounded-custom card-bg shadow-custom mt-6">
                                            <div class="pure-card-header">
                                                <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                                                    <span class="text-primary d-flex align-items-center">
                                                        <i class="fa-solid fa-image"></i>
                                                    </span>
                                                    Loqotip
                                                </h3>
                                            </div>
                                            <div class="pure-card-body">
                                                <div class="alert alert-danger border-dashed border border-warning pt-6"
                                                    role="alert">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="d-flex align-items-start">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M10 10V14.8462M19 10C19 14.9706 14.9706 19 10 19C5.02944 19 1 14.9706 1 10C1 5.02944 5.02944 1 10 1C14.9706 1 19 5.02944 19 10ZM10.6923 6.5387C10.6923 6.92105 10.3824 7.23101 10 7.23101C9.61769 7.23101 9.30773 6.92105 9.30773 6.5387C9.30773 6.15635 9.61769 5.84639 10 5.84639C10.3824 5.84639 10.6923 6.15635 10.6923 6.5387Z"
                                                                    stroke="currentColor" stroke-width="1.5"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                        <div class="">
                                                            <h4 class="alert-heading">
                                                                Deleting Account
                                                            </h4>
                                                            <p class="m-0">For extra security, this requires you to
                                                                confirm your email or phone number when you reset
                                                                yousignr password.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="deleteAccount">
                                                    <label class="form-check-label" for="deleteAccount"> Confirm
                                                        that I
                                                        want to delete my account.</label>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-danger">
                                                        Delete Account
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                    </div><!-- page content end -->

                </div>
            </div><!-- app content end -->
            <?php require_once('inc/footer.php'); ?>

            <div class="app-backdrop"></div>
            <!-- app content end -->
        </div>
        <!-- app wrapper end -->
    </div>

    <?php require_once('inc/scripts_url.php'); ?>
</body>

</html>