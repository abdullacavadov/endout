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

                    <form id="member_add" method="post">

                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Heyət üzvü əlavə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="staff-members.php">Heyət üzvləri</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Heyət üzvü əlavə et</li>
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
                                                    <label for="full_name" class="form-label">Tam adı</label>
                                                    <input class="form-control" id="full_name" name="full_name"
                                                        placeholder="Məs: Elvin Həsənov" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input class="form-control" id="email" name="email"
                                                        placeholder="Məs: elvin.hesenov@example.com" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="phone" class="form-label">Telefon</label>
                                                    <input class="form-control" id="phone" name="phone"
                                                        placeholder="Məs: +994 50 123 45 67" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="password" class="form-label">Şifrə</label>
                                                    <input class="form-control" id="password" name="password"
                                                        placeholder="Məs: ********" type="password" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="role" class="form-label">Vəzifə</label>
                                                    <select class="form-control" id="role" name="role" required>
                                                        <option value="">Vəzifə seçin</option>
                                                        <?php
                                                        $statement = $pdo->prepare("SELECT * FROM tbl_roles ORDER BY role_name ASC");
                                                        $statement->execute();
                                                        $permissions = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($permissions as $permission): ?>
                                                            <option value="<?= $permission['role_name']; ?>">
                                                                <?= htmlspecialchars($permission['role_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-6">
                                                    <label for="photo" class="form-label">Şəkil</label>
                                                    <input class="form-control" id="photo" name="photo" type="file"
                                                        required>


                                                </div>

                                                <div id="prewiew" class="col-md-6">
                                                    <img id="preview_img" src="#" alt="Şəkil önizlemesi"
                                                        style="display: none; max-width: 200px; max-height: 200px;">
                                                </div>

                                                <script>
                                                    document.getElementById('photo').addEventListener('change', function (event) {
                                                        const file = event.target.files[0];
                                                        if (file) {
                                                            const reader = new FileReader();
                                                            reader.onload = function (e) {
                                                                const previewImg = document.getElementById('preview_img');
                                                                previewImg.src = e.target.result;
                                                                previewImg.style.display = 'block';
                                                            }
                                                            reader.readAsDataURL(file);
                                                        }
                                                    });
                                                </script>
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

            const form = document.getElementById("member_add");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/create/staff_members.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Heyət üzvü uğurla əlavə edildi.",
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