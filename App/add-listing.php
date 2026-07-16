<?php
require_once __DIR__ . "/inc/config.php";
require_login($pdo);
require_verify($pdo, 'verify-phone');

require_once __DIR__ . "/api/data/market_data.php";
require_once __DIR__ . "/api/data/user_data.php";
require_once __DIR__ . "/api/data/subscription_data.php";

$listingAccess = can_create_listing($pdo, $customerId);
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
			<div class="container" style="min-height: 20vh;">

				<div class="profile-content" style="margin-top: 70px;">

					<?php if ($cust_type === 'partner'): ?>



						<?php if ($listingAccess['allowed']): ?>

							<div class="alert alert-info" style="margin-top: 150px;">
								Qalan elan limiti:
								<?= $listingAccess['remaining'] ?>
								/ <?= $listingAccess['max_post'] ?>
							</div>

							<form id="listingForm" enctype="multipart/form-data">
								<div class="messages-form">
									<div class="card">

										<div class="card-body">

											<div class="form-set">
												<div class="row">
													<div class="col-lg-6 col-md-12">
														<div class="form-floating">
															<select class="form-select" id="listing_type" name="listing_type">
																<option value="" selected disabled>-- seçin --</option>
																<?php
																$st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM listing_types
                                                                                    ORDER BY name ASC");

																$st->execute();



																while ($lt = $st->fetch(PDO::FETCH_ASSOC)) {
																	?>

																	<option value="<?= $lt['id']; ?>">
																		<?= $lt['name']; ?>
																	</option>
																<?php } ?>
															</select>
															<label for="listing_type">Elanın növü <span>*</span></label>
														</div>
													</div>
													<div class="col-lg-6 col-md-12">
														<div class="form-floating">
															<select class="form-select" name="sale_mode" id="sale_mode">
																<option value="" selected disabled>-- seçin --</option>
																<?php
																$st = $pdo->prepare("
                                                                                    SELECT id, name
                                                                                    FROM sale_modes
                                                                                    ORDER BY name ASC");

																$st->execute();



																while ($sm = $st->fetch(PDO::FETCH_ASSOC)) {
																	?>

																	<option value="<?= $sm['id']; ?>">
																		<?= $sm['name']; ?>
																	</option>
																<?php } ?>
															</select>
															<label for="sale_mode">Satış növü <span>*</span></label>
														</div>
													</div>


												</div>
											</div>

											<div class="form-set">
												<div class="row">
													<div class="col-lg-4 col-md-12">
														<div class="form-floating">
															<select class="form-select" id="mainCategory"></select>
															<label for="mainCategory">Əsas kateqoriya <span>*</span></label>
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-floating">
															<select class="form-select" id="midCategory"></select>
															<label for="midCategory">Kateqoriya <span>*</span></label>
														</div>
													</div>

													<div class="col-lg-4 col-md-12">
														<div class="form-floating">
															<select class="form-select" id="subCategory"
																name="category_id"></select>
															<label for="subCategory">Alt kateqoriya <span>*</span></label>
														</div>
													</div>
												</div>
											</div>



											<div class="form-set form-floating">
												<input type="text" id="bashliq" name="title" class="form-control pass-input"
													placeholder=" ">
												<label for="bashliq">Elan başlığı <span>*</span></label>
											</div>

											<div class="form-set form-floating">
												<textarea style="height: 200px" id="desc" name="description"
													class="form-control pass-input" placeholder=" " maxlength="5000"></textarea>
												<label for="desc">Üstünlükləri və vacib məqamları qeyd
													edin
													<span>*</span></label>
											</div>

											<div class="row">
												<div class="col-2">
													<div class="form-set form-floating">
														<select id="currency" name="currency" class="form-control pass-input">
															<option value="" selected disabled>-- seçin --</option>
															<option value="AZN">AZN</option>
															<option value="USD">USD</option>
															<option value="EUR">EUR</option>
														</select>
														<label for="currency">Valyuta <span>*</span></label>
													</div>
												</div>
												<div class="col-5" id="old_price_div" style="display: none;">
													<div class="form-set form-floating">
														<input type="text" id="old_price" name="old_price"
															class="form-control pass-input" placeholder=" ">
														<label for="old_price">Köhnə qiymət (AZN) <span>*</span></label>
													</div>
												</div>
												<div class="col-5">
													<div class="form-set form-floating">
														<input type="text" id="new_price" name="price"
															class="form-control pass-input" placeholder=" ">
														<label for="new_price">Hazırkı qiymət (AZN) <span>*</span></label>
													</div>
												</div>
											</div>

											<div class="form-floating">
												<select class="form-select" id="countries" name="countries[]" multiple></select>
												<label for="countries">Elanın göstəriləcəyi ölkələr <span>*</span></label>

												<style>
													.ts-control {
														border: none;
														padding: 6px 15px 6px 38px;
													}

													.ts-control>input::placeholder {
														color: #7c7c7c;
													}

													.ts-wrapper.is-invalid .ts-wrapper {
														border: 1px solid #dc3545 !important;
														border-radius: 6px;
													}
												</style>
											</div>


										</div>
									</div>




									<div class="card media-section">
										<div class="card-header">
											<h4>Medialar </h4>
										</div>
										<div class="card-body">



											<div class="gallery-media">
												<div class="galleryimg-upload" id="imagePreview"></div>


												<div class="settings-upload-btn">
													<input type="file" accept="image/*" name="images[]"
														class="hide-input image-upload" id="file2" multiple>
													<label for="file2" class="file-upload"><i class="fas fa-images"></i> Şəkil
														yüklə</label>
												</div>
											</div>


											<div class="gallery-media">

												<div class="galleryimg-upload" id="videoPreview"></div>

												<div class="settings-upload-btn">
													<input type="file" accept="video/*" name="video_path" class="hide-input"
														id="file3">

													<label for="file3" class="file-upload">
														<i class="fas fa-video"></i> Video yüklə
													</label>
												</div>

											</div>
										</div>
									</div>
									<button class="btn btn-primary" type="submit"> Elanı əlavə et</button>
								</div>
							</form>

						<?php else: ?>

							<div class="alert alert-danger" style="margin-top: 150px;">
								<?= htmlspecialchars($listingAccess['reason']) ?>
							</div>

						<?php endif; ?>

					<?php else: ?>
						<div class="alert alert-danger" style="margin-top: 150px;">
							Yalnız marketpleysli istifadəçilər elan əlavə edə bilər
						</div>
					<?php endif; ?>

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

	<script src="../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js"
		data-cf-settings="2f56c08bf4c64a5a606bde4d-|49" defer></script>

	<!-- Tom Select -->
	<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>







	<!-- Sortable JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"
		integrity="sha512-csIng5zcB+XpulRUa+ev1zKo7zRNGpEaVfNB9On1no9KYTEY/rLGAEEpvgdw6nim1WdTuihZY1eqZ31K7/fZjw=="
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script defer>
		document.addEventListener("DOMContentLoaded", function () {
			const oldPriceDiv = document.getElementById("old_price_div");
			const listingTypeSelect = document.getElementById("listing_type");

			const main = document.getElementById("mainCategory");
			const mid = document.getElementById("midCategory");
			const sub = document.getElementById("subCategory");

			let countriesTom = null;

			listingTypeSelect.addEventListener("change", () => {
				if (listingTypeSelect.value == 1 || listingTypeSelect.value == 2) {
					document.getElementById("old_price_div").style.display = "block";
				} else {
					document.getElementById("old_price_div").style.display = "none";
				}
			});

			function loadCategories(select, url) {
				fetch(url)
					.then(res => res.json())
					.then(data => {
						select.innerHTML = '<option value="">-- seçin --</option>';
						data.forEach(cat => {
							select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
						});
					});
			}

			// ilkin yüklə (əsas kateqoriya)
			loadCategories(main, `<?= $base_url; ?>/api/data/get_categories.php?depth=0`);

			// əsas dəyişəndə
			main.addEventListener("change", () => {
				const id = main.value;



				// reset
				mid.innerHTML = '<option value="">-- seçin --</option>';
				sub.innerHTML = '<option value="">-- seçin --</option>';

				if (!id) return;



				loadCategories(mid, `<?= $base_url; ?>/api/data/get_categories.php?parent_id=${id}&depth=1`);
			});

			// orta dəyişəndə
			mid.addEventListener("change", () => {
				const id = mid.value;

				// reset
				sub.innerHTML = '<option value="">-- seçin --</option>';

				if (!id) return;

				loadCategories(sub, `<?= $base_url; ?>/api/data/get_categories.php?parent_id=${id}&depth=2`);
			});

			async function loadCountries() {
				const res = await fetch("<?= $base_url; ?>/api/data/market_countries.php");
				const data = await res.json();
				if (countriesTom) countriesTom.destroy();
				const selected = data.map(c => c.id);
				countriesTom = new TomSelect("#countries", {
					options: data,
					items: selected,
					valueField: "id",
					labelField: "name",
					searchField: "name",
					plugins: ["remove_button"],
					maxItems: null,
					render: {
						option: function (data, escape) {
							return `<div><img class="me-1" src="https://flagcdn.com/16x12/${data.iso2.toLowerCase()}.png">${escape(data.name)}</div>`;
						},
						item: function (data, escape) {
							return `<div><img class="me-1" src="https://flagcdn.com/16x12/${data.iso2.toLowerCase()}.png">${escape(data.name)}</div>`;
						}
					}
				});
			}
			loadCountries();

			// 2️⃣ Şəkil seçimi və preview
			const fileInput = document.getElementById("file2");
			const preview = document.getElementById("imagePreview");
			let selectedImages = [];

			fileInput.addEventListener("change", function () {
				const files = Array.from(this.files);

				if (selectedImages.length + files.length > 10) {
					showError("Maximum 10 şəkil əlavə edə bilərsiniz");
					return;
				}

				function updateFileInput() {
					const dt = new DataTransfer();
					selectedImages.forEach(file => dt.items.add(file));
					fileInput.files = dt.files;
				}

				files.forEach(file => {
					selectedImages.push(file);

					const reader = new FileReader();
					reader.onload = function (e) {
						const div = document.createElement("div");
						div.className = "gallery-upload";
						div.fileRef = file;

						div.innerHTML = `
				<img src="${e.target.result}" class="img-fluid" style="height:200px;width:200px;object-fit:contain;border:1px solid gray;cursor:move;">
				<a href="#" class="profile-img-del"><i class="feather-trash-2"></i></a>
			`;

						div.querySelector(".profile-img-del").addEventListener("click", function (ev) {
							ev.preventDefault();

							// selectedImages-dən çıxart
							const index = selectedImages.indexOf(div.fileRef);
							if (index > -1) selectedImages.splice(index, 1);

							div.remove();

							// input-u yenilə
							updateFileInput();
						});

						preview.appendChild(div);
					};
					reader.readAsDataURL(file);
				});

				// input-u yenilə
				updateFileInput();

				// reset et ki, eyni faylı yenidən seçmək mümkün olsun
				fileInput.value = "";
			});

			// 3️⃣ Video seçimi və preview
			const videoInput = document.getElementById("file3");
			const videoPreview = document.getElementById("videoPreview");

			videoInput.addEventListener("change", function () {
				const file = this.files[0];
				if (!file || !file.type.startsWith("video/")) return showError("Yalnız video seçə bilərsiniz"), this.value = "";

				videoPreview.innerHTML = "";
				const div = document.createElement("div");
				div.className = "gallery-upload";

				const video = document.createElement("video");
				video.src = URL.createObjectURL(file);
				video.controls = false;

				const playBtn = document.createElement("div");
				playBtn.className = "video-play";
				playBtn.innerHTML = '<i class="fas fa-play"></i>';

				playBtn.onclick = () => { video.paused ? video.play() && (playBtn.style.display = "none") : video.pause() && (playBtn.style.display = "flex"); };
				video.onclick = () => { video.pause(); playBtn.style.display = "flex"; };

				video.style.cssText = "height:250px;width:250px;object-fit:contain;border:1px solid gray;background:black;border-radius:8px;";

				const remove = document.createElement("a");
				remove.href = "#";
				remove.className = "profile-img-del";
				remove.innerHTML = '<i class="feather-trash-2"></i>';
				remove.addEventListener("click", e => { e.preventDefault(); div.remove(); videoInput.value = ""; });

				div.appendChild(video); div.appendChild(playBtn); div.appendChild(remove);
				videoPreview.appendChild(div);
			});

			// 4️⃣ Drag & Drop Sortable
			new Sortable(preview, {
				animation: 150,
				onEnd: function () {
					selectedImages = Array.from(preview.children).map(div => div.fileRef);
				}
			});

			// 5️⃣ Form submit
			document.getElementById("listingForm").addEventListener("submit", async function (e) {
				e.preventDefault();
				const form = document.getElementById("listingForm");
				const formData = new FormData(form);

				// Sortable ilə düzülmüş şəkilləri göndər
				selectedImages.forEach((file, index) => {
					formData.append('images[]', file);
					formData.append('sort_order[]', index + 1);
				});

				const res = await fetch("./api/create/create_listing.php", { method: "POST", body: formData });
				const data = await res.json();

				if (data.success) {
					showError("Elan əlavə edildi");
					window.location.href = "./my-listings";
				} else {
					showError(data.error);
				}
			});

		});
	</script>
</body>

</html>