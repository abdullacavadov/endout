<?php include("inc/config.php"); ?>

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
            <div class="app-content-wrapper py-20 pb-13">
                <div class="container ">

                    <form id="country_add" method="post">

                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Ölkə əlavə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="countries.php">Ölkələr</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Ölkə əlavə et</li>
                                    </ol>


                                </nav>
                            </div>


                        </div> <!-- breadcrumb end -->

                        <div class="page-content">
                            <div class="row gy-5 mb-5">
                                <div class="col-md-12">
                                    <div class="pure-card rounded-custom card-bg shadow-custom">

                                        <div class="pure-card-body mt-5">
                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-9">
                                                    <label for="name" class="form-label">Ölkə adı (İngiliscə)</label>
                                                    <input class="form-control" id="name" name="name"
                                                        placeholder="Məs: Azerbaijan">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="iso2" class="form-label">ISO 2</label>
                                                    <input class="form-control" id="iso2" name="iso2"
                                                        placeholder="Məs: Azerbaijan">
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-4">
                                                    <label for="area" class="form-label">Region</label>
                                                    <select class="form-control" id="area" name="area">
                                                        <option value="">-- Region seç --</option>

                                                        <option value="Europe">
                                                            Avropa
                                                        </option>

                                                        <option value="Asia">
                                                            Asiya
                                                        </option>

                                                        <option value="Africa">
                                                            Afrika
                                                        </option>

                                                        <option value="North America">
                                                            Şimali Amerika
                                                        </option>

                                                        <option value="South America">
                                                            Cənubi Amerika
                                                        </option>

                                                        <option value="Oceania">
                                                            Okeaniya
                                                        </option>

                                                        <option value="Antarctica">
                                                            Antarktida
                                                        </option>
                                                    </select>
                                                </div>


                                                <div class="col-md-4">
                                                    <label for="country_audience" class="form-label">Əhali sayı</label>
                                                    <input class="form-control" id="country_audience" name="country_audience"
                                                        placeholder="Məs: 15000000">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="coefficient" class="form-label">Əmsal (Defolt x1.00)</label>
                                                    <input class="form-control" id="coefficient" name="coefficient"
                                                        placeholder="Məs: 0.80">
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-3">
                                                    <label for="dial_code" class="form-label">Ölkə kodu</label>
                                                    <input class="form-control" id="dial_code" name="dial_code"
                                                        placeholder="Məs: +994">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="currency_name" class="form-label">Valyuta (İngiliscə)</label>
                                                    <input class="form-control" id="currency_name" name="currency_name"
                                                        placeholder="Məs: Azerbaijani Manat">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="currency_code" class="form-label">Valyuta kodu</label>
                                                    <input class="form-control" id="currency_code" name="currency_code"
                                                        placeholder="Məs: AZN">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="currency_symbol" class="form-label">Valyuta simvolu</label>
                                                    <input class="form-control" id="currency_symbol" name="currency_symbol"
                                                        placeholder="Məs: ₼">
                                                </div>

                                                
                                            </div>

                                         
                                            <div class="d-flex align-items-center justify-content-end gap-2 mt-4">
                                                <button type="submit"
                                                    class="btn btn-primary d-flex align-items-center gap-2">
                                                    Yadda saxla
                                                </button>
                                            </div>

                                        </div>


                                    </div>


                                </div>
                            </div>

                        </div>
                </div><!-- page content end -->

                </form>


            </div>
        </div><!-- app content end -->
        <?php include("inc/footer.php"); ?>

        <div class="app-backdrop"></div>
        <!-- app content end -->
    </div>
    <!-- app wrapper end -->
    </div>

    <?php include("inc/scripts_url.php"); ?>


    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const form = document.getElementById("country_add");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/create/country.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Ölkə uğurla əlavə edildi.",
                                icon: "success",
                                draggable: true
                            });
                            form.reset();
                        } else {

                            Swal.fire({
                                title: "Xəta baş verdi",
                                text: response,
                                icon: "error",
                                draggable: true
                            });

                        }

                    })

                    .catch(error => {

                        Swal.fire({
                            title: "Server xətası baş verdi.",
                            text: "Zəhmət olmasa, yenidən cəhd edin.",
                            icon: "error",
                            draggable: true
                        });

                    })

                    .finally(() => {
                        submitBtn.disabled = false;
                    });

            });

        });
    </script>

</body>

</html>