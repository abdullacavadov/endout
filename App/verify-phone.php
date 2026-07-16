<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_unverified($pdo, 'dashboard');
require_once __DIR__ . "/api/_csrf.php";
?>



<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once './inc/head.php'; ?>

	<style>
		.otp-wrapper {
			display: flex;
			gap: 10px;
			justify-content: center;
		}

		.otp-input {
			width: 50px;
			height: 55px;
			text-align: center;
			font-size: 22px;
			border: 1px solid #ccc;
			border-radius: 8px;
		}
	</style>
</head>

<body>

	<div class="main-wrapper home-nine">
		<!-- Header -->
		<?php include_once __DIR__ . "/inc/header.php"; ?>
		<!-- /Header -->



		<!-- /Breadscrumb Section -->

		<!-- Login Section -->
		<div class="login-content" style="margin-top: 80px;">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-lg-5 mx-auto">
						<div class="login-wrap">

							<div class="login-header">
								<h3>OTP təsdiq</h3>
								<p>Zəhmət olmasa telefon nömrənizi təsdiq edin</p>
							</div>

							<!-- Login Form -->
							<form id="otpPhoneForm" autocomplete="off">
								<input type="hidden" name="_csrf"
									value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

								<div id="otpMsg" style="display:none;margin:10px 0;"></div>

								<div class="otp-wrapper">
									<input type="text" maxlength="1" class="otp-input" inputmode="numeric">
									<input type="text" maxlength="1" class="otp-input" inputmode="numeric">
									<input type="text" maxlength="1" class="otp-input" inputmode="numeric">
									<input type="text" maxlength="1" class="otp-input" inputmode="numeric">
								</div>

								<input type="hidden" name="otp" id="otpFull">

								<div id="otpDebug" class="alert alert-warning" style="display:none"></div>

								<button class="btn btn-primary w-100 login-btn" id="sendBtn" type="button">
									Kodu göndər
								</button>

								<button class="btn btn-primary w-100 login-btn" id="confirmBtn" style="display:none"
									type="submit" disabled>
									Təsdiqlə
								</button>
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
		(() => {
			"use strict";

			const LS_KEY = "phoneOtpResendUntil";

			const form = document.getElementById("otpPhoneForm");
			const csrf = form?.querySelector('input[name="_csrf"]')?.value || "";

			const inputs = document.querySelectorAll(".otp-input");
			const otpFull = document.getElementById("otpFull");

			const msg = document.getElementById("otpMsg");
			const debug = document.getElementById("otpDebug");

			const sendBtn = document.getElementById("sendBtn");
			const confirmBtn = document.getElementById("confirmBtn");

			let timer = null;

			// ===== UI =====
			function showMsg(text, ok = true) {
				msg.style.display = "block";
				msg.className = ok ? "alert alert-success" : "alert alert-danger";
				msg.textContent = text;
			}

			function safeJson(res) {
				return res.text().then(raw => {
					try { return JSON.parse(raw); }
					catch {
						if (debug) {
							debug.style.display = "block";
							debug.textContent = raw;
						}
						return { ok: false, error: "Server JSON qaytarmadı" };
					}
				});
			}

			// ===== OTP INPUT LOGIC =====
			inputs[0]?.focus();

			inputs.forEach((input, i) => {
				input.addEventListener("input", e => {
					let v = e.target.value.replace(/\D/g, "").slice(0, 1);
					input.value = v;

					if (v && i < inputs.length - 1) {
						inputs[i + 1].focus();
					}

					updateOTP();
				});

				input.addEventListener("keydown", e => {
					if (e.key === "Backspace" && !input.value && i > 0) {
						inputs[i - 1].focus();
					}
				});

				input.addEventListener("paste", e => {
					e.preventDefault();
					const data = e.clipboardData.getData("text").replace(/\D/g, "");

					inputs.forEach((inp, idx) => {
						inp.value = data[idx] || "";
					});

					inputs[Math.min(data.length, inputs.length) - 1]?.focus();
					updateOTP();
				});
			});

			function updateOTP() {
				const code = [...inputs].map(i => i.value).join("");
				otpFull.value = code;
				confirmBtn.disabled = code.length !== 4;
			}

			// ===== COUNTDOWN =====
			function startCountdown(until) {
				clearInterval(timer);

				function tick() {
					const sec = Math.ceil((until - Date.now()) / 1000);

					if (sec <= 0) {
						clearInterval(timer);
						localStorage.removeItem(LS_KEY);
						sendBtn.disabled = false;
						sendBtn.textContent = "Kodu göndər";
						return;
					}

					sendBtn.disabled = true;
					sendBtn.textContent = `Yenidən (${sec})`;
				}

				tick();
				timer = setInterval(tick, 1000);
			}

			function setCountdown(sec) {
				const until = Date.now() + sec * 1000;
				localStorage.setItem(LS_KEY, until);
				startCountdown(until);
			}

			// resume
			const saved = parseInt(localStorage.getItem(LS_KEY) || 0);
			if (saved > Date.now()) startCountdown(saved);
			else localStorage.removeItem(LS_KEY);

			// ===== SEND OTP =====
			sendBtn?.addEventListener("click", async () => {
				const saved = parseInt(localStorage.getItem(LS_KEY) || 0);
				if (saved > Date.now()) return;

				sendBtn.disabled = true;
				sendBtn.textContent = "Göndərilir...";
				showMsg("Kod göndərilir...");

				try {
					const res = await fetch("api/verify/send_phone_otp.php", {
						method: "POST",
						headers: { "Content-Type": "application/x-www-form-urlencoded" },
						body: new URLSearchParams({ _csrf: csrf })
					});

					const data = await safeJson(res);

					if (data.rate_limited) {
						setCountdown(data.remaining_seconds || 60);
						showMsg(data.message || "Gözlə", false);
						return;
					}

					if (!data.ok) throw new Error(data.error);

					showMsg(data.message || "Kod göndərildi");

					if (data.test_otp && debug) {
						debug.style.display = "block";
						debug.textContent = data.test_otp;
					}

					confirmBtn.style.display = "block";
					inputs[0].focus();

					setCountdown(data.cooldown_seconds || 60);

				} catch (e) {
					sendBtn.disabled = false;
					sendBtn.textContent = "Kodu göndər";
					showMsg(e.message || "Xəta", false);
				}
			});

			// ===== CONFIRM OTP =====
			form?.addEventListener("submit", async e => {
				e.preventDefault();

				const code = otpFull.value;

				if (!/^\d{4}$/.test(code)) {
					showMsg("OTP 4 rəqəm olmalıdır", false);
					return;
				}

				confirmBtn.disabled = true;

				try {
					const res = await fetch("api/verify/confirm_phone_otp.php", {
						method: "POST",
						headers: { "Content-Type": "application/x-www-form-urlencoded" },
						body: new URLSearchParams({ _csrf: csrf, otp: code })
					});

					const data = await safeJson(res);

					if (!data.ok) throw new Error(data.error);

					showMsg(data.message || "Təsdiqləndi");

					localStorage.removeItem(LS_KEY);

					if (data.redirect) window.location.href = data.redirect;

				} catch (e) {
					showMsg(e.message || "Xəta", false);
				} finally {
					confirmBtn.disabled = false;
				}
			});

		})();
	</script>

</body>


</html>