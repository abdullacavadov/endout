<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";

$tenderAccess = can_create_tender($pdo, $customerId);
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once './inc/head.php'; ?>

	<style>
		.video-play {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 60px;
			height: 60px;
			background: rgba(0, 0, 0, 0.6);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-size: 22px;
			cursor: pointer;
		}
	</style>
</head>

<body>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once './inc/header.php'; ?>
		<!-- /Header -->


		<!-- /Breadscrumb Section -->

		<!-- Profile Content -->
		<div class="dashboard-content listing-section ">
			<div class="container" style="min-height: 20vh;">

				<div class="profile-content" style="margin-top: 70px;">


					<?php if ($tenderAccess['allowed']): ?>

						<div class="alert alert-info" style="margin-top: 150px;">
							Qalan tender limiti:
							<?= $tenderAccess['remaining'] ?>
							/ <?= $tenderAccess['max_tender'] ?>
						</div>

						<form id="tenderForm" enctype="multipart/form-data">
							<div class="messages-form">
								<div class="card">

									<div class="card-body">


										<div class="form-set form-floating">
											<input type="text" id="bashliq" name="title" class="form-control pass-input"
												placeholder=" ">
											<label for="bashliq">Tender başlığı <span>*</span></label>
										</div>

										<div class="form-set form-floating">
											<textarea style="height: 200px" id="desc" name="description"
												class="form-control pass-input" placeholder=" " maxlength="5000"></textarea>
											<label for="desc">Məlumat <span>*</span></label>
										</div>

										<div class="form-set form-floating">
											<select id="country" class="form-control" name="country_id">
												<option value="" disabled selected>-- Ölkə seçin --</option>

												<?php $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name");
												while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
													<option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?>
													</option>
												<?php endwhile; ?>

											</select>
											<label for="country">Tenderin ölkəsi <span>*</span></label>
										</div>

										<div class="row">

											<div class="col-6">
												<div class="form-set form-floating">
													<input type="date" id="start_date" name="start_date"
														class="form-control pass-input" placeholder=" ">
													<label for="start_date">Başlanğıc tarixi</label>
												</div>
											</div>
											<div class="col-6">
												<div class="form-set form-floating">
													<input type="date" id="end_date" name="end_date"
														class="form-control pass-input" placeholder=" ">
													<label for="end_date">Bitmə tarixi</label>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-2">
												<div class="form-set form-floating">
													<select id="currency" name="currency" class="form-control pass-input">
														<option value="" selected disabled>-- seçin --</option>
														<option value="AZN">AZN</option>
														<option value="USD">USD</option>
														<option value="EUR">EUR</option>
														<option value="TRY">TRY</option>
														<option value="RUB">RUB</option>
														<option value="GBP">GBP</option>
														<option value="CHF">CHF</option>
														<option value="JPY">JPY</option>
														<option value="AED">AED</option>
														<option value="CNY">CNY</option>
													</select>
													<label for="currency">Valyuta <span>*</span></label>
												</div>
											</div>
											<div class="col-5">
												<div class="form-set form-floating">
													<input type="text" id="budget_min" name="budget_min"
														class="form-control pass-input" placeholder=" ">
													<label for="budget_min">Minimum büdcə</label>
												</div>
											</div>
											<div class="col-5">
												<div class="form-set form-floating">
													<input type="text" id="budget_max" name="budget_max"
														class="form-control pass-input" placeholder=" ">
													<label for="budget_max">Maksimum büdcə</label>
												</div>
											</div>
										</div>

										<div class="form-floating">
											<select class="form-select" id="countries" name="countries[]" multiple></select>
											<label for="countries">Tenderin göstəriləcəyi ölkələr <span>*</span></label>

											<style>
												.ts-control {
													border: none;
													padding: 6px 15px 6px 38px;
												}

												.ts-control>input::placeholder {
													color: #7c7c7c;
												}

												.ts-wrapper.is-invalid .ts-wrapper {
													border: 1px solid #dc3545 !important;
													border-radius: 6px;
												}
											</style>
										</div>

										<div class="row mt-3">

											<style>
												.iti {
													width: 100%;
												}

												.contact-label {
													position: absolute;
													left: 92% !important;
												}
											</style>

											<div class="col-6">
												<div class="form-set form-floating">
													<input type="tel" id="phone" class="form-control" placeholder=" "
														inputmode="numeric" style="width: 100%; height: 58px;"
														name="contact_number" />
													<label for="contact_number" class="contact-label">
														<i class="fas fa-phone"></i>
													</label>
												</div>
											</div>
											<div class="col-6">
												<div class="form-set form-floating">
													<input type="email" id="contact_email" name="contact_email"
														class="form-control pass-input" placeholder=" ">
													<label for="contact_email" class="contact-label">
														<i class="fas fa-envelope"></i>
													</label>
												</div>
											</div>
										</div>


										<div class="form-set group-img">
											<div class="group-img">
												<i class="feather-tag" style="z-index: 3; top: 14px"></i>
												<select id="tags" class="form-control" name="tags[]" multiple>
												</select>
												<style>
													.ts-control {
														border: none;
														padding: 6px 15px 6px 45px;
													}

													.ts-control>input::placeholder {
														color: #7c7c7c;
													}

													.ts-wrapper.multi .ts-control>div {
														margin-left: 8px !important;
													}

													.ts-wrapper.is-invalid .ts-wrapper {
														border: 1px solid #dc3545 !important;
														border-radius: 6px;
													}
												</style>
											</div>
										</div>


									</div>
								</div>


								<button class="btn btn-primary" type="submit"> Sorğunu əlavə et</button>
							</div>
						</form>

					<?php else: ?>

						<div class="alert alert-danger" style="margin-top: 150px;">
							<?= htmlspecialchars($tenderAccess['reason']) ?>
						</div>

					<?php endif; ?>



				</div>
			</div>
		</div>
		<!-- /Profile Content -->

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


	<script src="js/alert-modal.js"></script>

	<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
		data-cf-settings="2f56c08bf4c64a5a606bde4d-|49" defer></script>

	<!-- Tom Select -->
	<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


	<!-- intl-tel-input JS -->
	<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>





	<!-- Sortable JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"
		integrity="sha512-csIng5zcB+XpulRUa+ev1zKo7zRNGpEaVfNB9On1no9KYTEY/rLGAEEpvgdw6nim1WdTuihZY1eqZ31K7/fZjw=="
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>


	<script>
		document.addEventListener("DOMContentLoaded", function () {

			let controller;
			const phoneInput = document.getElementById("phone");
			const el = document.querySelector("#tags");


			// ---------- intlTelInput init ----------
			try {
				const userCountry = "<?= strtolower($user_country_code ?? 'us'); ?>";

				iti = window.intlTelInput(phoneInput, {
					initialCountry: userCountry,
					separateDialCode: false,
					nationalMode: false,
					autoPlaceholder: "polite",
					utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
				});
			} catch (e) {
				console.warn("intlTelInput init olmadı:", e);
			}



			// ❗ Əgər artıq init olunubsa → yenidən yaratma
			if (el.tomselect) {
				return;
			}

			const tagSelect = new TomSelect(el, {
				valueField: "keyword",
				labelField: "keyword",
				searchField: "keyword",

				create: function (input) {
					return { keyword: input };
				},

				persist: false,
				maxItems: null,
				placeholder: "Mağazanızı təsvir edən açar sözlər (məs: elektronika, geyim, ev əşyaları)",
				createOnBlur: true,

				load: function (query, callback) {
					if (!query.length) return callback();

					if (controller) controller.abort();
					controller = new AbortController();

					fetch("<?= $base_url ?>/api/data/tags.php?q=" + encodeURIComponent(query), {
						signal: controller.signal
					})
						.then(res => res.json())
						.then(callback)
						.catch(() => callback());
				},

				render: {
					option: (item, escape) => `<div>${escape(item.keyword)}</div>`,
					item: (item, escape) => `<div>${escape(item.keyword)}</div>`,
					option_create: (data, escape) =>
						`<div class="create">Yeni tag: <strong>${escape(data.input)}</strong></div>`
				},

				onKeyDown: function (e) {
					if (e.key === " ") {
						e.preventDefault();

						const value = this.inputValue().trim().toLowerCase();
						if (!value) return;

						const existing =
							this.options[value] ||
							Object.values(this.options).find(opt => opt.keyword.startsWith(value));

						if (existing) {
							this.addItem(existing.keyword);
						} else {
							this.addOption({ keyword: value });
							this.addItem(value);
						}

						this.clearTextbox();
					}
				}
			});

		});
	</script>


	<script defer>
		document.addEventListener("DOMContentLoaded", function () {


			let countriesTom = null;



			async function loadCountries() {
				const res = await fetch("<?= $base_url; ?>/api/data/market_countries.php");
				const data = await res.json();
				if (countriesTom) countriesTom.destroy();
				const selected = data.map(c => c.id);
				countriesTom = new TomSelect("#countries", {
					options: data,
					items: selected,
					valueField: "id",
					labelField: "name",
					searchField: "name",
					plugins: ["remove_button"],
					maxItems: null,
					render: {
						option: function (data, escape) {
							return `<div><img class="me-1" src="https://flagcdn.com/16x12/${data.iso2.toLowerCase()}.png">${escape(data.name)}</div>`;
						},
						item: function (data, escape) {
							return `<div><img class="me-1" src="https://flagcdn.com/16x12/${data.iso2.toLowerCase()}.png">${escape(data.name)}</div>`;
						}
					}
				});
			}
			loadCountries();




			// 5️⃣ Form submit
			document.getElementById("tenderForm").addEventListener("submit", async function (e) {
				e.preventDefault();
				const form = document.getElementById("tenderForm");
				const formData = new FormData(form);

				if (iti) {
					const fullNumber = iti.getNumber(); // default E.164
					if (fullNumber) formData.set('contact_number', fullNumber);
				} else {
					// fallback
					formData.set('contact_number', (phoneInput.value || '').trim());
				}

				const res = await fetch("./api/create/create_tender.php", { method: "POST", body: formData });
				const data = await res.json();

				if (data.success) {
					showError("Tender əlavə edildi");
					window.location.href = "./my-inquiry";
				} else {
					showError(data.error);
				}
			});

		});
	</script>
</body>

</html>