<?php require_once("inc/config.php"); require_once("inc/admin_auth.php"); require_admin_permission($pdo, "package"); ?>

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
                            <h2 class="fw-semibold fs-7">Abunəlik paketləri</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Ana Səhifə</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Abunəlik paketləri</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="package-features.php" class="btn btn-warning d-flex align-items-center gap-2">
                                <i class="fa-solid fa-gears"></i>
                                Limit və xüsusiyyətlər
                            </a>

                            <a href="package-add.php" class="btn btn-primary d-flex align-items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 1V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Paket əlavə et
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
                                                <svg width="24" height="18" viewBox="0 0 24 18" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M21.2653 10.9126L20.8077 11.5068H20.8077L21.2653 10.9126ZM22.6544 13.8691L22.0721 13.3965H22.0721L22.6544 13.8691ZM20.774 14.25C20.3598 14.25 20.024 14.5858 20.024 15C20.024 15.4142 20.3598 15.75 20.774 15.75V14.25ZM19.0676 9.25305C18.655 9.21573 18.2904 9.5199 18.253 9.93242C18.2157 10.345 18.5199 10.7096 18.9324 10.7469L19.0676 9.25305ZM2.73465 10.9126L3.19224 11.5068H3.19224L2.73465 10.9126ZM1.34555 13.8691L1.92788 13.3965H1.92788L1.34555 13.8691ZM3.22596 15.75C3.64017 15.75 3.97596 15.4142 3.97596 15C3.97596 14.5858 3.64017 14.25 3.22596 14.25V15.75ZM5.06757 10.7469C5.4801 10.7096 5.78427 10.345 5.74695 9.93242C5.70963 9.5199 5.34495 9.21573 4.93242 9.25305L5.06757 10.7469ZM5.5 8.75C5.91421 8.75 6.25 8.41421 6.25 8C6.25 7.58579 5.91421 7.25 5.5 7.25V8.75ZM5.5 3.75C5.91421 3.75 6.25 3.41421 6.25 3C6.25 2.58579 5.91421 2.25 5.5 2.25V3.75ZM8.08377 12.1112L7.68933 11.4733L8.08377 12.1112ZM15.9162 12.1112L16.3106 11.4733L15.9162 12.1112ZM6.01467 15.6474L5.48718 16.1806V16.1806L6.01467 15.6474ZM17.9853 15.6474L17.4578 15.1143V15.1143L17.9853 15.6474ZM20.8077 11.5068C21.2273 11.8299 21.7171 12.1397 22.0394 12.5477C22.1874 12.7351 22.2403 12.8758 22.2487 12.9752C22.2551 13.051 22.2443 13.1844 22.0721 13.3965L23.2368 14.3418C23.6123 13.8791 23.7875 13.3714 23.7433 12.8489C23.7012 12.3499 23.4669 11.935 23.2164 11.6179C22.7418 11.0171 21.9894 10.5235 21.7229 10.3184L20.8077 11.5068ZM22.0721 13.3965C21.5767 14.0069 21.1735 14.25 20.774 14.25V15.75C21.8731 15.75 22.6619 15.0501 23.2368 14.3418L22.0721 13.3965ZM18.9324 10.7469C19.5727 10.8049 20.2174 11.0522 20.8077 11.5068L21.7229 10.3184C20.9186 9.69902 20.0061 9.33796 19.0676 9.25305L18.9324 10.7469ZM19.75 5.5C19.75 6.4665 18.9665 7.25 18 7.25V8.75C19.7949 8.75 21.25 7.29493 21.25 5.5H19.75ZM18 3.75C18.9665 3.75 19.75 4.5335 19.75 5.5H21.25C21.25 3.70507 19.7949 2.25 18 2.25V3.75ZM2.27705 10.3184C2.01061 10.5235 1.25814 11.0171 0.783541 11.6179C0.533058 11.935 0.298819 12.3499 0.256646 12.8489C0.212479 13.3714 0.387655 13.8791 0.763212 14.3418L1.92788 13.3965C1.75571 13.1844 1.74491 13.051 1.75132 12.9752C1.75972 12.8758 1.8126 12.7351 1.96058 12.5477C2.2829 12.1397 2.7727 11.8299 3.19224 11.5068L2.27705 10.3184ZM0.763212 14.3418C1.33807 15.0501 2.12686 15.75 3.22596 15.75V14.25C2.82645 14.25 2.42327 14.0069 1.92788 13.3965L0.763212 14.3418ZM4.93242 9.25305C3.9939 9.33796 3.08135 9.69902 2.27705 10.3184L3.19224 11.5068C3.7826 11.0522 4.42727 10.8049 5.06757 10.7469L4.93242 9.25305ZM2.25 5.5C2.25 7.29493 3.70508 8.75 5.5 8.75V7.25C4.5335 7.25 3.75 6.4665 3.75 5.5H2.25ZM5.5 2.25C3.70508 2.25 2.25 3.70507 2.25 5.5H3.75C3.75 4.5335 4.5335 3.75 5.5 3.75V2.25ZM8.47821 12.7491C10.6325 11.417 13.3674 11.417 15.5217 12.7491L16.3106 11.4733C13.6728 9.84224 10.3271 9.84224 7.68933 11.4733L8.47821 12.7491ZM8.81559 17.75H15.1843V16.25H8.81559V17.75ZM6.54215 15.1143C6.24786 14.8231 6.24425 14.6527 6.25178 14.5812C6.26373 14.4676 6.34381 14.2859 6.5888 14.0368C7.09833 13.5189 7.88621 13.1152 8.47821 12.7491L7.68933 11.4733C7.25955 11.739 6.19702 12.2962 5.51951 12.9849C5.17099 13.3391 4.82339 13.8221 4.76002 14.4242C4.69221 15.0683 4.96562 15.6646 5.48718 16.1806L6.54215 15.1143ZM15.5217 12.7491C16.1137 13.1152 16.9016 13.5189 17.4111 14.0368C17.6561 14.2859 17.7362 14.4676 17.7482 14.5812C17.7557 14.6527 17.7521 14.8231 17.4578 15.1143L18.5127 16.1806C19.0343 15.6646 19.3077 15.0683 19.2399 14.4242C19.1765 13.8221 18.8289 13.3391 18.4804 12.9849C17.8029 12.2962 16.7404 11.739 16.3106 11.4733L15.5217 12.7491ZM17.4578 15.1143C16.7252 15.839 16.0171 16.25 15.1843 16.25V17.75C16.5838 17.75 17.6511 17.033 18.5127 16.1806L17.4578 15.1143ZM5.48718 16.1806C6.3488 17.033 7.41613 17.75 8.81559 17.75V16.25C7.98284 16.25 7.27468 15.839 6.54215 15.1143L5.48718 16.1806ZM14.75 4.5C14.75 6.01878 13.5187 7.25 12 7.25V8.75C14.3472 8.75 16.25 6.84721 16.25 4.5H14.75ZM12 7.25C10.4812 7.25 9.24997 6.01878 9.24997 4.5H7.74997C7.74997 6.84721 9.65276 8.75 12 8.75V7.25ZM9.24997 4.5C9.24997 2.98122 10.4812 1.75 12 1.75V0.25C9.65276 0.25 7.74997 2.15279 7.74997 4.5H9.24997ZM12 1.75C13.5187 1.75 14.75 2.98122 14.75 4.5H16.25C16.25 2.15279 14.3472 0.25 12 0.25V1.75Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            Abunəlik paketləri
                                        </h3>
                                    </div>
                                    <div class="pure-card-body">

                                    <div class="alert alert-primary">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-circle-info"></i>
                                            <span class="text-custom-body">Paketləri sürükləyərək sıralaya bilərsiniz.</span>
                                        </div>
                                    </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="40">
                                                            <i class="fa-solid fa-arrows-up-down-left-right"></i>
                                                        </th>
                                                        <th scope="col" class="text-custom-paragraph fw-medium">
                                                            Paket adı
                                                        </th>
                                                        <th scope="col" class="text-custom-paragraph fw-medium">
                                                            Baza qiyməti
                                                        </th>
                                                        <th scope="col" class="text-custom-paragraph fw-medium">
                                                            Müddət
                                                        </th>
                                                        <th scope="col" class="text-custom-paragraph fw-medium">
                                                            Sıralama
                                                        </th>
                                                        <th scope="col" class="text-custom-paragraph fw-medium">
                                                            Status
                                                        </th>
                                                        <th scope="col" class="text-custom-paragraph fw-medium">
                                                            Əməliyyat
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sortable-packages">

                                                    <?php
$statement = $pdo->prepare("
    SELECT
        t1.*,
        COUNT(t2.id) AS feature_count
    FROM packages t1
    LEFT JOIN package_features t2 ON t1.id = t2.package_id
    GROUP BY t1.id
    ORDER BY t1.sort_order ASC
");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {

?>
                                                        <tr data-id="<?= $row['id']; ?>">
                                                            <td class="drag-handle text-center" style="cursor:move">
                                                                ☰
                                                            </td>
                                                            <td>
                                                                 <span class="text-custom-body fw-bolder"><?= $row['name']; ?></span>
                                                            </td>
                                                            
                                                            <td>
                                                                <span class="text-custom-body"><?= $row['base_price']; ?> $</span>
                                                            </td>
                                                            <td>
                                                                  <span class="text-custom-body"><?= $row['duration_days']; ?> gün</span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="text-custom-body"><?= $row['sort_order']; ?></span>
                                                            </td>
                                                            <td>
                                                                <?php if ($row['is_active'] == 1) { ?>
                                                                    <span class="badge bg-success">
                                                                        Aktiv
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-danger">
                                                                        Deaktiv
                                                                    </span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <a href="package-edit.php?pid=<?= $row['id']; ?>"
                                                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                                        type="button" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        data-bs-original-title="Redaktə et">
                                                                        <svg width="17" height="16" viewBox="0 0 17 16"
                                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M9.19922 2.39996L14.0992 7.29996"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linejoin="round" />
                                                                            <path d="M9.89941 15L15.4994 15"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round" />
                                                                        </svg>
                                                                    </a>
                                                                    <a href="api/delete/package.php?pid=<?= $row['id']; ?>"
                                                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill delete-btn"
                                                                        type="button" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        data-bs-original-title="Sil"
                                                                        data-package-name="<?= $row['name']; ?>">
                                                                        <svg width="15" height="16" viewBox="0 0 15 16"
                                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M13.0508 3.44995L12.617 10.4675C12.5061 12.2605 12.4507 13.1569 12.0013 13.8015C11.7791 14.1201 11.493 14.3891 11.1613 14.5912C10.4903 15 9.59207 15 7.7957 15C5.99696 15 5.09759 15 4.42612 14.5904C4.09414 14.3879 3.80798 14.1185 3.58586 13.7993C3.13659 13.1538 3.0824 12.256 2.97401 10.4606L2.55078 3.44995"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round" />
                                                                            <path d="M14.1 3.44998H1.5"
                                                                                stroke="currentColor" stroke-width="1.5"
                                                                                stroke-linecap="round" />
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
                                                            </td>
                                                        </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>

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

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    <script>
        new Sortable(document.getElementById('sortable-packages'), {
            animation: 150,
            handle: ".drag-handle",

            onEnd: function () {

                let order = [];

                document.querySelectorAll("#sortable-packages tr").forEach((row, index) => {
                    order.push({
                        id: row.dataset.id,
                        sort_order: index + 1
                    });
                });

                fetch("api/update/package_sort_order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(order)
                })
                .then(r => r.json())
                .then(res => {

                    if(res.status){

                        document.querySelectorAll("#sortable-packages tr").forEach((row,index)=>{
                            row.querySelector("td:nth-child(5)").innerHTML = index + 1;
                        });

                    }else{
                        alert("Sıralama yadda saxlanılmadı.");
                    }

                });

            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const deleteButtons = document.querySelectorAll(".delete-btn");

            deleteButtons.forEach(button => {

                button.addEventListener("click", function (e) {

                    e.preventDefault();

                    const deleteUrl = this.getAttribute("href");
                    const packageName = this.getAttribute('data-package-name');
                    const src = this.getAttribute('data-flag');

                    Swal.fire({
                        title: packageName,
                        text: "Bu paket silinəcək və geri qaytarılmayacaq!",
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
</body>

</html>