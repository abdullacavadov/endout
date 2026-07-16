<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";
?>

<!DOCTYPE html>
<html lang="az">

<head>
    <?php require_once "./inc/head.php"; ?>
</head>

<body>


    <div class="main-wrapper home-nine">

        <!-- Header -->
        <?php require_once "./inc/header.php"; ?>
        <!-- /Header -->



        <!-- Dashboard Content -->
        <div class="dashboard-content listing-section ">
            <div class="container">
                <div style="margin-top: 70px;">
                    <?php include "./inc/dashboard_menus.php"; ?>
                </div>
                <div class="dash-listingcontent dashboard-info">
                    <div class="dash-cards card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <?php if ($cust_type === 'partner'): ?>
                                    <a class="btn" style="background: none; border: 1px solid #2563EB;" href="./b2b"
                                        disabled>Mənə aid sorğular</a>
                                <?php endif; ?>
                                <a class="btn" style="background: #2563EB; border: 1px solid #2563EB; color: #fff;"
                                    href="./my-inquiry">Mənim sorğularım</a>
                            </div>


                            <a href="./add-tender" class="btn"
                                style="background: #25eba9; border: 1px solid #25eba9; color: #000;"><i
                                    class="fas fa-plus"></i> Yeni sorğu əlavə
                                et</a>

                        </div>
                        <div class="card-body">


                            <div class="listing-search">
                                <div class="filter-content form-set">
                                    <form method="GET" id="searchForm">

                                        <?php
                                        $sort = $_GET['s'] ?? 'new';

                                        switch ($sort) {

                                            case 'old':
                                                $order = "t.id ASC";
                                                break;

                                            case 'az':
                                                $order = "t.title ASC";
                                                break;

                                            case 'za':
                                                $order = "t.title DESC";
                                                break;

                                            default:
                                                $order = "t.id DESC";

                                        }
                                        ?>


                                        <div class="listing-search">

                                            <div class="filter-content form-set">
                                                <div class="group-img" style="position:relative">

                                                    <input type="text" class="form-control" name="q" id="searchInput"
                                                        placeholder="Axtar..." style="color: darkslategray"
                                                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

                                                    <i class="feather-search"></i>

                                                    <button type="submit" style="
position:absolute;
right:-20px;
top: 0;
border:none;
background:none;
z-index: 10;
cursor:pointer;
">
                                                        <i class="feather-search"
                                                            style="color: blue; font-size: 18px;"></i>
                                                    </button>

                                                    <button type="button" id="clearSearch" style="
                                                                                position:absolute;
                                                                                right:35px;
                                                                                top:0;
                                                                                border:none;
                                                                                background:none;
                                                                                font-size:18px;
                                                                                cursor:pointer;
                                                                                display:none;
                                                                                ">
                                                        <i class="fa-solid fa-x"></i>
                                                    </button>

                                                </div>
                                            </div>

                                            <input type="hidden" name="s" value="<?= htmlspecialchars($sort) ?>">
                                            <input type="hidden" name="p" value="1">

                                        </div>

                                    </form>
                                </div>
                                <div class="sorting-div">
                                    <div class="sortbyset">
                                        <span class="sortbytitle">Sırala</span>


                                        <div class="sorting-select">
                                            <select class="form-control select" id="sortSelect">

                                                <option value="new" <?= ($sort == 'new') ? 'selected' : '' ?>>Yenilər
                                                </option>
                                                <option value="old" <?= ($sort == 'old') ? 'selected' : '' ?>>Köhnələr
                                                </option>
                                                <option value="az" <?= ($sort == 'az') ? 'selected' : '' ?>>A-Z</option>
                                                <option value="za" <?= ($sort == 'za') ? 'selected' : '' ?>>Z-A</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="listing-table datatable">
                                    <thead>
                                        <tr>
                                            <th class="no-sort">Tender</th>
                                            <th class="no-sort">Tarix</th>
                                            <th class="no-sort">Teqlər</th>
                                            <th>Status</th>
                                            <th class="no-sort">Baxış</th>
                                            <th class="no-sort">Əməliyyat</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        $search = trim($_GET['q'] ?? '');

                                        $where = "
                                        WHERE t.customer_id = ?";

                                        $params = [$customerId];


                                        if ($search !== '') {

                                            $where .= "
                                            AND (
                                                t.title LIKE ?
                                                OR t.description LIKE ?
                                            )
                                            ";

                                            $searchParam = "%{$search}%";

                                            $params[] = $searchParam;
                                            $params[] = $searchParam;
                                        }

                                        $limit = 5;
                                        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;

                                        if ($page < 1) {
                                            $page = 1;
                                        }

                                        $offset = ($page - 1) * $limit;

                                        $countSt = $pdo->prepare("
                                            SELECT COUNT(DISTINCT t.id)

                                            FROM tenders t

                                            INNER JOIN tender_tags tt
                                            ON tt.tender_id = t.id

                                            $where
                                        ");





                                        $totalTender = $countSt->fetchColumn();

                                        $totalPages = ceil($totalTender / $limit);

                                        $st = $pdo->prepare("
                                            SELECT
                                                t.*,
                                                t.id AS tender_id,

                                                GROUP_CONCAT(DISTINCT tags.keyword SEPARATOR ', ') AS tag_names

                                            FROM tenders t

                                            INNER JOIN tender_tags tt
                                            ON tt.tender_id = t.id

                                            LEFT JOIN tags
                                            ON tags.id = tt.tag_id

                                            $where

                                            GROUP BY t.id

                                            ORDER BY $order

                                            LIMIT $limit OFFSET $offset
                                        ");

                                        $st->execute($params);



                                        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {


                                            ?>
                                            <tr>

                                                <td>
                                                    <h6>
                                                        <a href="./tender/<?= urlencode($row['slug']) ?>/<?= $row['tender_id'] ?>"
                                                            class="text-decoration-underline">
                                                            <?= htmlspecialchars($row['title']) ?>
                                                        </a>
                                                    </h6>
                                                </td>

                                                <td>
                                                    <?= date('d.m.Y H:i', strtotime($row['start_date'])) ?> -
                                                    <?= date('d.m.Y H:i', strtotime($row['end_date'])) ?>
                                                </td>

                                                <td>
                                                    <span class="badge bg-info d-flex flex-wrap gap-1">
                                                        <?= htmlspecialchars($row['tag_names']) ?>
                                                    </span>
                                                </td>

                                                <td>

                                                    <?php if ($row['status'] === 'active'): ?>

                                                        <span class="badge bg-success">
                                                            Aktiv
                                                        </span>

                                                    <?php elseif ($row['status'] === 'pending'): ?>

                                                        <span class="badge bg-warning">
                                                            Yoxlanır
                                                        </span>

                                                    <?php elseif ($row['status'] === 'cancelled'): ?>

                                                        <span class="badge bg-danger">
                                                            Ləğv edilib
                                                        </span>

                                                    <?php elseif ($row['status'] === 'expired'): ?>

                                                        <span class="badge bg-secondary">
                                                            Vaxtı bitib
                                                        </span>

                                                    <?php elseif ($row['status'] === 'deleted'): ?>

                                                        <span class="badge bg-dark">
                                                            Silinib
                                                        </span>

                                                    <?php endif; ?>

                                                </td>

                                                <td>
                                                    <?= (int) $row['views'] ?>
                                                </td>

                                                <td>
                                                    <?php if ($row['status'] != 'expired' && $row['status'] != 'cancelled'): ?>
                                                        <a href="./edit-tender?t=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <a href="javascript:void(0)"
                                                        class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-id="<?= $row['tender_id']; ?>" title="Tenderi sil"><i
                                                            class="feather-trash-2"></i></a>
                                                    </a>
                                                </td>


                                            </tr>

                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="blog-pagination">
                                <nav>
                                    <ul class="pagination">

                                        <?php if ($page > 1): ?>
                                            <li class="page-item previtem">
                                                <a class="page-link"
                                                    href="?p=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&s=<?= $sort ?>">
                                                    Əvvəlki
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <li class="justify-content-center pagination-center">
                                            <div class="pagelink">
                                                <ul>

                                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                            <a class="page-link"
                                                                href="?p=<?= $i ?>&q=<?= urlencode($search) ?>&s=<?= $sort ?>">
                                                                <?= $i ?>
                                                            </a>
                                                        </li>

                                                    <?php endfor; ?>

                                                </ul>
                                            </div>
                                        </li>

                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item nextlink">
                                                <a class="page-link"
                                                    href="?p=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&s=<?= $sort ?>">
                                                    Növbəti
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>
                                </nav>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Dashboard Content -->

        <?php include('./inc/alert_modal.php'); ?>


        <!-- Footer -->
        <?php require_once "./inc/footer.php"; ?>
        <!-- /Footer -->

    </div>

    <!-- scrollToTop start -->
    <div class="progress-wrap active-progress">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;">
            </path>
        </svg>
    </div>
    <!-- scrollToTop end -->



    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js" type="text/javascript"></script>

    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <!-- Aos -->
    <script src="assets/plugins/aos/aos.js"></script>

    <!-- Top JS -->
    <script src="assets/js/backToTop.js"></script>

    <!-- Datatables JS -->
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"
        type="efe53e190f11ea9b32c5018d-text/javascript"></script>
    <script src="assets/plugins/datatables/datatables.min.js"></script>

    <!-- Fearther JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <script src="js/alert-modal.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el)
    })
</script>

<script>
    document.getElementById("sortSelect").addEventListener("change", function () {

        const form = document.getElementById("searchForm");

        const params = new URLSearchParams(new FormData(form));

        params.set("s", this.value);
        params.set("p", 1);

        window.location.search = params.toString();

    });
</script>


<script>
    const searchInput = document.getElementById("searchInput");
    const clearBtn = document.getElementById("clearSearch");

    if (searchInput.value.length) { clearBtn.style.display = "block"; }

    clearBtn.addEventListener("click", function () {

        searchInput.value = "";

        const params = new URLSearchParams(window.location.search);

        params.delete("q");
        params.set("p", 1);

        window.location.search = params.toString();

    });
</script>




<script>
    document.addEventListener("DOMContentLoaded", () => {

        const btn = document.getElementById("logoutBtn");
        if (!btn) return;

        btn.addEventListener("click", async (e) => {
            e.preventDefault();

            try {
                const res = await fetch("api/auth/logout.php", {
                    method: "POST",
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                });

                const data = await res.json();

                if (data.ok) {
                    window.location.href = "./login";
                }

            } catch (err) {
                showError("Logout zamanı xəta baş verdi.");
            }
        });

    });
</script>


<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {

        btn.addEventListener('click', async function () {

            let id = this.dataset.id;


            console.log("clicked id:", id);

            const confirmed = await Swal.fire({
                title: "Diqqət",
                text: "Bu tenderi silmək istədiyinizə əminsiniz?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sil",
                confirmButtonColor: "#ff0000",
                cancelButtonColor: "#2563EB",
                cancelButtonText: "Ləğv et"
            });

            if (!confirmed.isConfirmed) {
                return;
            }

            const tender_card = this.closest(".tender-item");

            fetch('<?= $base_url ?>/api/delete/tender.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(id)
            })
                .then(res => res.json())
                .then(data => {

                    if (data.success) {
                        showError("Tender uğurla silindi");
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                    } else {
                        showError(data.error);
                    }

                })
                .catch(() => {
                    console.error(err);
                    showError("Server xətası baş verdi");
                });

        });

    });
</script>

</html>