<?php include("inc/config.php"); ?>

<?php
if (!isset($_GET['mid']) || empty($_GET['mid'])) {
    header("Location: staff-members.php");
    exit();
}

$member_id = $_GET['mid'];

$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE id = ?");
$statement->execute([$member_id]);
$member = $statement->fetch(PDO::FETCH_ASSOC);

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

                    <form id="member_edit" method="post">

                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Heyət üzvünü redaktə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="staff-members.php">Heyət üzvləri</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Heyət üzvünü redaktə et</li>
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
                                                        placeholder="Məs: Elvin Həsənov" value="<?= htmlspecialchars($member['full_name']) ?>" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input class="form-control" id="email" name="email"
                                                        placeholder="Məs: elvin.hesenov@example.com" value="<?= htmlspecialchars($member['email']) ?>" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="phone" class="form-label">Telefon</label>
                                                    <input class="form-control" id="phone" name="phone"
                                                        placeholder="Məs: +994 50 123 45 67" value="<?= htmlspecialchars($member['phone']) ?>" required>
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
                                                        $roles = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($roles as $role): ?>
                                                            <option value="<?= $role['role_name']; ?>" <?= ($member['role'] === $role['role_name']) ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($role['role_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                            </div>

                                            <input type="hidden" name="member_id" value="<?= $member_id; ?>">


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

            const form = document.getElementById("member_edit");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/update/staff_members.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Heyət üzvü uğurla yeniləndi.",
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