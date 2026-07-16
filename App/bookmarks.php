<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/inc/config.php";

require_once __DIR__ . "/api/data/user_data.php";
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once __DIR__ . '/inc/head.php'; ?>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">

	<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

	<style>
		.listing-slider img {
			height: 180px;
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

<body data-base-url="<?= $base_url ?>">>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once __DIR__ . '/inc/header.php'; ?>
		<!-- /Header -->


		<!-- Breadscrumb Section -->
		<div class="breadcrumb-bar">
			<div class="container">
				<div class="row align-items-center text-center">
					<div class="col-md-12 col-12">
						<h2 class="breadcrumb-title">Seçilmişlər</h2>
					</div>
				</div>
			</div>
		</div>
		<!-- /Breadscrumb Section -->

		<!-- Bookmark Content -->
		<div class="dashboard-content">
			<div class="container">

				<div class="bookmarks-content grid-view featured-slider">
					<div class="row" id="listingContainer">

						<?php
						if (empty($_SESSION['customer_id'])): ?>
							<div class="alert alert-danger d-flex justify-content-between align-items-center" role="alert">
								Giriş etmədən favoritlər görünmür
								<a class="btn btn-primary" href="<?= $base_url; ?>/login">Daxil ol</a>
							</div>
							<?php
							$listings = [];
						else:
							?>

							<?php

							$limit = 8;
							$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
							if ($page < 1)
								$page = 1;

							$offset = ($page - 1) * $limit;

							// 🔥 vacib
							$params = [];

							$customerId = $_SESSION['customer_id'] ?? 0;



							// 🔥 yalnız favoritlər
							$where = "WHERE l.status = 'active'";
							$params[':cust_id'] = $customerId;


							// ================= QUERY =================
						
							$st = $pdo->prepare("
									SELECT 
										l.id AS lid,
										l.slug,
										l.title,
										l.price,
										l.old_price,
										l.currency,
										l.description,
										l.views,
										l.created_at,

										fav.created_at,

										m.name AS market_name,
										m.id AS market_id,
										m.logo AS market_logo,

										ctry.name AS country_name,
										ctry.iso2 AS country_code,
										city.name AS city_name,

										l.main_category_id,
										l.mid_category_id,
										l.sub_category_id,

										lcm.name AS main_c_name,
										lcmid.name AS mid_c_name,
										lcs.name AS sub_c_name,

										GROUP_CONCAT(DISTINCT li.image_path ORDER BY li.sort_order) AS images,

										ROUND(AVG(lr.rating),1) AS rating_avg,
										COUNT(lr.id) AS rating_count

									FROM listings l

									-- 🔥 əsas filter: favoritlər
									INNER JOIN favorites fav 
										ON fav.listing_id = l.id 
										AND fav.customer_id = :cust_id

									-- premium varsa saxla, yoxsa sil
									LEFT JOIN premium_listings plc 
										ON plc.listing_id = l.id 
										AND plc.expires_at > NOW()

									LEFT JOIN markets m ON m.id = l.market_id
									LEFT JOIN countries ctry ON ctry.id = m.country_id
									LEFT JOIN cities city ON city.id = m.city_id

									LEFT JOIN listing_categories_main lcm ON lcm.id = l.main_category_id
									LEFT JOIN listing_categories_mid lcmid ON lcmid.id = l.mid_category_id
									LEFT JOIN listing_categories_sub lcs ON lcs.id = l.sub_category_id

									LEFT JOIN listing_images li ON li.listing_id = l.id
									LEFT JOIN listings_reviews lr ON lr.listing_id = l.id

									$where

									GROUP BY l.id

									ORDER BY fav.created_at DESC

									LIMIT $limit OFFSET $offset
								");



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


							// ================= EXECUTE =================
						
							try {
								$st->execute($params);
								$listings = $st->fetchAll(PDO::FETCH_ASSOC);
							} catch (PDOException $e) {
								echo "<pre>";
								echo $e->getMessage();
								print_r($params);
								exit;
							}

							?>

							<?php if (empty($listings)): ?>
								<p>Heç nə tapılmadı</p>
							<?php else: ?>
								<p>
									<span id="listCount"><?= count($listings) ?></span> nəticə tapıldı
								</p>
							<?php endif; ?>


							<?php foreach ($listings as $row): ?>
								<?php $images = !empty($row['images']) ? explode(",", $row['images']) : []; ?>


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


								<div class="col-lg-3 col-md-4 col-sm-6 listing-item" data-id="<?= (int) $row['lid'] ?>">
									<div class="card aos aos-init aos-animate" data-aos="fade-up" style="height: 400px;">
										<div class="blog-widget">
											<div class="blog-img">
												<a href="listing/<?= urlencode($row['slug']) ?>/<?= (int) $row['lid'] ?>">
													<div class="swiper listing-slider">

														<div class="swiper-wrapper">

															<?php foreach ($images as $img): ?>

																<div class="swiper-slide">
																	<img style="border: 1px solid gray"
																		src="assets/img/uploads/listings/<?= $img; ?>" alt="">
																</div>

															<?php endforeach; ?>

														</div>

														<div class="swiper-pagination"></div>

													</div>

												</a>
												<div class="fav-item" style="justify-content: end;">

													<button type="button" class="fav-icon remove-fav"
														data-id="<?= (int) $row['lid'] ?>">
														<i class="fa-solid fa-heart text-danger"></i>
													</button>
												</div>
											</div>
											<div class="bloglist-content">
												<div class="card-body" style="padding: 7px;">
													<div class="blogfeaturelink" style="height: 30px; margin: 8px;">

														<div class="blog-features">
															<span><a
																	href="<?= $base_url; ?>/listings?main=<?= $row['main_category_id'] ?>"><?= $row['main_c_name'] ?>
																	/ </a></span>
															<span><a
																	href="<?= $base_url; ?>/listings?main=<?= $row['main_category_id'] ?>&mid=<?= $row['mid_category_id'] ?>"><?= $row['mid_c_name'] ?>
																	/ </a></span>
															<span><a
																	href="<?= $base_url; ?>/listings?main=<?= $row['main_category_id'] ?>&mid=<?= $row['mid_category_id'] ?>&sub=<?= $row['sub_category_id'] ?>"><?= $row['sub_c_name'] ?></a></span>
														</div>

													</div>
													<h6 style="height: 50px;"><a style="font-size: 13px;"
															href="listing/<?= urlencode($row['slug']) ?>/<?= (int) $row['lid'] ?>"><?= htmlspecialchars($row['title']) ?></a>
													</h6>
													<div class="blog-location-details d-flex flex-wrap">
														<div class="location-info">
															<i class="feather-map-pin"></i> <?= $row['country_code'] ?>
															<?= $row['city_name'] ?>,

														</div>
														<br>
														<div class="location-info">
															<i class="fa-solid fa-calendar-days"></i>
															<?= date("d.m.y / H:i", strtotime($row['created_at'])) ?>
														</div>
													</div>
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


																echo '<br><span>' . number_format($convertedOld, 2) . ' ' . $userCurrency . '</span>';
															}
															?>
														</div>
														<div class="ratings">
															<span><i class="fa-solid fa-star"></i>
																<?= $row['rating_avg'] ?? '0.0' ?></span>
															(<?= $row['rating_count'] ?>)
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

							<?php endforeach; ?>


							<button class=" col-12 btn btn-primary" id="loadMoreBtn">Daha çox göstər</button>


						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
		<!-- /Bookmark Content -->

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
	<script src="<?= $base_url; ?>/assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="<?= $base_url; ?>/assets/js/bootstrap.bundle.min.js"></script>

	<!-- Select2 JS -->
	<script src="<?= $base_url; ?>/assets/plugins/select2/js/select2.min.js"></script>

	<!-- Aos -->
	<script src="<?= $base_url; ?>/assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="<?= $base_url; ?>/assets/js/backToTop.js"></script>

	<!-- Fearther JS -->
	<script src="<?= $base_url; ?>/assets/js/feather.min.js"></script>

	<!-- Custom JS -->
	<script src="<?= $base_url; ?>/assets/js/script.js"></script>

	<script src="<?= $base_url; ?>/js/alert-modal.js"></script>


	<script>
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
		document.addEventListener("click", async (e) => {

			const btn = e.target.closest(".remove-fav");

			if (!btn) return;

			e.preventDefault();
			e.stopPropagation();


			const listingId = btn.dataset.id;
			const card = btn.closest(".listing-item");

			if (!listingId || !card) return;

			// UX: loading
			card.style.opacity = "0.5";
			card.style.pointerEvents = "none";

			try {
				const res = await fetch("<?= $base_url; ?>/api/send/favorites.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ listing_id: listingId })
				});
				const data = await res.json();

				if (data.status === "removed") {

					showError(data.message || "Elan SEÇİLMİŞLƏRDƏN silindi");

					const countEl = document.getElementById("listCount");

					if (countEl) {
						let current = parseInt(countEl.textContent) || 0;

						if (current > 0) {
							countEl.textContent = current - 1;
						}
					}

					// 🔥 ƏN VACİB HİSSƏ — DOM-dan sil
					card.style.transition = "0.3s";
					card.style.opacity = "0";

					setTimeout(() => {
						card.remove();
					}, 300);



					// boş qaldısa mesaj göstər
					const container = document.getElementById("listingContainer");
					if (container && container.querySelectorAll(".listing-item").length === 0) {
						container.innerHTML = "<p>Favorit siyahınız boşdur</p>";
					}

				}

				else if (data.status === "not_logged_in") {
					showError(data.message);
					setTimeout(() => location.href = "/login", 1200);
				}

				else {
					showError(data.message || "Xəta baş verdi");

					// geri qaytar
					card.style.opacity = "1";
					card.style.pointerEvents = "auto";
				}

			} catch (err) {
				console.error(err);
				showError("Server xətası");

				card.style.opacity = "1";
				card.style.pointerEvents = "auto";
			}
		});
	</script>
</body>


</html>