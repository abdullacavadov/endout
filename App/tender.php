<?php
require_once __DIR__ . "/inc/config.php";


$slug = $_GET['slug'] ?? '';

$st = $pdo->prepare("
    SELECT
        t.*,

        c.full_name,
		

        GROUP_CONCAT(DISTINCT tags.keyword SEPARATOR ', ') AS tag_names,

        GROUP_CONCAT(
            DISTINCT tc_country.name
            SEPARATOR ', '
        ) AS country_names

    FROM tenders t

    LEFT JOIN customers c
    ON c.id = t.customer_id

    LEFT JOIN tender_tags tt
    ON tt.tender_id = t.id

    LEFT JOIN tags
    ON tags.id = tt.tag_id

    LEFT JOIN tender_countries tc
    ON tc.tender_id = t.id

    LEFT JOIN countries tc_country
    ON tc_country.id = tc.country_id

    WHERE t.slug = ?

    GROUP BY t.id

    LIMIT 1
");

$st->execute([$slug]);

$tender = $st->fetch(PDO::FETCH_ASSOC);

if (!$tender) {

	http_response_code(404);

	exit('Tender tapılmadı');

}





$isExpired = strtotime($tender['end_date']) < time();
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once __DIR__ . "/inc/head.php"; ?>
</head>

<body>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once __DIR__ . "/inc/header.php"; ?>
		<!-- /Header -->


		<?php
		if ($tender['status'] === 'deleted') {

			http_response_code(410);

			exit('<div class="alert alert-dark text-center" style="margin-top: 200px;" role="alert">Bu tender silinib</div>');

		} elseif ($tender['status'] === 'pending') {

			http_response_code(403);

			exit('<div class="alert alert-warning text-center" style="margin-top: 200px;" role="alert">Bu tender hələ yoxlanışdadır</div>');

		} elseif ($tender['status'] === 'cancelled') {

			http_response_code(403);

			exit('<div class="alert alert-danger text-center" style="margin-top: 200px;" role="alert">Bu tender ləğv edilib</div>');

		} elseif ($tender['status'] === 'expired') {

			http_response_code(403);

			exit('<div class="alert alert-secondary text-center" style="margin-top: 200px;" role="alert">Bu tenderin vaxtı bitib</div>');

		}


		$upd = $pdo->prepare("
			UPDATE tenders
			SET views = views + 1
			WHERE id = ?
		");

		$upd->execute([$tender['id']]);
		?>

		<!--Blog Banner-->
		<div class="blogbanner">




			<div class="blogbanner-content">
				<h1> <?= htmlspecialchars($tender['title']) ?></h1>
				<ul class="entry-meta meta-item">
					<li>

					</li>
					<li class="date-icon"><i class="fa-solid fa-calendar-days"></i>
						<?= date('d.m.Y H:i', strtotime($tender['created_at'])) ?></li>

					<li>
						<?php if (!$isExpired): ?>

							<span class="badge bg-success">
								Aktiv
							</span>

						<?php else: ?>

							<span class="badge bg-secondary">
								Vaxtı bitib
							</span>

						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>
		<!--/Blog Banner-->

		<!--Blog Content-->
		<div class="blogdetail-content">
			<div class="container">
				<div class="row">
					<div class="col-lg-8 col-md-12">
						<blockquote class="blockquote">
							<h5>
								Bu tender aşağıdakı ölkələr üçün keçərlidir:
							</h5>

							<div class="tag-list">
								<ul class="tags">

									<?php
									$countries = explode(',', $tender['country_names']);

									foreach ($countries as $country):
										?>

										<li>
											<?= htmlspecialchars(trim($country)) ?>
										</li>

									<?php endforeach; ?>

								</ul>
							</div>

						</blockquote>
						<p>
							<?= nl2br(htmlspecialchars($tender['description'])) ?>
						</p>
					</div>

					<div class="col-lg-4 col-md-12">

						<h6>Paylaşan</h6>
						<p>
							<?= htmlspecialchars($tender['full_name']) ?>
							<br>
							<?php
							$st = $pdo->prepare("
    SELECT
        t.country_id,
        c.name,
		c.iso2

    FROM tenders t

    LEFT JOIN countries c
    ON c.id = t.country_id

    WHERE t.slug = ?

    LIMIT 1
");

							$st->execute([$slug]);

							$country = $st->fetch(PDO::FETCH_ASSOC);
							?>

							<img src="https://flagicons.lipis.dev/flags/4x3/<?= htmlspecialchars(strtolower($country['iso2'])) ?>.svg"
								alt="Flag" style="width:20px;vertical-align:middle;margin-right:5px;">

							<?= htmlspecialchars($country['name']) ?>
							<br>
							<a href="mailto:<?= htmlspecialchars($tender['contact_email']) ?>">
								<i class="fas fa-envelope"></i>
								<?= htmlspecialchars($tender['contact_email']) ?>
							</a>
							<br>
							<a href="tel:<?= htmlspecialchars($tender['contact_number']) ?>">
								<i class="fas fa-phone"></i>
								<?= htmlspecialchars($tender['contact_number']) ?>
							</a>

						</p>

						<hr>

						<h6>Keçərlidir</h6>
						<p>
							<?= date('d.m.Y', strtotime($tender['start_date'])) ?>
							-
							<?= date('d.m.Y', strtotime($tender['end_date'])) ?>
							<br>

						<div id="countdown" data-end="<?= date('c', strtotime($tender['end_date'])) ?>">
							Yüklənir...
						</div>

						<script>

							const countdownEl = document.getElementById('countdown');

							const endDate = new Date(
								countdownEl.dataset.end
							).getTime();

							function updateCountdown() {

								const now = new Date().getTime();

								const distance = endDate - now;

								if (distance <= 0) {

									countdownEl.innerHTML = `
			<span style="color:red">
				Tender vaxtı bitib
			</span>
		`;

									clearInterval(timer);

									return;
								}

								const days = Math.floor(
									distance / (1000 * 60 * 60 * 24)
								);

								const hours = Math.floor(
									(distance % (1000 * 60 * 60 * 24))
									/ (1000 * 60 * 60)
								);

								const minutes = Math.floor(
									(distance % (1000 * 60 * 60))
									/ (1000 * 60)
								);

								const seconds = Math.floor(
									(distance % (1000 * 60))
									/ 1000
								);

								countdownEl.innerHTML = `
		<div style="
			display:flex;
			gap:10px;
			flex-wrap:wrap;
		">

			<div class="count-box">
				<strong>${days}</strong>
				<small>Gün</small>
			</div>

			<div class="count-box">
				<strong>${hours}</strong>
				<small>Saat</small>
			</div>

			<div class="count-box">
				<strong>${minutes}</strong>
				<small>Dəqiqə</small>
			</div>

			<div class="count-box">
				<strong>${seconds}</strong>
				<small>Saniyə</small>
			</div>

		</div>
	`;
							}

							updateCountdown();

							const timer = setInterval(
								updateCountdown,
								1000
							);

						</script>

						<style>
							.count-box {
								min-width: 80px;
								padding: 12px;
								border-radius: 10px;
								background: #f5f5f5;
								text-align: center;
							}

							.count-box strong {
								display: block;
								font-size: 24px;
								color: #111;
							}

							.count-box small {
								color: #666;
								font-size: 13px;
							}
						</style>
						</p>

						<h6>Büdcə</h6>

						<p>

							<?php if ($tender['budget_min'] && $tender['budget_max']): ?>

								<?= number_format($tender['budget_min'], 2) ?>
								-
								<?= number_format($tender['budget_max'], 2) ?>
								<?= htmlspecialchars($tender['currency']) ?>

							<?php else: ?>

								Razılaşma yolu ilə

							<?php endif; ?>

						</p>
					</div>
				</div>

				<div class="share-postsection">
					<div class="row">
						<div class="col-lg-4">
							<div class="sharelink">
								<a href="javasvript:void();" class="share-img share-btn"
									data-url="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>"><i
										class="fas fa-light fa-share-nodes"></i></a>
								<a href="javasvript:void();" class="share-text share-btn"
									data-url="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">Paylaş</a>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="tag-list">
								<ul class="tags">

									<?php
									$tags = explode(',', $tender['tag_names']);

									foreach ($tags as $tag):
										?>

										<li>
											<?= htmlspecialchars(trim($tag)) ?>
										</li>

									<?php endforeach; ?>


								</ul>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<!--/tender Content-->


		<!-- Footer -->
		<?php require_once __DIR__ . "/inc/footer.php"; ?>
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
	<script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
	<script src="assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>

	<!-- Aos -->
	<script src="assets/plugins/aos/aos.js"></script>

	<!-- Top JS -->
	<script src="assets/js/backToTop.js"></script>

	<!-- Fearther JS -->
	<script src="assets/js/feather.min.js"></script>


	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>

	<script>
		document.addEventListener("click", async function (e) {
			const btn = e.target.closest(".share-btn");
			if (!btn) return;

			const url = btn.dataset.url;

			// 1. Native share (mobil)
			if (navigator.share) {
				try {
					await navigator.share({
						title: document.title,
						url: url
					});
				} catch (err) {
					console.log("Share cancelled");
				}
				return;
			}

			// 2. Copy to clipboard (desktop fallback)
			try {
				await navigator.clipboard.writeText(url);
				showError("Link kopyalandı ✅");
			} catch (err) {
				// köhnə browser fallback
				const temp = document.createElement("input");
				temp.value = url;
				document.body.appendChild(temp);
				temp.select();
				document.execCommand("copy");
				temp.remove();

				showError("Link kopyalandı ✅");
			}
		});
	</script>

</body>

</html>