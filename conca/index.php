<?php
include("inc/config.php");
?>

<!DOCTYPE html>
<html lang="az">

<head>
    <?php include("inc/head.php"); ?>
</head>

<body>



    <div class="app-main">

        <!-- app wrapper start -->
        <div id="app-wrapper" class="app-wrapper d-flex flex-column align-items-stretch min-vh-100">

            <!-- app sidebar start -->
            <?php include("inc/sidebar.php"); ?>
            <!-- app sidebar end -->

            <!-- app header start -->
            <?php include("inc/navbar.php"); ?>
            <!-- app header end -->

            <!-- app content start -->
            <div class="app-content-wrapper pt-13 pb-13 px-5">

                <div class="container-fluid">

                    <div class="page-header pb-7">
                        <h2 class="fw-semibold fs-7">Ecommerce Dashboard</h2>
                    </div> <!-- breadcrumb end -->

                    <div class="page-content">
                        <div class="row gx-3 gy-6">
                            <div class="col-xl-8">
                                <div class="row row g-3 row-cols-xxl-3 row-cols-lg-3 row-cols-md-2 row-cols-1 mb-3">
                                    <div class="col">
                                        <div class="card shadow-custom rounded-custom">
                                            <div class="card-body p-6 position-relative">
                                                <div class="btn-icon bg-label-primary rounded-pill btn-lg mb-3">
                                                    <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M10.9508 10.5399C7.5008 10.5399 4.58984 11.1037 4.58984 13.2794C4.58984 15.4561 7.5196 16 10.9508 16C14.4008 16 17.3117 15.4362 17.3117 13.2605C17.3117 11.0839 14.382 10.5399 10.9508 10.5399Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.4"
                                                            d="M10.9476 8.46703C13.2837 8.46703 15.1569 6.58307 15.1569 4.23351C15.1569 1.88306 13.2837 0 10.9476 0C8.61146 0 6.73828 1.88306 6.73828 4.23351C6.73828 6.58307 8.61146 8.46703 10.9476 8.46703Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.4"
                                                            d="M20.0886 5.21926C20.693 2.84179 18.9209 0.706573 16.6645 0.706573C16.4192 0.706573 16.1846 0.73359 15.9554 0.779519C15.9249 0.786723 15.8909 0.802032 15.873 0.829049C15.8524 0.86327 15.8676 0.909199 15.89 0.938917C16.5678 1.89531 16.9573 3.05973 16.9573 4.3097C16.9573 5.50744 16.6001 6.62413 15.9733 7.5508C15.9088 7.64626 15.9661 7.77504 16.0798 7.79485C16.2374 7.82277 16.3986 7.83718 16.5634 7.84168C18.2064 7.88491 19.6811 6.82135 20.0886 5.21926Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M21.8094 10.8169C21.5086 10.1721 20.7824 9.72996 19.6783 9.51292C19.1572 9.38504 17.747 9.20493 16.4352 9.22925C16.4155 9.23195 16.4048 9.24546 16.403 9.25446C16.4003 9.26707 16.4057 9.28868 16.4316 9.30219C17.0378 9.60388 19.3811 10.916 19.0865 13.6834C19.074 13.8032 19.1698 13.9067 19.2888 13.8887C19.8655 13.8059 21.3492 13.4853 21.8094 12.4866C22.0637 11.9588 22.0637 11.3456 21.8094 10.8169Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.4"
                                                            d="M6.04508 0.779793C5.81675 0.732964 5.58126 0.706848 5.33592 0.706848C3.0795 0.706848 1.3075 2.84207 1.91279 5.21953C2.31931 6.82162 3.79403 7.88518 5.4371 7.84195C5.60185 7.83745 5.76392 7.82214 5.92062 7.79513C6.03433 7.77531 6.09164 7.64653 6.02717 7.55107C5.40039 6.6235 5.04312 5.50771 5.04312 4.30997C5.04312 3.0591 5.43352 1.89468 6.11134 0.939192C6.13283 0.909473 6.14894 0.863545 6.12745 0.829324C6.10954 0.801407 6.07641 0.786998 6.04508 0.779793Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M2.32156 9.51267C1.21752 9.7297 0.492248 10.1719 0.191392 10.8167C-0.0637974 11.3453 -0.0637974 11.9586 0.191392 12.4872C0.651629 13.485 2.13531 13.8065 2.71195 13.8885C2.83104 13.9065 2.92595 13.8038 2.91342 13.6831C2.61883 10.9166 4.9621 9.60453 5.56918 9.30284C5.59425 9.28843 5.59962 9.26772 5.59694 9.25421C5.59515 9.2452 5.5853 9.2317 5.5656 9.22989C4.25294 9.20468 2.84358 9.38479 2.32156 9.51267Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </div>
                                                <span class="fz-12px fw-medium d-block">Customer</span>
                                                <h3 class="h6 fs-9 mb-1">146k</h3>
                                                <div
                                                    class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                                                    <span
                                                        class="bg-label-success px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6 11V1" stroke="currentColor" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M1 6L6 1L11 6" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        20.8%
                                                    </span>
                                                    <span class="fz-12px fw-medium">
                                                        Previous month
                                                    </span>
                                                </div>
                                                <div class="position-absolute top-9 end-3 p-1">
                                                    <img src="assets/img/icons/dashboard/chart-up.html" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card shadow-custom rounded-custom">
                                            <div class="card-body p-6 position-relative">
                                                <div class="btn-icon bg-label-warning rounded-pill btn-lg mb-3">
                                                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M17.3721 16.7442H0.627907C0.284651 16.7442 0 16.4595 0 16.1162C0 15.773 0.284651 15.4883 0.627907 15.4883H17.3721C17.7153 15.4883 18 15.773 18 16.1162C18 16.4595 17.7153 16.7442 17.3721 16.7442Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M7.11719 1.67442V16.7442H10.8846V1.67442C10.8846 0.753488 10.5079 0 9.37765 0H8.62416C7.49393 0 7.11719 0.753488 7.11719 1.67442Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.4"
                                                            d="M1.46484 6.6977V16.7442H4.81368V6.6977C4.81368 5.77677 4.4788 5.02328 3.47415 5.02328H2.80438C1.79973 5.02328 1.46484 5.77677 1.46484 6.6977Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.4"
                                                            d="M13.1875 10.8838V16.7442H16.5363V10.8838C16.5363 9.96284 16.2015 9.20935 15.1968 9.20935H14.527C13.5224 9.20935 13.1875 9.96284 13.1875 10.8838Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </div>
                                                <span class="fz-12px fw-medium d-block">Total Sales</span>
                                                <h3 class="h6 fs-9 mb-1">562k</h3>
                                                <div
                                                    class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                                                    <span
                                                        class="bg-label-success px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6 11V1" stroke="currentColor" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M1 6L6 1L11 6" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        42.06%
                                                    </span>
                                                    <span class="fz-12px fw-medium">
                                                        This month
                                                    </span>
                                                </div>
                                                <div class="position-absolute top-9 end-3 p-1">
                                                    <img src="assets/img/icons/dashboard/chart-up.html" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card shadow-custom rounded-custom">
                                            <div class="card-body p-6 position-relative">
                                                <div class="btn-icon bg-label-success rounded-pill btn-lg mb-3">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.4"
                                                            d="M4.88256 14.9003C5.62898 14.9003 6.2405 15.5118 6.2405 16.2672C6.2405 17.0137 5.62898 17.6252 4.88256 17.6252C4.12715 17.6252 3.51562 17.0137 3.51562 16.2672C3.51562 15.5118 4.12715 14.9003 4.88256 14.9003ZM14.9997 14.9003C15.7461 14.9003 16.3576 15.5118 16.3576 16.2672C16.3576 17.0137 15.7461 17.6252 14.9997 17.6252C14.2443 17.6252 13.6327 17.0137 13.6327 16.2672C13.6327 15.5118 14.2443 14.9003 14.9997 14.9003Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M0.792051 0.0074916L2.93688 0.331239C3.24264 0.386097 3.46747 0.637001 3.49444 0.942763L3.66531 2.95719C3.69229 3.24587 3.92611 3.4617 4.21388 3.4617H16.3589C16.9075 3.4617 17.2672 3.65055 17.6269 4.06423C17.9867 4.47791 18.0496 5.07145 17.9687 5.61013L17.1143 11.5095C16.9525 12.6435 15.9812 13.479 14.8391 13.479H5.02775C3.83168 13.479 2.84245 12.5617 2.74353 11.3755L1.91617 1.57227L0.558233 1.33845C0.198513 1.2755 -0.0532905 0.924777 0.0096604 0.565057C0.0726113 0.196344 0.423338 -0.0464664 0.792051 0.0074916ZM13.4002 6.78821H10.9092C10.5315 6.78821 10.2347 7.08498 10.2347 7.46268C10.2347 7.83139 10.5315 8.13716 10.9092 8.13716H13.4002C13.7779 8.13716 14.0747 7.83139 14.0747 7.46268C14.0747 7.08498 13.7779 6.78821 13.4002 6.78821Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </div>
                                                <span class="fz-12px fw-medium d-block">Total Order</span>
                                                <h3 class="h6 fs-9 mb-1">83k</h3>
                                                <div
                                                    class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                                                    <span
                                                        class="bg-label-danger px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6 1V11" stroke="currentColor" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M1 6L6 11L11 6" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        3.5%
                                                    </span>
                                                    <span class="fz-12px fw-medium">
                                                        This month
                                                    </span>
                                                </div>
                                                <div class="position-absolute top-9 end-3 p-1">
                                                    <img src="assets/img/icons/dashboard/chart-down.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-custom rounded-custom">
                                    <div class="card-body px-6 py-9">
                                        <div class="d-flex justify-content-between flex-wrap gap-5 mb-6">
                                            <div class="">
                                                <h3 class="h6 mb-0 fs-5 fw-semibold">Sales Summary</h3>
                                            </div>
                                            <div class="">
                                                <select class="form-select form-select-sm">
                                                    <option selected>This Month</option>
                                                    <option>Last Month</option>
                                                    <option>Last 3 Months</option>
                                                    <option>Last 6 Months</option>
                                                    <option>Last Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="salesSummaryChart" style="height: 250px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card shadow-custom rounded-custom">
                                    <div class="card-body px-6 py-9">
                                        <div class="d-flex justify-content-between flex-wrap gap-5 mb-6">
                                            <div class="">
                                                <h3 class="h6 mb-0 fs-5 fw-semibold">Discounted Product Sales</h3>
                                                <p class="text-custom-paragraph">Users from all channels</p>
                                            </div>
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-icon btn-sm btn-ghost-primary dropdown-toggle text-custom-paragraph hide-arrow"
                                                    type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <svg width="16" height="4" viewBox="0 0 16 4" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M1.69545 3.89089C1.23077 3.89089 0.832025 3.72449 0.499215 3.39168C0.166405 3.05887 0 2.66013 0 2.19545C0 1.73077 0.166405 1.33203 0.499215 0.999216C0.832025 0.666406 1.23077 0.5 1.69545 0.5C2.16013 0.5 2.55887 0.666406 2.89168 0.999216C3.22449 1.33203 3.39089 1.73077 3.39089 2.19545C3.39089 2.50314 3.3124 2.78571 3.15542 3.04317C3.00471 3.30063 2.80063 3.50785 2.54317 3.66484C2.29199 3.81554 2.00942 3.89089 1.69545 3.89089Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M8 3.89089C7.53532 3.89089 7.13658 3.72449 6.80377 3.39168C6.47096 3.05887 6.30455 2.66013 6.30455 2.19545C6.30455 1.73077 6.47096 1.33203 6.80377 0.999216C7.13658 0.666406 7.53532 0.5 8 0.5C8.46468 0.5 8.86342 0.666406 9.19623 0.999216C9.52904 1.33203 9.69545 1.73077 9.69545 2.19545C9.69545 2.50314 9.61695 2.78571 9.45997 3.04317C9.30926 3.30063 9.10518 3.50785 8.84772 3.66484C8.59655 3.81554 8.31397 3.89089 8 3.89089Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M14.3046 3.89089C13.8399 3.89089 13.4411 3.72449 13.1083 3.39168C12.7755 3.05887 12.6091 2.66013 12.6091 2.19545C12.6091 1.73077 12.7755 1.33203 13.1083 0.999216C13.4411 0.666406 13.8399 0.5 14.3046 0.5C14.7692 0.5 15.168 0.666406 15.5008 0.999216C15.8336 1.33203 16 1.73077 16 2.19545C16 2.50314 15.9215 2.78571 15.7645 3.04317C15.6138 3.30063 15.4097 3.50785 15.1523 3.66484C14.9011 3.81554 14.6185 3.89089 14.3046 3.89089Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="dropdownMenuButton1">
                                                    <li><a class="dropdown-item" href="javascript:void(0);">Weekly</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);">Monthly</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);">Yearly</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mb-10">
                                            <div class="d-flex align-items-end gap-1 mb-2">
                                                <h4 class="mb-0 fs-9">3,602</h4>
                                                <span
                                                    class="bg-label-success px-px-8 py-px-1 rounded-md d-inline-flex align-items-center gap-1 fz-12px fw-medium mb-2">
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6 11V1" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M1 6L6 1L11 6" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    20.8%
                                                </span>
                                            </div>
                                            <p class="mb-0 text-custom-paragraph">Total discounted Sales this Month</p>
                                        </div>
                                        <div id="discountedProductsChart" style="height: 250px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-xl-4">
                                <div class="card shadow-custom rounded-custom h-100">
                                    <div class="card-body px-6 py-5">
                                        <div
                                            class="d-flex justify-content-between align-items-center flex-wrap gap-5 mb-10">
                                            <h3 class="h6 fs-5 fw-semibold mb-0">Top Customers</h3>
                                            <a href="#" class="btn btn-label-primary btn-xs fs-3">View All</a>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/avatar/11.html" alt="emma">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Emma Watson</h3>
                                                    <span class="fz-12px text-muted fw-medium">25 Orders</span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-custom-black fz-15px fw-semibold">$1,840</span>
                                            </div>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/avatar/12.jpg" alt="chris">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Chris Evans</h3>
                                                    <span class="fz-12px text-muted fw-medium">18 Orders</span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-custom-black fz-15px fw-semibold">$1,250</span>
                                            </div>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/avatar/13.jpg" alt="sofia">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Sofia Turner</h3>
                                                    <span class="fz-12px text-muted fw-medium">32 Orders</span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-custom-black fz-15px fw-semibold">$2,310</span>
                                            </div>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/avatar/14.jpg" alt="daniel">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Daniel Craig</h3>
                                                    <span class="fz-12px text-muted fw-medium">20 Orders</span>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-custom-black fz-15px fw-semibold">$1,670</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card shadow-custom rounded-custom h-100">
                                    <div class="card-body px-6 py-5">
                                        <div
                                            class="d-flex justify-content-between align-items-center flex-wrap gap-5 mb-10">
                                            <h3 class="h6 fs-5 fw-semibold mb-0">Top Countries</h3>
                                            <a href="#" class="btn btn-label-primary btn-xs fs-3">See All</a>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/flag/us.html" alt="united states">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-14px fw-medium">United States</h3>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-success">
                                                    <svg width="18" height="11" viewBox="0 0 18 11" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17 1L10.0909 8.125L6.45455 4.375L1 10"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12.6367 1H17.0004V5.5" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="text-custom-black fz-15px fw-semibold ">$1,840</span>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/flag/germany.png" alt="germany">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-14px fw-medium">Germany</h3>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-danger">
                                                    <svg width="18" height="11" viewBox="0 0 18 11" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17 10L10.0909 2.875L6.45455 6.625L1 1"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12.6367 10H17.0004V5.5" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="text-custom-black fz-15px fw-semibold ">$1,210</span>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/flag/netherlands.png" alt="netherlands">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-14px fw-medium">Netherlands</h3>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-success">
                                                    <svg width="18" height="11" viewBox="0 0 18 11" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17 1L10.0909 8.125L6.45455 4.375L1 10"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12.6367 1H17.0004V5.5" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="text-custom-black fz-15px fw-semibold ">$4,700</span>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-md rounded-pill flex-shrink-0">
                                                    <img src="assets/img/flag/uk.png" alt="uk">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-14px fw-medium">United Kingdom</h3>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-success">
                                                    <svg width="18" height="11" viewBox="0 0 18 11" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17 1L10.0909 8.125L6.45455 4.375L1 10"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12.6367 1H17.0004V5.5" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="text-custom-black fz-15px fw-semibold ">$10,416</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card shadow-custom rounded-custom h-100">
                                    <div class="card-body px-6 py-5">
                                        <div
                                            class="d-flex justify-content-between align-items-center flex-wrap gap-5 mb-10">
                                            <h3 class="h6 fs-5 fw-semibold mb-0">Top Store</h3>
                                            <div class="">
                                                <select class="form-select form-select-sm">
                                                    <option selected>This Month</option>
                                                    <option>Last Month</option>
                                                    <option>Last 3 Months</option>
                                                    <option>Last 6 Months</option>
                                                    <option>Last Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/store/store-1.html" alt="Online Mart">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Online Mart</h3>
                                                    <span class="fz-12px text-muted fw-medium">48 Products</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-custom-black fz-15px fw-semibold ">$126,840</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/store/store-2.html" alt="Bigbajar">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Bigbajar</h3>
                                                    <span class="fz-12px text-muted fw-medium">145 Products</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-custom-black fz-15px fw-semibold ">$420,140</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/store/store-3.png" alt="Digital House">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Digital House</h3>
                                                    <span class="fz-12px text-muted fw-medium">541 Products</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-custom-black fz-15px fw-semibold ">$20,760</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-md rounded-pill">
                                                    <img src="assets/img/store/store-4.png" alt="Jam Online">
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-0 fz-15px fw-semibold">Jam Online</h3>
                                                    <span class="fz-12px text-muted fw-medium">41 Products</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="text-custom-black fz-15px fw-semibold ">$14,230</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="pure-card rounded-custom card-bg shadow-custom">
                                    <div
                                        class="pure-card-header d-flex flex-wrap align-items-center justify-content-between gap-7">
                                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                                            <span class="text-primary d-flex align-items-center">
                                                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1.28208 13.2489L1.6281 11.2865C2.03705 8.96713 2.24153 7.80744 3.05129 7.12082C3.86105 6.4342 5.02425 6.4342 7.35064 6.4342H12.1494C14.4758 6.4342 15.6389 6.4342 16.4487 7.12082C17.2585 7.80744 17.4629 8.96713 17.8719 11.2865L18.2179 13.2489C18.7838 16.4584 19.0668 18.0632 18.1955 19.1171C17.3242 20.171 15.7146 20.171 12.4954 20.171H7.00462C3.78542 20.171 2.17582 20.171 1.30452 19.1171C0.433222 18.0632 0.716174 16.4584 1.28208 13.2489Z"
                                                        stroke="currentColor" stroke-width="1.5"></path>
                                                    <path
                                                        d="M5.48682 6.43421L5.6458 4.52638C5.82368 2.39185 7.60804 0.75 9.74997 0.75C11.8919 0.75 13.6763 2.39185 13.8541 4.52638L14.0131 6.43421"
                                                        stroke="currentColor" stroke-width="1.5"></path>
                                                    <path
                                                        d="M12.592 9.27635C12.4689 10.6151 11.2332 11.6448 9.74994 11.6448C8.26667 11.6448 7.03101 10.6151 6.90783 9.27635"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                                    </path>
                                                </svg>
                                            </span>
                                            All Orders
                                        </h3>

                                        <div class="">
                                            <div class="form-control-icon ">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.22221 13.4444C10.6586 13.4444 13.4444 10.6586 13.4444 7.22221C13.4444 3.78578 10.6586 1 7.22221 1C3.78578 1 1 3.78578 1 7.22221C1 10.6586 3.78578 13.4444 7.22221 13.4444Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M15 15L11.6167 11.6166" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                </svg>
                                                <input type="text" class="" placeholder="Enter Keywords...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pure-card-body pb-3">
                                        <div class="table-responsive table-check-parent">
                                            <table class="table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" class="fw-medium d-flex align-items-center">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input select-all"
                                                                    type="checkbox" id="inlineCheckbox1"
                                                                    value="option1">
                                                            </div>
                                                            Order ID
                                                        </th>
                                                        <th scope="col" class="fw-medium">Customer</th>
                                                        <th scope="col" class="fw-medium">Date</th>
                                                        <th scope="col" class="fw-medium">Amount</th>
                                                        <th scope="col" class="fw-medium">Payment</th>
                                                        <th scope="col" class="fw-medium">Status</th>
                                                        <th scope="col" class="fw-medium">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1001</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/01.html"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">Steven Smith
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 12, 2025</span></td>
                                                        <td><span class="text-custom-body">$120.00</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-primary">
                                                                <span class="badge badge-dot bg-primary me-1"></span>
                                                                Pending
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-warning">Delivered</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1002</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/02.jpg"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">Emily Davis
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 13, 2025</span></td>
                                                        <td><span class="text-custom-body">$250.00</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-success">
                                                                <span class="badge badge-dot bg-success me-1"></span>
                                                                Paid
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-success">Shipped</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1003</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/03.jpg"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">Michael Brown
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 14, 2025</span></td>
                                                        <td><span class="text-custom-body">$85.50</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-danger">
                                                                <span class="badge badge-dot bg-danger me-1"></span>
                                                                Failed
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-danger">Cancelled</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1004</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/04.html"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">Sarah Johnson
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 15, 2025</span></td>
                                                        <td><span class="text-custom-body">$310.00</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-warning">
                                                                <span class="badge badge-dot bg-warning me-1"></span>
                                                                Pending
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-primary">Processing</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1005</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/05.html"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">David Wilson
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 16, 2025</span></td>
                                                        <td><span class="text-custom-body">$420.00</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-success">
                                                                <span class="badge badge-dot bg-success me-1"></span>
                                                                Paid
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-success">Delivered</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1006</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/06.html"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">Chris Lee</h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 17, 2025</span></td>
                                                        <td><span class="text-custom-body">$155.75</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-primary">
                                                                <span class="badge badge-dot bg-primary me-1"></span>
                                                                Pending
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-warning">On Hold</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input row-check"
                                                                        type="checkbox" value="option1">
                                                                </div>
                                                                <span class="text-primary fw-medium">#1007</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img class="rounded-md" src="assets/img/avatar/07.jpg"
                                                                    width="40" height="40" alt="Avatar">
                                                                <div class="ms-3">
                                                                    <h3 class="h6 mb-0 text-custom-body">Linda Martinez
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span class="text-custom-body">Aug 18, 2025</span></td>
                                                        <td><span class="text-custom-body">$260.00</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center text-success">
                                                                <span class="badge badge-dot bg-success me-1"></span>
                                                                Paid
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-label-primary">Processing</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="dropdown">
                                                                    <button
                                                                        class="btn btn-sm btn-icon btn-icon-secondary dropdown-toggle hide-arrow rounded-pill"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                        <svg width="3" height="14" viewBox="0 0 3 14"
                                                                            fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M3 12.5165C3 12.9231 2.85278 13.272 2.55833 13.5632C2.26389 13.8544 1.91111 14 1.5 14C1.08889 14 0.736112 13.8544 0.441667 13.5632C0.147223 13.272 0 12.9231 0 12.5165C0 12.1099 0.147223 11.761 0.441667 11.4698C0.736112 11.1786 1.08889 11.033 1.5 11.033C1.77222 11.033 2.02222 11.1016 2.25 11.239C2.47778 11.3709 2.66111 11.5495 2.8 11.7747C2.93333 11.9945 3 12.2418 3 12.5165Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 7C3 7.40659 2.85278 7.75549 2.55833 8.0467C2.26389 8.33791 1.91111 8.48352 1.5 8.48352C1.08889 8.48352 0.736112 8.33791 0.441667 8.0467C0.147223 7.75549 0 7.40659 0 7C0 6.59341 0.147223 6.24451 0.441667 5.9533C0.736112 5.66209 1.08889 5.51648 1.5 5.51648C1.77222 5.51648 2.02222 5.58517 2.25 5.72253C2.47778 5.8544 2.66111 6.03297 2.8 6.25824C2.93333 6.47802 3 6.72527 3 7Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3 1.48351C3 1.89011 2.85278 2.23901 2.55833 2.53022C2.26389 2.82143 1.91111 2.96703 1.5 2.96703C1.08889 2.96703 0.736112 2.82143 0.441667 2.53022C0.147223 2.23901 0 1.89011 0 1.48351C0 1.07692 0.147223 0.728022 0.441667 0.436812C0.736112 0.145604 1.08889 0 1.5 0C1.77222 0 2.02222 0.0686817 2.25 0.206044C2.47778 0.337913 2.66111 0.516483 2.8 0.741757C2.93333 0.961538 3 1.20879 3 1.48351Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#">Edit</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">View</a>
                                                                        </li>
                                                                        <li><a class="dropdown-item" href="#">Delete</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination">
                                                    <li class="page-item"><a class="page-link" href="#">Previous</a>
                                                    </li>
                                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- page content end -->

                </div>
            </div><!-- app content end -->
            

            <div class="app-backdrop"></div>
            <!-- app content end -->
        </div>
        <!-- app wrapper end -->
    </div>

    <?php include("inc/scripts_url.php"); ?>
</body>



</html>