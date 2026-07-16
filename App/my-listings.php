<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_role('partner');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once "./inc/head.php"; ?>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">

	<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

	<style>
		.listing-slider img {
			height: 100px;
			object-fit: cover;
			width: 100%;
			border-radius: 8px;
		}

		.swiper-pagination {
			position: unset !important;
		}

		.swiper-pagination-bullet {
			background-color: #c10037;
			filter: drop-shadow(1px 1px 3px #fff);
			height: var(--swiper-pagination-bullet-height, var(--swiper-pagination-bullet-size, 4px)) !important;
		}
	</style>
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
					<?php include  "./inc/dashboard_menus.php"; ?>
				</div>
				<div class="dash-listingcontent dashboard-info">
					<div class="dash-cards card">
						<div class="card-header">
							<h4>Elanlarım</h4>

						</div>
						<div class="card-body">
							<div class="listing-search">
								<div class="filter-content form-set">
									<form method="GET" id="searchForm">

										<?php
										$sort = $_GET['s'] ?? 'new';

										switch ($sort) {

											case 'old':
												$order = "l.id ASC";
												break;

											case 'az':
												$order = "l.title ASC";
												break;

											case 'za':
												$order = "l.title DESC";
												break;

											default:
												$order = "l.id DESC";

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
								<table class="listing-table datatable" id="listdata-table">
									<thead>
										<tr>
											<th class="no-sort">Şəkil</th>
											<th class="no-sort">Detallar</th>
											<th>Status</th>
											<th class="no-sort">Baxış</th>
											<th class="no-sort">Əməliyyat</th>
										</tr>
									</thead>
									<tbody>

										<?php
										$search = $_GET['q'] ?? '';
										$where = "WHERE l.customer_id = ?";
										$params = [$_SESSION['customer_id']];

										if ($search != '') {

											$where .= " AND (
												l.title LIKE ?
												OR cmain.name LIKE ?
												OR cmid.name LIKE ?
												OR csub.name LIKE ?
												)";

											$searchParam = "%$search%";

											$params[] = $searchParam;
											$params[] = $searchParam;
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
											SELECT COUNT(DISTINCT l.id)

											FROM listings l

											LEFT JOIN categories csub ON csub.id = l.category_id
											LEFT JOIN categories cmid ON cmid.id = csub.parent_id
											LEFT JOIN categories cmain ON cmain.id = cmid.parent_id

											$where
											");

										$countSt->execute($params);

										$totalListings = $countSt->fetchColumn();

										$totalPages = ceil($totalListings / $limit);

										$st = $pdo->prepare("
										SELECT 
										l.id as lid,
										l.title,
										l.category_id,
										l.type_id,
										l.sale_mode_id,
										l.price,
										l.old_price,
										l.currency,
										l.slug,
										l.views,
										l.status,
										l.created_at,

										cmain.name AS main_category,
										cmid.name AS mid_category,
										csub.name AS sub_category,

										pl.listing_id AS isPro,

										GROUP_CONCAT(DISTINCT lc.country_id) AS countries,
										(
											SELECT GROUP_CONCAT(li2.image_path ORDER BY li2.sort_order ASC)
											FROM listing_images li2
											WHERE li2.listing_id = l.id
											LIMIT 5
										) AS images

										FROM listings l

										LEFT JOIN categories csub
										ON csub.id = l.category_id

										LEFT JOIN categories cmid
										ON cmid.id = csub.parent_id

										LEFT JOIN categories cmain
										ON cmain.id = cmid.parent_id

										LEFT JOIN listing_countries lc
										ON lc.listing_id = l.id

										LEFT JOIN listing_images li
										ON li.listing_id = l.id

										LEFT JOIN premium_listings pl
										ON pl.listing_id = l.id

										$where

										GROUP BY l.id
										ORDER BY $order
										LIMIT $limit OFFSET $offset
										");

										$st->execute($params);



										while ($row = $st->fetch(PDO::FETCH_ASSOC)) {

											$countries = explode(",", $row['countries']);
											$images = explode(",", $row['images']); 
											?>

										
  

											<tr>
												<td>
													<div class="listingtable-img">
														<a href="listing/<?= urlencode($row['slug']) ?>/<?= (int)$row['lid'] ?>">
															<div class="swiper listing-slider" style="box-shadow: 2px 2px 7px #e59e9ead;
																		   border-radius: 5px;">

																<div class="swiper-wrapper">

																	<?php foreach ($images as $img) { ?>

																		<div class="swiper-slide">
																			<img class="img-fluid avatar-img"
																				src="assets/img/uploads/listings/<?= $img; ?>"
																				alt="">
																		</div>

																	<?php } ?>

																</div>

																<div class="swiper-pagination"></div>

															</div>
														</a>
													</div>
												</td>
												<td>
													<h6><a
															href="listing/<?= urlencode($row['slug']) ?>/<?= (int)$row['lid'] ?>">
															<?= htmlentities($row['title']); ?>
														</a>
													</h6>
													<div class="listingtable-rate">
														<a href="javascript:void(0)" class="cat-icon">
															<?= htmlentities($row['main_category'] . " / " . $row['mid_category'] . " / " . $row['sub_category']); ?>
														</a>
														<p class="discount-amt">
															<?php if ($row['old_price'] > 0): ?>
																<small class="fixed-amt"
																	style="font-size: 12px; margin-right: 7px;">
																	<del><?= htmlentities($row['old_price'] . " " . $row['currency']); ?></del></small>
															<?php endif; ?>
															<?= htmlentities($row['price'] . " " . $row['currency']); ?>
														</p>
													</div>
													<p style="font-size: 11px; margin-top: 10px;">
														<i><?= date('d.m.y h:i', strtotime($row['created_at'])); ?></i>
													</p>
												</td>
												<td>
													<?php
													if ($row['status'] == 'draft') {
														echo '<span style="display: flex !important; justify-content: center; align-items: center; color: #fff; width: 120px !important; height: 38px; border-radius: 6px; background: #ff8b52;">Qaralamada</span>';
													} elseif ($row['status'] == 'moderation') {
														echo '<span style="display: flex !important; justify-content: center; align-items: center; color: #fff; width: 120px !important; height: 38px; border-radius: 6px; background: #4287f5;">Yoxlamada</span>';
													} elseif ($row['status'] == 'active') {
														echo '<span style="display: flex !important; justify-content: center; align-items: center; color: #fff; width: 120px !important; height: 38px; border-radius: 6px; background: #00a323;">Aktiv</span>';
													} elseif ($row['status'] == 'rejected') {
														echo '<span style="display: flex !important; justify-content: center; align-items: center; color: #fff; width: 120px !important; height: 38px; border-radius: 6px; background: #ff0000;">Rədd edilmiş</span>';
													} elseif ($row['status'] == 'expired') {
														echo '<span style="display: flex !important; justify-content: center; align-items: center; color: #fff; width: 120px !important; height: 38px; border-radius: 6px; background: #7d7d7d;">Vaxtı bitmiş</span>';
													} ?>
												</td>
												<td><Span class="views-count"><?= htmlentities($row['views']); ?></span>
												</td>
												<td>
													<div class="action">
														<a href="./payment-pro?l=<?= $row['lid']; ?>" data-bs-toggle="tooltip"
															data-bs-placement="top" title="Premium et"
															class="action-btn btn btn-warning"
															<?php if($row['isPro']){
																echo 'style="background: orange; color: #fff;"';
															}
															?>
															><i
																class="fa-solid fa-gem"></i>
														</a>
														<a href="listing/<?= urlencode($row['slug']) ?>/<?= (int)$row['lid'] ?>" class="action-btn btn-view"
															data-bs-toggle="tooltip" data-bs-placement="top"
															title="Elana bax"><i class="feather-eye"></i></a>
														<a href="./edit-listing?l=<?= $row['lid']; ?>"
															data-bs-toggle="tooltip" data-bs-placement="top"
															title="Düzəliş et" class="action-btn btn-edit"><i
																class="feather-edit-3"></i></a>
														<a href="javascript:void(0)" class="action-btn btn-trash"
															data-bs-toggle="tooltip" data-bs-placement="top"
															data-id="<?= $row['lid']; ?>" title="Elanı sil"><i
																class="feather-trash-2"></i></a>

													</div>
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
	<?php include('./inc/alert_modal.php'); ?>

</body>

<script>
	document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
		new bootstrap.Tooltip(el)
	})

	new Swiper(".listing-slider", {
		slidesPerView: 1,
		spaceBetween: 0,
		loop: true,

		pagination: {
			el: ".swiper-pagination",
			clickable: true
		}
	});
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
	document.querySelectorAll('.btn-trash').forEach(btn => {
		btn.addEventListener('click', function () {

			let id = this.dataset.id;

			if (!confirm("Bu elanı silmək istədiyinizə əminsiniz?")) {
				return;
			}

			fetch('api/delete/listing.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: 'id=' + id
			})
				.then(res => res.json())
				.then(data => {

					if (data.success) {
						location.reload();
					} else {
						showError(data.error);
					}

				});

		});
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

</html>