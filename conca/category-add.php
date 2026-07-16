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

                    <form id="category_add">

                        <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                            <div class="">
                                <h2 class="fw-semibold fs-7">Kateqoriya əlavə et</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="categories.php">Kateqoriyalar</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Kateqoriya əlavə et</li>
                                    </ol>


                                </nav>
                            </div>


                        </div> <!-- breadcrumb end -->

                        <div class="page-content">
                            <div class="row gy-5 mb-5">
                                <div class="col-md-12">
                                    <div class="pure-card rounded-custom card-bg shadow-custom">

                                        <div class="pure-card-body mt-5">

                                            <?php
                                            $statement = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
                                            $statement->execute();
                                            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="depth" class="form-label">Kateqoriya dərəcəsi</label>

                                                    <select class="form-control" id="depth" name="depth" required>
                                                        <option value="0" selected>Baş kateqoriya</option>
                                                        <option value="1">Orta kateqoriya</option>
                                                        <option value="2">Alt kateqoriya</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5 d-none" id="parent-wrapper">
                                                <div class="col-md-12">
                                                    <label for="parent_id" class="form-label">Üst kateqoriya</label>

                                                    <select class="form-control" id="parent_id" name="parent_id">
                                                        <option value="">Üst kateqoriya seçin</option>
                                                    </select>
                                                </div>
                                            </div>



                                            <div class="row gy-5 mb-5">
                                                <div class="col-md-12">
                                                    <label for="name" class="form-label">Kateqoriya adı</label>
                                                    <input class="form-control" id="name" name="name"
                                                        placeholder="Məs: Elektronika" required>
                                                </div>
                                            </div>

                                            <div class="row gy-5 mb-5" id="photo_wrapper">
                                                <div class="col-md-6">
                                                    <label for="photo" class="form-label">Şəkil</label>
                                                    <input class="form-control" id="photo" name="photo" type="file"
                                                        accept="image/*">


                                                </div>

                                                <div id="prewiew" class="col-md-6">
                                                    <img id="preview_img" src="#" alt="Şəkil önizlemesi"
                                                        style="display: none; max-width: 200px; max-height: 200px;">
                                                </div>


                                                <script>
                                                    const depthSelect = document.getElementById('depth');
                                                    const parentWrapper = document.getElementById('parent-wrapper');
                                                    const parentSelect = document.getElementById('parent_id');

                                                    const photo_wrapper = document.getElementById('photo_wrapper');

                                                    const categories = <?= json_encode($categories); ?>;

                                                    depthSelect.addEventListener('change', function () {

                                                        const depth = parseInt(this.value);

                                                        parentSelect.innerHTML =
                                                            '<option value="">Üst kateqoriya seçin</option>';

                                                        // Baş kateqoriya
                                                        if (depth === 0) {

                                                            parentWrapper.classList.add('d-none');
                                                            parentSelect.removeAttribute('required');
                                                            photo_wrapper.classList.add('d-block');
                                                            return;
                                                        } else {

                                                            photo_wrapper.classList.remove('d-block');
                                                            photo_wrapper.classList.add('d-none');
                                                        }

                                                        parentWrapper.classList.remove('d-none');
                                                        parentSelect.setAttribute('required', 'required');

                                                        // Parent depth
                                                        const parentDepth = depth - 1;

                                                        const filtered = categories.filter(cat =>
                                                            parseInt(cat.depth) === parentDepth
                                                        );

                                                        filtered
                                                            .sort((a, b) => {

                                                                // depth=2 üçün:
                                                                // üst_kateqoriya / orta_kateqoriya
                                                                if (depth === 2) {

                                                                    const parentA = categories.find(p => p.id == a.parent_id);
                                                                    const parentB = categories.find(p => p.id == b.parent_id);

                                                                    const parentNameA = parentA ? parentA.name : '';
                                                                    const parentNameB = parentB ? parentB.name : '';

                                                                    // əvvəl üst kateqoriya adına görə
                                                                    const parentCompare =
                                                                        parentNameA.localeCompare(parentNameB, 'az');

                                                                    if (parentCompare !== 0) {
                                                                        return parentCompare;
                                                                    }

                                                                    // sonra orta kateqoriya adına görə
                                                                    return a.name.localeCompare(b.name, 'az');
                                                                }

                                                                // depth=1 üçün normal sıralama
                                                                return a.name.localeCompare(b.name, 'az');
                                                            })

                                                            .forEach(cat => {

                                                                const option = document.createElement('option');

                                                                option.value = cat.id;

                                                                // depth=2
                                                                if (depth === 2) {

                                                                    const parentCategory = categories.find(parent =>
                                                                        parent.id == cat.parent_id
                                                                    );

                                                                    if (parentCategory) {

                                                                        option.textContent =
                                                                            parentCategory.name + ' / ' + cat.name;

                                                                    } else {

                                                                        option.textContent = cat.name;
                                                                    }

                                                                } else {

                                                                    // depth=1
                                                                    option.textContent = cat.name;
                                                                }

                                                                parentSelect.appendChild(option);
                                                            });
                                                    });
                                                </script>

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

            const form = document.getElementById("category_add");

            form.addEventListener("submit", function (e) {

                e.preventDefault();

                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch("./api/create/category.php", {
                    method: "POST",
                    body: formData
                })

                    .then(response => response.text())

                    .then(response => {

                        if (response.trim() === "success") {

                            Swal.fire({
                                title: "Kateqoriya uğurla əlavə edildi.",
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