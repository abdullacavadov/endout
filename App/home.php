<?php
require_once __DIR__ . "/inc/config.php";
?>


<!DOCTYPE html>
<html lang="az">

<head>
    <?php require_once __DIR__ . '/inc/head.php'; ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">

    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

    <style>
        .listing-slider img {
            height: 170px;
            object-fit: cover;
            width: 100%;
            border-radius: 8px;
        }

        .swiper-pagination {
            position: unset !important;
        }

        .swiper-pagination-bullet {
            background-color: #c10037;
            filter: drop-shadow(1px 1px 3px #fff);
            height: var(--swiper-pagination-bullet-height, var(--swiper-pagination-bullet-size, 4px)) !important;
        }
    </style>
</head>

<body style="background-color: black;">

    <div class="main-wrapper home-nine">
        <!-- Header -->
        <?php require_once './inc/header.php'; ?>
        <!-- /Header -->


        <!-- Banner -->
        <section class="banner-section banner-nine" style="background-image: url(./assets/img/bg/banner.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="banner-contents" >
                            <h1 style="text-shadow: 0 0 3px black;" class="aos" data-aos="fade-up" data-aos-anchor-placement="top-bottom">Lorem
                                ipsum dolor sit.</h1>
                            <h6 style="text-shadow: 0 0 3px black;" class="mb-0 aos" data-aos="fade-up" data-aos-anchor-placement="top-bottom">Lorem
                                ipsum, dolor sit amet consectetur adipisicing elit. Officia!</h6>
                            <p style="text-shadow: 0 0 3px black;" class="aos" data-aos="fade-up" data-aos-delay="200">Lorem Ipsum is simply dummy
                                text of
                                the printing and typesetting industry. Lorem Ipsum has been the industry's
                                standard
                                dummy text ever since the 1500s,</p>
                            <div class="banner-nine-btn-group">
                                <a href="categories" class="aos" data-aos="fade-up" data-aos-delay="300">
                                    <span>Reklam butonu</span>
                                </a>
                                <a href="categories" class="aos" data-aos="fade-up" data-aos-delay="300">
                                    <span>Reklam butonu</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Banner -->

        <!-- Search Filter Section -->
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                font-size: 13px !important;
            }

            .select2-container--default .select2-results__option--highlighted[aria-selected],
            .select2-results__option[aria-selected],
            .select2-container--default .select2-results__option[aria-disabled=true],
            .car-search-filter .car-filter-section .form-control {
                font-size: 12px !important;
            }

            .select2-container .select2-selection--single {
                height: 41px !important;
            }

            .select2-container {
                width: 100% !important;
            }
        </style>
        <section class="car-search-filter realestate-search-filter  aos" data-aos="fade-up" data-aos-delay="400">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="car-filter-section">


                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-discount-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-discount" type="button" role="tab"
                                        aria-controls="pills-discount" aria-selected="true">Endirim</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-outlet-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-outlet" type="button" role="tab"
                                        aria-controls="pills-outlet" aria-selected="false">Outlet</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-sale-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-sale" type="button" role="tab" aria-controls="pills-sale"
                                        aria-selected="false">Satış</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-discount" data-value="1"
                                    role="tabpanel" aria-labelledby="pills-discount-tab" tabindex="0">
                                    <div class="search-tab-col">
                                        <form action="./listings" class="clean-form">

                                            <input type="hidden" name="lt" class="mainCategory">

                                            <div class="row align-items-center search-form">
                                                <div class="col-12 col-lg-10 datepicker-col search-group">
                                                    <div class="row">
                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-hash"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <input type="text" name="q"
                                                                        class="border-0 text-truncate px-0 form-control"
                                                                        placeholder="Açar söz...">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search real-select">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-layers"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <select name="sm"
                                                                        style="font-size: 12px !important; height: 25px;"
                                                                        class="form-control select">

                                                                        <option value="" selected disabled>-- Satış növü
                                                                            --</option>
                                                                        <?php
                                                                        $params = [];
                                                                        $st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM sale_modes
                                                                                    ORDER BY name ASC");

                                                                        $st->execute($params);



                                                                        while ($sm = $st->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>

                                                                            <option value="<?= $sm['id']; ?>">
                                                                                <?= $sm['name']; ?>
                                                                            </option>
                                                                        <?php } ?>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search real-select">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-globe"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <select class="form-control select" name="country">
                                                                        <option selected disabled>-- Ölkə --
                                                                        </option>
                                                                        <?php
                                                                        $params = [];
                                                                        $st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM countries
                                                                                    ORDER BY name ASC");

                                                                        $st->execute($params);



                                                                        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>

                                                                            <option value="<?= $row['id']; ?>">
                                                                                <?= $row['name']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mt-1">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="d-flex real-estate-search">
                                                                        <div
                                                                            class="flex-shrink-0 d-flex align-items-center">
                                                                            <div class="icon-blk rounded-circle">
                                                                                <span><i
                                                                                        class="fa-solid fa-arrow-down-wide-short"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 estate-input">
                                                                            <input type="text" name="min_price"
                                                                                class="border-0 text-truncate px-0 form-control"
                                                                                placeholder="Min. qiymət...">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div class="d-flex real-estate-search">
                                                                        <div
                                                                            class="flex-shrink-0 d-flex align-items-center">
                                                                            <div class="icon-blk rounded-circle">
                                                                                <span><i
                                                                                        class="fa-solid fa-arrow-up-wide-short"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 estate-input">
                                                                            <input type="text" name="max_price"
                                                                                class="border-0 text-truncate px-0 form-control"
                                                                                placeholder="Max. qiymət...">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-2 real-search-bar">
                                                    <button class="btn car-search-icon" type="submit">Tətbiq
                                                        et</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="pills-outlet" data-value="2" role="tabpanel"
                                    aria-labelledby="pills-discount-tab" tabindex="0">
                                    <div class="search-tab-col">
                                        <form action="./listings" class="clean-form">

                                            <input type="hidden" name="lt" class="mainCategory">

                                            <div class="row align-items-center search-form">
                                                <div class="col-12 col-lg-10 datepicker-col search-group">
                                                    <div class="row">
                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-hash"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <input type="text" name="q"
                                                                        class="border-0 text-truncate px-0 form-control"
                                                                        placeholder="Açar söz...">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search real-select">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-layers"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <select name="sm"
                                                                        style="font-size: 12px !important; height: 25px;"
                                                                        class="form-control select">
                                                                        <option value="" selected disabled>-- Satış növü
                                                                            --</option>
                                                                        <?php
                                                                        $params = [];
                                                                        $st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM sale_modes
                                                                                    WHERE id != 1
                                                                                    ORDER BY name ASC");

                                                                        $st->execute($params);



                                                                        while ($sm = $st->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>

                                                                            <option value="<?= $sm['id']; ?>">
                                                                                <?= $sm['name']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search real-select">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-globe"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <select class="form-control select" name="country">
                                                                        <option selected disabled>-- Ölkə seç --
                                                                        </option>
                                                                        <?php
                                                                        $params = [];
                                                                        $st = $pdo->prepare("
                                                                                    SELECT id, name

                                                                                    FROM countries
                                                                                    ORDER BY name ASC");

                                                                        $st->execute($params);



                                                                        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>

                                                                            <option value="<?= $row['id']; ?>">
                                                                                <?= $row['name']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mt-1">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="d-flex real-estate-search">
                                                                        <div
                                                                            class="flex-shrink-0 d-flex align-items-center">
                                                                            <div class="icon-blk rounded-circle">
                                                                                <span><i
                                                                                        class="fa-solid fa-arrow-down-wide-short"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 estate-input">
                                                                            <input type="text" name="min_price"
                                                                                class="border-0 text-truncate px-0 form-control"
                                                                                placeholder="Min. qiymət...">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div class="d-flex real-estate-search">
                                                                        <div
                                                                            class="flex-shrink-0 d-flex align-items-center">
                                                                            <div class="icon-blk rounded-circle">
                                                                                <span><i
                                                                                        class="fa-solid fa-arrow-up-wide-short"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 estate-input">
                                                                            <input type="text" name="max_price"
                                                                                class="border-0 text-truncate px-0 form-control"
                                                                                placeholder="Max. qiymət...">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-2 real-search-bar">
                                                    <button class="btn car-search-icon" type="submit">Tətbiq
                                                        et</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="pills-sale" data-value="3" role="tabpanel"
                                    aria-labelledby="pills-sale-tab" tabindex="0">
                                    <div class="search-tab-col">
                                        <form action="./listings" class="clean-form">

                                            <input type="hidden" name="lt" class="mainCategory">

                                            <div class="row align-items-center search-form">
                                                <div class="col-12 col-lg-10 datepicker-col search-group">
                                                    <div class="row">
                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-hash"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <input type="text" name="q"
                                                                        class="border-0 text-truncate px-0 form-control"
                                                                        placeholder="Açar söz...">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search real-select">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-layers"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <select name="sm"
                                                                        style="font-size: 12px !important; height: 25px;"
                                                                        class="form-control select">
                                                                        <option value="" selected disabled>-- Satış növü
                                                                            --</option>
                                                                        <?php
                                                                        $params = [];
                                                                        $st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM sale_modes
                                                                                    ORDER BY name ASC");

                                                                        $st->execute($params);



                                                                        while ($sm = $st->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>

                                                                            <option value="<?= $sm['id']; ?>">
                                                                                <?= $sm['name']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mt-1">
                                                            <div class="d-flex real-estate-search real-select">
                                                                <div class="flex-shrink-0 d-flex align-items-center">
                                                                    <div class="icon-blk rounded-circle">
                                                                        <span><i class="feather-globe"></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 estate-input">
                                                                    <select class="form-control select" name="country">
                                                                        <option selected disabled>-- Ölkə seç --
                                                                        </option>
                                                                        <?php
                                                                        $params = [];
                                                                        $st = $pdo->prepare("
                                                                                    SELECT id, name

                                                                                    FROM countries
                                                                                    ORDER BY name ASC");

                                                                        $st->execute($params);



                                                                        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                                                                            ?>

                                                                            <option value="<?= $row['id']; ?>">
                                                                                <?= $row['name']; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mt-1">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="d-flex real-estate-search">
                                                                        <div
                                                                            class="flex-shrink-0 d-flex align-items-center">
                                                                            <div class="icon-blk rounded-circle">
                                                                                <span><i
                                                                                        class="fa-solid fa-arrow-down-wide-short"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 estate-input">
                                                                            <input type="text" name="min_price"
                                                                                class="border-0 text-truncate px-0 form-control"
                                                                                placeholder="Min. qiymət...">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div class="d-flex real-estate-search">
                                                                        <div
                                                                            class="flex-shrink-0 d-flex align-items-center">
                                                                            <div class="icon-blk rounded-circle">
                                                                                <span><i
                                                                                        class="fa-solid fa-arrow-up-wide-short"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1 estate-input">
                                                                            <input type="text" name="max_price"
                                                                                class="border-0 text-truncate px-0 form-control"
                                                                                placeholder="Max. qiymət...">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-2 real-search-bar">
                                                    <button class="btn car-search-icon" type="submit">Tətbiq
                                                        et</button>
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
        </section>
        <!-- /Search Filter Section -->



        <!-- Elanlar -->

        <section class="featured-listing-section mt-3">
            <div class="footer-six-bg footer-nine-bg">
                <img src="assets/img/bg/feature-bg.png" alt="">
            </div>
            <div class="container">

                <style>
                    .add-fav:hover {
                        font-weight: 900;
                        cursor: pointer;
                        transform: scale(1.2);
                        /* solid üçün */
                    }
                </style>

                <div class="row aos g-3" id="listingContainer" data-aos="fade-up" data-aos-delay="200">

                    <div class="col-12">
                        <h3 class="text-light">Premium elanlar</h3>
                    </div>

                    <?php
                    $stmt = $pdo->prepare("SELECT currency_code FROM countries WHERE iso2 = ?");
                    $stmt->execute([$user_country_code]);
                    $userCurrency = $stmt->fetchColumn();

                    $limit = 8;
                    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    if ($page < 1)
                        $page = 1;

                    $offset = ($page - 1) * $limit;



                    $where = "WHERE l.status = 'active' AND m.is_active = 1 AND plc.listing_id IS NOT NULL";
                    $params = [];

                    $stCountry = $pdo->prepare("SELECT id FROM countries WHERE iso2 = ?");
                    $stCountry->execute([$user_country_code]);
                    $user_country_id = $stCountry->fetchColumn();

                    // ölkə filteri (VACİB)
                    $where .= " AND lc.country_id = ?";
                    $params[] = $user_country_id;


                    $st = $pdo->prepare("
                                SELECT 
                                    l.id,
                                    l.slug,
                                    l.title,
                                    l.price,
                                    l.old_price,
                                    l.currency,
                                    l.created_at,

                                    c.slug_path,

                                    lt.name AS listing_type_name,
                                    lt.slug AS listing_type_slug,

                                    m.name AS market_name,
                                    ctry.name AS country_name,
                                    ctry.iso2 AS country_code,
                                    city.name AS city_name,


                                    GROUP_CONCAT(DISTINCT li.image_path ORDER BY li.sort_order) AS images,

                                    ROUND(AVG(lr.rating),1) AS rating_avg,
                                    COUNT(lr.id) AS rating_count

                                

                                FROM listings l

                                

                                INNER JOIN premium_listings plc 
                                    ON plc.listing_id = l.id 
                                    AND plc.expires_at > NOW()

                                INNER JOIN listing_countries lc 
                                    ON lc.listing_id = l.id

                                LEFT JOIN markets m 
                                    ON m.id = l.market_id

                                LEFT JOIN countries ctry 
                                    ON ctry.id = m.country_id

                                LEFT JOIN cities city 
                                    ON city.id = m.city_id

                                LEFT JOIN listing_types lt 
                                    ON lt.id = l.type_id

                                LEFT JOIN categories c 
                                    ON c.id = l.category_id

                                LEFT JOIN listing_images li 
                                    ON li.listing_id = l.id

                                LEFT JOIN listings_reviews lr 
                                    ON lr.listing_id = l.id

                                $where

                                GROUP BY l.id

                                ORDER BY l.id DESC

                                LIMIT $limit OFFSET $offset
                            ");

                    $st->execute($params);
                    $listings = $st->fetchAll(PDO::FETCH_ASSOC);

                    $catMap = [];

                    foreach ($cats as $c) {
                        $catMap[$c['slug']] = $c['name'];
                    }


                    function getRates()
                    {
                        $xml = simplexml_load_file("https://www.cbar.az/currencies/" . date('d.m.Y') . ".xml");

                        $rates = [
                            'AZN' => 1 // baza
                        ];

                        foreach ($xml->ValType as $type) {
                            foreach ($type->Valute as $valute) {
                                $code = (string) $valute['Code'];
                                $value = (float) $valute->Value;

                                $rates[$code] = $value;
                            }
                        }

                        return $rates;
                    }

                    function convertCurrency($amount, $from, $to, $rates)
                    {
                        if ($from === $to)
                            return $amount;

                        if (!isset($rates[$from]) || !isset($rates[$to])) {
                            return null; // unsupported currency
                        }

                        // from → AZN
                        $azn = $amount * $rates[$from];

                        // AZN → to
                        return $azn / $rates[$to];
                    }

                    $rates = getRates();


                    ?>

                    <?php foreach ($listings as $row): ?>
                        <?php $images = !empty($row['images']) ? explode(",", $row['images']) : []; ?>

                        <?php
                        $slugs = explode('/', $row['slug_path']);
                        $paths = [];
                        $current = '';

                        foreach ($slugs as $slug) {
                            $current .= ($current ? '/' : '') . $slug;
                            $paths[] = $current;
                        }

                        $names = [];

                        foreach ($slugs as $slug) {
                            $names[] = $catMap[$slug] ?? $slug;
                        }

                        $converted = convertCurrency(
                            $row['price'],
                            $row['currency'],   // məsələn USD
                            $userCurrency,       // məsələn EUR
                            $rates
                        );

                        if (!$converted) {
                            $converted = $row['price'];
                            $userCurrency = 'USD';
                        }
                        ?>

                        <div class="col-md-3 col-sm-6">

                            <div class="feature-rent">
                                <div>

                                    <a
                                        href="<?= $base_url ?>/listing/<?= urlencode($row['slug']) ?>/<?= (int) $row['id'] ?>">

                                        <div class="swiper listing-slider" style="min-height: 205px;">

                                            <div class="swiper-wrapper">

                                                <?php foreach ($images as $img): ?>

                                                    <div class="swiper-slide">
                                                        <img class="img-fluid avatar-img" style="border: 1px solid gray"
                                                            src="assets/img/uploads/listings/<?= $img; ?>" alt="">
                                                    </div>

                                                <?php endforeach; ?>

                                            </div>

                                            <div class="swiper-pagination"></div>

                                        </div>
                                    </a>
                                </div>

                                <div class="home-img-text">

                                    <div class="d-flex justify-content-between">
                                        <h6>
                                            <?= number_format($converted, 2) . ' ' . $userCurrency ?>

                                            <?php
                                            if ($row['old_price'] != 0) {
                                                $convertedOld = convertCurrency(
                                                    $row['old_price'],
                                                    $row['currency'],
                                                    $userCurrency,
                                                    $rates
                                                );

                                                if (!$convertedOld) {
                                                    $convertedOld = $row['old_price'];
                                                }


                                                echo '<small class="ms-1 text-muted"><del>' . number_format($convertedOld, 2) . ' ' . $userCurrency . '</del></small>';
                                            }
                                            ?>
                                        </h6>

                                        <i class="fas fa-gem" style="color: orangered"></i>
                                    </div>



                                    <h3 style="height: 60px;">
                                        <a
                                            href="<?= $base_url ?>/listing/<?= urlencode($row['slug']) ?>/<?= (int) $row['id'] ?>">
                                            <?= htmlspecialchars(mb_strimwidth($row['title'], 0, 110, '...')) ?>
                                        </a>
                                    </h3>

                                    <div class="top-room-details">
                                        <span style="font-size: 10px !important;">

                                            <?php foreach ($names as $i => $name): ?>

                                                <a href="<?= $base_url ?>/listings/<?= $paths[$i] ?>">
                                                    <?= htmlspecialchars($name) ?>
                                                </a>

                                                <?php if ($i < count($names) - 1): ?>
                                                    /
                                                <?php endif; ?>

                                            <?php endforeach; ?>

                                        </span>

                                        <span>
                                            <a href="<?= $base_url ?>/listings?lts=<?= $row['listing_type_slug'] ?>"
                                                style="color: #2563EB">
                                                <i class="fa-solid fa-star"></i>
                                                <?= htmlspecialchars($row['listing_type_name']) ?>
                                            </a>
                                        </span>
                                    </div>

                                    <div class="detail-btm-blk"
                                        style="display:flex; gap:7px; align-items: center; margin-top: 15px; font-size: 12px;">

                                        <div class="rating-badge" style="display: flex;
                                                                        gap: 6px;
                                                                        justify-content: center;
                                                                        align-items: center;
                                                                        border-radius: 20px;
                                                                        background:red;
                                                                        color:#fff;
                                                                        width:50px;
                                                                        height: 22px;">
                                            <i class="fa-solid fa-star"></i>
                                            <?= $row['rating_avg'] ?? '0.0' ?>
                                        </div>

                                        <small>(
                                            <?= $row['rating_count'] ?> rəy)
                                        </small>

                                    </div>

                                    <div class="house-type" style="padding: 0;">
                                        <span class="room-type">
                                            <?= $row['country_code'] ?>,
                                            <?= $row['city_name'] ?>,
                                            <?= date("d.m.y / H:i", strtotime($row['created_at'])) ?>
                                        </span>
                                    </div>

                                </div>
                            </div>



                        </div>

                    <?php endforeach; ?>

                    <button class="col-12 btn btn-primary" style="margin: 100px 0; " id="loadMoreBtn">Daha
                        çox göstər</button>

                </div>



            </div>
        </section>

        <!-- /Elanlar -->


        <?php include('./inc/alert_modal.php'); ?>


        <!-- Footer -->
        <section class="footer-six footer-nine">
            <div class="footer-six-bg footer-nine-bg">
                <img src="assets/img/bg/footer-bg.png" alt="">
            </div>
            <div class="container">

                <div class=" row position-relative">
                    <div class="col-md-6 col-sm-12">
                        <div class="foot-nine-logo">
                            <a href="home"><img src="assets/img/logo.png" height="50" alt=""></a>
                            <p>
                                EndOut ilə ən yaxşı təklifləri kəşf et, fürsətləri dəyərləndir və həyatını daha
                                sərfəli et.
                                <hr>
                                <strong class="text-light">Mükəmməl həyat fürsətlərlə başlayır.</strong>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <div class="footer-six-right">
                            <div class="footer-send-mail">
                                <input type="text" class="form-control"
                                    placeholder="Yeniliklər üçün e-poçt ünvanınızı daxil edin">
                                <a href="javascript:void(0);">Abunə ol</a>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="footer-six-top footer-nine-top">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-12">
                            <div class="social-icon-six social-icon-nine">
                                <h3>EndOut sosial mediada</h3>
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" target="_blank"><i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" target="_blank"><i class="fab fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" target="_blank"><i
                                                class="fab fa-instagram"></i></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" target="_blank"><i
                                                class="fab fa-linkedin-in"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="footer-six-center foot-nine-list">
                                <div class="footer-six-center-content">
                                    <h6>Ümumi ziyarətçi</h6>
                                    <a href="javascript:void(0);">4.565.697</a>
                                </div>
                                <div class="footer-six-center-content">
                                    <h6>Ümumi elan sayı</h6>
                                    <a href="javascript:void(0);">5.156.597</a>
                                </div>
                                <div class="footer-six-center-content">
                                    <h6>Toll Free Customer Care</h6>
                                    <a href="javascript:void(0);">+91 26447 99875</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="custom-line foot-nine-line">
                <div class="footer-six-bottom pt-0">
                    <p class="mb-0">© 2026 EndOut. Bütün hüquqlar qorunur.</p>
                    <div class="footer-six-center-list">
                        <ul>
                            <li><a href="home"> Ana səhifə</a></li>
                            <li><a href="javascript:void(0);"> Sayt xəritəsi</a></li>
                            <li><a href="privacy-policy"> İstifadə şərtləri</a></li>
                            <li><a href="privacy-policy"> Məxfilik siyasəti</a></li>
                            <li><a href="privacy-policy"> Cookie siyasəti</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Footer -->

    </div>




    </div>

    <!-- scrollToTop start -->
    <div class="home-nine progress-wrap active-progress">
        <svg class="progress-circle svg-content" width="99px" height="99px" viewBox="0 0 200 200">
            <path d="  M 0, 35.5 C 0, 0 0, 0 35.5, 0 S 71, 0 71, 35.5 71, 71 35.5, 71 0, 71 0, 35.5"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;">
            </path>
        </svg>
    </div>
    <!-- scrollToTop end -->


    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Datetimepicker JS -->
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <!-- Aos -->
    <script src="assets/plugins/aos/aos.js"></script>

    <!-- Top JS -->
    <script src="assets/js/backToTop.js"></script>

    <!-- counterup JS -->
    <script src="assets/js/jquery.waypoints.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>

    <!-- Fearther JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Owl Carousel JS -->
    <script src="assets/js/owl.carousel.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>


    <script src="js/alert-modal.js"></script>


    <script src="assets/plugins/rocket-loader.min.js" data-cf-settings="fca7c691a9a41484a930bc86-|49" defer></script>

    <script>
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el)
        })

        function initSliders(scope = document) {
            scope.querySelectorAll(".listing-slider").forEach(el => {
                // əgər artıq init olunubsa skip et
                if (el.classList.contains("swiper-initialized")) return;

                new Swiper(el, {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: true,
                    pagination: {
                        el: el.querySelector(".swiper-pagination"),
                        clickable: true
                    }
                });
            });
        }

        initSliders();
    </script>

    <script>
        function setActiveTabValue() {
            const activeTab = document.querySelector(".tab-pane.show.active");
            if (!activeTab) return;

            const main = activeTab.querySelector(".mainCategory");
            if (!main) return;

            main.value = activeTab.dataset.value;
        }

        document.addEventListener("DOMContentLoaded", () => {
            setActiveTabValue();
            loadMidCategories();
        });

        document.querySelectorAll('[data-bs-toggle="pill"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', function (e) {

                const target = document.querySelector(e.target.dataset.bsTarget);
                if (!target) return;

                const main = target.querySelector(".mainCategory");
                if (!main) return;

                main.value = target.dataset.value;

                loadMidCategories();
            });
        });

        
    </script>

    <script>
        let page = 1;
        const btn = document.getElementById("loadMoreBtn");
        const container = document.getElementById("listingContainer");

        btn.addEventListener("click", () => {
            page++;

            fetch(`?page=${page}`)
                .then(res => res.text())
                .then(html => {

                    let doc = new DOMParser().parseFromString(html, "text/html");
                    let items = doc.querySelectorAll(".feature-rent");

                    if (items.length === 0) {
                        btn.disabled = true;
                        btn.innerText = "Hamısı göstərildi";
                        return;
                    }

                    items.forEach(el => {
                        container.appendChild(el);
                        initSliders();
                    });

                });
        });
    </script>

    <script>
        document.addEventListener("submit", function (e) {
            const form = e.target;

            if (!form.classList.contains("clean-form")) return;

            e.preventDefault();

            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                // boş, null, whitespace → skip
                if (value && value.toString().trim() !== "") {
                    params.append(key, value);
                }
            }

            let action = form.getAttribute("action") || window.location.pathname;

            // əgər artıq ? varsa düzgün concat et
            const url = params.toString()
                ? `${action}?${params.toString()}`
                : action;

            window.location.href = url;
        });
    </script>
</body>

</html>