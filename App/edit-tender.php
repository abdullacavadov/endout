<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

if (!isset($_GET['t']) || !ctype_digit($_GET['t'])) {
	header("Location: ./my-inqquiry");
	exit;
}

$tid = (int) $_GET['t'];

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";

?>


<?php

$stmt = $pdo->prepare("
    SELECT 
        t.id AS tid,
        t.*,

        GROUP_CONCAT(DISTINCT tc.country_id) AS countries,
        GROUP_CONCAT(DISTINCT tt.tag_id) AS tags

    FROM tenders t

    LEFT JOIN tender_tags tt 
        ON tt.tender_id = t.id

    LEFT JOIN tender_countries tc
        ON tc.tender_id = t.id

    WHERE t.id = ?
    AND t.customer_id = ?

    GROUP BY t.id

    LIMIT 1
");

$stmt->execute([$tid, $_SESSION['customer_id']]);
$tender = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tender) {
	header("Location: ./my-inqquiry");
	exit;
}

if ($tender['customer_id'] != $_SESSION['customer_id']) {
	header("Location: ./my-inqquiry");
	exit;
}

$selectedTags = !empty($tender['tags'])
	? explode(',', $tender['tags'])
	: [];

$selectedCountries = !empty($tender['countries'])
	? explode(',', $tender['countries'])
	: [];
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


		<!-- /Breadscrumb Section -->

		<!-- Profile Content -->
		<div class="dashboard-content listing-section ">
			<div class="container">

				<div class="profile-content" style="margin-top: 70px">



					<form id="tenderForm" enctype="multipart/form-data">
						<?php if ($tender['status'] == 'cancelled'): ?>
							<div class="alert alert-danger mb-5 mt-5">
								Bu tender moderasiya tərəfindən təsdiqlənməyib. Ətraflı məlumat e-poçt ünvanınıza
								göndərilib.
							</div>

						<?php elseif ($tender['status'] == 'pending'): ?>

							<div class="alert alert-info mb-5 mt-5">
								Bu tender hal-hazırda moderasiyada gözləyir. Nəticə barədə məlumat e-poçt ünvanınıza
								göndəriləcək.
							</div>

						<?php elseif ($tender['status'] == 'expired'): ?>

							<div class="alert alert-warning mb-5 mt-5">
								Bu tenderinizin müddəti bitmişdir. Tenderinizi yeniləyərək aktiv edə bilərsiniz. Tender
								limitini
								doldurmamısızsa tenderi yenidən yarada bilərsiniz.
							</div>
						<?php else: ?>

							<input type="hidden" name="tid" value="<?= $tender['id'] ?>">



							<div class="messages-form">
								<div class="card">

									<div class="card-body">


										<div class="form-set form-floating">
											<input type="text" id="bashliq" name="title" class="form-control pass-input"
												placeholder=" " value="<?= htmlentities($tender['title']) ?>">
											<label for="bashliq">Tender başlığı <span>*</span></label>
										</div>

										<div class="form-set form-floating">
											<textarea style="height: 200px" id="desc" name="description"
												class="form-control pass-input" placeholder=" "
												maxlength="5000"><?= htmlentities($tender['description']) ?></textarea>
											<label for="desc">Məlumat <span>*</span></label>
										</div>

										<div class="form-set form-floating">
											<select id="country" class="form-control" name="country_id">
												<option value="" disabled selected>-- Ölkə seçin --</option>

												<?php $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name");
												while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
													<option value="<?= $row['id'] ?>" <?= $tender['country_id'] == $row['id'] ? 'selected' : '' ?>>
														<?= htmlspecialchars($row['name']) ?>
													</option>
												<?php endwhile; ?>

											</select>
											<label for="country">Tenderin ölkəsi <span>*</span></label>
										</div>

										<div class="row">

											<div class="col-6">
												<div class="form-set form-floating">
													<input type="date" id="start_date" name="start_date"
														class="form-control pass-input" placeholder=" "
														value="<?= date('Y-m-d', strtotime($tender['start_date'])) ?>">
													<label for="start_date">Başlanğıc tarixi</label>
												</div>
											</div>
											<div class="col-6">
												<div class="form-set form-floating">
													<input type="date" id="end_date" name="end_date"
														class="form-control pass-input" placeholder=" "
														value="<?= date('Y-m-d', strtotime($tender['end_date'])) ?>">
													<label for="end_date">Bitmə tarixi</label>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-2">
												<div class="form-set form-floating">
													<select id="currency" name="currency" class="form-control pass-input">
														<option value="" selected disabled>-- seçin --</option>
														<option value="AZN" <?= $tender['currency'] == 'AZN' ? 'selected' : '' ?>>AZN</option>
														<option value="USD" <?= $tender['currency'] == 'USD' ? 'selected' : '' ?>>USD</option>
														<option value="EUR" <?= $tender['currency'] == 'EUR' ? 'selected' : '' ?>>EUR</option>
														<option value="TRY" <?= $tender['currency'] == 'TRY' ? 'selected' : '' ?>>TRY</option>
														<option value="RUB" <?= $tender['currency'] == 'RUB' ? 'selected' : '' ?>>RUB</option>
														<option value="GBP" <?= $tender['currency'] == 'GBP' ? 'selected' : '' ?>>GBP</option>
														<option value="CHF" <?= $tender['currency'] == 'CHF' ? 'selected' : '' ?>>CHF</option>
														<option value="JPY" <?= $tender['currency'] == 'JPY' ? 'selected' : '' ?>>JPY</option>
														<option value="AED" <?= $tender['currency'] == 'AED' ? 'selected' : '' ?>>AED</option>
														<option value="CNY" <?= $tender['currency'] == 'CNY' ? 'selected' : '' ?>>CNY</option>
													</select>
													<label for="currency">Valyuta <span>*</span></label>
												</div>
											</div>
											<div class="col-5">
												<div class="form-set form-floating">
													<input type="text" id="budget_min" name="budget_min"
														class="form-control pass-input" placeholder=" "
														value="<?= htmlentities($tender['budget_min']) ?>">
													<label for="budget_min">Minimum büdcə</label>
												</div>
											</div>
											<div class="col-5">
												<div class="form-set form-floating">
													<input type="text" id="budget_max" name="budget_max"
														class="form-control pass-input" placeholder=" "
														value="<?= htmlentities($tender['budget_max']) ?>">
													<label for="budget_max">Maksimum büdcə</label>
												</div>
											</div>
										</div>

									

										<div class="row mt-3">

									
											<div class="col-6">
												<div class="form-set form-floating">
													<input type="tel" id="contact_number" class="form-control pass-input" placeholder=" "
														name="contact_number"
														value="<?= htmlentities($tender['contact_number']) ?>" />
													<label for="contact_number" class="contact-label">
														Əlaqə nömrəsi <span>*</span>
													</label>
												</div>
											</div>
											<div class="col-6">
												<div class="form-set form-floating">
													<input type="email" id="contact_email" name="contact_email"
														class="form-control pass-input" placeholder=" "
														value="<?= htmlentities($tender['contact_email']) ?>">
													<label for="contact_email" class="contact-label">
														Əlaqə e-poçtu <span>*</span>
													</label>
												</div>
											</div>
										</div>



									</div>
								</div>


								<button class="btn btn-primary" type="submit"> Sorğunu əlavə et</button>


							</div>

						<?php endif; ?>
					</form>

				</div>


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


	<!-- Sortable JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"
		integrity="sha512-csIng5zcB+XpulRUa+ev1zKo7zRNGpEaVfNB9On1no9KYTEY/rLGAEEpvgdw6nim1WdTuihZY1eqZ31K7/fZjw=="
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script>
		document.getElementById("tenderForm").addEventListener("submit", async function (e) {

			e.preventDefault();



			const formData = new FormData(this);


			const res = await fetch("./api/update/tender.php", {
				method: "POST",
				body: formData
			});

			const data = await res.json();

			if (data.success) {
				showError("Tender yeniləndi");
				setTimeout(function () {
					location.href = "./my-inquiry";
				}, 2500)
			} else {
				showError(data.error);
			}
		});

	</script>



</body>

</html>