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
		<?php include_once __DIR__ . "/inc/header.php"; ?>
		<!-- /Header -->


		<!-- Breadscrumb Section -->
		<div class="breadcrumb-bar">
			<div class="container">
				<div class="row align-items-center text-center">
					<div class="col-md-12 col-12">
						<h3 class="breadcrumb-title">Qeydiyyat</h3>

					</div>
				</div>
			</div>
		</div>
		<!-- /Breadscrumb Section -->

		<!-- Login Section -->
		<div class="login-content">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-lg-5 mx-auto">
						<div class="login-wrap">

							<div class="login-header">
								<h3>Xoş gəlmisiniz</h3>
								<p>Zəhmət olmasa qeydiyyat üçün tələb olunan məlumatlarınızı daxil edin</p>
							</div>

							<!-- Resgister Form -->
							<form id="signupForm">

								<?php require_once __DIR__ . "/api/_csrf.php"; ?>
								<input type="hidden" name="_csrf"
									value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

								<div class="form-set group-img">
									<div class="group-img">
										<i class="feather-user"></i>
										<input type="text" class="form-control" placeholder="Tam adınız"
											name="cust_full_name" />

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
								<div class="form-set group-img">
									<div class="group-img">
										<i class="feather-phone"></i>
										<input type="tel" id="phone" class="form-control" placeholder="Mobil nömrə"
											inputmode="numeric" style="width: 100%;" name="cust_phone" />

									</div>
								</div>

								<div class="form-set group-img">
									<div class="group-img">
										<i class="feather-mail"></i>
										<input type="email" class="form-control" placeholder="E-poçt ünvanı"
											name="cust_email" />

									</div>
								</div>


								<div class="form-set">
									<div class="pass-group group-img">
										<i class="feather-lock"></i>
										<input type="password" class="form-control" placeholder="Şifrə"
											name="cust_password">
										<span class="toggle-password fa-solid fa-eye"></span>
									</div>
								</div>

								<div class="form-set">
									<div class="pass-group group-img">
										<i class="feather-lock"></i>
										<input type="password" class="form-control" placeholder="Şifrəni təsdiqlə"
											name="cust_repasword">
										<span class="toggle-password fa-solid fa-eye"></span>
									</div>
								</div>



								<script>
									document.querySelectorAll(".toggle-password").forEach(function (toggle) {
										toggle.addEventListener("click", function () {

											const wrapper = this.closest(".pass-group");
											const input = wrapper.querySelector("input");
											if (!input) return;

											if (input.type === "password") {
												input.type = "text";
												this.classList.remove("fa-eye");
												this.classList.add("fa-eye-slash");
											} else {
												input.type = "password";
												this.classList.remove("fa-eye-slash");
												this.classList.add("fa-eye");
											}
										});
									});
								</script>


								<div class="row">

									<div class="col-12">
										<div class="text-md-end">
											<a class="forgot-link" href="forgot-password">Şifrənizi
												unutmusuz?</a>
										</div>
									</div>
								</div>
								<button class="btn btn-primary w-100 login-btn" type="submit">Növbəti <i
										class="fa-solid fa-angles-right"></i></button>
								<div class="register-link text-center">
									<p>Artıq bir hesabınız var? <a class="forgot-link" href="login">Daxil
											ol</a>
									</p>
								</div>

							</form>
							<!-- /Register Form -->

						</div>
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
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


	<!-- Datetimepicker JS -->
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

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




	<script>
		(() => {
			"use strict";

			const csrfToken = document.querySelector('input[name="_csrf"]')?.value || "";

			// Elements
			const form = document.getElementById("signupForm");
			const phoneInput = document.getElementById("phone");

			function validateSignup(fd) {
				const full = (fd.get("cust_full_name") || "").trim();
				const phone = (fd.get("cust_phone") || "").trim();
				const email = (fd.get("cust_email") || "").trim();
				const pass = fd.get("cust_password") || "";
				const rep = fd.get("cust_repasword") || "";

				if (!full) return "Tam adınızı daxil edin.";
				if (!phone) return "Telefon nömrəsini daxil edin.";
				if (!email) return "Email daxil edin.";
				if (pass.length < 6) return "Şifrə ən az 6 simvol olmalıdır.";
				if (pass !== rep) return "Şifrələr uyğun deyil.";

				return null;
			}


			let iti = null;
			let signupSessionToken = null;

			// ---------- Helpers ----------
			async function safeJson(res) {
				const raw = await res.text();
				try {
					const json = JSON.parse(raw);
					return { json, raw };
				} catch {
					return { json: { ok: false, error: "Server JSON qaytarmadı." }, raw };
				}
			}


			function setLoading(btn, loading, textLoading = "Gözləyin...") {
				if (!btn) return;
				if (loading) {
					btn.dataset.prevText = btn.innerHTML;
					btn.disabled = true;
					btn.innerHTML = textLoading;
				} else {
					btn.disabled = false;
					if (btn.dataset.prevText) btn.innerHTML = btn.dataset.prevText;
				}
			}

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


			// ---------- SIGNUP SUBMIT ----------
			form.addEventListener("submit", async (e) => {
				e.preventDefault();

				if (iti) phoneInput.value = iti.getNumber();

				const fd = new FormData(form); // ✅ formdan götür

				const err = validateSignup(fd);
				if (err) return showError(err);

				const submitBtn = form.querySelector('button[type="submit"]');
				setLoading(submitBtn, true, "Göndərilir...");

				try {
					const res = await fetch(`api/auth/signup-initiate-user.php`, {
						method: "POST",
						body: fd,
						credentials: "same-origin"
					});

					const { json, raw } = await safeJson(res);

					if (!res.ok || !json.ok) {
						console.log("signup-initiate RAW:", raw || "");
						showError(json.error || "Xəta baş verdi.");
						return;
					}

					window.location.href = "./verify-phone.php";

				} catch (err) {
					console.error(err);
					showError("Serverə qoşulmaq olmadı.");
				} finally {
					setLoading(submitBtn, false);
				}
			});



		})();
	</script>



</body>


</html>