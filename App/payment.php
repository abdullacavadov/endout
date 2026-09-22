<?php
require_once __DIR__ . "/inc/config.php";

require_login($pdo);
require_verify($pdo, 'verify-phone');
require_role('partner');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/payment/payment_data.php";



function fmtMoney($n)
{
	$x = (float) ($n ?? 0);
	return number_format($x, 2, '.', '');
}

?>
<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once './inc/head.php'; ?>

	<style>
		/* responsive */
		#packagesWrap .price-card {
			cursor: pointer;
			border: 1px solid #c3c2c2;
		}

		.price-card:hover i,
		.price-card-selected i,
		.price-card:hover {
			color: #fff !important;
		}

		.btn-check:checked+.btn {
			background-color: #2563EB !important;
			color: #fff !important;
		}
	</style>
</head>

<body>
	<div class="main-wrapper home-nine">
		<!-- Header -->
		<?php require_once './inc/header.php'; ?>
		<!-- /Header -->

		<!-- Breadscrumb Section -->
		<div class="breadcrumb-bar">
			<div class="container">
				<div class="row align-items-center text-center">
					<div class="col-md-12 col-12">
						<h2 class="breadcrumb-title">Qiymətləndirmə</h2>
					</div>
				</div>
			</div>
		</div>
		<!-- /Breadscrumb Section -->

		<div id="pricingLoader" style="
									position:absolute;
									left:0;
									display:none;
									align-items:center;
									justify-content:center;
									width:100%;
									height:100%;
									background:rgba(255,255,255,0.6);
									z-index:10;
								">
			<div class="loader"></div>

			<style>
				.loader {
					position: relative;
					width: 100px;
					height: 100px;
				}

				.loader:before,
				.loader:after {
					content: '';
					border-radius: 50%;
					position: absolute;
					inset: 0;
					box-shadow: 0 0 14px 2px rgba(0, 0, 0, 0.3) inset;
				}

				.loader:after {
					box-shadow: 0 5px 0 #2563EB inset;
					animation: rotate 2s linear infinite;
				}

				@keyframes rotate {
					0% {
						transform: rotate(0)
					}

					100% {
						transform: rotate(360deg)
					}
				}
			</style>
		</div>

		<!-- Pricing Plan Section -->
		<section class="pricingplan-section pricing-page" id="pricingContainer">

			<div class="container">

				<div class="row">

					<div class="col-md-4">

						<div class="card">
							<div class="card-body" style="box-shadow: 3px 3px 12px gray; border-radius: 12px;">
								<h6>İstifadəçi</h6>
								<div><b>Ad:</b>
									<?= htmlspecialchars($jsPayload['customer']['full_name']) ?>
								</div>
								<div><b>Telefon:</b>
									<?= htmlspecialchars($jsPayload['customer']['phone']) ?>
								</div>
								<div><b>Email:</b>
									<?= htmlspecialchars($jsPayload['customer']['email']) ?>
								</div>
								<div><b>Mağaza:</b>
									<?= htmlspecialchars($jsPayload['market']['name']) ?>

									<?php
									if ($jsPayload['market']['type'] === 'physical_market') {
										echo "<i><small>(Fiziki mağaza)</small></i>";

									} else {
										echo "<i><small>(Onlayn e-ticarət)</small></i>";
									}
									?>

								</div>

								<hr>


								<div class="card mt-3">

									<div class="mb-2">
										<b>Ümumi auditoriya:</b> <span id="sumAudience">0</span>
									</div>


									<div class="table-responsive">
										<table class="table table-sm">
											<thead>
												<tr>
													<th>Ölkə</th>
													<th>İstifadəçi</th>
													<th>Sil</th>
												</tr>
											</thead>
											<tbody id="countriesTbody">
												<?php
												$stmt = $pdo->prepare("
												SELECT *
												FROM market_ad_countries mac
												JOIN countries c ON c.id = mac.country_id
												WHERE mac.market_id = ?
												");

												$stmt->execute([$market_id]);
												$countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

												$existingIds = array_map(fn($c) => $c['country_id'], $countries);

												?>

												<?php foreach ($countries as $row): ?>
													<tr>
														<td>
															<?= htmlspecialchars($row['name']) ?>
														</td>
														<td>
															<?= number_format($row['country_audience'] / 1000000, 1) . 'mln'; ?>
														</td>

														<td>
															<button
																class="btn btn-sm btn-link text-danger p-0 js-del-country"
																data-country-id="<?= $row['country_id'] ?>" title="Sil">
																<i class="fas fa-trash"></i>
															</button>
														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="1">

														<select id="addCountrySelect"
															class="form-control form-control-sm border border-primary">
														</select>
													</td>
													<td colspan="2">
														<button id="addCountryBtn" style="min-height: 46px;"
															class="btn btn-sm btn-outline-primary w-100"><i
																class="fas fa-plus"></i> Əlavə et</button>
													</td>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>

									<div class="mb-3">
										<b>Ödəniləcək məbləğ:</b> <span id="sumPrice">0</span> <span
											id="currency2">₼</span>
										<br>
										<small><a style="color: #2563EB; font-size: 13px;" id="ppb">Hesablanma
												qaydası</a></small>

										<br>

										<select class="form-control form-control-sm" id="currencySelect"></select>


									</div>

									<button id="payBtn" class="btn btn-primary w-100 mt-3" disabled>Ödəniş et</button>
									<div class="text-muted mt-2" style="font-size: 12px;">
										Qeyd: Qiymətə ƏDV daxildir.
									</div>
								</div>
							</div>
						</div>

					</div>


					<div class="col-md-8">
						<div class="card p-3 mb-3" style="box-shadow: 3px 3px 12px gray; border-radius: 12px;">

							<div class="section-heading">
								<div class="container">
									<div class="row text-center">
										<h2>Abunəlik <span style="background-color: #2563EB;">Pla</span>nları</h2>
										<div>
											<div
												class="d-flex justify-content-center align-items-center flex-wrap gap-2">

												<input type="radio" class="btn-check" name="duration" id="dur1"
													value="1">
												<label class="btn btn-outline-primary" for="dur1">1 Aylıq</label>

												<input type="radio" class="btn-check" name="duration" id="dur3"
													value="3" checked>
												<label class="btn btn-outline-primary" for="dur3">🔥 3 Aylıq</label>

												<input type="radio" class="btn-check" name="duration" id="dur6"
													value="6">
												<label class="btn btn-outline-primary" for="dur6">6 Aylıq</label>

												<input type="radio" class="btn-check" name="duration" id="dur12"
													value="12">
												<label class="btn btn-outline-primary" for="dur12">1 İllik</label>

											</div>
										</div>
									</div>
								</div>
							</div>

							<div id="packagesWrap" class="d-flex justify-content-center gap-2">

								<!-- JS render edəcək -->

							</div>

						</div>
					</div>


					<input type="hidden" id="csrfToken" value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">
					<form id="payForm" action="<?= $base_url ?>/api/payment/init.php" method="POST" class="d-none">
						<input type="hidden" name="_csrf" id="payCsrf">
						<input type="hidden" name="package_id" id="payPackageId">
						<input type="hidden" name="duration_months" id="payDurationMonths">
						<!-- optional: sadəcə yoxlama üçün -->
					</form>

				</div>

		</section>
		<!-- /Pricing Plan Section -->



		<div id="ppm" class="modal">
			<div class="modal-content">
				<span class="btn btn-danger close">&times;</span>
				<h5>Qiymət hesablama siyasəti</h5>
				<small class="mt-2">
					{Ümumi auditoriya sayı} / 10.000.000 * {Ölkə əmsalı} * {Seçilən paketin aylıq qiyməti}
				</small>
				<br>
				<small><i>Qeyd: İlk mərhələdə bir neçə ayın ödənişi əvvəlcədən edilərsə seçilən paketə uyğun olaraq
						ümumi qiymətə 5%, 10% və 15% endirim tətbiq olunur</i></small>
			</div>
		</div>

		<style>
			.modal {
				display: none;
				/* gizli by default */
				position: fixed;
				z-index: 1000;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.5);
				/* arxa plan qaraldı */
			}

			.modal-content {
				background-color: #fff;
				margin: 15% auto;
				/* ekranın mərkəzi */
				padding: 20px;
				border-radius: 8px;
				width: 400px;
				text-align: center;
				position: relative;
			}

			.close {
				position: absolute;
				top: 10px;
				right: 15px;
				font-size: 24px;
				cursor: pointer;
			}
		</style>

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
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>

	<script src="js/alert-modal.js"></script>


	<!-- Tom Select -->
	<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


	<style>
		#pricingContainer.loading {
			filter: blur(4px);
			pointer-events: none;
		}
	</style>


	<script>
		(() => {
			"use strict";

			const DATA = <?= json_encode($jsPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

			const packagesWrap = document.getElementById("packagesWrap");
			const countriesTbody = document.getElementById("countriesTbody");

			const currencySelect = document.getElementById("currencySelect");
			let currencyTom = null;
			const sumAudienceEl = document.getElementById("sumAudience");
			const sumPriceEl = document.getElementById("sumPrice");
			const currencyEl = document.getElementById("currency2");
			const payBtn = document.getElementById("payBtn");

			const addCountrySelect = document.getElementById("addCountrySelect");
			const addCountryBtn = document.getElementById("addCountryBtn");

			const csrfToken = document.getElementById("csrfToken");

			// 🔥 SERVER DATA CACHE
			let SERVER = {
				packages: {},
				countries: {},
				total: 0
			};



			function showLoader(state) {
				if (state) {
					pricingContainer.style.filter = "blur(4px)";
					pricingContainer.style.pointerEvents = "none";
					pricingLoader.style.display = "flex";
				} else {
					pricingContainer.style.filter = "none";
					pricingContainer.style.pointerEvents = "auto";
					pricingLoader.style.display = "none";
				}
			}

			function renderCountrySelect() {
				addCountrySelect.innerHTML = '<option value="">Ölkə seç</option>';

				const selectedIds = DATA.adCountries.map(c => Number(c.country_id));

				// 🔥 BURDA DÜZƏLİŞ
				const allCountries = SERVER.allCountries || [];

				const currentPkgId = selectedPackageId();
				const currentPkg = SERVER.packages[currentPkgId] || {};

				const isFree =
					currentPkg.invalid !== true &&
					Number(currentPkg.total) <= 0;

				allCountries.forEach(c => {

					if (Number(c.country_audience) <= 0) return;

					// free paketdirsə yalnız öz ölkəsi görünəcək
					if (isFree && Number(c.id) !== Number(SERVER.marketCountry)) {
						return;
					}

					// artıq seçilmişdirsə göstərmə
					if (selectedIds.includes(Number(c.id))) {
						return;
					}

					const opt = document.createElement("option");

					opt.value = c.id;
					opt.textContent = c.name;

					addCountrySelect.appendChild(opt);
				});

				// UX
				if (addCountrySelect.options.length === 1) {
					const opt = document.createElement("option");
					opt.textContent = "Bütün ölkələr seçilib";
					opt.disabled = true;
					addCountrySelect.appendChild(opt);
				}
			}

			function selectedPackageId() {
				const el = document.querySelector('input[name="package_id"]:checked');
				return el ? Number(el.value) : 0;
			}

			function selectedMonths() {
				const el = document.querySelector('input[name="duration"]:checked');
				return el ? Number(el.value) : 1;
			}

			function fmtMoney(n) {
				const val = Number(n);
				return Number.isFinite(val) ? val.toFixed(2) : "0.00";
			}

			function fmtAudience(n) {
				return (Number(n || 0) / 1_000_000).toFixed(1) + " mln";
			}

			function escapeHtml(s) {
				return String(s ?? "").replace(/[&<>"']/g, m => ({
					"&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
				}[m]));
			}

			// =========================
			// 🔥 FETCH (ONLY SOURCE)
			// =========================
			function isFreePackageById(packageId) {
				const pkg = SERVER.packages?.[packageId];

				if (!pkg) return false;

				return Number(pkg.total || 0) <= 0;
			}

			async function forceOwnCountryOnly() {

				const marketCountry = Number(SERVER.marketCountry);

				const ownCountry = (SERVER.allCountries || []).find(
					c => Number(c.id) === marketCountry
				);

				if (!ownCountry) {
					showError("Bu paket sizin ölkə üçün keçərli deyil");
					return false;
				}

				// əvvəl hamısını sil
				for (const country of [...DATA.adCountries]) {

					const fd = new FormData();

					fd.append("_csrf", csrfToken.value);
					fd.append("country_id", country.country_id);

					await fetch("<?= $base_url ?>/api/payment/country_delete.php", {
						method: "POST",
						body: fd
					});
				}

				// sonra öz ölkəsini əlavə et
				const fd = new FormData();

				fd.append("_csrf", csrfToken.value);
				fd.append("country_id", marketCountry);

				const res = await fetch("<?= $base_url ?>/api/payment/country_add.php", {
					method: "POST",
					body: fd
				});

				const data = await res.json().catch(() => null);

				if (!data || !data.ok) {
					showError("Ölkə əlavə edilə bilmədi");
					return false;
				}

				DATA.adCountries = [{
					country_id: ownCountry.id,
					country_name: ownCountry.name,
					country_audience: ownCountry.country_audience
				}];

				return true;
			}

			async function fetchCalc() {
				showLoader(true);

				const fd = new FormData();

				fd.append("_csrf", csrfToken.value);
				fd.append("package_id", selectedPackageId());
				fd.append("duration", selectedMonths());
				fd.append("currency", currencyTom ? currencyTom.getValue() : "USD");
				fd.append("market_id", <?= (int) $jsPayload['market']['id'] ?>);

				try {
					const res = await fetch("<?= $base_url ?>/api/payment/calc.php", {
						method: "POST",
						body: fd
					});


					const data = await res.json().catch(() => null);

					if (!data || !data.ok) {
						console.error("Calc error " + (data?.message || "Unknown error"));
						return;
					}

					SERVER = data;

					if (data.allCountries) {
						SERVER.allCountries = data.allCountries;
					}

					const currentPkg = data.packages[selectedPackageId()] || {};
					const isFree = Number(currentPkg.total || 0) <= 0;

					if (isFree) {

						DATA.adCountries = DATA.adCountries.filter(c =>
							Number(c.country_id) === Number(data.marketCountry)
						);

					}

					renderAll();


				} finally {
					showLoader(false); // ✅ BURDA
				}
			}

			// =========================
			// 🔥 RENDER
			// =========================
			function renderPackagesSkeleton() {
				packagesWrap.innerHTML = "";

				DATA.packages.forEach((p, idx) => {
					const id = `pkg_${p.id}`;
					const isChecked = idx === 1;

					const wrap = document.createElement("div");
					wrap.className = "pkg-item d-flex";
					wrap.style.width = "24%";

					wrap.innerHTML = `
			<input class="btn-check" type="radio" name="package_id" id="${id}" value="${p.id}" ${isChecked ? "checked" : ""}>
			
			<label for="${id}" class="flex-fill">
				<div class="price-card flex-fill ${isChecked ? "price-card-selected" : ""}" data-package-id="${p.id}">
					
					<div class="price-head">
						<div class="price-level">
							<h6>${escapeHtml(p.name)}</h6>
						</div>

						<h4>
							<span class="js-pkg-price">...</span>
							<span class="js-pkg-currency"></span>
							<span class="js-pkg-period"></span>
						</h4>

						<div class="js-discount" style="
							font-size:15px;
							position:absolute;
							top:12px;
							right:9px;
							background:#ffc107;
							width:40px;
							height:40px;
							display:flex;
							align-items:center;
							justify-content:center;
							border-radius:50%;
							color: #000 !important;
						"></div>
					</div>

					
						<div class="price-features" style="font-size: 12px;">

						</div>
					

				</div>
			</label>
		`;

					packagesWrap.appendChild(wrap);
				});
			}

			function renderPackagesData() {
				document.querySelectorAll(".price-card").forEach(card => {
					const pid = Number(card.dataset.packageId);

					const pkg = SERVER.packages[pid] ?? {};
					const isInvalid = pkg.invalid === true;
					const price = Number(pkg.total ?? 0);
					const features = Array.isArray(pkg.features) ? pkg.features : [];

					if (isInvalid) {
						document.querySelectorAll('input[name="package_id"][value="' + pid + '"]')
							.forEach(r => r.checked = false);
						card.querySelector(".js-pkg-price").textContent = "Sizin ölkə üçün uyğun deyil";
						card.classList.remove("price-card-selected");
						const radio = document.querySelector(
							'input[name="package_id"][value="' + pid + '"]'
						);

						if (radio) {
							radio.disabled = true;
						}
						card.style.opacity = 0.6;
						card.style.pointerEvents = "none";
						card.querySelector(".js-discount").style.display = "none";
						card.querySelector(".js-pkg-currency").style.display = "none";
						card.querySelector(".js-pkg-period").style.display = "none";
						features.length = 0; // xüsusiyyətləri də sil
					} else {
						card.querySelector(".js-pkg-price").textContent = fmtMoney(price);
					}

					card.querySelector(".js-pkg-currency").textContent = currencyTom ? currencyTom.getValue() : "USD";

					const featuresWrap = card.querySelector(".price-features");

					const FEATURE_LABELS = {
						max_post: (v) => {
							if (Number(v) === 9999) {
								return "<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> Limitsiz post paylaşımı</span>";
							} else if (Number(v) === 0) {
								return "<span><i class='fa-solid fa-circle-xmark' style='color: rgb(185, 22, 22);'></i> Post paylaşımı</span>";
							}

							return `<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> ${v} post paylaşımı</span>`;
						},


						max_countries: (v) => {
							if (Number(v) === 9999) {
								return "<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> Limitsiz ölkə seçimi</span>";
							} else if (Number(v) === 0) {
								return "<span><i class='fa-solid fa-circle-xmark' style='color: rgb(185, 22, 22);'></i> Ölkə seçimi</span>";
							}

							return `<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> ${v} ölkə seçimi</span>`;
						},

						max_reels: (v) => {
							if (Number(v) === 9999) {
								return "<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> Limitsiz reels paylaşımı</span>";
							} else if (Number(v) === 0) {
								return "<span><i class='fa-solid fa-circle-xmark' style='color: rgb(185, 22, 22);'></i> Reels paylaşımı</span>";
							}

							return `<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> ${v} reels paylaşımı</span>`;
						},

						max_tender: (v) => {
							if (Number(v) === 9999) {
								return "<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> Limitsiz tender paylaşımı</span>";
							} else if (Number(v) === 0) {
								return "<span><i class='fa-solid fa-circle-xmark' style='color: rgb(185, 22, 22);'></i> Tender paylaşımı</span>";
							}

							return `<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> ${v} tender paylaşımı</span>`;
						},

						analytics_enabled: (v) => v == 1 ? "<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> Analitika</span>" : "<span><i class='fa-solid fa-circle-xmark' style='color: rgb(185, 22, 22);'></i> Analitika</span>",

						priority_support: (v) => v == 1 ? "<span><i class='fa-solid fa-circle-check' style='color: rgb(22, 185, 136);'></i> Prioritet dəstək" : "<span><i class='fa-solid fa-circle-xmark' style='color: rgb(185, 22, 22);'></i> Prioritet dəstək</span>",
					};

					featuresWrap.innerHTML = features.map(f => {
						const formatter = FEATURE_LABELS[f.feature_key];

						if (!formatter) return null;

						const text = formatter(f.feature_value);

						if (!text) return null;

						return `
							<p>
								${text}
							</p>
						`;
					}).filter(Boolean).join("");

					// period
					const months = selectedMonths();
					const periodMap = {
						1: "/ 1 ay",
						3: "/ 3 ay",
						6: "/ 6 ay",
						12: "/ 1 il"
					};
					card.querySelector(".js-pkg-period").textContent = periodMap[months];

					// discount
					const discountMap = {
						3: "-5%",
						6: "-10%",
						12: "-15%"
					};

					const discEl = card.querySelector(".js-discount");
					const txt = discountMap[months] || "";

					discEl.textContent = txt;
					discEl.style.display = txt ? "flex" : "none";

					if (price <= 0) {
						discEl.style.display = "none";
					}
				});
			}



			function renderCountries() {
				countriesTbody.innerHTML = "";

				let totalAudience = 0;

				DATA.adCountries.forEach(c => {
					totalAudience += Number(c.country_audience || 0);

					const price = SERVER.countries[c.country_id] ?? 0;

					const tr = document.createElement("tr");
					tr.innerHTML = `
				<td>${escapeHtml(c.country_name)}</td>
				<td>${fmtAudience(c.country_audience)}</td>
				<td>
					<button class="btn btn-sm btn-link text-danger p-0 js-del-country" data-country-id="${c.country_id}">
						<i class="fas fa-trash"></i>
					</button>
				</td>
			`;
					countriesTbody.appendChild(tr);
				});

				sumAudienceEl.textContent = fmtAudience(totalAudience);
			}

			function renderTotal() {
				sumPriceEl.textContent = fmtMoney(SERVER.total);
				currencyEl.textContent = currencyTom ? currencyTom.getValue() : "USD";
			}

			function renderAll() {

				renderPackagesData();
				renderCountries();
				renderCountrySelect();
				renderTotal();

				payBtn.disabled = !(DATA.adCountries.length > 0);
			}

			// =========================
			// EVENTS
			// =========================

			document.querySelectorAll('input[name="duration"]').forEach(r => {
				r.addEventListener("change", fetchCalc);
			});



			packagesWrap.addEventListener("change", async (e) => {

				if (e.target.name !== "package_id") return;

				const newPackageId = Number(e.target.value);

				const card = e.target.closest(".pkg-item")?.querySelector(".price-card");

				document.querySelectorAll(".price-card")
					.forEach(c => c.classList.remove("price-card-selected"));

				if (card) {
					card.classList.add("price-card-selected");
				}

				const currentPkg = SERVER.packages?.[newPackageId] || {};

				const isFree =
					currentPkg.invalid !== true &&
					Number(currentPkg.total) <= 0;

				if (!isFree) {
					await fetchCalc();
					return;
				}

				const confirmed = await Swal.fire({
					title: "Diqqət",
					text: "Pulsuz paketi seçdiyiniz halda ancaq öz ölkənizə elan göstərə biləcəksiz. Digər ölkə imkanları silinəcək!",
					icon: "warning",
					showCancelButton: true,
					confirmButtonText: "Davam et",
					confirmButtonColor: "#515151",
					cancelButtonColor: "#2563EB",
					cancelButtonText: "Ləğv et"
				});

				if (!confirmed.isConfirmed) {

					e.target.checked = false;

					const prev = document.querySelector(
						'input[name="package_id"]:not([value="' + newPackageId + '"]):checked'
					);

					if (prev) {
						prev.checked = true;
					}

					return;
				}

				const ok = await forceOwnCountryOnly();

				if (!ok) {

					e.target.checked = false;

					return;
				}

				await fetchCalc();
			});

			// =========================
			// COUNTRY ADD / DELETE
			// =========================

			countriesTbody.addEventListener("click", async (e) => {
				const btn = e.target.closest(".js-del-country");
				if (!btn) return;

				const fd = new FormData();
				fd.append("_csrf", csrfToken.value);
				fd.append("country_id", btn.dataset.countryId);

				const res = await fetch("<?= $base_url ?>/api/payment/country_delete.php", {
					method: "POST",
					body: fd
				});

				const data = await res.json().catch(() => null);
				if (!data || !data.ok) return;

				DATA.adCountries = DATA.adCountries.filter(x => x.country_id != btn.dataset.countryId);

				await fetchCalc();
			});

			addCountryBtn.addEventListener("click", async () => {
				const val = addCountrySelect.value;
				if (!val) return;

				const fd = new FormData();
				fd.append("_csrf", csrfToken.value);
				fd.append("country_id", val);

				const res = await fetch("<?= $base_url ?>/api/payment/country_add.php", {
					method: "POST",
					body: fd
				});

				const data = await res.json().catch(() => null);
				if (!data || !data.ok) return;

				const selected = (SERVER.allCountries || []).find(c => c.id == val);

				if (selected) {
					DATA.adCountries.push({
						country_id: selected.id,
						country_name: selected.name,
						country_audience: selected.country_audience
					});
				}

				await fetchCalc();
			});

			// =========================
			// PAYMENT
			// =========================

			payBtn.addEventListener("click", async () => {
				if (payBtn.disabled) return;

				const packageId = selectedPackageId();
				const months = selectedMonths();
				const marketId = Number(DATA.market.id);

				if (!packageId || !months || !marketId) {
					await Swal.fire({
						title: "Xəta",
						text: "Ödəniş üçün paket və müddət seçilməlidir.",
						icon: "error",
						confirmButtonText: "Bağla"
					});
					return;
				}

				payBtn.disabled = true;
				const originalText = payBtn.textContent;
				payBtn.textContent = "Ödəniş hazırlanır...";

				try {
					/*
					 * 1. Əvvəlcə local order yaradılır.
					 * Məbləğ client-dən götürülmür; server özü hesablayır.
					 */
					const orderResponse = await fetch("<?= $base_url ?>/api/payment/create_order.php", {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							"X-CSRF-Token": csrfToken.value
						},
						body: JSON.stringify({
							market_id: marketId,
							months: months,
							package_id: packageId
						})
					});

					const orderData = await orderResponse.json().catch(() => null);

					if (!orderData || !orderData.ok || !orderData.order_id) {
						throw new Error(
							orderData?.message || "Sifariş yaradıla bilmədi."
						);
					}

					/*
					 * 2. Yaradılmış order üçün BirBank payment başlanır.
					 */
					const paymentResponse = await fetch("<?= $base_url ?>/api/payment/init_payment.php", {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
							"X-CSRF-Token": csrfToken.value
						},
						body: JSON.stringify({
							order_id: Number(orderData.order_id),
							provider: "birbank"
						})
					});

					const paymentData = await paymentResponse.json().catch(() => null);

					if (!paymentData || !paymentData.ok || !paymentData.redirect_url) {
						throw new Error(
							paymentData?.message || "BirBank ödənişi başladılmadı."
						);
					}

					/*
					 * 3. BirBank HPP səhifəsinə keç.
					 */
					window.location.href = paymentData.redirect_url;

				} catch (error) {
					console.error("Payment error:", error);

					payBtn.disabled = false;
					payBtn.textContent = originalText;

					await Swal.fire({
						title: "Ödəniş xətası",
						text: error?.message || "Ödəniş başlatmaq mümkün olmadı.",
						icon: "error",
						confirmButtonText: "Bağla"
					});
				}
			});

			// =========================
			// INIT
			// =========================
			function initCurrencySelect() {
				currencyTom = new TomSelect("#currencySelect", {
					options: [
						{ id: "AZN", name: "AZN", iso2: "az" },
						{ id: "USD", name: "USD", iso2: "us" },
						{ id: "EUR", name: "EUR", iso2: "eu" },
						{ id: "TRY", name: "TRY", iso2: "tr" },
						{ id: "RUB", name: "RUB", iso2: "ru" },
						{ id: "GBP", name: "GBP", iso2: "gb" },
						{ id: "CHF", name: "CHF", iso2: "ch" },
						{ id: "JPY", name: "JPY", iso2: "jp" },
						{ id: "AED", name: "AED", iso2: "ae" },
						{ id: "CNY", name: "CNY", iso2: "cn" }
					],
					valueField: "id",
					labelField: "name",
					searchField: ["name", "id"],
					maxItems: 1,
					create: false,

					onChange: function () {
						fetchCalc();
					},

					render: {
						option: function (data, escape) {
							return `
					<div class="d-flex align-items-center">
						<img class="me-2"
							 src="https://flagcdn.com/16x12/${data.iso2}.png"
							 alt="${escape(data.name)}">
						<span>${escape(data.name)}</span>
					</div>
				`;
						},
						item: function (data, escape) {
							return `
					<div class="d-flex align-items-center">
						<img class="me-2"
							 src="https://flagcdn.com/16x12/${data.iso2}.png"
							 alt="${escape(data.name)}">
						<span>${escape(data.name)}</span>
					</div>
				`;
						}
					},

					onInitialize: function () {
						this.setValue("USD", true);
						renderPackagesSkeleton();
						fetchCalc();
					}
				});
			}

			initCurrencySelect();

		})();
	</script>

	<script>
		const modal = document.getElementById("ppm");
		const openBtn = document.getElementById("ppb");
		const closeBtn = document.querySelector(".close");

		openBtn.addEventListener("click", () => {
			modal.style.display = "block";
		});

		closeBtn.addEventListener("click", () => {
			modal.style.display = "none";
		});

		window.addEventListener("click", (e) => {
			if (e.target === modal) {
				modal.style.display = "none";
			}
		});
	</script>


</body>

</html>