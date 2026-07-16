<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_role('partner');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php require_once './inc/head.php'; ?>
</head>

<body>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once './inc/header.php'; ?>
		<!-- /Header -->


		<!-- Profile Content -->
		<div class="dashboard-content listing-section ">
			<div class="container">
				<div style="margin-top: 70px;">
					<ul class="dashborad-menus">
						<li>
							<a href="./dashboard">
								<i class="feather-grid"></i> <span>İdarəetmə paneli</span>
							</a>
						</li>
						<li>
							<a href="./profile">
								<i class="fa-solid fa-user"></i> <span>Hesab</span>
							</a>
						</li>
						<li class="active">
							<a href="./my-market">
								<i class="fas fa-solid fa-store"></i> <span>Mağaza</span>
							</a>
						</li>
						<li>
							<a href="./my-listings">
								<i class="feather-list"></i> <span>Elanlarım</span>
							</a>
						</li>
						<li>
							<a href="./messages">
								<i class="fa-solid fa-comment-dots"></i> <span>Mesajlar</span>
							</a>
						</li>
						<li>
							<a href="/reviews">
								<i class="fas fa-solid fa-star"></i> <span>Sorğular</span>
							</a>
						</li>
						<li>
							<a href="#" id="logoutBtn">
								<i class="fas fa-light fa-circle-arrow-left"></i> <span>Çıxış</span>
							</a>
						</li>
					</ul>
				</div>
				<div class="profile-content">
					<div class="dashboard-info">

						<div class="card dash-cards">
							<div class="card-header">
								<h4 class="d-flex align-items-center gap-2 m-0">
									<span id="marketNameText"><?= htmlspecialchars($market_name) ?></span>

									<input id="marketNameInput" type="text" class="form-control form-control-sm"
										style="display:none; max-width: 320px;"
										value="<?= htmlspecialchars($market_name) ?>" />

									<button id="editMarketNameBtn" type="button" class="bg-transparent border-0 p-0"
										data-market-id="<?= (int) $market['id'] ?>" aria-label="Mağaza adını dəyiş">
										<i class="fa-solid fa-pen-to-square"></i>
									</button>


								</h4>

								<!-- CSRF -->
								<input type="hidden" id="csrfToken"
									value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">
								<small id="marketNameMsg" class="text-danger d-block mt-1"
									style="display:none;"></small>

								<small>(<i><?php if ($market_type == "online_market")
									echo "Onlayn mağaza";
								else
									echo "Fiziki mağaza"; ?></i>)</small>
							</div>
							<div class="card-body">
								<div class="row profile-photo">


									<div class="profile-img col-md-5 mt-1">
										<div class="settings-upload-img">
											<img id="marketLogoPreview"
												src="assets/img/market-logo/<?= htmlspecialchars($market_logo) ?>"
												alt="<?= htmlspecialchars($market_name) ?>" class="img-fluid">
										</div>

										<div class="settings-upload-btn">
											<input type="file" accept="image/*" name="market_logo"
												class="hide-input image-upload" id="marketLogoInput"
												data-market-id="<?= (int) $market['id'] ?>">

											<label for="marketLogoInput" class="file-upload">Yeni loqotip
												yüklə</label>
											<br>
											<i style="font-size: 10px;">Max fayl ölçüsü: 10 MB</i>

											<!-- Progress -->
											<div id="logoProgressWrap"
												style="display:none; margin-top:10px; max-width:320px;">
												<progress id="logoProgress" value="0" max="100"
													style="width:100%;"></progress>
												<div style="font-size:12px;">
													<span id="logoProgressText">0%</span>
												</div>
											</div>
										</div>

										<input type="hidden" id="csrfToken"
											value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">
										<small id="logoMsg" class="text-danger d-block mt-2"
											style="display:none;"></small>


									</div>

									<div class="col-md-1">
										<i class="fa-solid fa-eye"></i> <strong><?= (int) $market_views ?></strong>
									</div>

									<div class="col-md-6 mt-1">
										<div class="row">
											<div class="col-12 location-info">
												<style>
													.location-info {
														text-align: end;
													}

													@media screen and (max-width: 991px) {
														.location-info {
															text-align: left;
															margin-top: 30px;
														}
													}
												</style>
												<span
													style="padding: 15px 25px; border: 3px dotted #c10037; border-radius: 8px;">
													<i class="fa-solid fa-map-pin" style="color: #c10037"></i>
													<?= htmlspecialchars($market_country_name) ?>,
													<?= htmlspecialchars($market_city_name) ?>
												</span>
											</div>
										</div>
									</div>


								</div>

								<div class="profile-form">
									<form>
										<div class="row">
											<div class="col-lg-6 col-md-6">
												<div class="form-set">
													<label class="col-form-label">Əlaqə nömrəsi</label>
													<div class="editable-group group-img" data-field="phone_number">
														<span class="lock-icon">
															<i class="feather-phone-call"></i>
														</span>

														<input type="tel" class="form-control editable-input"
															value="<?= htmlspecialchars($market_phone) ?>"
															data-original="<?= htmlspecialchars($market_phone) ?>"
															readonly>

														<div class="edit-actions d-none">
															<button type="button"
																class="btn btn-sm btn-success btn-ok">OK</button>
															<button type="button"
																class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
														</div>
													</div>
												</div>
											</div>


											<div class="col-lg-6 col-md-6">
												<div class="form-set">
													<label class="col-form-label">E-poçt</label>
													<div class="editable-group group-img" data-field="email">
														<i class="feather-mail"></i>

														<input type="text" class="form-control editable-input"
															value="<?= htmlspecialchars($market_email) ?>"
															data-original="<?= htmlspecialchars($market_email) ?>"
															readonly>

														<div class="edit-actions d-none">
															<button type="button"
																class="btn btn-sm btn-success btn-ok">OK</button>
															<button type="button"
																class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
														</div>
													</div>
												</div>
											</div>

											<style>
												.editable-group {
													position: relative;
												}

												.edit-actions {
													position: absolute;
													right: 5px;
													top: 50%;
													transform: translateY(-50%);
													display: flex;
													gap: 5px;
												}
											</style>
										</div>


										<?php if ($market_type === 'physical_market'): ?>
											<div class="row">

												<div class="form-set">
													<label class="col-form-label">Ünvan</label>
													<div class="editable-group group-img" data-field="address">
														<i class="feather-map-pin"></i></span>
														<input type="text" class="form-control editable-input"
															value="<?= htmlspecialchars($market_address) ?>"
															data-original="<?= htmlspecialchars($market_address) ?>"
															readonly>
														<div class="edit-actions d-none">
															<button type="button"
																class="btn btn-sm btn-success btn-ok">OK</button>
															<button type="button"
																class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
														</div>
													</div>
												</div>


												<div class="col-12">
													<div class="form-set">
														<label class="col-form-label">Elanların göstəriləcəyi
															ölkələr</label>
														<div class="group-img">
															<i class="feather-globe"></i></span>
															<input type="text" class="form-control" readonly
																value="<?= htmlspecialchars($countryList) ?>">
														</div>
													</div>
												</div>

											<?php endif; ?>



											<div class="row">
												<div class="col-lg-6 col-md-6">
													<div class="form-set">
														<label class="col-form-label">Promokod</label>
														<div class="editable-group group-img" data-field="promocode">
															<span class="lock-icon">
																<i class="fa-solid fa-bullhorn"></i>
															</span>

															<input type="tel" class="form-control editable-input"
																value="<?= htmlspecialchars($market_promo_code) ?>"
																data-original="<?= htmlspecialchars($market_promo_code) ?>"
																>

															<div class="edit-actions d-none">
																<button type="button"
																	class="btn btn-sm btn-success btn-ok">OK</button>
																<button type="button"
																	class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
															</div>
														</div>
													</div>
												</div>


												<div class="col-lg-6 col-md-6">
													<div class="form-set">
														<label class="col-form-label">Endirim</label>
														<div class="editable-group group-img" data-field="promodiscount">
															<i class="feather-percent"></i>

															<input type="text" class="form-control editable-input"
																value="<?= htmlspecialchars($market_promo_discount) ?>"
																data-original="<?= htmlspecialchars($market_promo_discount) ?>"
																>

															<div class="edit-actions d-none">
																<button type="button"
																	class="btn btn-sm btn-success btn-ok">OK</button>
																<button type="button"
																	class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
															</div>
														</div>
													</div>
												</div>

												<style>
													.editable-group {
														position: relative;
													}

													.edit-actions {
														position: absolute;
														right: 5px;
														top: 50%;
														transform: translateY(-50%);
														display: flex;
														gap: 5px;
													}
												</style>
											</div>

											<?php if ($market_type === 'online_market'): ?>
												<div class="form-set">
													<label class="col-form-label">Mağaza URL</label>
													<div class="group-img editable-group" data-field="url">
														<i class="feather-link"></i>
														<input type="text" class="form-control editable-input"
															value="<?= htmlspecialchars($market_url) ?>"
															data-original="<?= htmlspecialchars($market_url) ?>" readonly>
														<div class="edit-actions d-none">
															<button type="button"
																class="btn btn-sm btn-success btn-ok">OK</button>
															<button type="button"
																class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
														</div>
													</div>
												</div>
											<?php endif; ?>


											<div class="form-set">
												<label class="col-form-label">Mağaza haqqında</label>
												<div class="pass-group group-img editable-group"
													data-field="description">
													<textarea rows="4"
														class="form-control editable-input"><?= htmlspecialchars($market_description) ?></textarea>
													<div class="edit-actions d-none">
														<button type="button"
															class="btn btn-sm btn-success btn-ok">OK</button>
														<button type="button"
															class="btn btn-sm btn-secondary btn-cancel">İmtina</button>
													</div>
												</div>
											</div>


											<div class="row socialmedia-info" id="socialMediaWrap"
												data-market-id="<?= (int) $market['id'] ?>">

												<div class="col-12" id="socialList">
													<div class="row">
														<?php foreach ($market_socials as $social): ?>
															<div class="col-lg-6 col-md-6 social-item"
																data-social-id="<?= (int) $social['id'] ?>">
																<div class="form-set">
																	<label
																		class="col-form-label d-flex align-items-center justify-content-between">
																		<span><?= htmlspecialchars($social['social_name']) ?></span>

																		<div class="d-flex gap-2">
																			<button type="button"
																				class="bg-transparent border-0 p-0 social-edit-btn"
																				aria-label="URL dəyiş">
																				<i class="fa-solid fa-pen-to-square"></i>
																			</button>

																			<button type="button"
																				class="bg-transparent border-0 p-0 social-delete-btn"
																				aria-label="Sil">
																				<i class="fa-solid fa-trash"></i>
																			</button>
																		</div>
																	</label>

																	<div class="group-img">
																		<span class="lock-icon">
																			<i
																				class="fab <?= htmlspecialchars($social['social_icon']) ?>"></i>
																		</span>

																		<input type="text"
																			class="form-control social-url-input"
																			value="<?= htmlspecialchars($social['social_url']) ?>"
																			readonly>
																	</div>

																	<small class="text-danger d-block mt-1 social-row-msg"
																		style="display:none;"></small>
																</div>
															</div>
														<?php endforeach; ?>
													</div>
												</div>

												<!-- Yeni əlavə etmə formu buraya gələcək -->
												<div class="col-12 mt-2" id="addSocialFormWrap" style="display:none;">
												</div>

												<div class="col-lg-12 col-md-12 mt-2">
													<button type="button" class="btn btn-success w-100"
														id="add_social_btn">
														<i class="fa-solid fa-plus"></i> Sosial media hesabı əlavə et
													</button>
												</div>

												<small id="socialMsg" class="text-danger d-block mt-2"
													style="display:none;"></small>
											</div>

											<input type="hidden" id="csrfToken"
												value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">


									</form>
								</div>

							</div>

						</div>

					</div>
				</div>

			</div>
		</div>
	</div>
	</div>
	<!-- /Profile Content -->


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
	<?php include('./inc/alert_modal.php'); ?>

	<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
		data-cf-settings="37d30e4c122c312a5c11c41a-|49" defer></script>

	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const btn = document.getElementById("editMarketNameBtn");
			const text = document.getElementById("marketNameText");
			const input = document.getElementById("marketNameInput");
			const msg = document.getElementById("marketNameMsg");
			const csrf = document.getElementById("csrfToken")?.value || "";

			const API_URL = "api/update/market-name.php"; // öz path-in

			let editing = false;
			let oldValue = (text.textContent || "").trim();
			let isSaving = false;

			function showError(t) {
				if (!msg) return;
				msg.style.display = "block";
				msg.textContent = t;
			}
			function clearError() {
				if (!msg) return;
				msg.style.display = "none";
				msg.textContent = "";
			}

			function setIcon(isOk) {
				btn.innerHTML = isOk
					? '<i class="fa-solid fa-check"></i>'
					: '<i class="fa-solid fa-pen-to-square"></i>';
			}

			function startEdit() {
				editing = true;
				clearError();
				oldValue = (text.textContent || "").trim();

				input.value = oldValue;
				text.style.display = "none";
				input.style.display = "inline-block";
				input.focus();
				input.select();

				setIcon(true);
			}

			function stopEdit(restoreOld = false) {
				editing = false;
				clearError();

				if (restoreOld) input.value = oldValue;

				text.style.display = "inline";
				input.style.display = "none";

				setIcon(false);
			}

			async function save() {
				if (isSaving) return;
				clearError();

				const marketId = btn.dataset.marketId;
				const newName = (input.value || "").trim();

				if (newName.length < 2) {
					showError("Mağaza adı ən az 2 simvol olmalıdır.");
					input.focus();
					return;
				}
				if (newName === oldValue) {
					stopEdit(false);
					return;
				}

				isSaving = true;
				btn.disabled = true;

				try {
					const form = new FormData();
					form.append("_csrf", csrf);
					form.append("market_id", marketId);
					form.append("name", newName);

					const res = await fetch(API_URL, {
						method: "POST",
						body: form,
						headers: { "X-Requested-With": "XMLHttpRequest" }
					});

					const raw = await res.text();
					let data = null;
					try { data = JSON.parse(raw); } catch (e) { }

					if (!res.ok || !data || data.ok !== true) {
						const err = (data && (data.error || data.message)) ? (data.error || data.message) : "Xəta baş verdi.";
						showError(err);
						return;
					}

					text.textContent = newName;
					stopEdit(false);

				} catch (e) {
					showError("Şəbəkə xətası. Yenidən yoxla.");
				} finally {
					isSaving = false;
					btn.disabled = false;
				}
			}

			// ✅ KRİTİK: blur-da bağlamağı SİLİRİK.
			// input.addEventListener("blur", () => { if (editing) stopEdit(true); });

			// ✅ KRİTİK: düyməyə klik edəndə input blur olmadan işləsin deyə mousedown ilə prevent edirik
			btn.addEventListener("mousedown", (e) => {
				if (editing) e.preventDefault(); // fokus inputda qalsın, blur olmasın
			});

			btn.addEventListener("click", () => {
				if (!editing) startEdit();
				else save(); // ✓ klik -> save
			});

			input.addEventListener("keydown", (e) => {
				if (e.key === "Enter") {
					e.preventDefault();
					save();
				}
				if (e.key === "Escape") {
					e.preventDefault();
					stopEdit(true);
				}
			});

			input.addEventListener("blur", () => {
				if (editing) stopEdit(true);
			});
		});
	</script>


	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const input = document.getElementById("marketLogoInput");
			const img = document.getElementById("marketLogoPreview");
			const msg = document.getElementById("logoMsg");
			const csrf = document.getElementById("csrfToken")?.value || "";

			const wrap = document.getElementById("logoProgressWrap");
			const bar = document.getElementById("logoProgress");
			const barTxt = document.getElementById("logoProgressText");

			const API_URL = "/endout/App/api/update/market-logo.php"; // öz path-in

			function showError(text) {
				msg.style.display = "block";
				msg.textContent = text;
			}
			function clearError() {
				msg.style.display = "none";
				msg.textContent = "";
			}

			function showProgress() {
				wrap.style.display = "block";
				bar.value = 0;
				barTxt.textContent = "0%";
			}
			function hideProgress() {
				wrap.style.display = "none";
				bar.value = 0;
				barTxt.textContent = "0%";
			}

			input.addEventListener("change", () => {
				clearError();

				const file = input.files?.[0];
				if (!file) return;

				if (!file.type.startsWith("image/")) {
					showError("Yalnız şəkil faylı yükləmək olar.");
					input.value = "";
					return;
				}
				if (file.size > 10 * 1024 * 1024) {
					showError("Fayl 10MB-dan böyük ola bilməz.");
					input.value = "";
					return;
				}

				const oldSrc = img.src;

				// Instant preview
				img.src = URL.createObjectURL(file);

				const fd = new FormData();
				fd.append("_csrf", csrf);
				fd.append("market_id", input.dataset.marketId);
				fd.append("market_logo", file);

				showProgress();

				const xhr = new XMLHttpRequest();
				xhr.open("POST", API_URL, true);
				xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

				xhr.upload.onprogress = (e) => {
					if (!e.lengthComputable) return;
					const percent = Math.round((e.loaded / e.total) * 100);
					bar.value = percent;
					barTxt.textContent = percent + "%";
				};

				xhr.onload = () => {
					hideProgress();

					let data = null;
					try { data = JSON.parse(xhr.responseText); } catch (e) { }

					if (xhr.status >= 200 && xhr.status < 300 && data && data.ok) {
						// cache bust
						img.src = data.new_logo_url + "?t=" + Date.now();
						input.value = "";
					} else {
						img.src = oldSrc;
						showError((data && (data.error || data.message)) ? (data.error || data.message) : "Xəta baş verdi.");
					}
				};

				xhr.onerror = () => {
					hideProgress();
					img.src = oldSrc;
					showError("Şəbəkə xətası.");
				};

				xhr.send(fd);
			});
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const wrap = document.getElementById("socialMediaWrap");
			const list = document.getElementById("socialList");
			const formWrap = document.getElementById("addSocialFormWrap");
			const addBtn = document.getElementById("add_social_btn");
			const msg = document.getElementById("socialMsg");
			const csrf = document.getElementById("csrfToken")?.value || "";

			const API_URL = "/endout/App/api/create/market-social.php"; // <-- path-i uyğunlaşdır
			const marketId = wrap.dataset.marketId;

			// select optionlar (istəsən backend-dən də çəkərik)
			const SOCIALS = [
				{ name: "Facebook", icon: "fa-facebook-f" },
				{ name: "Instagram", icon: "fa-instagram" },
				{ name: "TikTok", icon: "fa-tiktok" },
				{ name: "YouTube", icon: "fa-youtube" },
				{ name: "X (Twitter)", icon: "fa-x-twitter" },
				{ name: "LinkedIn", icon: "fa-linkedin-in" },
				{ name: "Telegram", icon: "fa-telegram" },
				{ name: "WhatsApp", icon: "fa-whatsapp" },
				{ name: "Website", icon: "fa-globe" } // bu "fab" deyil, amma istəsən ayrıca idarə edərik
			];

			function showError(t) {
				msg.style.display = "block";
				msg.textContent = t;
			}
			function clearError() {
				msg.style.display = "none";
				msg.textContent = "";
			}

			function renderAddForm() {
				const options = SOCIALS.map(s => `<option value="${encodeURIComponent(s.name)}" data-icon="${s.icon}">
	  ${s.name}
	</option>`).join("");

				formWrap.innerHTML = `
	  <div class="card p-3">
		<div class="row g-2 align-items-end">
		  <div class="col-lg-4 col-md-5">
			<label class="col-form-label">Sosial şəbəkə</label>
			<select class="form-select" id="socialTypeSelect">
			  <option value="">Seçin...</option>
			  ${options}
			</select>
		  </div>
		  <div class="col-lg-6 col-md-7">
			<label class="col-form-label">URL</label>
			<input type="text" class="form-control" id="socialUrlInput" placeholder="https://...">
		  </div>
		  <div class="col-lg-2 col-md-12 d-flex gap-2">
			<button type="button" class="btn btn-primary w-100" id="socialOkBtn">
			  <i class="fa-solid fa-check"></i>
			</button>
			<button type="button" class="btn btn-outline-secondary w-100" id="socialCancelBtn">
			  <i class="fa-solid fa-xmark"></i>
			</button>
		  </div>
		</div>
	  </div>
	`;
				formWrap.style.display = "block";
			}

			function closeAddForm() {
				formWrap.style.display = "none";
				formWrap.innerHTML = "";
				clearError();
			}

			function appendSocialToUI(social) {
				const html = `
	<div class="col-lg-6 col-md-6 social-item" data-social-id="${social.id}">
	  <div class="form-set">
		<label class="col-form-label d-flex align-items-center justify-content-between">
		  <span>${escapeHtml(social.social_name)}</span>

		  <div class="d-flex gap-2">
			<button type="button" class="bg-transparent border-0 p-0 social-edit-btn" aria-label="URL dəyiş">
			  <i class="fa-solid fa-pen-to-square"></i>
			</button>
			<button type="button" class="bg-transparent border-0 p-0 social-delete-btn" aria-label="Sil">
			  <i class="fa-solid fa-trash"></i>
			</button>
		  </div>
		</label>

		<div class="group-img">
		  <span class="lock-icon"><i class="fab ${escapeHtml(social.social_icon)}"></i></span>
		  <input type="text" class="form-control social-url-input"
				 value="${escapeHtml(social.social_url)}" readonly>
		</div>

		<small class="text-danger d-block mt-1 social-row-msg" style="display:none;"></small>
	  </div>
	</div>
  `;
				const row = list.querySelector(".row");
				row.insertAdjacentHTML("beforeend", html);
			}

			function escapeHtml(str) {
				return String(str ?? "")
					.replaceAll("&", "&amp;")
					.replaceAll("<", "&lt;")
					.replaceAll(">", "&gt;")
					.replaceAll('"', "&quot;")
					.replaceAll("'", "&#039;");
			}

			addBtn.addEventListener("click", () => {
				if (formWrap.style.display === "block") {
					closeAddForm();
				} else {
					renderAddForm();
					clearError();
				}
			});

			// Delegation: form içi buttonlar
			formWrap.addEventListener("click", async (e) => {
				const ok = e.target.closest("#socialOkBtn");
				const cancel = e.target.closest("#socialCancelBtn");

				if (cancel) {
					closeAddForm();
					return;
				}

				if (!ok) return;

				clearError();

				const select = document.getElementById("socialTypeSelect");
				const urlInput = document.getElementById("socialUrlInput");

				const typeVal = decodeURIComponent(select.value || "");
				const urlVal = (urlInput.value || "").trim();

				if (!typeVal) {
					showError("Sosial şəbəkəni seçin.");
					return;
				}
				if (!urlVal || urlVal.length < 5) {
					showError("URL daxil edin.");
					return;
				}

				// Seçilən icon
				const opt = select.options[select.selectedIndex];
				const icon = opt?.dataset?.icon || "fa-link";

				ok.disabled = true;

				try {
					const fd = new FormData();
					fd.append("_csrf", csrf);
					fd.append("market_id", marketId);
					fd.append("social_name", typeVal);
					fd.append("social_url", urlVal);
					fd.append("social_icon", icon);

					const res = await fetch(API_URL, {
						method: "POST",
						body: fd,
						headers: { "X-Requested-With": "XMLHttpRequest" }
					});

					const data = await res.json();

					if (!res.ok || !data.ok) {
						showError(data.error || "Xəta baş verdi.");
						return;
					}

					// UI-ya əlavə et (serverdən qaydanı götürürük)
					appendSocialToUI(data.social);
					closeAddForm();

				} catch (err) {
					showError("Şəbəkə xətası.");
				} finally {
					ok.disabled = false;
				}
			});

		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const listWrap = document.getElementById("socialList"); // səndə var
			const csrf = document.getElementById("csrfToken")?.value || "";
			const API_UPDATE_URL = "/endout/App/api/update/market-social-url.php"; // <-- path
			const API_DELETE_URL = "/endout/App/api/delete/market-social.php"; // <-- path

			function showRowError(item, text) {
				const el = item.querySelector(".social-row-msg");
				if (!el) return;
				el.style.display = "block";
				el.textContent = text;
			}
			function clearRowError(item) {
				const el = item.querySelector(".social-row-msg");
				if (!el) return;
				el.style.display = "none";
				el.textContent = "";
			}

			// ✅ DELETE listener (listWrap içində delegation)
			listWrap.addEventListener("click", async (e) => {
				const delBtn = e.target.closest(".social-delete-btn");
				if (!delBtn) return;

				const item = delBtn.closest(".social-item");
				if (!item) return;

				const socialId = item.dataset.socialId;
				clearRowError(item);

				if (!socialId) {
					showRowError(item, "social_id tapılmadı.");
					return;
				}

				if (!confirm("Bu sosial media linkini silmək istəyirsən?")) return;

				delBtn.disabled = true;

				try {
					const fd = new FormData();
					fd.append("_csrf", csrf);
					fd.append("social_id", socialId);

					const res = await fetch(API_DELETE_URL, {
						method: "POST",
						body: fd,
						headers: { "X-Requested-With": "XMLHttpRequest" }
					});

					const data = await res.json();

					if (!res.ok || !data.ok) {
						showRowError(item, data.error || "Silinmədi.");
						return;
					}

					item.remove();
				} catch (err) {
					showRowError(item, "Şəbəkə xətası.");
				} finally {
					delBtn.disabled = false;
				}
			});

			function setBtnIcon(btn, ok) {
				btn.innerHTML = ok
					? '<i class="fa-solid fa-check"></i>'
					: '<i class="fa-solid fa-pen-to-square"></i>';
			}

			function showRowError(item, text) {
				const el = item.querySelector(".social-row-msg");
				if (!el) return;
				el.style.display = "block";
				el.textContent = text;
			}

			function clearRowError(item) {
				const el = item.querySelector(".social-row-msg");
				if (!el) return;
				el.style.display = "none";
				el.textContent = "";
			}

			// Delegation: list içində bütün pen/ok düymələrini idarə edirik
			listWrap.addEventListener("mousedown", (e) => {
				// edit rejimində düyməyə basanda input blur olmasın
				const btn = e.target.closest(".social-edit-btn");
				if (btn && btn.dataset.mode === "edit") {
					e.preventDefault();
				}
			});

			listWrap.addEventListener("click", async (e) => {
				const btn = e.target.closest(".social-edit-btn");
				if (!btn) return;

				const item = btn.closest(".social-item");
				const input = item?.querySelector(".social-url-input");
				if (!item || !input) return;

				clearRowError(item);

				const socialId = item.dataset.socialId;
				const mode = btn.dataset.mode || "view";

				if (mode === "view") {
					// edit aç
					btn.dataset.mode = "edit";
					btn.dataset.old = input.value;
					input.readOnly = false;
					input.focus();
					input.select();
					setBtnIcon(btn, true);
					return;
				}

				// save
				const newUrl = (input.value || "").trim();
				const oldUrl = btn.dataset.old || "";

				if (newUrl.length < 5) {
					showRowError(item, "URL daxil edin.");
					input.focus();
					return;
				}

				if (newUrl === oldUrl) {
					// dəyişməyibsə bağla
					btn.dataset.mode = "view";
					input.readOnly = true;
					setBtnIcon(btn, false);
					return;
				}

				btn.disabled = true;

				try {
					const fd = new FormData();
					fd.append("_csrf", csrf);
					fd.append("social_id", socialId);
					fd.append("social_url", newUrl);

					const res = await fetch(API_UPDATE_URL, {
						method: "POST",
						body: fd,
						headers: { "X-Requested-With": "XMLHttpRequest" }
					});

					const data = await res.json();

					if (!res.ok || !data.ok) {
						input.value = oldUrl; // geri qaytar
						showRowError(item, data.error || "Xəta baş verdi.");
						return;
					}

					// uğurlu
					input.value = data.social_url; // serverdən qaydanı qoyaq
					btn.dataset.mode = "view";
					input.readOnly = true;
					setBtnIcon(btn, false);

				} catch (err) {
					input.value = oldUrl;
					showRowError(item, "Şəbəkə xətası.");
				} finally {
					btn.disabled = false;
				}
			});

			// Enter -> save, Esc -> cancel
			listWrap.addEventListener("keydown", (e) => {
				const input = e.target.closest(".social-url-input");
				if (!input) return;

				const item = input.closest(".social-item");
				const btn = item?.querySelector(".social-edit-btn");
				if (!item || !btn) return;

				if (e.key === "Enter") {
					e.preventDefault();
					btn.click();
				}
				if (e.key === "Escape") {
					e.preventDefault();
					const oldUrl = btn.dataset.old || input.value;
					input.value = oldUrl;
					input.readOnly = true;
					btn.dataset.mode = "view";
					setBtnIcon(btn, false);
					clearRowError(item);
				}
			});
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {

			document.querySelectorAll(".editable-group").forEach(group => {

				const input = group.querySelector(".editable-input");
				const actions = group.querySelector(".edit-actions");
				const okBtn = group.querySelector(".btn-ok");
				const cancelBtn = group.querySelector(".btn-cancel");
				const field = group.dataset.field;

				// Input click
				input.addEventListener("click", () => {
					input.removeAttribute("readonly");
					actions.classList.remove("d-none");
					input.focus();
				});

				// Cancel
				cancelBtn.addEventListener("click", () => {
					input.value = input.dataset.original;
					input.setAttribute("readonly", true);
					actions.classList.add("d-none");
				});

				// OK
				okBtn.addEventListener("click", async () => {

					const newValue = input.value.trim();
					const marketId = <?= (int) $market['id'] ?>;

					try {
						const res = await fetch("./api/update/market-field.php", {
							method: "POST",
							headers: {
								"Content-Type": "application/json"
							},
							body: JSON.stringify({
								market_id: marketId,
								field: field,
								value: newValue
							})
						});

						const data = await res.json();

						if (data.ok) {
							input.dataset.original = newValue;
							input.setAttribute("readonly", true);
							actions.classList.add("d-none");
						} else {
							showError(data.error || "Xəta baş verdi");
						}

					} catch (err) {
						showError("Server xətası");
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

</body>


</html>