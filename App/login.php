<?php
require_once __DIR__ . "/inc/config.php";
require_once __DIR__ . "/api/data/user_data.php";

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


		
				<!-- Breadscrumb Section -->
				<div class="breadcrumb-bar">
					<div class="container">
						<div class="row align-items-center text-center">
							<div class="col-md-12 col-12">
								<h3 class="breadcrumb-title">Giriş</h3>

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
										<p>Zəhmət olmasa hesab məlumatlarınızı daxil edin</p>
									</div>

									<!-- Login Form -->
									<form id="loginForm">

										<?php require_once __DIR__ . "/api/_csrf.php"; ?>
										<input type="hidden" name="_csrf"
											value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

										<div id="loginMsg" style="display:none;margin:10px 0;"></div>


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
												<input type="tel" id="phone" class="form-control"
													placeholder="Mobil nömrə" inputmode="numeric" style="width: 100%;"
													name="cust_phone" />

											</div>
										</div>
										<div class="form-set">
											<div class="pass-group group-img">
												<i class="feather-lock"></i>
												<input type="password" id="password" class="form-control pass-input"
													placeholder="Şifrə" name="cust_password">
												<span id="togglePass" class="toggle-password feather-eye"></span>

												<script>
													document.addEventListener("DOMContentLoaded", () => {
														const toggle = document.getElementById("togglePass");
														const passInput = document.getElementById("password");
														if (!toggle || !passInput) return;

														toggle.addEventListener("click", function () {
															if (passInput.type === "password") {
																passInput.type = "text";
																this.classList.remove("feather-eye");
																this.classList.add("feather-eye-off");
															} else {
																passInput.type = "password";
																this.classList.remove("feather-eye-off");
																this.classList.add("feather-eye");
															}
														});
													});
												</script>


											</div>
										</div>
										<div class="row">
											<div class="col-md-6 col-sm-6">
												<label class="custom_check">
													<input type="checkbox" name="rememberme" class="rememberme">
													<span class="checkmark"></span>Məni xatırla
												</label>
											</div>
											<div class="col-md-6 col-sm-6">
												<div class="text-md-end">
													<a class="forgot-link" href="forgot-password">Şifrənizi
														unutmusuz?</a>
												</div>
											</div>
										</div>
										<button class="btn btn-primary w-100 login-btn" type="submit">Daxil ol</button>
										<div class="register-link text-center">
											<p>Hələ də hesabınız yoxdur? <a class="forgot-link"
													href="./signup-method">Qeydiyyat</a>
											</p>
										</div>

									</form>
									<!-- /Login Form -->

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



	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const form = document.getElementById("loginForm");
			const msg = document.getElementById("loginMsg");
			const phoneInput = document.getElementById("phone");

			let iti = null;


			function showMsg(text, ok = false) {
				msg.style.display = "block";
				msg.textContent = text;
				msg.className = ok ? "alert alert-success" : "alert alert-danger";
			}

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

			form.addEventListener("submit", async (e) => {
				e.preventDefault();

				const fd = new FormData(form);

				if (iti) {
					const fullNumber = iti.getNumber(); // default E.164
					if (fullNumber) fd.set('cust_phone', fullNumber);
				} else {
					// fallback
					fd.set('cust_phone', (phoneInput.value || '').trim());
				}

				try {
					const res = await fetch("api/auth/login-process.php", {
						method: "POST",
						body: fd,
						headers: { "X-Requested-With": "XMLHttpRequest" }
					});

					const data = await res.json().catch(() => null);

					if (!res.ok || !data) {
						showMsg("Mobil nömrə və ya şifrə yanlışdır.");
						return;
					}

					if (!data.ok) {
						showMsg(data.error || "Daxil olmaq alınmadı.");
						return;
					}

					showMsg("Uğurla daxil oldunuz.", true);

					// yönləndirmə
					window.location.href = data.redirect || "dashboard";

				} catch (err) {
					showMsg("Şəbəkə xətası. Yenidən cəhd edin.");
				}
			});
		});
	</script>






</body>


</html>