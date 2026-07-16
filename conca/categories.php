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
                    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
                        <div class="">
                            <h2 class="fw-semibold fs-7">Kateqoriyalar</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Ana Səhifə</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Kateqoriyalar</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="">
                            <a href="category-add.php" class="btn btn-primary d-flex align-items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 1V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Kateqoriya əlavə et
                            </a>
                        </div>
                    </div> <!-- breadcrumb end -->

                    <div class="page-content">
                        <div class="row">
                            <div class="col-12">
                                <div class="pure-card rounded-custom card-bg shadow-custom">
                                    <div class="pure-card-header">
                                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                                            <span class="text-primary d-flex align-items-center">
                                                <i class="fa-solid fa-layer-group"></i>
                                            </span>
                                            Kateqoriyalar
                                        </h3>
                                    </div>
                                    <div class="pure-card-body">


                                        <?php

                                        $stmt = $pdo->query("SELECT * FROM categories ORDER BY parent_id, name");
                                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        $tree = [];
                                        $refs = [];

                                        foreach ($categories as $category) {
                                            $category['children'] = [];

                                            $refs[$category['id']] = $category;

                                            if ($category['parent_id'] == null) {
                                                $tree[$category['id']] = &$refs[$category['id']];
                                            } else {
                                                $refs[$category['parent_id']]['children'][] = &$refs[$category['id']];
                                            }
                                        }

                                        ?>

                                        <?php

                                        function renderCategoriesTable($categories, $level = 0)
                                        {
                                            foreach ($categories as $category) {

                                                $hasChildren = !empty($category['children']);

                                                echo '
            <tr>
                <td>' . (int) $category['id'] . '</td>

                <td style="padding-left:' . ($level * 30) . 'px;">
                    ' . str_repeat('— ', $level) . htmlspecialchars($category['name']) . '
                </td>

                <td>' . htmlspecialchars($category['slug']) . '</td>

                <td>' . (int) $category['depth'] . '</td>

                <td>
                    ' . ($hasChildren
                                                    ? '<span class="badge bg-primary">Parent</span>'
                                                    : '<span class="badge bg-secondary">Child</span>') . '
                </td>

                <td>
                    <button class="btn btn-sm btn-warning">
                        Edit
                    </button>

                    <button class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </td>
            </tr>
        ';

                                                if ($hasChildren) {
                                                    renderCategoriesTable($category['children'], $level + 1);
                                                }
                                            }
                                        }
                                        ?>

                                        <!-- =========================
CSS
========================= -->

                                        <style>
                                            .category-tree {
                                                user-select: none;
                                            }

                                            .category-item {
                                                margin: 4px 0;
                                            }

                                            .category-row {
                                                display: flex;
                                                align-items: center;
                                                justify-content: space-between;
                                                gap: 10px;
                                                border: 1px solid #e5e7eb;
                                                border-radius: 10px;
                                                padding: 10px 14px;
                                                transition: .2s;
                                            }

                                            .category-row:hover {
                                                background: #04396d;
                                                color: #fff;
                                            }

                                            .category-left {
                                                display: flex;
                                                align-items: center;
                                                gap: 10px;
                                            }

                                            .category-children {
                                                margin-left: 28px;
                                                margin-top: 6px;
                                                border-left: 1px dashed #d1d5db;
                                                padding-left: 14px;
                                            }

                                            .toggle-btn {
                                                width: 22px;
                                                height: 22px;
                                                border: none;
                                                background: #000000;
                                                color: #fff;
                                                border-radius: 6px;
                                                font-size: 14px;
                                                cursor: pointer;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                            }

                                            .toggle-btn.empty {
                                                visibility: hidden;
                                            }

                                            .category-name {
                                                font-weight: 500;
                                            }

                                            .category-actions {
                                                display: flex;
                                                gap: 6px;
                                            }

                                            .category-actions button {
                                                border: none;
                                                padding: 6px 10px;
                                                border-radius: 8px;
                                                cursor: pointer;
                                                font-size: 12px;
                                            }

                                            .sortable-ghost {
                                                opacity: .4;
                                            }

                                            .sortable-drag {
                                                transform: rotate(2deg);
                                            }
                                        </style>

                                        <!-- =========================
LAYOUT
========================= -->

                                        <div class="row">

                                            <!-- LEFT -->
                                            <div class="col-lg-5">

                                                <div class="card shadow-sm border-0">



                                                    <div class="card-body">

                                                        <div class="category-tree">

                                                            <?php renderCategoryTree($tree); ?>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- RIGHT -->
                                            <div class="col-lg-7">

                                                <div class="card shadow-sm border-0 position-sticky" style="top:20px;">

                                                    <div class="card-header">
                                                        <h5 class="mb-0">
                                                            <strong>Düzəliş et</strong>
                                                        </h5>
                                                    </div>

                                                    <div class="card-body">

                                                        <form id="category-edit">

                                                            <input type="hidden" name="id" id="category_id">

                                                            <div class="mb-3">
                                                                <label class="form-label">
                                                                    Ad
                                                                </label>

                                                                <input type="text" class="form-control" name="name"
                                                                    id="category_name">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">
                                                                    Slug
                                                                </label>

                                                                <input type="text" class="form-control" name="slug"
                                                                    id="category_slug">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">
                                                                    Slug path (auto-generated)
                                                                </label>

                                                                <input type="text" class="form-control" name="slug_path"
                                                                    id="slug_path" readonly>

                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">
                                                                    Üst kateqoriya
                                                                </label>

                                                                <select class="form-select" name="parent_id"
                                                                    id="category_parent">
                                                                    <option value="0" cate>
                                                                        Əsas kateqoriya
                                                                    </option>

                                                                    <?php renderParentOptions($tree); ?>
                                                                </select>


                                                                <script>

                                                                    const nameInput = document.getElementById('category_name');
                                                                    const slugInput = document.getElementById('category_slug');
                                                                    const slugPathInput = document.getElementById('slug_path');
                                                                    const parentSelect = document.getElementById('category_parent');

                                                                    let manualSlugEdit = false;

                                                                    /* =========================
                                                                    SLUGIFY
                                                                    ========================= */

                                                                    function slugify(text) {

                                                                        return text
                                                                            .toString()
                                                                            .toLowerCase()
                                                                            .trim()

                                                                            .replace(/ə/g, 'e')
                                                                            .replace(/ü/g, 'u')
                                                                            .replace(/ö/g, 'o')
                                                                            .replace(/ğ/g, 'g')
                                                                            .replace(/ş/g, 's')
                                                                            .replace(/ç/g, 'c')
                                                                            .replace(/ı/g, 'i')

                                                                            .replace(/[^a-z0-9\s-]/g, '')
                                                                            .replace(/\s+/g, '-')
                                                                            .replace(/-+/g, '-');
                                                                    }

                                                                    /* =========================
                                                                    SLUG PATH GENERATOR
                                                                    ========================= */

                                                                    function updateSlugPath() {

                                                                        const slug = slugInput.value.trim();

                                                                        const selectedOption =
                                                                            parentSelect.selectedOptions[0];

                                                                        const parentSlugPath =
                                                                            selectedOption?.dataset.slugPath || '';

                                                                        slugPathInput.value =
                                                                            parentSlugPath
                                                                                ? parentSlugPath + '/' + slug
                                                                                : slug;
                                                                    }

                                                                    /* =========================
                                                                    AUTO SLUG FROM NAME
                                                                    ========================= */

                                                                    nameInput.addEventListener('input', function () {

                                                                        if (!manualSlugEdit) {

                                                                            slugInput.value = slugify(this.value);
                                                                        }

                                                                        updateSlugPath();
                                                                    });

                                                                    /* =========================
                                                                    MANUAL SLUG EDIT
                                                                    ========================= */

                                                                    slugInput.addEventListener('input', function () {

                                                                        manualSlugEdit = true;

                                                                        slugInput.value = slugify(slugInput.value);

                                                                        updateSlugPath();
                                                                    });

                                                                    /* =========================
                                                                    PARENT CHANGE
                                                                    ========================= */

                                                                    parentSelect.addEventListener('change', function () {

                                                                        updateSlugPath();
                                                                    });

                                                                </script>
                                                            </div>

                                                            <button class="btn btn-success float-end" type="submit">
                                                                Yadda saxla
                                                            </button>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <?php

                                        /* =========================
                                        TREE RENDER
                                        ========================= */

                                        function renderCategoryTree($categories)
                                        {
                                            echo '<div class="sortable-group">';

                                            foreach ($categories as $category) {

                                                $hasChildren = !empty($category['children']);

                                                ?>

                                                <div class="category-item" data-id="<?= $category['id'] ?>">

                                                    <div class="category-row">

                                                        <div class="category-left">

                                                            <button class="toggle-btn <?= !$hasChildren ? 'empty' : '' ?>"
                                                                type="button">
                                                                <?= $hasChildren ? '-' : '+' ?>
                                                            </button>

                                                            <div class="category-name">
                                                                <?= htmlspecialchars($category['name']) ?>
                                                            </div>

                                                        </div>

                                                        <div class="category-actions">






                                                            <button
                                                                class="btn btn-sm btn-icon btn-icon-secondary rounded-pill edit-category"
                                                                type="button" data-id="<?= $category['id'] ?>"
                                                                data-name="<?= htmlspecialchars($category['name']) ?>"
                                                                data-slug="<?= htmlspecialchars($category['slug']) ?>"
                                                                data-slugpath="<?= htmlspecialchars($category['slug_path']) ?>"
                                                                data-parent="<?= $category['parent_id'] ?>"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-original-title="Düzəliş et">
                                                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M9.19922 2.39996L14.0992 7.29996"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M9.89941 15L15.4994 15" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </button>

                                                            <a class="btn btn-sm btn-icon btn-icon-secondary rounded-pill delete-btn"
                                                                href="api/delete/category.php?cid=<?= $category['id'] ?>"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-original-title="Sil">
                                                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M13.0508 3.44995L12.617 10.4675C12.5061 12.2605 12.4507 13.1569 12.0013 13.8015C11.7791 14.1201 11.493 14.3891 11.1613 14.5912C10.4903 15 9.59207 15 7.7957 15C5.99696 15 5.09759 15 4.42612 14.5904C4.09414 14.3879 3.80798 14.1185 3.58586 13.7993C3.13659 13.1538 3.0824 12.256 2.97401 10.4606L2.55078 3.44995"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" />
                                                                    <path d="M14.1 3.44998H1.5" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round" />
                                                                    <path
                                                                        d="M10.6371 3.45L10.1592 2.46421C9.84181 1.80938 9.6831 1.48197 9.40931 1.27776C9.34858 1.23247 9.28428 1.19218 9.21703 1.15729C8.91385 1 8.54999 1 7.82228 1C7.07629 1 6.7033 1 6.39509 1.16388C6.32678 1.20021 6.2616 1.24213 6.20022 1.28922C5.92326 1.50169 5.76855 1.84109 5.45913 2.51988L5.03516 3.45"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" />
                                                                    <path d="M6.05078 11.15L6.05078 6.95003"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" />
                                                                    <path d="M9.55078 11.15L9.55078 6.94995"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" />
                                                                </svg>
                                                            </a>

                                                        </div>

                                                    </div>

                                                    <?php if ($hasChildren): ?>

                                                        <div class="category-children">

                                                            <?php renderCategoryTree($category['children']); ?>

                                                        </div>

                                                    <?php endif; ?>

                                                </div>

                                                <?php
                                            }

                                            echo '</div>';
                                        }

                                        /* =========================
                                        PARENT OPTIONS
                                        ========================= */

                                        function renderParentOptions($categories, $level = 0)
                                        {
                                            foreach ($categories as $category) {

                                                echo '
                                                <option value="' . $category['id'] . '" 
                                                data-slug-path="' . htmlspecialchars($category['slug_path']) . '">
                                                    ' . str_repeat('— ', $level) . htmlspecialchars($category['name']) . '
                                                </option>
                                            ';

                                                if (!empty($category['children'])) {
                                                    renderParentOptions($category['children'], $level + 1);
                                                }
                                            }
                                        }

                                        ?>





                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- page content end -->

                </div>
            </div><!-- app content end -->
            <?php include 'inc/footer.php'; ?>

            <div class="app-backdrop"></div>
            <!-- app content end -->
        </div>
        <!-- app wrapper end -->
    </div>

    <?php include 'inc/scripts_url.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const deleteButtons = document.querySelectorAll(".delete-btn");

            deleteButtons.forEach(button => {

                button.addEventListener("click", function (e) {

                    e.preventDefault();

                    const deleteUrl = this.getAttribute("href");

                    Swal.fire({
                        title: "Əminsiniz?",
                        text: "Bu məlumat silinəcək və geri qaytarılmayacaq!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Bəli, sil",
                        cancelButtonText: "Ləğv et"
                    })

                        .then((result) => {

                            if (result.isConfirmed) {

                                window.location.href = deleteUrl;

                            }

                        });

                });

            });

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>

        /* =========================
        COLLAPSE
        ========================= */

        document.querySelectorAll('.toggle-btn').forEach(btn => {

            btn.addEventListener('click', function () {

                const parent = this.closest('.category-item');

                const children = parent.querySelector(':scope > .category-children');

                if (!children) return;

                if (children.style.display === 'none') {

                    children.style.display = '';

                    this.innerHTML = '-';

                } else {

                    children.style.display = 'none';

                    this.innerHTML = '+';
                }
            });
        });

        /* =========================
        EDIT
        ========================= */

        document.querySelectorAll('.edit-category').forEach(btn => {

            btn.addEventListener('click', function () {

                document.getElementById('category_id').value = this.dataset.id;

                document.getElementById('category_name').value = this.dataset.name;

                document.getElementById('category_slug').value = this.dataset.slug;

                document.getElementById('slug_path').value = this.dataset.slugpath;

                document.getElementById('category_parent').value = this.dataset.parent;
            });
        });

        /* =========================
        SORTABLE
        ========================= */

        document.querySelectorAll('.sortable-group').forEach(group => {

            new Sortable(group, {

                animation: 150,

                ghostClass: 'sortable-ghost',

                dragClass: 'sortable-drag',

                group: 'nested',

                fallbackOnBody: true,

                swapThreshold: 0.65,

                onEnd: function (evt) {

                    const item = evt.item;

                    const categoryId = item.dataset.id;

                    let parent = item.closest('.category-children');

                    let parentItem = parent
                        ? parent.closest('.category-item')
                        : null;

                    let parentId = parentItem
                        ? parentItem.dataset.id
                        : 0;

                    console.log({
                        id: categoryId,
                        parent_id: parentId,
                        slug_path: slugPath,
                        name: name
                    });


                }
            });
        });

    </script>

    <script>
        document.getElementById('category-edit').addEventListener('submit', function (e) {

            e.preventDefault();

            const formData = new FormData(this);

            fetch('api/update/category.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {

                    if (data.trim() === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Uğurlu',
                            text: 'Məlumat uğurla yeniləndi!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Xəta',
                            text: data,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Xəta',
                        text: 'Server xətası baş verdi.',
                    });
                });
        });
    </script>
</body>

</html>