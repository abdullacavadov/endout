<?php
require_once __DIR__ . "/inc/config.php";
require_once __DIR__ . "/api/data/user_data.php";
require_login($pdo);
require_unverified_email($pdo);
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


		<div class="row">
			<div class="col-lg-2 col-md-2 sticky-banner"
				style="background-image:url(https://prodimages.everythingneon.com/l100-10487-custom-open-neon-sign_giant.gif);">
			</div>


			<div class="col-lg-8 col-md-12 col-xs-12">

				<!-- /Breadscrumb Section -->

				<!-- Login Section -->
				<div class="login-content" style="margin-top: 80px;">
					<div class="container">
						<div class="row">
							<div class="col-md-6 col-lg-5 mx-auto">
								<div class="login-wrap">

									<div class="login-header">
										<h3>OTP e-poçt təsdiqi</h3>
										<p>Zəhmət olmasa e-poçt ünvanınızı təsdiq edin</p>
									</div>

									<!-- Login Form -->
									<form id="otpEmailForm" autocomplete="off">

										<?php require_once __DIR__ . "/api/_csrf.php"; ?>
										<input type="hidden" name="_csrf"
											value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

										<div id="otpMsg" style="display:none;margin:10px 0;"></div>


										<div class="form-set">
											<div class="pass-group group-img">
												<i class="feather-mail"></i>
												<input id="otp" name="otp" inputmode="numeric" maxlength="6"
													placeholder="Məs: 123456" class="form-control pass-input">



											</div>

											<div id="otpDebug" class="alert alert-warning" style="display:none"></div>

										</div>
										<div class="row">

											<input type="hidden" value="<?= $cust_email ?>" id="custEmail">


										</div>

										<button class="btn btn-primary w-100 login-btn" id="sendBtn" type="button">Kodu
											göndər</button>

										<button class="btn btn-primary w-100 login-btn" id="confirmBtn"
											style="display:none" type="submit" disabled>Təsdiqlə</button>

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


			<div class="col-lg-2 col-md-8 sticky-banner"
				style="background-image:url(https://prodimages.everythingneon.com/l100-10487-custom-open-neon-sign_giant.gif)">
			</div>
		</div>




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

	<script>
		(() => {
			"use strict";

			const API_BASE = "/endout/App/api";
			const form = document.getElementById("otpEmailForm");
			if (!form) return;

			const csrf = form.querySelector('input[name="_csrf"]')?.value || "";

			const otpInput = document.getElementById("otp");
			const msg = document.getElementById("otpMsg");
			const debug = document.getElementById("otpDebug");

			const sendBtn = document.getElementById("sendBtn");
			const confirmBtn = document.getElementById("confirmBtn");

			function showMsg(text, ok = true) {
				msg.style.display = "block";
				msg.className = ok ? "alert alert-success" : "alert alert-danger";
				msg.innerHTML = text;
			}

			async function safeJson(res) {
				const raw = await res.text();
				try { return JSON.parse(raw); } catch {
					if (debug) { debug.style.display = "block"; debug.textContent = raw; }
					return { ok: false, error: "Server cavabı oxunmadı." };
				}
			}

			// OTP input yalnız rəqəm
			otpInput?.addEventListener("input", () => {
				otpInput.value = otpInput.value.replace(/\D/g, "").slice(0, 6);
				const ready = otpInput.value.length === 6;
				confirmBtn.disabled = !ready;
			});


			const LS_KEY_EMAIL_UNTIL = "emailOtpResendUntil";
			let countdownTimer = null;

			function clearCountdown() {
				if (countdownTimer) {
					clearInterval(countdownTimer);
					countdownTimer = null;
				}
			}

			function startCountdownFromUntil(untilMs) {
				clearCountdown();

				function tick() {
					const now = Date.now();
					const remaining = Math.ceil((untilMs - now) / 1000);

					if (remaining <= 0) {
						clearCountdown();
						localStorage.removeItem(LS_KEY_EMAIL_UNTIL);
						sendBtn.disabled = false;
						sendBtn.textContent = "Kodu göndər";
						return;
					}

					sendBtn.disabled = true;
					sendBtn.textContent = `Yenidən göndər (${remaining})`;
				}

				tick(); // dərhal göstər
				countdownTimer = setInterval(tick, 1000);
			}

			function persistAndStartCountdown(seconds) {
				const untilMs = Date.now() + seconds * 1000;
				localStorage.setItem(LS_KEY_EMAIL_UNTIL, String(untilMs));
				startCountdownFromUntil(untilMs);
			}

			// səhifə refresh olanda davam etsin
			(function resumeCountdownOnLoad() {
				const untilStr = localStorage.getItem(LS_KEY_EMAIL_UNTIL);
				const untilMs = untilStr ? parseInt(untilStr, 10) : 0;

				if (untilMs && untilMs > Date.now()) {
					startCountdownFromUntil(untilMs);
				} else {
					localStorage.removeItem(LS_KEY_EMAIL_UNTIL);
				}
			})();


			// 1) Kodu göndər
			sendBtn?.addEventListener("click", async () => {
				// əgər countdown gedirsə, klik etməsin
				const untilStr = localStorage.getItem(LS_KEY_EMAIL_UNTIL);
				const untilMs = untilStr ? parseInt(untilStr, 10) : 0;
				if (untilMs && untilMs > Date.now()) return;

				sendBtn.disabled = true;
				sendBtn.textContent = "Göndərilir...";
				showMsg("Kod göndərilir...", true);

				try {
					const res = await fetch(`${API_BASE}/verify/send_email_otp.php`, {
						method: "POST",
						headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
						body: new URLSearchParams({ _csrf: csrf })
					});

					const data = await safeJson(res);

					// Rate limit cavabı: backend remaining_seconds göndərir
					if (data.rate_limited) {
						persistAndStartCountdown(Number(data.remaining_seconds || 60));
						showMsg(data.message || "Kod artıq göndərilib.", false);
						return;
					}

					if (!data.ok) {
						throw new Error(data.error || "Xəta baş verdi.");
					}

					showMsg(data.message || "Təsdiq kodu emailə göndərildi.", true);

					// uğurlu göndərmədən sonra da 60 saniyə blok et (backend də bloklayır)
					persistAndStartCountdown(60);

					// UI
					confirmBtn.style.display = "block";
					otpInput.focus();

				} catch (e) {
					// Xəta olarsa localStorage-a yazmırıq, button yenidən aktiv olsun
					sendBtn.disabled = false;
					sendBtn.textContent = "Kodu göndər";
					showMsg(e.message || "Xəta baş verdi.", false);
				}
			});

			// 2) Təsdiqlə
			form.addEventListener("submit", async (e) => {
				e.preventDefault();

				const code = (otpInput?.value || "").trim();
				if (!/^\d{6}$/.test(code)) {
					showMsg("OTP 6 rəqəm olmalıdır.", false);
					return;
				}

				confirmBtn.disabled = true;

				try {
					const res = await fetch(`${API_BASE}/verify/confirm_email_otp.php`, {
						method: "POST",
						headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
						body: new URLSearchParams({ _csrf: csrf, otp: code })
					});

					const data = await safeJson(res);
					if (!data.ok) throw new Error(data.error || "Təsdiqləmə alınmadı.");

					showMsg(data.message || "Email təsdiqləndi.", true);

					// istəsən yönləndir
					if (data.redirect) window.location.href = data.redirect;
				} catch (e2) {
					showMsg(e2.message || "Xəta baş verdi.", false);
				} finally {
					confirmBtn.disabled = false;
				}
			});
		})();
	</script>




</body>


</html>