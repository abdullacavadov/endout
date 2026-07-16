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

                    <form id="roles_add" method="post">

                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Vəzifə əlavə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="staff-roles.php">Vəzifələr</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Vəzifə əlavə et</li>
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
                                                <div class="col-md-12">
                                                    <label for="roles" class="form-label">Vəzifə adı</label>
                                                    <input class="form-control" id="roles" name="role_name"
                                                        placeholder="Məs: Admin">
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-3">
                                                <div class="col-md-12">
                                                    <label for="role_permissions" class="form-label">İcazələr</label>

                                                    <div class="d-flex flex-wrap gap-5">

                                                        <?php
                                                        $statement = $pdo->prepare("SELECT * FROM tbl_permissions ORDER BY id DESC");
                                                        $statement->execute();
                                                        $permissions = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($permissions as $permission): ?>
                                                            <div class="form-check d-flex align-items-center gap-2">

                                                                <input class="form-check-input" type="checkbox"
                                                                    style="height: 25px; width: 25px;"
                                                                    value="<?= $permission['permission']; ?>"
                                                                    name="role_permissions[]"
                                                                    id="perm_<?= $permission['id']; ?>">

                                                                <label class="form-check-label"
                                                                    for="perm_<?= $permission['id']; ?>">
                                                                    <?= htmlspecialchars($permission['name']); ?>
                                                                </label>

                                                            </div>
                                                        <?php endforeach; ?>

                                                    </div>

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

            const form = document.getElementById("roles_add");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/create/staff_roles.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Vəzifə uğurla əlavə edildi.",
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