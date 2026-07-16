<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_role('partner');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";

$listing_id = (int) ($_GET['l'] ?? 0);

$stmt = $pdo->prepare("
    SELECT id
    FROM listings
    WHERE id = ? AND customer_id = ?
    LIMIT 1
");

$stmt->execute([$listing_id, $_SESSION['customer_id']]);
$listing = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once "./inc/head.php"; ?>

	<style>
		.premium-card {
			cursor: pointer;
			border: 2px solid #c7c7c7;
			transition: 0.2s;
		}

		.premium-card.active {
			border-color: #ffc107;
			background: #fff8e1;
			border: 2px solid #ff9d00;
		}
	</style>
</head>

<body>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once "./inc/header.php"; ?>
		<!-- /Header -->



		<!-- Dashboard Content -->
		<div class="dashboard-content listing-section ">
			<div class="container">
				<div style="margin-top: 70px;">

				</div>
				<div class="dash-listingcontent dashboard-info">
					<div class="dash-cards card">
						<div class="card-header">
							<h4>Elanı Premium et</h4>



						</div>
						<div class="card-body">
							<?php
							$stmt = $pdo->query("
								SELECT id,name,duration_days,price,currency,is_best
								FROM premium_plans
								WHERE status = 1
								ORDER BY price ASC
							");

							$plans = $stmt->fetchAll();

							function getRates()
							{
								$xml = simplexml_load_file("https://www.cbar.az/currencies/" . date('d.m.Y') . ".xml");

								$rates = [
									'AZN' => 1 // baza
								];

								foreach ($xml->ValType as $type) {
									foreach ($type->Valute as $valute) {
										$code = (string) $valute['Code'];
										$value = (float) $valute->Value;

										$rates[$code] = $value;
									}
								}

								return $rates;
							}

							function convertCurrency($amount, $from, $to, $rates)
							{
								if ($from === $to)
									return $amount;

								if (!isset($rates[$from]) || !isset($rates[$to])) {
									return null; // unsupported currency
								}

								// from → AZN
								$azn = $amount * $rates[$from];

								// AZN → to
								return $azn / $rates[$to];
							}

							$stmt = $pdo->prepare("SELECT currency_code, coefficient FROM countries WHERE iso2 = ?");
							$stmt->execute([$user_country_code]);
							$userCurrency = $stmt->fetchColumn();

							$coeff = $userCurrency['coefficient'] ?? 1;

							$rates = getRates();
							?>


							<form id="premiumForm" method="post">

								<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
								<input type="hidden" name="listing_id" value="<?= $listing_id ?>">

								<div class="row g-3">

									<div class="col-md-12">
										<div class="alert alert-warning">
											Elan axtarış nəticələrində irəli çəkiləcək, öz kateqoriyasındakı bütün VIP
											elanlar arasında təsadüfi qaydada göstəriləcək və xidmətin aktivlik
											müddətinin sonunadək əsas səhifədə qalacaq.
										</div>
									</div>

									<div class="col-md-12">
										<div id="premiumMessage"></div>
									</div>

									<?php foreach ($plans as $plan): ?>

										<?php
										$priceWithCoeff = $plan['price'] * $coeff;
										$converted = convertCurrency(
											$priceWithCoeff,   // istifadəçi ölkəsinin əmsalı ilə qiyməti tənzimlənmiş halda
											$plan['currency'],   // məsələn USD
											$userCurrency,       // məsələn EUR
											$rates
										);

										if (!$converted) {
											$priceWithCoeff; // əmsal ilə tənzimlənmiş qiymət, amma valyuta çevrilməsi olmadan
											$userCurrency = 'USD';
										}
										?>

										<div class="col-md-4">

											<?php $isBest = $plan['is_best']; ?>

											<label class="card premium-card h-100 p-3 <?= $isBest ? 'active' : '' ?>">



												<?php if ($isBest): ?>
													<span
														class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle">
														<i class="fa-solid fa-fire"></i> Ən sərfəli
													</span>
												<?php endif; ?>

												<input type="radio" name="plan_id" value="<?= $plan['id'] ?>" class="d-none"
													<?= $isBest ? 'checked' : '' ?> required>

												<div class="card-body text-center border border-warning rounded">

													<h5 class="card-title mb-2">
														<?= $plan['duration_days'] ?> gün
													</h5>

													<div class="fs-4 fw-bold text-warning">
														<?= number_format($converted, 2) . ' ' . $userCurrency ?>
													</div>

												</div>

											</label>

										</div>

									<?php endforeach; ?>

								</div>

								<div class="d-flex justify-content-center">
									<button class="btn btn-warning mt-4 w-50">
										<i class="fas fa-gem"></i> Premium et
									</button>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Dashboard Content -->


		<!-- Footer -->
		<?php require_once "./inc/footer.php"; ?>
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
	<script src="assets/js/bootstrap.bundle.min.js" type="text/javascript"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- Datatables JS -->
	<script src="assets/plugins/datatables/jquery.dataTables.min.js"
		type="efe53e190f11ea9b32c5018d-text/javascript"></script>
	<script src="assets/plugins/datatables/datatables.min.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>

	<script src="js/alert-modal.js"></script>
	<?php include('./inc/alert_modal.php'); ?>

	<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
		data-cf-settings="efe53e190f11ea9b32c5018d-|49" defer></script>


	<script>
		document.querySelectorAll('.premium-card').forEach(card => {

			card.addEventListener('click', () => {

				document.querySelectorAll('.premium-card')
					.forEach(c => c.classList.remove('active'));

				card.classList.add('active');

				card.querySelector('input').checked = true;

			});

		});
	</script>

	<script>
		document.getElementById('premiumForm').addEventListener('submit', async function (e) {

			e.preventDefault();

			const form = this;
			const msgBox = document.getElementById('premiumMessage');

			msgBox.innerHTML = '';

			const formData = new FormData(form);

			try {

				const res = await fetch("./api/create/premium.php", {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				});

				const data = await res.json();

				if (data.ok) {

					msgBox.innerHTML =
						`<div class="alert alert-success">
				${data.message}
			</div>`;

					window.location.href = './my-listings';

				} else {

					msgBox.innerHTML =
						`<div class="alert alert-danger">
				${data.error}
			</div>`;

				}

			} catch (err) {

				msgBox.innerHTML =
					`<div class="alert alert-danger">
			Server xətası baş verdi
		</div>`;

			}

		});
	</script>
</body>





</html>