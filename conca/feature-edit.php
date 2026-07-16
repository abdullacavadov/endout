<?php include("inc/config.php"); ?>

<?php
if (!isset($_GET['fid']) || empty($_GET['fid'])) {
    header("Location: staff-members.php");
    exit();
}

$feature_id = $_GET['fid'];

$statement = $pdo->prepare("SELECT * FROM feature_definitions WHERE id = ?");
$statement->execute([$feature_id]);
$feature = $statement->fetch(PDO::FETCH_ASSOC);
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

                    <form id="feature_edit" method="post">


                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Paket imkanının redaktəsi</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="packages.php">Abunəlik paketi imkanları</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Abunəlik paketi
                                            imkanlarının
                                            redaktəsi</li>
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
                                                <div class="col-md-4">
                                                    <label for="title" class="form-label">Başlıq</label>
                                                    <input class="form-control" id="title" name="title"
                                                        placeholder="Məs: Premium"
                                                        value="<?= htmlspecialchars($feature['title']) ?>">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="feature_key" class="form-label">Feature key</label>
                                                    <input class="form-control" id="feature_key" name="feature_key"
                                                        placeholder="Məs: max_post" type="text"
                                                        value="<?= htmlspecialchars($feature['feature_key']) ?>">
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="value_type" class="form-label">Dəyər
                                                        növü</label>
                                                    <select class="form-control" id="value_type" name="value_type">
                                                        <option value="string" <?= $feature['value_type'] == 'string' ? 'selected' : '' ?>>Mətn</option>
                                                        <option value="int" <?= $feature['value_type'] == 'int' ? 'selected' : '' ?>>Tam ədəd</option>
                                                        <option value="decimal" <?= $feature['value_type'] == 'decimal' ? 'selected' : '' ?>>Onluq ədəd</option>
                                                        <option value="bool" <?= $feature['value_type'] == 'bool' ? 'selected' : '' ?>>Boolean</option>
                                                        <option value="JSON" <?= $feature['value_type'] == 'JSON' ? 'selected' : '' ?>>JSON</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">Açıqlama</label>
                                                    <textarea class="form-control" 
                                                              id="description" rows="4"
                                                              name="description"><?= htmlspecialchars($feature['description']) ?></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="category" class="form-label">Kateqoriya</label>
                                                    <select class="form-control" id="category" name="category"
                                                        placeholder="Məs: Maksimum post">
                                                        <option value="limit" <?= $feature['category'] === 'limit' ? 'selected' : '' ?>>Limit</option>
                                                        <option value="enabled" <?= $feature['category'] === 'enabled' ? 'selected' : '' ?>>İmkan</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="is_active" class="form-label">Status</label>
                                                    <select class="form-control" id="is_active" name="is_active"
                                                        placeholder="Məs: Maksimum post">
                                                        <option value="1" <?= $feature['is_active'] == 1 ? 'selected' : '' ?>>Aktiv</option>
                                                        <option value="0" <?= $feature['is_active'] == 0 ? 'selected' : '' ?>>Deaktiv</option>
                                                    </select>
                                                </div>


                                               
                                            </div>


                                            <input type="hidden" name="id" value="<?= $feature['id']; ?>">


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

            const form = document.getElementById("feature_edit");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/update/feature_definitions.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Abunəlik paketinin xüsusiyyəti uğurla yeniləndi.",
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