<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/inc/config.php";
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once __DIR__ . '/inc/head.php'; ?>
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
						<h2 class="breadcrumb-title">Mağazalar</h2>
					</div>
				</div>
			</div>
		</div>
		<!-- /Breadscrumb Section -->

		<!-- Blog List -->

		<div class="container">

			<div class="row g-3 mt-3 mb-3" id="listingContainer">

				<?php
				$stmt = $pdo->prepare("
    SELECT 
        m.id AS mid,
        m.name,
		m.slug,
        m.logo,
        m.phone_number,
        m.views,
        m.description,
        c.name AS country_name,
        ct.name AS city_name,

        COALESCE(lc.listing_count, 0) AS listing_count,
        COALESCE(sc.subs_count, 0) AS subs_count

    FROM markets m

    LEFT JOIN countries c ON c.id = m.country_id
    LEFT JOIN cities ct ON ct.id = m.city_id

    LEFT JOIN (
        SELECT market_id, COUNT(*) AS listing_count
        FROM listings
        WHERE status = 'active'
        GROUP BY market_id
    ) lc ON lc.market_id = m.id

    LEFT JOIN (
        SELECT market_id, COUNT(*) AS subs_count
        FROM market_subs
        WHERE is_active = 1
        GROUP BY market_id
    ) sc ON sc.market_id = m.id

    WHERE m.is_active = 1
    ORDER BY m.id DESC
    LIMIT 20
");

				$stmt->execute();
				$markets = $stmt->fetchAll(PDO::FETCH_ASSOC);
				?>


				<?php foreach ($markets as $market): ?>

					<!-- ITEM -->
					<div class="col-md-4">
						<a href="<?= $base_url ?>/market/<?= urlencode($market['slug']) ?>/<?= (int) $market['mid'] ?>">
							<div class="card h-100 shadow" style="border: 1px solid #dedede">
								<div class="row g-0 h-100">


									<!-- LOGO -->
									<div class="col-4 d-flex align-items-center justify-content-center bg-light p-2"
										style="border-radius: 10px;">
										<img src="./assets/img/market-logo/<?= htmlspecialchars($market['logo']) ?>"
											class="img-fluid" alt="<?= htmlspecialchars($market['name']); ?>">
									</div>

									<!-- CONTENT -->
									<div class="col-8">
										<div class="card-body p-2">

											<h6 class="card-title mb-1 fw-bold">
												<?= htmlspecialchars($market['name']); ?>
											</h6>

											<p class="card-text small text-muted mb-2">
												<?= htmlspecialchars($market['country_name'] . ' / ' . $market['city_name']); ?>
											</p>

											<div class="small text-muted mb-1">
												📞
												<?= htmlspecialchars($market['phone_number']); ?>
											</div>

											<div class="d-flex justify-content-between small">
												<span class="text-muted">
													<?= (int) $market['listing_count']; ?>
													elan
												</span>
												<span class="text-muted"><i class="far fa-eye"></i>
													<?= (int) $market['views']; ?>
												</span>
												<span class="text-muted"><i class="far fa-user"></i>
													<?= (int) $market['subs_count']; ?>
												</span>
											</div>

										</div>
									</div>


								</div>
							</div>
						</a>
					</div>

				<?php endforeach; ?>

				<button class=" col-12 btn btn-primary mb-5 mt-5" id="loadMoreBtn">Daha çox göstər</button>

			</div>




		</div>
		<!-- /Blog List -->

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

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>

	<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
		data-cf-settings="e9450544c5a6f8741d912311-|49" defer></script>



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
</body>


</html>