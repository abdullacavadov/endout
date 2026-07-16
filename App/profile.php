<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";


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


		<!-- Profile Content -->
		<div class="dashboard-content listing-section ">
			<div class="container">
				<div style="margin-top: 70px;">
					<?php include "./inc/dashboard_menus.php"; ?>
				</div>
				<div class="profile-content">
					<div class="row dashboard-info">
						<div class="col-lg-9">
							<div class="card dash-cards">
								<div class="card-header">
									<h4>Hesab məlumatlarım</h4>
								</div>
								<div class="card-body">

									<div class="profile-form">
										<div class="profile-form">
											<form id="profileInfoForm">

												<input type="hidden" name="_csrf"
													value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">

												<div class="form-set">
													<label class="col-form-label">Ad və soyad</label>
													<div class="pass-group group-img">
														<span class="lock-icon"><i class="feather-user"></i></span>
														<input type="text" class="form-control" name="full_name"
															value="<?= htmlspecialchars($cust_name) ?>">
													</div>
												</div>

												<div class="row">
													<div class="col-lg-6 col-md-6">
														<div class="form-set">
															<label class="col-form-label">Telefon nömrəsi</label>
															<div class="pass-group group-img">
																<span class="lock-icon"><i
																		class="feather-phone-call"></i></span>
																<input type="tel" class="form-control" name="phone"
																	value="<?= htmlspecialchars($cust_phone) ?>">
															</div>
														</div>
													</div>

													<div class="col-lg-6 col-md-6">
														<div class="form-set">
															<label class="col-form-label">E-poçt ünvanı</label>
															<div class="group-img">
																<i class="feather-mail"></i>
																<input type="text" class="form-control" name="email"
																	value="<?= htmlspecialchars($cust_email) ?>">
															</div>
														</div>
													</div>
												</div>

												<!-- nəticə mesajı -->
												<div id="profileInfoMsg" class="alert" style="display:none;"></div>

												<button class="btn btn-primary" type="submit">Məlumatları
													yenilə</button>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="profile-sidebar">
								<div class="card">
									<div class="card-header">
										<h4>Şifrəni yenilə</h4>
									</div>
									<div class="card-body">
										<form id="profilePasswordForm">

											<input type="hidden" name="_csrf"
												value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">


											<style>
												input::placeholder {
													font-size: 11px;
												}
											</style>
											<div class="form-set"> <label class="col-form-label">Cari şifrə</label>
												<div class="pass-group group-img">
													<span class="lock-icon"><i class="feather-lock"></i></span>
													<input type="password" class="form-control pass-input"
														name="current_password" placeholder="Cari şifrəni daxil edin">
												</div>

												<a class="text-decoration-underline text-primary mt-5" style="font-size: 12px;"
													href="./forget-password">Şifrənizi unutmusunuz?</a>

											</div>

											<div class="form-set"> <label class="col-form-label">Yeni şifrə</label>
												<div class="pass-group group-img"> <span class="lock-icon"><i
															class="feather-lock"></i></span> <input type="password"
														class="form-control pass-input" name="new_password"
														placeholder="Yeni şifrəni daxil edin">
													<span class="toggle-password feather-eye"></span>
												</div>
											</div>

											<div class="form-set"> <label class="col-form-label">Yeni şifrəni
													təsdiqlə</label>
												<div class="pass-group group-img"> <span class="lock-icon"><i
															class="feather-lock"></i></span> <input type="password"
														class="form-control pass-input" name="confirm_password"
														placeholder="Yeni şifrəni təsdiqləyin">
													<span class="toggle-password feather-eye"></span>
												</div>
											</div>


											<!-- nəticə mesajı -->
											<div id="profilePassMsg" class="alert" style="display:none;"></div>

											<button class="btn btn-primary" type="submit">Şifrəni yenilə</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Profile Content -->
		<?php include('./inc/alert_modal.php'); ?>

		<?php require_once './inc/footer.php'; ?>

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


	<script>
		(() => {
			"use strict";

			// -------- Helpers --------
			function showMsg(el, text, ok = false) {
				if (!el) return;
				el.style.display = "block";
				el.textContent = text;
				el.className = "alert " + (ok ? "alert-success" : "alert-danger");
			}

			async function safeJson(res) {
				const txt = await res.text();
				try { return JSON.parse(txt); } catch { return { ok: false, error: txt || "JSON deyil" }; }
			}

			async function postForm(url, form) {
				const fd = new FormData(form);
				const res = await fetch(url, { method: "POST", body: fd, credentials: "same-origin" });
				const data = await safeJson(res);
				if (!res.ok && data && data.ok !== true) {
					// server 4xx/5xx qaytarıbsa, yenə də mesaj göstərək
					return { ok: false, error: data.error || `HTTP ${res.status}` };
				}
				return data;
			}

			function setBtnLoading(form, isLoading) {
				const btn = form.querySelector('button[type="submit"]');
				if (!btn) return;
				btn.disabled = isLoading;
				btn.dataset.oldText ||= btn.textContent;
				btn.textContent = isLoading ? "Gözləyin..." : btn.dataset.oldText;
			}

			// -------- Form 1: Profil məlumatları --------
			const infoForm = document.getElementById("profileInfoForm");
			const infoMsg = document.getElementById("profileInfoMsg");

			if (infoForm) {
				infoForm.addEventListener("submit", async (e) => {
					e.preventDefault();
					showMsg(infoMsg, "", true);
					infoMsg.style.display = "none";

					const fullName = (infoForm.querySelector('[name="full_name"]')?.value || "").trim();
					const phone = (infoForm.querySelector('[name="phone"]')?.value || "").trim();
					const email = (infoForm.querySelector('[name="email"]')?.value || "").trim();

					if (!fullName) return showMsg(infoMsg, "Ad və soyad boş ola bilməz.");
					if (!phone) return showMsg(infoMsg, "Telefon nömrəsi boş ola bilməz.");
					if (!email) return showMsg(infoMsg, "E-poçt boş ola bilməz.");

					setBtnLoading(infoForm, true);
					try {
						// API: /api/update/profile.php (sən istəyə görə adlandır)
						const data = await postForm('<?= $base_url; ?>/api/update/profile.php', infoForm);

						if (data.ok) {
							showMsg(infoMsg, "Məlumatlar uğurla yeniləndi.", true);
							if (data.needs_verification) {
								// əgər telefon və ya email dəyişibsə, istifadəçiyə bildiriş göstər və verify-otp səhifəsinə yönləndir
								showMsg(infoMsg, "Məlumatlar uğurla yeniləndi. Təsdiqləmə üçün verify-otp səhifəsinə yönləndirilirsiniz.", true);
								setTimeout(() => {
									window.location.href = "verify-otp";
								}, 2000);
							}
						} else {
							showMsg(infoMsg, data.error || "Məlumatları yeniləmək mümkün olmadı.");
						}
					} catch (err) {
						showMsg(infoMsg, "Şəbəkə xətası: " + (err?.message || err));
					} finally {
						setBtnLoading(infoForm, false);
					}
				});
			}

			// -------- Form 2: Şifrə yeniləmə --------
			const passForm = document.getElementById("profilePasswordForm");
			const passMsg = document.getElementById("profilePassMsg");

			if (passForm) {
				passForm.addEventListener("submit", async (e) => {
					e.preventDefault();
					showMsg(passMsg, "", true);
					passMsg.style.display = "none";

					const cur = (passForm.querySelector('[name="current_password"]')?.value || "");
					const nw = (passForm.querySelector('[name="new_password"]')?.value || "");
					const cf = (passForm.querySelector('[name="confirm_password"]')?.value || "");

					if (!cur) return showMsg(passMsg, "Cari şifrəni daxil edin.");
					if (!nw) return showMsg(passMsg, "Yeni şifrəni daxil edin.");
					if (nw.length < 8) return showMsg(passMsg, "Yeni şifrə ən az 8 simvol olmalıdır.");
					if (nw !== cf) return showMsg(passMsg, "Yeni şifrə və təsdiq şifrəsi eyni deyil.");

					setBtnLoading(passForm, true);
					try {
						// API: /api/update/password.php
						const data = await postForm('<?= $base_url; ?>/api/update//password.php', passForm);

						if (data.ok) {
							showMsg(passMsg, data.message || "Şifrə uğurla yeniləndi. Çıxış edilir...", true);
							// inputları boşalt
							passForm.reset();
							// istifadəçini çıxışa at (session_destroy etdiyimiz üçün)
							setTimeout(() => {
								window.location.href = "login";
							}, 3500);
						} else {
							showMsg(passMsg, data.error || "Şifrəni yeniləmək mümkün olmadı.");
						}
					} catch (err) {
						showMsg(passMsg, "Şəbəkə xətası: " + (err?.message || err));
					} finally {
						setBtnLoading(passForm, false);
					}
				});
			}

			// -------- Password show/hide (səndə var idi, burada daha stabil) --------
			document.querySelectorAll(".toggle-password").forEach((toggle) => {
				toggle.addEventListener("click", function () {
					const passGroup = this.closest(".pass-group");
					const input = passGroup?.querySelector("input");
					if (!input) return;

					const isPass = input.type === "password";
					input.type = isPass ? "text" : "password";
					this.classList.toggle("feather-eye", !isPass);
					this.classList.toggle("feather-eye-off", isPass);
				});
			});

		})();
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