<?php
require_once __DIR__ . "/inc/config.php";
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php
	$pageType = 'listings';

	// $metaData = [
	// 	'title' => $listing['title'],
	// 	'description' => $listing['listing_desc'],
	// 	'slug' => $listing['listing_slug'],
	// 	'id' => $listing['lid']
	// ];
	
	?>

	<?php require_once __DIR__ . '/inc/head.php'; ?>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">

	<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

	<style>
		.listing-slider img {
			height: 170px;
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

	<style>
		.sticky-banner {
			position: sticky;
			top: 129px;
			height: calc(100vh - 129px);
			background-size: contain;
			background-position: center;
			background-repeat: no-repeat;
		}
	</style>
</head>

<body>

	<div class="main-wrapper home-nine">
		<!-- Header -->
		<?php require_once __DIR__ . '/inc/header.php'; ?>
		<!-- /Header -->


		<!-- Breadscrumb Section -->
		<div class="breadcrumb-bar">
			<div class="container">
				<div class="row align-items-center text-center">
					<div class="col-md-12 col-12">
						<h3 class="breadcrumb-title">Axtarış nəticəsi</h3>

					</div>
				</div>
			</div>
		</div>
		<!-- /Breadscrumb Section -->

		<!-- Main Content Section -->
		<div class="list-content">
			<div class="container">
				<div class="row">

					<!-- Sidebar yeri -->

					<div class="col-lg-12">
						<?php

						function getInt($key)
						{
							return (isset($_REQUEST[$key]) && $_REQUEST[$key] !== '')
								? (int) $_REQUEST[$key]
								: null;
						}

						$path = $_GET['path'] ?? '';
						$segments = array_values(array_filter(explode('/', $path)));

						$min_price = getInt('min_price');
						$max_price = getInt('max_price');
						$lt = getInt('lt');
						$sm = getInt('sm');
						$lts = $_GET['lts'] ?? null;
						$sms = $_GET['sms'] ?? null;
						$country = getInt('country');
						$text = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';

						$limit = 8;
						$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
						if ($page < 1)
							$page = 1;

						$offset = ($page - 1) * $limit;

						// --------------------
						// FILTERS
						// --------------------
						
						$where = "WHERE l.status = 'active' AND m.is_active = 1 AND plc.listing_id IS NOT NULL";
						$baseParams = [];

						if (!empty($segments)) {
							$fullPath = implode('/', $segments);

							// Əgər category slug_path uyğun gəlirsə
							$where .= " AND ct.slug_path LIKE :slug_path";
							$baseParams[':slug_path'] = $path . '%';
						}



						if ($lt) {
							$where .= " AND lt.id = :lt";
							$baseParams[':lt'] = $lt;
						}

						if ($sm) {
							$where .= " AND sm.id = :sm";
							$baseParams[':sm'] = $sm;
						}


						if ($lts) {
							$where .= " AND lt.slug = :lts";
							$baseParams[':lts'] = $lts;
						}

						if ($sms) {
							$where .= " AND sm.slug = :sms";
							$baseParams[':sms'] = $sms;
						}

						if ($country) {
							$where .= " AND ctry.id = :country";
							$baseParams[':country'] = $country;
						}

						if ($text !== '') {
							$where .= " AND (l.title LIKE :text1 OR l.description LIKE :text2)";
							$baseParams[':text1'] = "%$text%";
							$baseParams[':text2'] = "%$text%";
						}

						if ($min_price !== null) {
							$where .= " AND l.price >= :min_price";
							$baseParams[':min_price'] = $min_price;
						}

						if ($max_price !== null) {
							$where .= " AND l.price <= :max_price";
							$baseParams[':max_price'] = $max_price;
						}

						// --------------------
						// USER COUNTRY FILTER
						// --------------------
						
						$stCountry = $pdo->prepare("SELECT id FROM countries WHERE iso2 = ?");
						$stCountry->execute([$user_country_code]);
						$user_country_id = $stCountry->fetchColumn();

						if ($user_country_id) {
							$where .= " AND lc.country_id = :user_country";
							$baseParams[':user_country'] = (int) $user_country_id;
						}

						// --------------------
						// CUSTOMER
						// --------------------
						
						$customerId = $_SESSION['customer_id'] ?? 0;

						// --------------------
						// SORT
						// --------------------
						
						$sort = $_GET['sort'] ?? 'new';

						switch ($sort) {
							case 'expensive':
								$orderBy = "l.price DESC";
								break;
							case 'cheap':
								$orderBy = "l.price ASC";
								break;
							case 'name':
								$orderBy = "l.title ASC";
								break;
							case 'popular':
								$orderBy = "l.views DESC";
								break;
							default:
								$orderBy = "l.id DESC";
						}

						// =====================================================
						// ✅ COUNT QUERY (NO fav JOIN → NO cust_id)
						// =====================================================
						
						$countSql = "
						SELECT COUNT(DISTINCT l.id)
						FROM listings l

						INNER JOIN premium_listings plc 
							ON plc.listing_id = l.id 
							AND plc.expires_at > NOW()

						INNER JOIN listing_countries lc 
							ON lc.listing_id = l.id

						LEFT JOIN markets m ON m.id = l.market_id
						LEFT JOIN countries ctry ON ctry.id = m.country_id

						LEFT JOIN listing_types lt 
								ON lt.id = l.type_id

						LEFT JOIN sale_modes sm
								ON sm.id = l.sale_mode_id

						LEFT JOIN categories ct 
								ON ct.id = l.category_id

						$where
					";

						$countSt = $pdo->prepare($countSql);
						$countSt->execute($baseParams);
						$total = $countSt->fetchColumn();

						// =====================================================
						// ✅ LIST QUERY (fav istifadə edir → cust_id əlavə olunur)
						// =====================================================
						
						$listParams = $baseParams;
						$listParams[':cust_id'] = $customerId;

						$listSql = "
							SELECT 
								l.id,
								l.slug,
								l.title,
								l.price,
								l.old_price,
								l.currency,
								l.description,
								l.views,
								l.created_at,

								l.id AS lid,

								(fav.listing_id IS NOT NULL) AS is_fav,

								m.name AS market_name,
								m.id AS market_id,
								m.logo AS market_logo,

								ct.slug_path,
								lt.slug AS listing_type_slug,
								sm.slug AS sale_mode_slug,

								lt.name AS listing_type_name,
								lt.slug AS listing_type_slug,
								lt.id,

								sm.name AS sale_mode_name,
								sm.slug AS sale_mode_slug,
								sm.id AS sale_mode_id,

								ctry.name AS country_name,
								ctry.iso2 AS country_code,
								city.name AS city_name,


								GROUP_CONCAT(DISTINCT li.image_path ORDER BY li.sort_order) AS images,

								ROUND(AVG(lr.rating),1) AS rating_avg,
								COUNT(lr.id) AS rating_count

							FROM listings l

							INNER JOIN premium_listings plc 
								ON plc.listing_id = l.id 
								AND plc.expires_at > NOW()

							INNER JOIN listing_countries lc 
								ON lc.listing_id = l.id

							LEFT JOIN listing_types lt 
								ON lt.id = l.type_id

							LEFT JOIN sale_modes sm 
								ON sm.id = l.sale_mode_id

							LEFT JOIN categories ct 
								ON ct.id = l.category_id

							LEFT JOIN markets m ON m.id = l.market_id
							LEFT JOIN countries ctry ON ctry.id = m.country_id
							LEFT JOIN cities city ON city.id = m.city_id

							LEFT JOIN listing_images li ON li.listing_id = l.id

							LEFT JOIN favorites fav 
								ON fav.listing_id = l.id 
								AND fav.customer_id = :cust_id

							LEFT JOIN listings_reviews lr 
								ON lr.listing_id = l.id

							$where

							GROUP BY l.id

							ORDER BY $orderBy

							LIMIT $limit OFFSET $offset
						";

						$st = $pdo->prepare($listSql);
						$st->execute($listParams);
						$listings = $st->fetchAll(PDO::FETCH_ASSOC);

						$catMap = [];

						foreach ($cats as $c) {
							$catMap[$c['slug']] = $c['name'];
						}


						function getRates()
						{
							$xml = simplexml_load_file("https://www.cbar.az/currencies/" . date('d.m.Y') . ".xml");

							$rates = [
								'AZN' => 1 // baza
							];

							foreach ($xml->ValType as $type) {
								foreach ($type->Valute as $valute) {
									$code = (string) $valute['Code'];
									$value = (float) $valute->Value;

									$rates[$code] = $value;
								}
							}

							return $rates;
						}

						function convertCurrency($amount, $from, $to, $rates)
						{
							if ($from === $to)
								return $amount;

							if (!isset($rates[$from]) || !isset($rates[$to])) {
								return null; // unsupported currency
							}

							// from → AZN
							$azn = $amount * $rates[$from];

							// AZN → to
							return $azn / $rates[$to];
						}

						$rates = getRates();



						?>

						<div class="row sorting-div">
							<div class="col-lg-4 col-md-4 col-sm-5 col-12 align-items-center d-flex">

								<?php
								$start = $offset + 1;
								$end = $offset + count($listings);

								// əgər nəticə yoxdursa
								if ($total === 0) {
									$start = 0;
									$end = 0;
								}

								// limitdən artıq çıxmasın
								if ($end > $total) {
									$end = $total;
								}
								?>
								<div class="count-search">
									<p><?= $total ?> nəticədən <span><?= $start ?>-<?= $end ?></span></p>
								</div>

							</div>
							<div class="col-lg-8 col-md-8 col-sm-7 col-12 align-items-center">
								<div class="sortbyset">
									<span class="sortbytitle">Sıralamaq:</span>
									<form method="GET">
										<div class="sorting-select">
											<select name="sort" class="form-control select" id="sortSelect">
												<option value="expensive" <?= $sort === 'expensive' ? 'selected' : '' ?>>
													Əvvəlcə baha</option>
												<option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Ada
													görə</option>
												<option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>
													Əvvəlcə yeni</option>
												<option value="cheap" <?= $sort === 'cheap' ? 'selected' : '' ?>>
													Əvvəlcə ucuz</option>
												<option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>
													Əvvəlcə populyar</option>
											</select>
										</div>
									</form>
								</div>

							</div>
						</div>
						<div class="grid-view listgrid-sidebar">
							<div class="row" id="listingContainer">


								<?php foreach ($listings as $row): ?>
									<?php $images = !empty($row['images']) ? explode(",", $row['images']) : []; ?>

									<?php
									$slugPath = $row['slug_path'] ?? '';

									if (!is_string($slugPath) || $slugPath === '') {
										$slugs = [];
									} else {
										$slugs = explode('/', $slugPath);
									}
									$paths = [];
									$current = '';
									foreach ($slugs as $slug) {
										$current .= ($current ? '/' : '') . $slug;
										$paths[] = $current;
									}



									$names = [];

									foreach ($slugs as $slug) {
										$names[] = $catMap[$slug] ?? $slug;
									}
									?>

									<?php
									$stmt = $pdo->prepare("
                            SELECT currency_code 
                            FROM countries 
                            WHERE iso2 = ?
                        ");

									$stmt->execute([$user_country_code]);
									$userCurrency = $stmt->fetchColumn();

									$converted = convertCurrency(
										$row['price'],
										$row['currency'],   // məsələn USD
										$userCurrency,       // məsələn EUR
										$rates
									);

									if (!$converted) {
										$converted = $row['price'];
										$userCurrency = 'USD';
									}
									?>


									<div class="col-lg-3 col-md-6 listing-item" data-id="<?= (int) $row['lid'] ?>">
										<div class="card">
											<div class="blog-widget">
												<div class="blog-img" style="height: 190px">
													<a
														href="listing/<?= urlencode($row['slug']) ?>/<?= (int) $row['lid'] ?>">
														<div class="swiper listing-slider">

															<div class="swiper-wrapper">

																<?php foreach ($images as $img): ?>

																	<div class="swiper-slide">
																		<img class="img-fluid avatar-img"
																			style="border: 1px solid gray"
																			src="<?= $base_url ?>/assets/img/uploads/listings/<?= $img; ?>"
																			alt="">
																	</div>

																<?php endforeach; ?>

															</div>

															<div class="swiper-pagination"></div>

														</div>
													</a>
													<div class="fav-item">
														<?php
														$isFav = false;
														$isFav = (bool) $row['is_fav'];

														?>
														<button type="button" class="fav-icon toggle-fav"
															data-id="<?= (int) $row['lid'] ?>">
															<?= $isFav ? '<i class="fa-solid fa-heart text-danger"></i>' : '<i class="fa-regular fa-heart"></i>' ?>

														</button>
													</div>
												</div>
												<div class="bloglist-content">
													<div class="card-body" style="padding: 5px">
														<div class="blogfeaturelink">

															<div class="blog-features" style="height: 40px;">
																<span style="font-size: 10px !important;">

																	<?php foreach ($names as $i => $name): ?>

																		<a href="<?= $base_url ?>/listings/<?= $paths[$i] ?>">
																			<?= htmlspecialchars($name) ?>
																		</a>

																		<?php if ($i < count($names) - 1): ?>
																			/
																		<?php endif; ?>

																	<?php endforeach; ?>

																</span>
															</div>

														</div>
														<h6 style="margin: 0; height: 55px;">
															<a
																href="listing/<?= urlencode($row['slug']) ?>/<?= (int) $row['lid'] ?>">
																<?= htmlspecialchars(mb_strimwidth($row['title'], 0, 110, '...')) ?>
															</a>
														</h6>




														<div class="amount-details">
															<div class="amount">
																<span class="validrate">
																	<?= number_format($converted, 2) . ' ' . $userCurrency ?>
																</span>

																<?php
																if ($row['old_price'] != 0) {
																	$convertedOld = convertCurrency(
																		$row['old_price'],
																		$row['currency'],
																		$userCurrency,
																		$rates
																	);

																	if (!$convertedOld) {
																		$convertedOld = $row['old_price'];
																	}


																	echo '<span>' . number_format($convertedOld, 2) . ' ' . $userCurrency . '</span>';
																}
																?>

															</div>

															<a href="<?= $base_url ?>/listings?lts=<?= $row['listing_type_slug'] ?>"
																style="color: #2563EB">
																<i class="fa-solid fa-star"></i>
																<?= htmlspecialchars($row['listing_type_name']) ?>
															</a>

														</div>

														<div class="blog-location-details d-block">


															<div class="detail-btm-blk"
																style="display:flex; justify-content: space-between; align-items: center; margin-top: 15px; font-size: 12px;">

																<div style="display:flex; gap:7px; align-items: center;">
																	<div class="rating-badge" style="display: flex;
																		gap: 6px;
																		justify-content: center;
																		align-items: center;
																		border-radius: 20px;
																		background:red;
																		color:#fff;
																		width:50px;
																		height: 22px;">
																		<i class="fa-solid fa-star"></i>
																		<?= $row['rating_avg'] ?? '0.0' ?>
																	</div>

																	<small>(
																		<?= $row['rating_count'] ?> rəy)
																	</small>
																</div>

																<div class="blog-author text-end">
																	<span> <i class="feather-eye"></i>
																		<?= $row['views']; ?>
																	</span>
																</div>


															</div>



															<span class="room-type" style="display: flex;
																									justify-content: center;
																									margin-top: 8px;
																									font-size: 11px;">
																<?= $row['country_code'] ?>,
																<?= $row['city_name'] ?>,
																<?= date("d.m.y / H:i", strtotime($row['created_at'])) ?>
															</span>

														</div>

													</div>
												</div>
											</div>
										</div>
									</div>

								<?php endforeach; ?>

							</div>

							<button
								style="z-index: 5000; position: absolute; margin-bottom: 100px; left: 50%; transform: translateX(-50%);"
								class="btn btn-primary" id="loadMoreBtn">Daha çox göstər</button>
						</div>


					</div>
				</div>
			</div>
		</div>
		<!-- /Main Content Section -->

		<!-- Footer -->
		<?php require_once __DIR__ . '/inc/footer.php'; ?>
		<!-- /Footer -->


		<?php include('./inc/alert_modal.php'); ?>



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
	<script src="assets/js/bootstrap.bundle.min.js"></script>


	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- counterup JS -->
	<script src="assets/js/jquery.waypoints.js"></script>
	<script src="assets/js/jquery.counterup.min.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>


	<script src="js/alert-modal.js"></script>


	<script>
		document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
			new bootstrap.Tooltip(el)
		})

		function initSliders(scope = document) {
			scope.querySelectorAll(".listing-slider").forEach(el => {
				// əgər artıq init olunubsa skip et
				if (el.classList.contains("swiper-initialized")) return;

				new Swiper(el, {
					slidesPerView: 1,
					spaceBetween: 0,
					loop: true,
					pagination: {
						el: el.querySelector(".swiper-pagination"),
						clickable: true
					}
				});
			});
		}

		initSliders();
	</script>

	<script>
		let page = 1;
		const btn = document.getElementById("loadMoreBtn");
		const container = document.getElementById("listingContainer");

		btn.addEventListener("click", () => {
			page++;

			fetch(`?page=${page}`)
				.then(res => res.text())
				.then(html => {

					let doc = new DOMParser().parseFromString(html, "text/html");
					let items = doc.querySelectorAll(".feature-rent");

					if (items.length === 0) {
						btn.disabled = true;
						btn.innerText = "Hamısı göstərildi";
						return;
					}

					items.forEach(el => {
						container.appendChild(el);
						initSliders();
					});

				});
		});
	</script>

	<script>
		$('#sortSelect').on('change', function () {
			const url = new URL(window.location.href);

			url.searchParams.set('sort', this.value);

			// pagination sıfırlansın (vacib!)
			url.searchParams.delete('page');

			window.location.href = url.toString();
		});
	</script>

	<script>
		document.addEventListener("click", async (e) => {
			const btn = e.target.closest(".toggle-fav");
			if (!btn) return;

			e.preventDefault();

			const listingId = btn.dataset.id;
			const icon = btn.querySelector("i");


			try {
				const res = await fetch("<?= $base_url; ?>/api/send/favorites.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ listing_id: listingId })
				});

				const data = await res.json();

				// LOGIN YOXDUR
				if (data.status === "not_logged_in") {
					showError(data.message || "Davam etmək üçün daxil olun");

					return;
				}

				// ƏLAVƏ OLUNDU
				if (data.status === "added") {
					icon.classList.remove("fa-regular");
					icon.classList.add("fa-solid", "text-danger");

					showError(data.message);
				}

				// SİLİNDİ
				else if (data.status === "removed") {
					icon.classList.remove("fa-solid", "text-danger");
					icon.classList.add("fa-regular");



					showError(data.message || "SEÇİLMİŞLƏRDƏN silindi");
				}

				else {
					showError(data.message || "Xəta baş verdi");
				}

			} catch (err) {
				console.error(err);
				showError("Server xətası");
			}
		});
	</script>
</body>

</html>