<?php include("inc/config.php"); ?>

<?php
$packages = $pdo->prepare("SELECT * FROM packages ORDER BY sort_order ASC");
$packages->execute();
$packages = $packages->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$featureDefinitions = $pdo->prepare("SELECT * FROM feature_definitions ORDER BY category ASC, sort_order ASC");
$featureDefinitions->execute();
$featureDefinitions = $featureDefinitions->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$groupedFeatures = [];

foreach ($featureDefinitions as $feature) {
    $groupedFeatures[$feature['category']][] = $feature;
}
?>

<?php
$stmt = $pdo->query("
    SELECT package_id, feature_key, feature_value
    FROM package_features
");

$packageValues = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $packageValues[$row['package_id']][$row['feature_key']] = $row;

}
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




                    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                        <div class="">
                            <h2 class="fw-semibold fs-7">Paket xüsusiyyətləri</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="packages.php">Paketlər</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Paket xüsusiyyətləri</li>
                                </ol>


                            </nav>
                        </div>


                    </div> <!-- breadcrumb end -->

                    <div class="page-content">
                        <div class="row gy-5 mb-5">
                            <div class="col-md-12">
                                <div class="pure-card rounded-custom card-bg shadow-custom">

                                    <div class="pure-card-body mt-5">


<form id="package_features_edit" method="post">

    <ul class="nav nav-tabs mb-4" role="tablist">

        <?php foreach ($packages as $index => $package): ?>

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link <?= $index == 0 ? 'active' : '' ?>"
                    type="button"
                    data-bs-toggle="tab"
                    data-bs-target="#package-<?= $package['id'] ?>"
                    role="tab">

                    <?= htmlspecialchars($package['name']) ?>

                </button>

            </li>

        <?php endforeach; ?>

    </ul>


    <div class="tab-content">

        <?php foreach ($packages as $packageIndex => $package): ?>

            <div
                class="tab-pane fade <?= $packageIndex == 0 ? 'show active' : '' ?>"
                id="package-<?= $package['id'] ?>"
                role="tabpanel">

                <?php foreach ($groupedFeatures as $category => $features): ?>

                    <div class="card shadow-sm mb-3">

                        <div class="card-header bg-info text-dark fw-semibold">

                            <?= $category == 'enabled' ? 'İcazə verilən xüsusiyyətlər' : 'Limitlər' ?>

                        </div>

                        <div class="card-body">

                            <?php foreach ($features as $feature):

                                $key = $feature['feature_key'];

                                $value = $packageValues[$package['id']][$key]['feature_value'] ?? '';

                            ?>

                                <div class="row align-items-center mb-3">

                                    <div class="col-md-4">

                                        <label class="form-label fw-semibold mb-1">

                                            <?= htmlspecialchars($feature['title']) ?>

                                        </label>

                                        <?php if (!empty($feature['description'])): ?>

                                            <div class="small text-muted">

                                                <?= htmlspecialchars($feature['description']) ?>

                                            </div>

                                        <?php endif; ?>

                                    </div>


                                    <div class="col-md-8">

                                        <?php switch ($feature['value_type']):

                                            case 'bool': ?>

                                                <div class="btn-group" role="group">

                                                    <input
                                                        type="radio"
                                                        class="btn-check"
                                                        name="features[<?= $package['id'] ?>][<?= $key ?>]"
                                                        id="<?= $package['id'] . '_' . $key ?>_1"
                                                        value="1"
                                                        <?= $value == '1' ? 'checked' : '' ?>>

                                                    <label
                                                        class="btn btn-outline-primary"
                                                        for="<?= $package['id'] . '_' . $key ?>_1">
                                                        Aktiv
                                                    </label>


                                                    <input
                                                        type="radio"
                                                        class="btn-check"
                                                        name="features[<?= $package['id'] ?>][<?= $key ?>]"
                                                        id="<?= $package['id'] . '_' . $key ?>_0"
                                                        value="0"
                                                        <?= $value == '0' ? 'checked' : '' ?>>

                                                    <label
                                                        class="btn btn-outline-danger"
                                                        for="<?= $package['id'] . '_' . $key ?>_0">
                                                        Deaktiv
                                                    </label>

                                                </div>

                                                <?php break; ?>


                                            <?php case 'int': ?>

                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    name="features[<?= $package['id'] ?>][<?= $key ?>]"
                                                    value="<?= htmlspecialchars($value) ?>">

                                                <?php break; ?>


                                            <?php case 'decimal': ?>

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    class="form-control"
                                                    name="features[<?= $package['id'] ?>][<?= $key ?>]"
                                                    value="<?= htmlspecialchars($value) ?>">

                                                <?php break; ?>


                                            <?php case 'string': ?>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="features[<?= $package['id'] ?>][<?= $key ?>]"
                                                    value="<?= htmlspecialchars($value) ?>">

                                                <?php break; ?>


                                            <?php case 'json': ?>

                                                <textarea
                                                    class="form-control"
                                                    rows="4"
                                                    name="features[<?= $package['id'] ?>][<?= $key ?>]"><?= htmlspecialchars($value) ?></textarea>

                                                <?php break; ?>

                                        <?php endswitch; ?>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>


    <div class="text-end mt-4">

        <button
            type="submit"
            class="btn btn-primary">

            <i class="fa-solid fa-save me-2"></i>

            Yadda saxla

        </button>

    </div>

</form>



                                    </div>

                                </div>


                            </div>
                        </div>

                    </div>



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

            const form = document.getElementById("package_features_edit");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/update/package_features.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Paket məlumatları uğurla yeniləndi.",
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