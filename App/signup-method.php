<?php
require_once __DIR__ . "/inc/config.php";

require_guest();
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

				<div class="dashboard-details" style="margin-top: 70px">
					<div class="row">

						<h2 class="text-center">Qeydiyyat növü</h2>

						<div class="col-lg-6 col-md-6">
							<a href="./signup">
								<div class="card dash-cards">
									<div class="card-body">
										<div class="dash-top-content">
											<div class="dashcard-img">
												<img style="height: 120px" src="assets/img/icons/market.png"
													class="img-fluid" alt="user">
											</div>
										</div>
										<div class="dash-widget-info">
											<h6>Partnyor olmaq istəyənlər üçün</h6>
											<h3 class="counter">Mağaza qeydiyyatı</h3>
										</div>
									</div>
								</div>
							</a>

						</div>
						<div class="col-lg-6 col-md-6">
							<a href="./signup-user">
								<div class="card dash-cards">
									<div class="card-body">
										<div class="dash-top-content">
											<div class="dashcard-img">
												<img style="height: 120px" src="assets/img/icons/user.png"
													class="img-fluid" alt="market">
											</div>
										</div>
										<div class="dash-widget-info">
											<h6>Sıradan istifadəçi olmaq istəyənlər üçün</h6>
											<h3>Adi istifadəçi qeydiyyatı</h3>
										</div>
									</div>
								</div>
							</a>
						</div>

					</div>

				</div>
			</div>
		</div>
		<!-- /Dashboard Content -->

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

	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Datetimepicker JS -->
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- counterup JS -->
	<script src="assets/js/jquery.waypoints.js"></script>
	<script src="assets/js/jquery.counterup.min.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Owl Carousel JS -->
	<script src="assets/js/owl.carousel.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>

	<script src="js/alert-modal.js"></script>
	<?php include('./inc/alert_modal.php'); ?>

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