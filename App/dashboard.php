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
	<?php require_once './inc/head.php'; ?>
</head>

<body>

	<div class="main-wrapper home-nine">
		<!-- Header -->
		<?php require_once './inc/header.php'; ?>
		<!-- /Header -->



		<!-- Dashboard Content -->
		<div class="dashboard-content">
			<div class="container">
				<div style="margin-top: 70px">
					<?php include "./inc/dashboard_menus.php"; ?>
				</div>
				<div class="dashboard-details">
					<div class="row">

						<?php if ($cust_email_verified !== 1): ?>
							<div class="col-12">
								<div class="alert alert-warning d-flex justify-content-between align-items-center">
									<div>
										<b>Sorğular</b> haqqında məlumat almaq üçün <b>e-poçt ünanınızı təsdiqləyin.</b>
									</div>
									<a href="./verify-mail" class="btn btn-sm btn-primary">Təsdiqlə</a>
								</div>
							</div>
						<?php endif; ?>

						<?php if (!$hasActiveSub): ?>
							<div class="col-12 mb-2">
								<div class="alert alert-danger d-flex justify-content-between align-items-center"
									role="alert">
									<div>
										<b>Abunəliyiniz yoxdur.</b> Xidmətlərdən istifadə etmək üçün paket seçib ödəniş
										etməlisiniz.
									</div>
									<a href="./payment" class="btn btn-sm btn-primary">Paket seç</a>
								</div>
							</div>
						<?php endif; ?>

						<div class="col-lg-3 col-md-3">
							<div class="card dash-cards">
								<div class="card-body">
									<div class="dash-top-content">
										<div class="dashcard-img">
											<img src="assets/img/icons/verified.svg" class="img-fluid" alt="">
										</div>
									</div>
									<div class="dash-widget-info">
										<h6>Aktiv elan sayı</h6>
										<h3 class="counter">25</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-3">
							<div class="card dash-cards">
								<div class="card-body">
									<div class="dash-top-content">
										<div class="dashcard-img">
											<img src="assets/img/icons/rating.svg" class="img-fluid" alt="">
										</div>
									</div>
									<div class="dash-widget-info">
										<h6>Ümumi baxışlar</h6>
										<h3>15230</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-3">
							<div class="card dash-cards">
								<div class="card-body">
									<div class="dash-top-content">
										<div class="dashcard-img">
											<img src="assets/img/icons/chat.svg" class="img-fluid" alt="">
										</div>
									</div>
									<div class="dash-widget-info">
										<h6>Oxunmamış mesajlar</h6>
										<h3>15</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-3">
							<div class="card dash-cards">
								<div class="card-body">
									<div class="dash-top-content">
										<div class="dashcard-img">
											<img src="assets/img/icons/bookmark.svg" class="img-fluid" alt="">
										</div>
									</div>
									<div class="dash-widget-info">
										<h6>Mənə aid sorğular</h6>
										<h3>30</h3>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row dashboard-info">
						<div class="col-lg-6 d-flex">
							<div class="card dash-cards w-100">
								<div class="card-header">
									<h4>Səhifə ziyarətləri</h4>
									<div class="card-dropdown">
										<ul>
											<li class="nav-item dropdown has-arrow logged-item">
												<a href="#" class="dropdown-toggle pageviews-link"
													data-bs-toggle="dropdown" aria-expanded="false">
													<span>Bu gün</span>
												</a>
												<div class="dropdown-menu dropdown-menu-end">
													<a class="dropdown-item" href="javascript:void();">Son 7 gün</a>
													<a class="dropdown-item" href="javascript:void()">Son bir ay</a>
													<a class="dropdown-item" href="javascript:void()">Son bir il</a>
												</div>
											</li>
										</ul>
									</div>
								</div>
								<div class="card-body">
									<div id="review-chart"></div>
								</div>
							</div>
						</div>
						<div class="col-lg-6 d-flex">
							<div class="card dash-cards w-100">
								<div class="card-header">
									<h4>Abunəlik</h4>
									<div class="card-dropdown">
										<ul>
											<li class="nav-item dropdown has-arrow logged-item">
												<a href="<?= $base_url; ?>/packages" class="car-list-btn" style="background-color: #c10037;
															color: #ffffff;
															border-radius: 8px;
															padding: 13px 32px;
															border: 1px solid transparent;">
													<span><i class="feather-repeat"
															style="color: #fff;"></i></span>Yenilə
												</a>

											</li>
										</ul>
									</div>
								</div>
								<div class="card-body">
									<canvas id="subChart" width="220" height="220"></canvas>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Dashboard Content -->

		<?php include('./inc/alert_modal.php'); ?>

		<!-- Footer -->
		<?php require_once './inc/footer.php'; ?>
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

	<script src="assets/js/jquery-3.7.1.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Datetimepicker JS -->
	<script src="assets/js/moment.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- counterup JS -->
	<script src="assets/js/jquery.waypoints.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>
	<script src="assets/js/jquery.counterup.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Owl Carousel JS -->
	<script src="assets/js/owl.carousel.min.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js" type="fca7c691a9a41484a930bc86-text/javascript"></script>

	
	
	<script src="js/alert-modal.js"></script>
	

	<script src="assets/plugins/rocket-loader.min.js" data-cf-settings="fca7c691a9a41484a930bc86-|49" defer></script>

	<!-- intl-tel-input JS -->
	<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>

	<!-- Tom Select -->
	<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

	<!-- Chart.JS -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<script>
		// ===== DATA =====
		const startDateStr = "2026-01-15 15:30:00";
		const INTERVAL_DAYS = 30;

		const startDate = new Date(startDateStr.replace(" ", "T"));
		const now = new Date();

		const totalMs = INTERVAL_DAYS * 24 * 60 * 60 * 1000;
		const endDate = new Date(startDate.getTime() + totalMs);

		const passedMs = Math.min(Math.max(now - startDate, 0), totalMs);
		const percent = passedMs / totalMs;

		const remainingDays = Math.max(
			0,
			Math.ceil((endDate - now) / (24 * 60 * 60 * 1000))
		);

		const isExpired = remainingDays === 0;

		const used = percent;
		const remaining = 1 - percent;

		// ===== HELPERS =====
		function formatDMY(date) {
			const d = String(date.getDate()).padStart(2, "0");
			const m = String(date.getMonth() + 1).padStart(2, "0");
			const y = date.getFullYear();
			return `${d}.${m}.${y}`;
		}

		// ===== INTERACTION STATE =====
		let renewButtonArea = null;
		let isRenewHover = false;

		// ===== PLUGINS =====
		const centerTextPlugin = {
			id: "centerText",
			afterDraw(chart) {
				const { ctx, chartArea } = chart;
				const x = (chartArea.left + chartArea.right) / 2;
				const y = (chartArea.top + chartArea.bottom) / 2;

				const messageOffset = 12;

				ctx.save();
				ctx.textAlign = "center";

				// Başlıq
				ctx.font = "600 14px Arial";
				ctx.fillStyle = "#111";
				ctx.fillText("Premium Paket", x, y - 30);

				ctx.font = "12px Arial";

				if (isExpired) {
					// 🔴 Expired mesajı
					ctx.fillStyle = "#d32f2f";
					ctx.fillText(
						"Abunə müddəti başa çatıb",
						x,
						y - 10 + messageOffset
					);

					// 🔴 Yenilə düyməsi
					const btnWidth = 80;
					const btnHeight = 28;
					const btnY = y + 4 + messageOffset;

					if (isRenewHover) {
						ctx.shadowColor = "#d32f2f";
						ctx.shadowBlur = 12;
					}

					ctx.fillStyle = "#d32f2f";
					ctx.beginPath();
					ctx.roundRect(
						x - btnWidth / 2,
						btnY,
						btnWidth,
						btnHeight,
						6
					);
					ctx.fill();

					ctx.shadowBlur = 0;

					ctx.fillStyle = "#fff";
					ctx.font = "600 12px Arial";
					ctx.fillText("Yenilə", x, btnY + 19);

					renewButtonArea = {
						x: x - btnWidth / 2,
						y: btnY,
						width: btnWidth,
						height: btnHeight,
					};
				} else {
					renewButtonArea = null;

					ctx.fillStyle = "#666";
					ctx.fillText(
						"Yenilənmə: " + formatDMY(endDate),
						x,
						y + 5
					);
					ctx.fillText("Abunəliyin bitməsinə " + remainingDays + " gün qalıb", x, y + 25);
				}

				ctx.restore();
			},
		};

		const bgImg = new Image();
		bgImg.src =
			"https://media.istockphoto.com/id/1459373176/vector/abstract-defocused-background-spring-summer-sea.jpg?s=612x612&w=0&k=20&c=P6D1VrXeeKsJfyKzlJeIqxyNXkeYtMb6C1mW6p68xro=";

		const circleBackgroundPlugin = {
			id: "circleBackground",
			beforeDraw(chart) {
				if (!bgImg.complete) return;

				const { ctx, chartArea } = chart;
				const cx = (chartArea.left + chartArea.right) / 2;
				const cy = (chartArea.top + chartArea.bottom) / 2;
				const radius = Math.min(chartArea.width, chartArea.height) / 2;

				ctx.save();
				ctx.beginPath();
				ctx.arc(cx, cy, radius, 0, Math.PI * 2);
				ctx.clip();

				ctx.globalAlpha = isExpired ? 0.05 : 0.15;
				ctx.drawImage(
					bgImg,
					chartArea.left,
					chartArea.top,
					chartArea.width,
					chartArea.height
				);

				ctx.restore();
			},
		};

		// ===== CHART =====
		const canvas = document.getElementById("subChart");

		const chart = new Chart(canvas, {
			type: "doughnut",
			data: {
				datasets: [
					{
						data: [used, remaining],
						backgroundColor: isExpired
							? ["rgba(255,255,255,0)", "#999"]
							: ["rgba(255,255,255,0)", "#990853"],
						borderWidth: 0,
						borderRadius: 12,
					},
				],
			},
			options: {
				cutout: "95%",
				responsive: true,
				plugins: {
					legend: { display: false },
					tooltip: { enabled: false },
				},
			},
			plugins: [circleBackgroundPlugin, centerTextPlugin],
		});

		bgImg.onload = () => chart.update();

		// ===== HOVER LISTENER =====
		canvas.addEventListener("mousemove", (e) => {
			if (!renewButtonArea) {
				canvas.style.cursor = "default";
				isRenewHover = false;
				return;
			}

			const rect = canvas.getBoundingClientRect();
			const mouseX = e.clientX - rect.left;
			const mouseY = e.clientY - rect.top;

			const hover =
				mouseX >= renewButtonArea.x &&
				mouseX <= renewButtonArea.x + renewButtonArea.width &&
				mouseY >= renewButtonArea.y &&
				mouseY <= renewButtonArea.y + renewButtonArea.height;

			canvas.style.cursor = hover ? "pointer" : "default";

			if (hover !== isRenewHover) {
				isRenewHover = hover;
				chart.update();
			}
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




</body>

</html>