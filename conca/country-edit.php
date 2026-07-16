<?php include("inc/config.php"); ?>

<?php
if (!isset($_GET['cid']) || empty($_GET['cid'])) {
    header("Location: staff-members.php");
    exit();
}

$country_id = $_GET['cid'];

$statement = $pdo->prepare("SELECT * FROM countries WHERE id = ?");
$statement->execute([$country_id]);
$country = $statement->fetch(PDO::FETCH_ASSOC);

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
            <div class="app-content-wrapper py-20 pb-13">
                <div class="container ">

                    <form id="country_edit" method="post">

                    <input type="hidden" name="country_id" value="<?= $country_id; ?>">

                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Ölkə məlumatlarını redaktə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="countries.php">Ölkələr</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Ölkə məlumatlarını
                                            redaktə et</li>
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
                                                        placeholder="Məs: Azerbaijan"
                                                        value="<?= htmlspecialchars($country['name']) ?>" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="iso2" class="form-label">ISO 2</label>
                                                    <input class="form-control" id="iso2" name="iso2"
                                                        placeholder="Məs: Azerbaijan"
                                                        value="<?= htmlspecialchars($country['iso2']) ?>" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-4">
                                                    <label for="area" class="form-label">Region</label>
                                                    <select class="form-control" id="area" name="area" required>
                                                        <option value="">-- Region seç --</option>

                                                        <option value="Europe" <?php if ($country['area'] == 'Europe')
                                                            echo 'selected'; ?>>
                                                            Avropa
                                                        </option>

                                                        <option value="Asia" <?php if ($country['area'] == 'Asia')
                                                            echo 'selected'; ?>>
                                                            Asiya
                                                        </option>

                                                        <option value="Africa" <?php if ($country['area'] == 'Africa')
                                                            echo 'selected'; ?>>
                                                            Afrika
                                                        </option>

                                                        <option value="North America" <?php if ($country['area'] == 'North America')
                                                            echo 'selected'; ?>>
                                                            Şimali Amerika
                                                        </option>

                                                        <option value="South America" <?php if ($country['area'] == 'South America')
                                                            echo 'selected'; ?>>
                                                            Cənubi Amerika
                                                        </option>

                                                        <option value="Oceania" <?php if ($country['area'] == 'Oceania')
                                                            echo 'selected'; ?>>
                                                            Okeaniya
                                                        </option>

                                                        <option value="Antarctica" <?php if ($country['area'] == 'Antarctica')
                                                            echo 'selected'; ?>>
                                                            Antarktida
                                                        </option>
                                                    </select>
                                                </div>


                                                <div class="col-md-4">
                                                    <label for="country_audience" class="form-label">Əhali sayı</label>
                                                    <input class="form-control" id="country_audience" name="country_audience"
                                                        placeholder="Məs: 15000000"
                                                        value="<?= htmlspecialchars($country['country_audience']) ?>" required>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="coefficient" class="form-label">Əmsal (Defolt x1.00)</label>
                                                    <input class="form-control" id="coefficient" name="coefficient"
                                                        placeholder="Məs: 0.80"
                                                        value="<?= htmlspecialchars($country['coefficient']) ?>" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-3">
                                                    <label for="dial_code" class="form-label">Ölkə kodu</label>
                                                    <input class="form-control" id="dial_code" name="dial_code"
                                                        placeholder="Məs: +994"
                                                        value="<?= htmlspecialchars($country['dial_code']) ?>" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="currency_name" class="form-label">Valyuta (İngiliscə)</label>
                                                    <input class="form-control" id="currency_name" name="currency_name"
                                                        placeholder="Məs: Azerbaijani Manat"
                                                        value="<?= htmlspecialchars($country['currency_name']) ?>" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="currency_code" class="form-label">Valyuta kodu</label>
                                                    <input class="form-control" id="currency_code" name="currency_code"
                                                        placeholder="Məs: AZN"
                                                        value="<?= htmlspecialchars($country['currency_code']) ?>" required>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="currency_symbol" class="form-label">Valyuta simvolu</label>
                                                    <input class="form-control" id="currency_symbol" name="currency_symbol"
                                                        placeholder="Məs: ₼"
                                                        value="<?= htmlspecialchars($country['currency_symbol']) ?>" required>
                                                </div>

                                                
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="country_status" class="form-label">Status</label>
                                                    <select class="form-control" id="country_status" name="country_status" required>
                                                        <option value="active" <?php if ($country['country_status'] == 'acive')
                                                            echo 'selected'; ?>>Aktiv</option>
                                                        <option value="disabled" <?php if ($country['country_status'] == 'disabled')
                                                            echo 'selected'; ?>>Deaktiv</option>
                                                        
                                                    </select>
                                                </div>

                                            </div>

                                            <input type="hidden" name="country_id" value="<?= $country_id; ?>">


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

            const form = document.getElementById("country_edit");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/update/country.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Ölkə məlumatları uğurla yeniləndi.",
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