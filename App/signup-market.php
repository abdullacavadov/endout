<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone'); // phone_verified != 1 olarsa verify-otp-yə yönləndir
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once './inc/head.php'; ?>
</head>

<body>

	<div class="main-wrapper home-nine">
		<!-- Header -->
		<?php include_once __DIR__ . "/inc/header.php"; ?>
		<!-- /Header -->



		<!-- Breadscrumb Section -->
		<div class="breadcrumb-bar">
			<div class="container">
				<div class="row align-items-center text-center">
					<div class="col-md-12 col-12">
						<h3 class="breadcrumb-title">Mağaza məlumatları</h3>

					</div>
				</div>
			</div>
		</div>
		<!-- /Breadscrumb Section -->

		<!-- Login Section -->
		<div class="login-content">


			<div class="container">
				<p>Zəhmət olmasa mağaza məlumatlarını qeyd edin</p>
				<div class="row">
					<div class="col-md-6 col-lg-8">



						<!-- Login Form -->
						<form id="marketForm" enctype="multipart/form-data">

							<?php require_once __DIR__ . "/api/_csrf.php"; ?>
							<input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

							<div class="form-set group-img">
								<div class="group-img">
									<i class="feather-home"></i>
									<input type="text" class="form-control" placeholder="Mağaza adı"
										name="market_name" />
									<div class="invalid-feedback" id="error_market_name"></div>
								</div>
							</div>

							<style>
								input[type="number"]::-webkit-inner-spin-button {
									display: none;
								}

								.iti {
									width: 100%;
								}
							</style>


							<div class="form-set profile-img col-md-12">
								<div class="settings-upload-img">
									<img id="marketLogoPreview"
										src="https://st2.depositphotos.com/1561359/12101/v/950/depositphotos_121012076-stock-illustration-blank-photo-icon.jpg"
										alt="Logo" class="img-fluid">
								</div>

								<div class="settings-upload-btn">
									<input type="file" accept="image/*" name="market_logo"
										class="hide-input image-upload" id="marketLogoInput">

									<label for="marketLogoInput" class="file-upload">Loqotip
										yüklə</label>
									<br>
									<i style="font-size: 10px;">Max fayl ölçüsü: 10 MB</i>

									<!-- Progress -->
									<div id="logoProgressWrap" style="display:none; margin-top:10px; max-width:320px;">
										<progress id="logoProgress" value="0" max="100" style="width:100%;"></progress>
										<div style="font-size:12px;">
											<span id="logoProgressText">0%</span>
										</div>
									</div>
								</div>

							</div>

							<div class="form-set group-img row">
								<div class="group-img col-md-6">
									<i class="fa-solid fa-bullhorn"></i>
									<input type="text" class="form-control" placeholder="Promokod daxil edin (varsa)"
										name="market_promo_code" />
									<div class="invalid-feedback" id="error_market_promo_code"></div>
								</div>

								<div class="group-img col-md-6" style="display: none;" id="discountInput">
									<i class="fa-solid fa-percent"></i>
									<input type="number" class="form-control" min="0" max="100" step="1" length="3"
										placeholder="Endirim miqdarı daxil edin (varsa)" name="market_promo_discount" />
									<div class="invalid-feedback" id="error_market_promo_discount"></div>
								</div>

								<div class="group-img col-md-12">
									<div class="alert alert-primary" role="alert">
										Promokod vasitəsilə saytımızdan gələn müştərilərə endirim təklif edə bilərsiniz.
										Məsələn, 10% endirim üçün "SUMMER10" kimi bir kod yarada bilərsiniz.
										Promokodunuz varsa, onu daxil edin və endirim miqdarını göstərin. Bu,
										müştərilərinizin "EndOut" platformasından gəldiyini bilməyiniz və
										alış-veriş zamanı onlara xüsusi bir təklif irəli sürə bilməyiniz üçün tövsiyyə
										edilir.
									</div>
								</div>
								<script>
									document.addEventListener("DOMContentLoaded", () => {
										const promoInput = document.querySelector('input[name="market_promo_code"]');
										const discountInput = document.getElementById('discountInput');

										function toggleDiscount() {
											const hasPromo = promoInput.value.trim().length > 0;

											if (hasPromo) {
												discountInput.value = ""; // köhnə dəyəri sil (vacibdir)
												discountInput.style.display = "block";
											} else {
												discountInput.style.display = "none";
											}
										}

										// həm yazanda işləsin, həm də paste zamanı
										promoInput.addEventListener("input", toggleDiscount);

										// səhifə reload olanda da düzgün vəziyyət olsun
										toggleDiscount();
									});
								</script>
							</div>



							<div class="form-set group-img">
								<div class="group-img">
									<i class="feather-tag" style="z-index: 3; top: 14px"></i>
									<select id="market_tags" class="form-control" name="market_tags[]" multiple>
									</select>
									<div class="invalid-feedback" id="error_market_tags"></div>
									<style>
										.ts-control {
											border: none;
											padding: 6px 15px 6px 38px;
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

							<div class="form-set">
								<div class="group-img">
									<i class="feather-phone"></i>
									<input type="tel" id="phone" class="form-control"
										placeholder="Müştərilər üçün əlaqə nömrəsi" inputmode="numeric"
										style="width: 100%;" name="market_phone" />
									<div class="invalid-feedback" id="error_market_phone"></div>
								</div>
							</div>

							<div class="form-set group-img">
								<div class="group-img">
									<i class="feather-mail"></i>
									<input type="text" class="form-control" placeholder="Müştərilər üçün e-poçt ünvanı"
										name="market_email" />
									<div class="invalid-feedback" id="error_market_email"></div>
								</div>
							</div>

							<div class="form-set group-img">
								<div class="group-img">
									<i class="feather-layers" style="top: 14px"></i>
									<select class="form-control" id="market_type" name="market_type"
										style="padding: 6px 15px 6px 38px">
										<option value="" disabled selected>-- Mağaza növü --</option>
										<option value="online_market">Online</option>
										<option value="physical_market">Offline</option>
									</select>
									<div class="invalid-feedback" id="error_market_type"></div>
								</div>
							</div>

							<div class="form-set group-img col-md-12">
								<div class="group-img ">
									<i class="feather-file-text"></i>
									<textarea class="form-control" style="padding: 6px 10px 6px 38px;" rows="5"
										placeholder="Mağaza haqqında məlumat" name="market_desc"
										id="market_desc"></textarea>
									<div class="invalid-feedback" id="error_market_desc"></div>
								</div>
							</div>

							<div class="form-set group-img col-lg-6">
								<div class="group-img ">
									<select id="country" class="form-control" name="market_country">
										<option value="" disabled selected>-- Ölkə seçin --</option>
									</select>
									<div class="invalid-feedback" id="error_market_country"></div>
								</div>
							</div>

							<div class="form-set group-img col-lg-6">
								<div class="group-img ">
									<select id="city" class="form-control" name="market_city">
										<option value="" disabled selected>-- Şəhər seçin --</option>
									</select>
									<div class="invalid-feedback" id="error_market_city"></div>
								</div>
							</div>


							<div class="row" id="location" style="display: none;">

								<div class="form-set group-img col-md-12">
									<div class="group-img ">
										<i class="feather-map"></i>
										<textarea class="form-control" style="padding: 6px 10px 6px 38px;" rows="5"
											placeholder="Ünvan daxil edin" name="market_address"></textarea>
										<div class="invalid-feedback" id="error_market_address"></div>
									</div>
								</div>
								
							</div>


							<div class="form-set group-img">
								<div class="group-img">
									<i class="feather-link"></i>
									<input type="text" class="form-control"
										placeholder="Mağazanın URL ünvanı (vebsayt və ya sosial media profili, https://sayt.com)"
										name="market_url" />
									<div class="invalid-feedback" id="error_market_url"></div>

								</div>
							</div>

							<div class="form-set group-img">
								<div class="group-img">
									<i class="feather-globe" style="z-index: 3; top: 14px"></i>
									<select id="ad_countries" class="form-control" name="market_ad_view_countries[]"
										multiple>
									</select>
									<div class="invalid-feedback" id="error_market_ad_view_countries"></div>
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
							</div>





							<!-- /Login Form -->

					</div>

					<div class="col-md-6 col-lg-4">

						<style>
							.sticky-summary {
								position: sticky;
								top: 135px;
								z-index: 10;
							}

							/* Bootstrap row/col içində bəzən işləmir deyə vacib fix: */
							.row {
								overflow: visible;
							}
						</style>
						<div id="summary-box" class="card p-3 sticky-summary"
							style="background-image: url(https://png.pngtree.com/background/20210711/original/pngtree-light-vertical-stripes-texture-background-image-picture-image_1132424.jpg);">
							<h5>Ümumi məlumat</h5>

							<p>Seçilən ölkə: <b><span id="sumCountries">0</span></b></p>
							<p>Ümumi izləyici: <b><span id="sumAudience">0</span></b></p>

							<button class="btn btn-primary w-100 login-btn" type="submit">Ödəniş <i
									class="fa-solid fa-angles-right"></i></button>
						</div>




						</form>

					</div>
				</div>
			</div>


		</div>
		<!-- /Login Section -->


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

	<!-- intl-tel-input JS -->
	<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>



	<script>
		(() => {
			"use strict";

			const marketType = document.getElementById("market_type");
			const locationDiv = document.getElementById("location");
			const marketDesc = document.getElementById("market_desc");
			const phoneInput = document.getElementById("phone");

			const countrySelect = document.getElementById("country");
			const citySelect = document.getElementById("city");
			const adCountriesSelect = document.getElementById("ad_countries");

			const sumCountriesEl = document.getElementById("sumCountries");
			const sumAudienceEl = document.getElementById("sumAudience");

			let iti = null;

			const inputLogo = document.getElementById("marketLogoInput");
			const preview = document.getElementById("marketLogoPreview");


			function clearErrors() {
				document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

				document.querySelectorAll(".invalid-feedback").forEach(el => {
					el.textContent = "";
					el.classList.remove("d-block");
				});
			}

			function showFieldError(fieldName, message) {
				// 1) input tap (array field-lər üçün fallback)
				let input =
					document.querySelector(`[name="${fieldName}"]`) ||
					document.querySelector(`[name="${fieldName.replace(/\[\]$/, "")}[]"]`);

				// 2) error element tap (id-də [] istifadə etməmişik)
				const cleanName = fieldName.replace(/\[\]$/, "");
				const errorEl = document.getElementById(`error_${cleanName}`);

				// 3) əvvəl scroll target
				let scrollTarget = input;

				// 4) TomSelect varsa wrapper-a invalid ver
				if (input && input.tomselect) {
					const wrapper = input.tomselect.wrapper; // .ts-wrapper
					if (wrapper) {
						wrapper.classList.add("is-invalid");
						scrollTarget = wrapper;
					}
				} else if (input) {
					input.classList.add("is-invalid");
				}

				// 5) mesajı göstər (bootstrap bəzən display none saxlayır)
				if (errorEl) {
					errorEl.textContent = message;
					errorEl.classList.add("d-block");
				}

				// 6) scroll + focus
				if (scrollTarget) {
					scrollTarget.scrollIntoView({ behavior: "smooth", block: "center" });
					setTimeout(() => {
						// TomSelect olduqda focus control inputa düşsün
						if (input && input.tomselect) input.tomselect.focus();
						else input?.focus?.();
					}, 300);
				}
			}


			// LOGO PREVIEW
			if (inputLogo && preview) {
				inputLogo.addEventListener("change", () => {
					const file = inputLogo.files && inputLogo.files[0];
					if (!file) return;

					console.log("Selected file:", file);

					if (!file.type || !file.type.startsWith("image/")) {
						showError("Zəhmət olmasa şəkil faylı seçin.");
						inputLogo.value = "";
						return;
					}

					const MAX = 10 * 1024 * 1024;
					if (file.size > MAX) {
						showError("Fayl ölçüsü maksimum 10 MB olmalıdır.");
						inputLogo.value = "";
						return;
					}

					const url = URL.createObjectURL(file);
					preview.src = url;
					preview.onload = () => URL.revokeObjectURL(url);
				});
			}



			function destroyTomSelect(el) {
				if (el && el.tomselect) {
					el.tomselect.destroy();
					el.tomselect = null;
				}
			}

			function toggleMarketFields() {
				const val = marketType.value;
				if (val === "online_market") {
					locationDiv.style.display = "none";
				} else if (val === "physical_market") {
					locationDiv.style.display = "flex";
				} else {
					locationDiv.style.display = "none";
				}
			}


			// ---------- intlTelInput init ----------
			try {
				iti = window.intlTelInput(phoneInput, {
					initialCountry: "auto",
					separateDialCode: false,
					nationalMode: false,
					autoPlaceholder: "polite",
					utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
					geoIpLookup: function (callback) {
						fetch("https://ipapi.co/json/")
							.then(res => res.json())
							.then(data => callback((data && data.country_code) ? data.country_code : "us"))
							.catch(() => callback("us"));
					}
				});

				// ölkə dəyişəndə kodu inputa yaz
				phoneInput.addEventListener("countrychange", function () {
					const countryData = iti.getSelectedCountryData();
					const dialCode = "+" + countryData.dialCode;

					// əgər input boşdursa və ya əvvəl yalnız kod varsa
					if (
						phoneInput.value.trim() === "" ||
						phoneInput.value.startsWith("+")
					) {
						phoneInput.value = dialCode + " ";
					}
				});

				// ilk açılışda da kod qoy
				setTimeout(() => {
					const countryData = iti.getSelectedCountryData();
					if (countryData && !phoneInput.value) {
						phoneInput.value = "+" + countryData.dialCode + " ";
					}
				}, 300);

			} catch (e) {
				console.warn("intlTelInput init olmadı:", e);
			}


			async function updateSummary(countryCodes) {
				if (!countryCodes || !countryCodes.length) {
					sumCountriesEl.textContent = 0;
					sumAudienceEl.textContent = 0;
					return;
				}

				try {
					const r = await fetch(`api/geo/country-stats.php`, {
						method: "POST",
						headers: { "Content-Type": "application/json" },
						body: JSON.stringify({
							country_codes: countryCodes,
							market_type: marketType.value
						})
					});
					const res = await r.json();
					if (!r.ok || res.ok === false) throw new Error(res.error || "Summary error");

					sumCountriesEl.textContent = res.countries;
					sumAudienceEl.textContent = Number(res.totalAudience).toLocaleString();
				} catch (err) {
					console.error("Summary error:", err);
				}
			}

			async function loadCountries() {
				// reset
				countrySelect.length = 1;
				adCountriesSelect.innerHTML = "";

				const r = await fetch(`api/geo/countries.php`);
				const res = await r.json();
				if (!r.ok || !res.ok) throw new Error(res.error || "Countries load failed");

				res.data.forEach(c => {
					if (c.country_audience > 0) {
						// Offline country select (code)
						const opt1 = document.createElement("option");
						opt1.value = c.code;     // AZ
						opt1.textContent = c.name;
						countrySelect.appendChild(opt1);

						// Ad countries multi (code)
						const opt2 = document.createElement("option");
						opt2.value = c.code;
						opt2.textContent = c.name;
						adCountriesSelect.appendChild(opt2);
					}
				});

				destroyTomSelect(adCountriesSelect);
				new TomSelect(adCountriesSelect, {
					plugins: ['remove_button'],
					maxOptions: null,
					maxItems: null,
					placeholder: "Elanınızın göstəriləcəyi ölkələr",
					persist: false,
					onChange(values) { updateSummary(values); },
				});
			}

			async function loadCities(countryCode) {
				citySelect.innerHTML = '<option value="" disabled selected>Yüklənir...</option>';

				const r = await fetch(`api/geo/cities.php?country_code=${encodeURIComponent(countryCode)}`);
				const res = await r.json();
				if (!r.ok || !res.ok) throw new Error(res.error || "Cities load failed");

				citySelect.innerHTML = '<option value="" disabled selected>-- Şəhər seçin --</option>';
				res.data.forEach(city => {
					const opt = document.createElement("option");
					opt.value = city.id;     // int
					opt.textContent = city.name;
					citySelect.appendChild(opt);
				});
			}

			document.addEventListener("DOMContentLoaded", async () => {
				marketType.addEventListener("change", () => {
					toggleMarketFields();
					// market type dəyişəndə summary qiyməti dəyişə bilər
					const vals = adCountriesSelect.tomselect ? adCountriesSelect.tomselect.getValue() : [];
					updateSummary(Array.isArray(vals) ? vals : [vals]);
				});
				toggleMarketFields();

				try {
					await loadCountries();
				} catch (err) {
					console.error("Countries load error:", err);
				}

				countrySelect.addEventListener("change", async function () {
					const code = this.value; // AZ
					if (!code) return;
					try {
						await loadCities(code);
					} catch (err) {
						console.error("Cities load error:", err);
					}
				});

				const marketForm = document.getElementById("marketForm");

				marketForm.addEventListener("submit", async function (e) {
					e.preventDefault();

					clearErrors();

					if (iti) phoneInput.value = iti.getNumber();

					const fd = new FormData(marketForm);

					try {
						const res = await fetch(`api/auth/signup-market-save.php`, {
							method: "POST",
							body: fd
						});

						const data = await res.json();
						if (!res.ok || !data.ok) {
							// Əgər backend field əsaslı error qaytarırsa
							if (data.field && data.error) {
								showFieldError(data.field, data.error);
								return;
							}

							showError(data.error || "Xəta baş verdi.");
							return;
						}



						window.location.href = data.redirect || "/endout/App/payment";
					} catch (err) {
						console.error(err);
						showError("Serverə qoşulmaq olmadı.");
					}
				});
			});

		})();



	</script>



	<script>
		document.addEventListener("DOMContentLoaded", function () {

			let controller;

			const el = document.querySelector("#market_tags");

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






</body>


</html>