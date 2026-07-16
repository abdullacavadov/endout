<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered,
    .car-search-filter .car-filter-section .form-control {
        font-size: 13px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected],
    .select2-results__option[aria-selected],
    .select2-container--default .select2-results__option[aria-disabled=true]
     {
        font-size: 12px !important;
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
                                data-bs-target="#pills-discount" type="button" role="tab" aria-controls="pills-discount"
                                aria-selected="true">Endirim</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-outlet-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-outlet" type="button" role="tab" aria-controls="pills-outlet"
                                aria-selected="false">Outlet</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-discount-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-discount" type="button" role="tab" aria-controls="pills-discount"
                                aria-selected="false">Satış</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-discount" role="tabpanel"
                            aria-labelledby="pills-discount-tab" tabindex="0">
                            <div class="search-tab-col">
                                <form action="https://listee.dreamstechnologies.com/html/listing-grid-sidebar.html">
                                    <div class="row align-items-center search-form">
                                        <div class="col-12 col-lg-10 datepicker-col search-group">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-map-pin"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="name"
                                                                class="border-0 text-truncate px-0 form-control"
                                                                placeholder="Açar söz...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="d-flex real-estate-search real-select">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-users"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <select class="form-control select category-select">
                                                                <option value="">Kateqoriya seç</option>
                                                                <option>Topdan</option>
                                                                <option>Pərakəndə</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <div class="d-flex real-estate-search real-select">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-users"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <select class="form-control select category-select">
                                                                <option>Ölkə seç</option>
                                                                <option>Azərbaycan</option>
                                                            </select>
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
                        <div class="tab-pane fade show" id="pills-outlet" role="tabpanel"
                            aria-labelledby="pills-outlet-tab" tabindex="0">
                            <div class="search-tab-col">
                                <form action="https://listee.dreamstechnologies.com/html/listing-grid-sidebar.html">
                                    <div class="row align-items-center search-form">
                                        <div class="col-12 col-lg-10 datepicker-col search-group">
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-3">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-map-pin"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="name"
                                                                class="border-0 text-truncate px-0 form-control"
                                                                placeholder="Search location of Property">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-3">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="checkin"
                                                                class="border-0 text-truncate px-0 form-control datetimepicker"
                                                                placeholder="Check-in">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-3">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="checkout"
                                                                class="border-0 text-truncate px-0 form-control datetimepicker"
                                                                placeholder="Check- out">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-3">
                                                    <div class="d-flex real-estate-search real-select">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <img src="assets/img/icons/users.svg" alt="">
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <select class="form-control select category-select">
                                                                <option>Guest</option>
                                                                <option>User</option>
                                                                <option>Admin</option>
                                                                <option>Seller</option>
                                                                <option>Buyer</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-2 real-search-bar">
                                            <button class="btn car-search-icon" type="submit">Search
                                                Properties</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="pills-discount" role="tabpanel"
                            aria-labelledby="pills-discount-tab" tabindex="0">
                            <div class="search-tab-col">
                                <form action="https://listee.dreamstechnologies.com/html/listing-grid-sidebar.html">
                                    <div class="row align-items-center search-form">
                                        <div class="col-12 col-lg-10 datepicker-col search-group">
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-3">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-map-pin"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="name"
                                                                class="border-0 text-truncate px-0 form-control"
                                                                placeholder="Search location of Property">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-3">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="checkin"
                                                                class="border-0 text-truncate px-0 form-control datetimepicker"
                                                                placeholder="Check-in">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-3">
                                                    <div class="d-flex real-estate-search">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <span><i class="feather-calendar"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <input type="text" name="checkout"
                                                                class="border-0 text-truncate px-0 form-control datetimepicker"
                                                                placeholder="Check- out">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-3">
                                                    <div class="d-flex real-estate-search real-select">
                                                        <div class="flex-shrink-0 d-flex align-items-center">
                                                            <div class="icon-blk rounded-circle">
                                                                <img src="assets/img/icons/users.svg" alt="">
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 estate-input">
                                                            <select class="form-control select category-select">
                                                                <option>Guest</option>
                                                                <option>User</option>
                                                                <option>Admin</option>
                                                                <option>Seller</option>
                                                                <option>Buyer</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-2 real-search-bar">
                                            <button class="btn car-search-icon" type="submit">Search
                                                Properties</button>
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