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

                    <form id="package_create" method="post">


                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Paket əlavə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="packages.php">Paketlər</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Paket əlavə et</li>
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
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Paket adı (İngiliscə)</label>
                                                    <input class="form-control" id="name" name="name"
                                                        placeholder="Məs: Premium">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="duration_days" class="form-label">Müddət (gün)</label>
                                                    <input class="form-control" id="duration_days" name="duration_days"
                                                        placeholder="Məs: 30" type="number" min="1" step="1">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="base_price" class="form-label">Baza qiyməti
                                                        (USD)</label>
                                                    <input class="form-control" id="base_price" name="base_price"
                                                        placeholder="Məs: 100" type="number" min="0" step="0.01">
                                                </div>
                                            </div>


                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="is_active" class="form-label">Status</label>
                                                    <select class="form-control" id="is_active" name="is_active"
                                                        required>
                                                        <option value="1">Aktiv</option>
                                                        <option value="0">Deaktiv</option>

                                                    </select>
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

            const form = document.getElementById("package_create");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/create/package.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Paket əlavə edildi.",
                                icon: "success",
                                draggable: true
                            });
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