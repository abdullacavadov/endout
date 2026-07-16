<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

if (!isset($_GET['l']) || !ctype_digit($_GET['l'])) {
	header("Location: ./my-listings");
	exit;
}

$lid = (int) $_GET['l'];

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";

?>


<?php

$stmt = $pdo->prepare("
							SELECT 
							l.id as lid,
							l.*, 

							c.slug_path,

							lt.name AS listing_type_name,
							lt.slug AS listing_type_slug,

							GROUP_CONCAT(DISTINCT lc.country_id) AS countries
							FROM listings l 

							LEFT JOIN listing_types lt 
								ON lt.id = l.type_id

							LEFT JOIN categories c 
								ON c.id = l.category_id

							LEFT JOIN listing_countries lc 
							ON lc.listing_id = l.id

							WHERE l.id = ?
							AND l.customer_id = ?
							GROUP BY l.id
							LIMIT 1
							");

$stmt->execute([$lid, $_SESSION['customer_id']]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
	header("Location: ./my-listings");
	exit;
}

if ($listing['customer_id'] != $customerId) {
	header("Location: ./my-listings");
	exit;
}

$countries = $listing['countries']
	? explode(",", $listing['countries'])
	: [];

/* şəkillər */

$stmt = $pdo->prepare("
							SELECT id, image_path, sort_order
							FROM listing_images
							WHERE listing_id=?
							ORDER BY sort_order
							");

$stmt->execute([$listing['lid']]);

$images = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("
							SELECT id, video_path
							FROM listing_videos
							WHERE listing_id=?
							LIMIT 1
							");

$stmt->execute([$lid]);

$video = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
							SELECT slug, name
							FROM categories
						");

$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catMap = [];

foreach ($cats as $c) {
	$catMap[$c['slug']] = $c['name'];
}

$slugs = explode('/', $listing['slug_path']);
$paths = [];
$current = '';

foreach ($slugs as $slug) {
	$current .= ($current ? '/' : '') . $slug;
	$paths[] = $current;
}

$names = [];

foreach ($slugs as $slug) {
	$names[] = $catMap[$slug] ?? $slug;
}

$stmt = $pdo->prepare("
    SELECT 
        c3.id AS sub_id,
        c2.id AS mid_id,
        c1.id AS main_id
    FROM categories c3
    LEFT JOIN categories c2 ON c2.id = c3.parent_id
    LEFT JOIN categories c1 ON c1.id = c2.parent_id
    WHERE c3.id = ?
    LIMIT 1
");

$stmt->execute([$listing['category_id']]);
$catTree = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="az">

<head>
	<?php require_once './inc/head.php'; ?>

	<style>
		.video-play {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 60px;
			height: 60px;
			background: rgba(0, 0, 0, 0.6);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-size: 22px;
			cursor: pointer;
		}
	</style>
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



					<form id="listingForm" enctype="multipart/form-data">



						<?php if ($listing['status'] == 'rejected'): ?>
							<div class="alert alert-danger mb-5 mt-5">
								Bu elan moderasiya tərəfindən təsdiqlənməyib. Ətraflı məlumat e-poçt ünvanınıza göndərilib.
							</div>

						<?php elseif ($listing['status'] == 'moderation'): ?>

							<div class="alert alert-info mb-5 mt-5">
								Bu elan hal-hazırda moderasiyada gözləyir. Nəticə barədə məlumat e-poçt ünvanınıza
								göndəriləcək.
							</div>

						<?php elseif ($listing['status'] == 'expired'): ?>

							<div class="alert alert-warning mb-5 mt-5">
								Bu elanınızın müddəti bitmişdir. Elanınızı yeniləyərək aktiv edə bilərsiniz. Elan limitini
								doldurmamısızsa elanı yenidən yarada bilərsiniz.
							</div>
						<?php else: ?>

							<input type="hidden" name="lid" value="<?= $listing['id'] ?>">



							<div class="messages-form">
								<div class="card">
									<div class="card-body">

										<div class="form-set">
											<div class="row">

												<div class="col-lg-6 col-md-12">
													<div class="form-floating">
														<select class="form-select" id="listing_type" name="listing_type">

															<?php
															$st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM listing_types
                                                                                    ORDER BY name ASC");

															$st->execute();



															while ($lt = $st->fetch(PDO::FETCH_ASSOC)) {
																?>

																<option value="<?= $lt['id']; ?>"
																	<?= $lt['id'] == $listing['type_id'] ? 'selected' : '' ?>>
																	<?= $lt['name']; ?>
																</option>
															<?php } ?>

														</select>
														<label>Elanın növü <span>*</span></label>
													</div>
												</div>

												<div class="col-lg-6 col-md-12">
													<div class="form-floating">
														<select class="form-select" id="sale_mode_id" name="sale_mode_id">

															<?php
															$st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM sale_modes
                                                                                    ORDER BY name ASC");

															$st->execute();



															while ($sm = $st->fetch(PDO::FETCH_ASSOC)) {
																?>

																<option value="<?= $sm['id']; ?>"
																	<?= $sm['id'] == $listing['sale_mode_id'] ? 'selected' : '' ?>>
																	<?= $sm['name']; ?>
																</option>
															<?php } ?>

														</select>
														<label>Satış növü <span>*</span></label>
													</div>
												</div>

											</div>
										</div>
										<div class="form-set">
											<div class="row">

												<div class="col-lg-4 col-md-12">
													<div class="form-floating">
														<select class="form-select" id="mainCategory"
															name="main_category"></select>
														<label>Əsas kateqoriya <span>*</span></label>
													</div>
												</div>

												<div class="col-lg-4 col-md-12">
													<div class="form-floating">
														<select class="form-select" id="midCategory"
															name="mid_category"></select>
														<label>Kateqoriya <span>*</span></label>
													</div>
												</div>

												<div class="col-lg-4 col-md-12">
													<div class="form-floating">
														<select class="form-select" id="subCategory"
															name="sub_category"></select>
														<label>Alt kateqoriya <span>*</span></label>
													</div>
												</div>

											</div>
										</div>


										<div class="form-set form-floating">
											<input type="text" id="bashliq" name="title" class="form-control"
												value="<?= htmlspecialchars($listing['title']) ?>" placeholder=" ">
											<label>Elan başlığı <span>*</span></label>
										</div>


										<div class="form-set form-floating">
											<textarea style="height:200px" id="desc" name="description" class="form-control"
												maxlength="5000"
												placeholder=" "><?= htmlspecialchars($listing['description']) ?></textarea>

											<label>Üstünlükləri və vacib məqamları qeyd edin <span>*</span></label>
										</div>


										<div class="row">
											<div class="col-2">
												<div class="form-set form-floating">
													<select id="currency" name="currency" class="form-control pass-input">
														<option value="" selected disabled>-- seçin --</option>
														<option value="AZN" <?= $listing['currency'] == 'AZN' ? 'selected' : '' ?>>AZN</option>
														<option value="USD" <?= $listing['currency'] == 'USD' ? 'selected' : '' ?>>USD</option>
														<option value="EUR" <?= $listing['currency'] == 'EUR' ? 'selected' : '' ?>>EUR</option>
													</select>
													<label for="currency">Valyuta <span>*</span></label>
												</div>
											</div>

											<div class="col-5" id="old_price_div">
												<div class="form-set form-floating">
													<input type="text" id="old_price" name="old_price"
														value="<?= $listing['old_price'] ?>" class="form-control"
														placeholder=" ">
													<label>Köhnə qiymət (AZN)</label>
												</div>
											</div>

											<div class="col-5">
												<div class="form-set form-floating">
													<input type="text" id="new_price" name="price"
														value="<?= $listing['price'] ?>" class="form-control"
														placeholder=" ">
													<label>Hazırkı qiymət (AZN) <span>*</span></label>
												</div>
											</div>

										</div>


										<div class="form-floating">
											<select class="form-select" id="countries" name="countries[]" multiple></select>
											<label>Elanın göstəriləcəyi ölkələr <span>*</span></label>
										</div>

									</div>
								</div>


								<div class="card media-section">
									<div class="card-header">
										<h4>Multimedia</h4>
									</div>

									<div class="card-body">

										<div class="gallery-media">

											<div class="galleryimg-upload" id="imagePreview"
												style="display: flex; flex-wrap: wrap; row-gap: 20px;">

												<?php foreach ($images as $img): ?>

													<div class="gallery-upload existing-image" data-id="<?= $img['id'] ?>">

														<img src="assets/img/uploads/listings/<?= $img['image_path'] ?>"
															data-id="<?= $img['id']; ?>]"
															style="height:180px;width:180px;object-fit:contain;border:1px solid gray;cursor:move">

														<a href="#" class="profile-img-del existing-del">
															<i class="feather-trash-2"></i>
														</a>

													</div>

												<?php endforeach; ?>


											</div>


											<div class="settings-upload-btn">
												<input type="file" accept="image/*" name="images[]"
													class="hide-input image-upload" id="file2" multiple>

												<label for="file2" class="file-upload">
													<i class="fas fa-images"></i> Şəkil əlavə et
												</label>
											</div>

										</div>



										<div class="gallery-media">

											<div class="galleryimg-upload" id="videoPreview"></div>




											<?php if ($video): ?>

												<div class="gallery-upload" id="exVid" data-id="<?= $video['id'] ?>">

													<video width="320" controls>
														<source src="assets/video/uploads//listings/<?= $video['video_path'] ?>"
															type="video/mp4">
													</video>

													<a href="#" class="profile-img-del" id="exVidDel" style="left: 10px">
														<i class="feather-trash-2"></i>
													</a>


												</div>

											<?php endif; ?>

											<div class="settings-upload-btn">
												<input type="file" accept="video/*" name="video_path" class="hide-input"
													id="file3">

												<label for="file3" class="file-upload">
													<i class="fas fa-images"></i> Yeni video seç
												</label>
											</div>

										</div>

										<div class="alert alert-primary mt-3">
											<?php if ($listing['status'] == 'moderation') {
												$statusText = "Moderasiya mərhələsində";
											} elseif ($listing['status'] == 'rejected') {
												$statusText = "Təsdiqlənməmiş";
											} elseif ($listing['status'] == 'draft') {
												$statusText = "Qaralama";
											} elseif ($listing['status'] == 'expired') {
												$statusText = "Vaxtı bitmiş";
											} elseif ($listing['status'] == 'active') {
												$statusText = "Aktiv";
											} else {
												$statusText = $listing['status'];
											} ?>

											<?php if ($listing['status'] == 'active' || $listing['status'] == 'draft'): ?>
												<div class="form-floating">
													<select class="form-select" name="status">
														<option value="active" <?php if ($listing['status'] == 'active') {
															echo 'selected';
														} ?>>Aktiv</option>
														<option value="draft" <?php if ($listing['status'] == 'draft') {
															echo 'selected';
														} ?>>Qaralama</option>
													</select>
													<label>Elanın statusunu dəyişdir</label>
													<small>Qaralamada olan elanlar saytda göstərilmir</small>
												</div>
											<?php else: ?>
												Elanın statusu: <strong><?= $statusText; ?></strong>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>


					</div>

					<button class="btn btn-primary" type="submit">
						Elanı yenilə
					</button>

				</div>
				</form>
			<?php endif; ?>

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


	<!-- Tom Select -->
	<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


	<!-- Sortable JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"
		integrity="sha512-csIng5zcB+XpulRUa+ev1zKo7zRNGpEaVfNB9On1no9KYTEY/rLGAEEpvgdw6nim1WdTuihZY1eqZ31K7/fZjw=="
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script>
		const preview = document.getElementById("imagePreview");
		const fileInput = document.getElementById("file2");

		let newImages = [];

		/* =========================
		   SORTABLE INIT
		========================= */

		const sortable = new Sortable(preview, {
			animation: 150,
			ghostClass: 'sortable-ghost',
		});

		/* =========================
		   INIT EXISTING
		========================= */

		function markExisting() {
			document.querySelectorAll(".existing-image").forEach(el => {
				el.dataset.type = "existing";
			});
		}

		/* =========================
		   RENDER NEW IMAGES
		========================= */

		function renderNewImages() {

			document.querySelectorAll(".new-image").forEach(el => el.remove());

			newImages.forEach((file, i) => {

				const div = document.createElement("div");
				div.className = "gallery-upload new-image";
				div.dataset.type = "new";
				div.dataset.index = i;

				div.innerHTML = `
			<img src="${URL.createObjectURL(file)}"
				style="height:180px;width:180px;object-fit:contain;border:1px solid gray;cursor:move;">
			<a href="#" class="profile-img-del new-del">
				<i class="feather-trash-2"></i>
			</a>
		`;

				preview.appendChild(div);
			});
		}

		/* =========================
		   ADD FILES
		========================= */

		fileInput.addEventListener("change", function () {

			const files = Array.from(this.files);

			const total =
				document.querySelectorAll(".gallery-upload").length + files.length;

			if (total > 10) {
				showError("Maximum 10 şəkil ola bilər");
				return;
			}

			newImages.push(...files);

			renderNewImages();
		});

		/* =========================
		   DELETE (NEW)
		========================= */

		document.addEventListener("click", function (e) {

			const btn = e.target.closest(".new-del");
			if (!btn) return;

			e.preventDefault();

			const box = btn.closest(".new-image");
			const index = parseInt(box.dataset.index);

			newImages.splice(index, 1);

			renderNewImages();
		});

		document.addEventListener("click", async function (e) {

			const btn = e.target.closest(".existing-del");
			if (!btn) return;

			e.preventDefault();

			const box = btn.closest(".existing-image");
			const imgID = box.dataset.id;

			if (!confirm("Şəkil silinsin?")) return;

			const resimg = await fetch("./api/delete/listing_image.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({
					id: imgID
				})
			});

			const data = await resimg.json();

			if (data.success) {
				box.remove();
			} else {
				showError(data.error);
			}
		});

		/* =========================
		   SUBMIT
		========================= */

		document.getElementById("listingForm").addEventListener("submit", async function (e) {

			e.preventDefault();

			const formData = new FormData(this);

			formData.delete("images[]");
			formData.delete("existing_images[]");

			const items = preview.querySelectorAll(".gallery-upload");

			let existingOrder = [];
			let newIndexMap = [];

			items.forEach(el => {

				if (el.dataset.type === "existing") {
					existingOrder.push(el.dataset.id);
				} else {
					newIndexMap.push(parseInt(el.dataset.index));
				}
			});

			/* existing */
			existingOrder.forEach(id => {
				formData.append("existing_images[]", id);
			});

			/* new */
			newIndexMap.forEach(idx => {
				formData.append("images[]", newImages[idx]);
			});

			const res = await fetch("./api/update/listing.php", {
				method: "POST",
				body: formData
			});

			const data = await res.json();

			if (data.success) {
				showError("Elan yeniləndi");
				setTimeout(function () {
					location.href = "./my-listings";
				}, 2500)
			} else {
				showError(data.error);
			}
		});

		/* =========================
		   INIT
		========================= */

		markExisting();
	</script>


	<script>
		const main = document.getElementById("mainCategory");
		const mid = document.getElementById("midCategory");
		const sub = document.getElementById("subCategory");


		const selectedMain = <?= (int) ($catTree['main_id'] ?? 0) ?>;
		const selectedMid = <?= (int) ($catTree['mid_id'] ?? 0) ?>;
		const selectedSub = <?= (int) ($catTree['sub_id'] ?? 0) ?>;

		document.addEventListener("DOMContentLoaded", function () {
			const oldPriceDiv = document.getElementById("old_price_div");
			const listingTypeSelect = document.getElementById("listing_type");
			const oldPrice = <?= (float) $listing['old_price'] ?>;

			if (oldPrice > 0) {
				oldPriceDiv.style.display = "block";
			} else {
				oldPriceDiv.style.display = "none";
			}

			listingTypeSelect.addEventListener("change", () => {
				if (listingTypeSelect.value == 1 || listingTypeSelect.value == 2) {
					document.getElementById("old_price_div").style.display = "block";
				} else {
					document.getElementById("old_price_div").style.display = "none";
				}
			});
		});



		const adCountriesSelect = document.getElementById("countries");
		let countriesTom = null;

		/* main load */

		function loadCategories(select, url, selectedValue = null) {
			return fetch(url)
				.then(res => res.json())
				.then(data => {
					select.innerHTML = '<option value="">-- seçin --</option>';

					data.forEach(cat => {
						const selected = selectedValue == cat.id ? 'selected' : '';
						select.innerHTML += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
					});
				});
		}

		document.addEventListener("DOMContentLoaded", async function () {

			// əvvəl əsas kateqoriyanı yüklə və seç
			await loadCategories(
				main,
				`<?= $base_url; ?>/api/data/get_categories.php?depth=0`,
				selectedMain
			);

			// əgər main varsa → mid yüklə
			if (selectedMain) {
				await loadCategories(
					mid,
					`<?= $base_url; ?>/api/data/get_categories.php?parent_id=${selectedMain}&depth=1`,
					selectedMid
				);
			}

			// əgər mid varsa → sub yüklə
			if (selectedMid) {
				await loadCategories(
					sub,
					`<?= $base_url; ?>/api/data/get_categories.php?parent_id=${selectedMid}&depth=2`,
					selectedSub
				);
			}
		});

		main.addEventListener("change", async () => {
			const id = main.value;

			mid.innerHTML = '<option value="">-- seçin --</option>';
			sub.innerHTML = '<option value="">-- seçin --</option>';

			if (!id) return;

			await loadCategories(
				mid,
				`<?= $base_url; ?>/api/data/get_categories.php?parent_id=${id}&depth=1`
			);
		});

		mid.addEventListener("change", async () => {
			const id = mid.value;

			sub.innerHTML = '<option value="">-- seçin --</option>';

			if (!id) return;

			await loadCategories(
				sub,
				`<?= $base_url; ?>/api/data/get_categories.php?parent_id=${id}&depth=2`
			);
		});



		function destroyTomSelect(el) {
			if (el && el.tomselect) {
				el.tomselect.destroy();
				el.tomselect = null;
			}
		}

		async function loadCountries() {

			const res = await fetch("./api/data/market_countries.php");
			const data = await res.json();

			if (countriesTom) {
				countriesTom.destroy();
			}

			const selectedCountries = <?= json_encode(array_map('intval', $countries)) ?>;

			countriesTom = new TomSelect("#countries", {
				options: data,
				items: selectedCountries,   // 👈 burada selected olur
				valueField: "id",
				labelField: "name",
				searchField: "name",
				plugins: ["remove_button"],
				maxItems: null,
				render: {
					option: function (data, escape) {
						return `
					<div>
						<img class="me-1" src="https://flagcdn.com/16x12/${data.iso2.toLowerCase()}.png"> 
						${escape(data.name)}
					</div>
				`;
					},
					item: function (data, escape) {
						return `
					<div>
						<img class="me-1" src="https://flagcdn.com/16x12/${data.iso2.toLowerCase()}.png"> 
						${escape(data.name)}
					</div>
				`;
					}
				}
			});

		}



		loadCountries();
	</script>

	<script>



		const videoInput = document.getElementById("file3");
		const videoPreview = document.getElementById("videoPreview");

		videoInput.addEventListener("change", function () {

			const file = this.files[0];
			if (!file) return;

			const maxSize = 30 * 1024 * 1024;

			if (file.size > maxSize) {
				showError("Video maksimum 25MB ola bilər");
				this.value = "";
				return;
			}

			const url = URL.createObjectURL(file);

			videoPreview.innerHTML = `
		<video width="320" controls>
			<source src="${url}">
		</video>
	`;

		});


		document.addEventListener("click", async function (e) {

			const btn = e.target.closest("#exVidDel");
			if (!btn) return;

			e.preventDefault();

			const box = btn.closest("#exVid");
			const videoId = box.dataset.id;

			if (!confirm("Video silinsin?")) return;

			const res = await fetch("./api/delete/listing_video.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({
					id: videoId
				})
			});

			const data = await res.json();

			if (data.success) {
				box.remove();
			} else {
				showError(data.error);
			}

		});
	</script>
</body>

</html>