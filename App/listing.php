<?php
require_once __DIR__ . "/inc/config.php";



$slug = $_GET['slug'] ?? '';
$lid = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$lid) {
	http_response_code(404);
	exit('Not Found');
}


$stmt = $pdo->prepare("
SELECT 
    l.id AS lid,
	l.description AS listing_desc,
	l.slug AS listing_slug,
    l.*, 

    m.name AS market_name,
    m.logo AS market_logo,
	m.id AS market_id,
	m.slug AS market_slug,
	m.*,

	c.slug_path,

    lt.name AS listing_type_name,
    lt.slug AS listing_type_slug,

	ctry.name AS country,
	cty.name AS city,
	

    GROUP_CONCAT(DISTINCT lc.country_id) AS countries,

	COALESCE(r.rating_avg,0) AS rating_avg,
	COALESCE(r.rating_count,0) AS rating_count,
	COALESCE(r.star_5,0) AS star_5,
	COALESCE(r.star_4,0) AS star_4,
	COALESCE(r.star_3,0) AS star_3,
	COALESCE(r.star_2,0) AS star_2,
	COALESCE(r.star_1,0) AS star_1


FROM listings l

LEFT JOIN listing_types lt 
    ON lt.id = l.type_id

LEFT JOIN categories c 
    ON c.id = l.category_id

LEFT JOIN markets m 
    ON m.id = l.market_id

LEFT JOIN countries ctry
	ON ctry.id = m.country_id

LEFT JOIN cities cty
	ON cty.id = m.city_id

LEFT JOIN listing_countries lc 
    ON lc.listing_id = l.id

LEFT JOIN (
    SELECT 
        listing_id,
        ROUND(AVG(rating),1) AS rating_avg,
        COUNT(*) AS rating_count,
        SUM(rating = 5) AS star_5,
        SUM(rating = 4) AS star_4,
        SUM(rating = 3) AS star_3,
        SUM(rating = 2) AS star_2,
        SUM(rating = 1) AS star_1
    FROM listings_reviews
    GROUP BY listing_id
) r ON r.listing_id = l.id


WHERE l.id = ?
GROUP BY l.id
LIMIT 1
");

$stmt->execute([$lid]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);


$countries = $listing['countries']
	? explode(",", $listing['countries'])
	: [];

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


/* sosiallar */

$stmt = $pdo->prepare("
SELECT social_url, social_icon FROM markets_socials WHERE market_id=?
");

$stmt->execute([$listing['market_id']]);

$socials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="az">

<head>
	<?php
	$pageType = 'listing';

	$metaData = [
		'title' => $listing['title'],
		'description' => $listing['listing_desc'],
		'slug' => $listing['listing_slug'],
		'id' => $listing['lid']
	];

	?>
	<?php require_once './inc/head.php'; ?>

</head>

<body>


	<div class="main-wrapper home-nine">

		<!-- Header -->
		<?php require_once __DIR__ . '/inc/header.php'; ?>
		<!-- /Header -->



		<!--Galler Slider Section-->
		<div class="bannergallery-section mt-5">
			<div class="gallery-slider d-inline-flex align-items-center" style="margin-top: 90px">

				<?php foreach ($images as $img): ?>

					<img src="<?= $base_url; ?>/assets/img/uploads/listings/<?= $img['image_path'] ?>"
						style="height:220px;width:220px;object-fit:contain;">


				<?php endforeach; ?>

			</div>

		</div>

		<!--Details Description  Section-->
		<section class="details-description">
			<div class="container">
				<div class="about-details">
					<div class="about-headings">

						<div class="authordetails">
							<h5><?= htmlspecialchars($listing['title']); ?></h5>

							<a href="<?= $base_url; ?>/listings/<?= $listing['listing_type_slug'] ?>"
								class="btn btn-primary"><?= htmlspecialchars($listing['listing_type_name']); ?></a>



							<?php
							$avg = $listing['rating_avg'] ?? '0.0';
							$fullStars = floor($avg); // tam ulduzlar
							$halfStar = ($avg - $fullStars >= 0.5) ? true : false;
							$emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
							?>
							<div class="rating">
								<?php for ($i = 0; $i < $fullStars; $i++): ?>
									<i class="fas fa-star filled"></i>
								<?php endfor; ?>

								<?php if ($halfStar): ?>
									<i class="fas fa-star-half-alt filled"></i>
								<?php endif; ?>

								<?php for ($i = 0; $i < $emptyStars; $i++): ?>
									<i class="fa-regular fa-star rating-color"></i>
								<?php endfor; ?>

								<span class="d-inline-block average-rating">
									<?= $listing['rating_avg'] . ' (' . $listing['rating_count'] . ' rəy)'; ?>
								</span>
							</div>

							<span>
								<?php foreach ($names as $i => $name): ?>

									<a style="text-decoration: underline"
										href="<?= $base_url ?>/listings/<?= $paths[$i] ?>">
										<?= htmlspecialchars($name) ?>
									</a>

									<?php if ($i < count($names) - 1): ?>
										/
									<?php endif; ?>

								<?php endforeach; ?>
							</span>



						</div>
					</div>
					<div class="rate-details">
						<?php
						function getRates()
						{
							$xml = simplexml_load_file("https://www.cbar.az/currencies/01.04.2026.xml");

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

						$stmt = $pdo->prepare("
    SELECT currency_code 
    FROM countries 
    WHERE iso2 = ?
");

						$stmt->execute([$user_country_code]);
						$userCurrency = $stmt->fetchColumn();

						$rates = getRates();

						$converted = convertCurrency(
							$listing['price'],
							$listing['currency'],   // məsələn USD
							$userCurrency,       // məsələn EUR
							$rates
						);

						if (!$converted) {
							$converted = $listing['price'];
							$userCurrency = 'USD';
						}

						echo '<h2>' . number_format($converted, 2) . ' ' . $userCurrency . '</h2>';

						if ($listing['old_price'] != 0) {
							$convertedOld = convertCurrency(
								$listing['old_price'],
								$listing['currency'],
								$userCurrency,
								$rates
							);

							if (!$convertedOld) {
								$convertedOld = $listing['old_price'];
							}

							echo '<p><del>' . number_format($convertedOld, 2) . ' ' . $userCurrency . '</del></p>';
						}
						?>

					</div>
				</div>
				<div class="descriptionlinks">
					<div class="row">
						<div class="col-lg-9">
							<ul style="font-size: 12px">

								<?php if ($listing['url'] !== NULL): ?>
									<li><a href="<?= htmlspecialchars($listing['url']); ?>" target="_blank"><i
												class="fa-solid fa-link"></i> Vebsayt</a>
									</li>
								<?php endif; ?>
								<li>
									<?php $url = $base_url . '/listing/' . $listing['listing_slug'] . '/' . $listing['lid']; ?>
									<a href="javascript:void(0);" class="share-btn"
										data-url="<?= htmlspecialchars($url); ?>">
										<i class="feather-share-2"></i> Paylaş
									</a>
								</li>
								<li><a href="javascript:void(0);"><i class="fa-regular fa-comment-dots"></i>
										Qiymətləndir</a></li>
								<br>
								<li><a href="javascript:void(0);" class="complaint-btn" data-bs-toggle="modal"
										data-bs-target="#complaintModal">
										<i class="feather-flag"></i> Şikayət et
									</a>
								</li>
								<li>
									<?php
									$isFav = false;

									if (!empty($_SESSION['customer_id'])) {
										$stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE customer_id=? AND listing_id=?");
										$stmt->execute([$_SESSION['customer_id'], $lid]);
										$isFav = (bool) $stmt->fetchColumn();
									}
									?>

									<a href="javascript:void(0);"
										class="add-favorite <?= $isFav ? 'text-primary fw-bold' : '' ?>"
										data-listing-id="<?= $lid; ?>">
										<i class="fa-<?= $isFav ? 'solid' : 'regular' ?> fa-heart"></i>
										<?= $isFav ? 'Seçilmişlərdədir' : 'Seçilmişlərdə saxla' ?>
									</a>
								</li>
							</ul>
						</div>
						<div class="col-lg-3">
							<div class="callnow">
								<a href="" data-bs-toggle="modal" data-bs-target="#messageModal"> <i
										class="fa-solid fa-comment-dots"></i> Mesaj yaz</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--/Details Description  Section-->

		<!--Details Main  Section-->
		<div class="details-main-wrapper listing-section ">
			<div class="container">
				<div class="row">
					<div class="col-lg-9">
						<div class="card ">
							<div class="card-header">

							</div>
							<div class="card-body">
								<p>
									<span class="bar-icon">
										<span></span>
										<span></span>
										<span></span>
									</span>
									<?= nl2br($listing['listing_desc']); ?>
								</p>
							</div>
						</div>



						<!--Gallery Section-->
						<div class="card gallery-section ">
							<div class="card-header ">
								<img src="<?= $base_url; ?>/assets/img/galleryicon.svg" alt="gallery">
								<h4>Qalereya</h4>
							</div>
							<div class="card-body">
								<div class="gallery-content">
									<div class="row">

										<?php foreach ($images as $img): ?>

											<div class="col-lg-3 col-md-3 col-sm-3">
												<div class="gallery-widget">
													<a href="<?= $base_url; ?>/assets/img/uploads/listings/<?= $img['image_path'] ?>"
														data-fancybox="gallery1">
														<img class="img-fluid" alt="Image"
															src="<?= $base_url; ?>/assets/img/uploads/listings/<?= $img['image_path'] ?>">
													</a>
												</div>
											</div>

										<?php endforeach; ?>

										<?php if ($video): ?>
											<!-- 
													<video width="auto" height="300" controls>
														<source
															src="<?= $base_url; ?>/assets/video/uploads/listings/<?= $video['video_path'] ?>"
															type="video/mp4">
														Your browser does not support the video tag.
													</video> -->

											<style>
												/* fallback */
												@font-face {
													font-family: 'Material Icons';
													font-style: normal;
													font-weight: 400;
													src: url(https://fonts.gstatic.com/s/materialicons/v145/flUhRq6tzZclQEJ-Vdg-IuiaDsNc.woff2) format('woff2');
												}

												.material-icons {
													font-family: 'Material Icons';
													font-weight: normal;
													font-style: normal;
													font-size: 24px;
													line-height: 1;
													letter-spacing: normal;
													text-transform: none;
													display: inline-block;
													white-space: nowrap;
													word-wrap: normal;
													direction: ltr;
													-webkit-font-feature-settings: 'liga';
													-webkit-font-smoothing: antialiased;
												}

												.videoContainer {
													position: relative;
													width: 100%;
													font-family: Arial;
													background-color: black;
												}

												.videoContainer video {
													width: 100%;
													height: 300px;
													display: block;
												}

												/* ---- Controls ---- */
												.control {
													width: 100%;
													height: 30px;
													background-color: #262626;
													position: absolute;
													left: 0px;
													bottom: 0px;
													display: flex;
													z-index: 2147483647;
													/* ---- Progress ---- */
												}

												.control label {
													display: block;
													padding: 0px 10px;
													color: white;
													line-height: 30px;
												}

												.control a {
													display: block;
													width: 40px;
													height: 30px;
													color: #bebebe;
													text-align: center;
													text-decoration: none;
													line-height: 30px;
													position: relative;
													cursor: pointer;
													font-size: 14px;
												}

												.control a:hover {
													color: #c10037;
													text-decoration: none;
													background-color: #3f3f3f;
												}

												.control .play {
													margin-right: 5px;
												}

												.control .progress {
													width: 100%;
													height: 10px;
													background: #1b1b1b;
													-webkit-backdrop-filter: blur(20px);
													backdrop-filter: blur(20px);
													box-shadow: inset 0 -5px 10px rgba(0, 0, 0, 0.1);
													float: left;
													cursor: pointer;
													padding: 0;
													margin: 0;
													position: absolute;
													left: 0px;
													top: -10px;
													font-variant: normal;
													opacity: 0.7;
													overflow-x: hidden;
												}

												.control .progress-bar {
													background: #c10037;
													box-shadow: inset -30px 0px 69px -20px #c10037;
													height: 100%;
													position: relative;
													z-index: 999;
													width: 0;
												}

												.control .progress-bar:before {
													width: 5px;
													height: 10px;
													top: 0px;
													right: 0px;
													content: " ";
													background-color: #fff;
													position: absolute;
												}

												.control .time {
													color: #fff;
													font-weight: bold;
													font-size: 10px;
													line-height: 30px;
													width: 100%;
												}

												.control .volume {
													width: 30px;
												}

												.control .volume a {
													width: 30px;
												}

												.control .volume .volume-slider {
													width: 30px;
													height: 120px;
													bottom: 30px;
													display: none;
													overflow: hidden;
													position: absolute;
													z-index: 10100;
													background-color: #3f3f3f;
												}

												.control .volume .drag-line {
													width: 5px;
													height: 100px;
													background: #1b1b1b;
													border-radius: 8px;
													margin: 10px auto;
													position: relative;
												}

												.control .volume .line {
													width: 5px;
													height: 100%;
													background: #c10037;
													border-radius: 8px;
													margin: 0 auto;
													position: absolute;
													bottom: 0;
												}

												.control .volume:hover {
													background-color: #3f3f3f;
												}

												.control .volume:hover .volume-slider {
													display: block;
												}

												/* ----- Fullscreen ----- */
												.videoContainer:-webkit-full-screen {
													width: 100%;
													height: 100%;
												}

												.videoContainer:-ms-fullscreen {
													width: 100%;
													height: 100%;
												}

												.videoContainer:fullscreen {
													width: 100%;
													height: 100%;
												}

												.videoContainer:-ms-fullscreen {
													width: 100%;
													height: 100%;
												}

												.videoContainer:-webkit-full-screen {
													width: 100%;
													height: 100%;
												}

												video:-webkit-full-screen::media-controls-panel,
												video:-webkit-full-screen::media-controls,
												video:-webkit-full-screen::media-text-track-container {
													display: none !important;
													opacity: 0;
												}

												video:-ms-fullscreen::media-controls-panel,
												video:-ms-fullscreen::media-controls,
												video:-ms-fullscreen::media-text-track-container {
													display: none !important;
													opacity: 0;
												}

												video:fullscreen::media-controls-panel,
												video:fullscreen::media-controls,
												video:fullscreen::media-text-track-container {
													display: none !important;
													opacity: 0;
												}

												video:-ms-fullscreen::-ms-media-controls-panel,
												video:-ms-fullscreen::-ms-media-controls,
												video:-ms-fullscreen::-ms-media-text-track-container {
													display: none !important;
													opacity: 0;
												}

												video:-moz-full-screen::-moz-media-controls-panel,
												video:-moz-full-screen::-moz-media-controls,
												video:-moz-full-screen::-moz-media-text-track-container {
													display: none !important;
													opacity: 0;
												}

												video:-o-full-screen::-o-media-controls-panel,
												video:-o-full-screen::-o-media-controls,
												video:-o-full-screen::-o-media-text-track-container {
													display: none !important;
													opacity: 0;
												}

												video:-webkit-full-screen::-webkit-media-controls-panel,
												video:-webkit-full-screen::-webkit-media-controls,
												video:-webkit-full-screen::-webkit-media-text-track-container {
													display: none !important;
													opacity: 0;
												}


												.videoContainer {
													margin: 0 auto;
													box-shadow: 0 2px 12px gray;
												}
											</style>


											<div id="videoContainer" class="videoContainer">
												<video id="video" preload="metadata"
													poster="<?= $base_url; ?>/assets/img/banner/player.jpg" playsinline>
													<source
														src="<?= $base_url; ?>/assets/video/uploads/listings/<?= $video['video_path'] ?>"
														type="video/mp4">
													<p>Your browser does not support the video tag.</p>
												</video>

												<div class="control">
													<a class="play video-play material-icons">play_arrow</a>

													<div class="progress">
														<div class="progress-bar"></div>
													</div>
													<div class="time">
														<span class="ctime">0:00</span>
														<span class="stime"> / </span>
														<span class="ttime">0:00</span>
													</div>

													<div class="volume material-icons">
														<a class="toggle-sound video-volume-high">volume_up</a>
														<div class="volume-slider">
															<div class="drag-line">
																<div class="line"></div>
															</div>
														</div>
													</div>
													<a id="picture-in-picture"
														class="video-picture-in-picture-enter material-icons">picture_in_picture</a>
													<a id="airplay" class="video-airplay material-icons">airplay</a>
													<a class="fullscreen material-icons">fullscreen</a>
												</div>
											</div>

											<script>
												document.addEventListener("DOMContentLoaded", function () {
													const videoElement = document.querySelector("#video");
													const videoContainer = document.querySelector("#videoContainer");
													const playButton = document.querySelector(".play");
													const progressBar = document.querySelector(".progress-bar");
													const progressContainer = document.querySelector(".progress");
													const fullscreenButton = document.querySelector(".fullscreen");
													const volumeToggleButton = document.querySelector(".toggle-sound");
													const volumeSlider = document.querySelector(".volume-slider");
													const volumeLine = document.querySelector(".volume-slider .line");
													const pictureInPictureButton = document.getElementById("picture-in-picture");
													const airplayButton = document.getElementById("airplay");
													let controlVisibilityTimer;
													let savedVolumeLevel;

													setVideoVolume(0.8); // Set default volume to 80%

													videoElement.addEventListener("mouseenter", () => (videoElement.dataset.isHovered = "true"));
													videoElement.addEventListener("mouseleave", () => (videoElement.dataset.isHovered = "false"));

													document.addEventListener("keyup", function (event) {
														if (event.keyCode === 32) { // event.code can also be used as event.code === "Space"
															const isFullscreen = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement;
															const isHovered = videoElement.dataset.isHovered === "true";

															if (isFullscreen || isHovered) {
																togglePlayPause();
																displayControlsForDuration(3000);
															}
														}
													});

													videoElement.addEventListener("click", togglePlayPause);
													playButton.addEventListener("click", togglePlayPause);

													function togglePlayPause() {
														if (videoElement.paused) {
															videoElement.play();
															playButton.textContent = "pause";
														} else {
															videoElement.pause();
															playButton.textContent = "play_arrow";
														}
													}

													videoContainer.addEventListener("mouseenter", showControls);
													videoContainer.addEventListener("mouseleave", hideControls);
													videoContainer.addEventListener("mousemove", () => displayControlsForDuration(3000));

													function displayControlsForDuration(milliseconds) {
														if (controlVisibilityTimer) {
															clearTimeout(controlVisibilityTimer);
															controlVisibilityTimer = 0;
														}

														showControls();
														videoContainer.style.cursor = "auto";
														controlVisibilityTimer = setTimeout(function () {
															hideControls();
															videoContainer.style.cursor = "none";
														}, milliseconds);
													}

													function showControls() {
														document.querySelector(".control").style.display = "flex";
													}

													function hideControls() {
														document.querySelector(".control").style.display = "none";
													}

													fullscreenButton.addEventListener("click", toggleFullscreen);

													function toggleFullscreen() {
														const isFullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
														const fullscreenEnabled = document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled;

														if (isFullscreen) {
															fullscreenButton.classList.replace("video-fullscreen-exit", "video-fullscreen-enter");
															exitFullscreen();
														} else if (fullscreenEnabled) {
															fullscreenButton.classList.replace("video-fullscreen-enter", "video-fullscreen-exit");
															enterFullscreen(videoContainer);
														}
													}

													function exitFullscreen() {
														if (document.exitFullscreen) {
															document.exitFullscreen();
														} else if (document.webkitExitFullscreen) {
															document.webkitExitFullscreen();
														} else if (document.mozCancelFullScreen) {
															document.mozCancelFullScreen();
														} else if (document.msExitFullscreen) {
															document.msExitFullscreen();
														}
													}

													function enterFullscreen(element) {
														if (element.requestFullscreen) {
															element.requestFullscreen();
														} else if (element.webkitRequestFullscreen) {
															element.webkitRequestFullscreen();
														} else if (element.mozRequestFullScreen) {
															element.mozRequestFullScreen();
														} else if (element.msRequestFullscreen) {
															element.msRequestFullscreen();
														}
													}

													setInterval(function () {
														document.querySelector(".ctime").innerHTML = formatTime(Math.round(videoElement.currentTime));
														document.querySelector(".ttime").innerHTML = formatTime(videoElement.duration - Math.round(videoElement.currentTime));
													}, 500);

													function formatTime(seconds) {
														const minutes = Math.floor(seconds / 60);
														const formattedMinutes = minutes >= 10 ? minutes : "0" + minutes;
														seconds = Math.floor(seconds % 60);
														const formattedSeconds = seconds >= 10 ? seconds : "0" + seconds;
														return `${formattedMinutes}:${formattedSeconds}`;
													}

													videoElement.addEventListener("timeupdate", updateProgressBar);
													progressContainer.addEventListener("mousedown", handleProgressScrub);

													function updateProgressBar() {
														const percentComplete = videoElement.currentTime / videoElement.duration;
														updateProgressBarWidth(percentComplete);
													}

													function handleProgressScrub(event) {
														const x = event.pageX - progressContainer.getBoundingClientRect().left;
														const percentComplete = x / progressContainer.offsetWidth;
														updateProgressBarWidth(percentComplete);
														updateVideoTime(percentComplete);
													}

													function updateProgressBarWidth(percentComplete) {
														progressBar.style.width = percentComplete * 100 + "%";
													}

													function updateVideoTime(percentComplete) {
														videoElement.currentTime = percentComplete * videoElement.duration;
													}

													volumeToggleButton.addEventListener("click", function () {
														if (videoElement.muted) {
															videoElement.muted = false;
															setVideoVolume(savedVolumeLevel);
														} else {
															videoElement.muted = true;
															savedVolumeLevel = videoElement.volume;
															setVideoVolume(0);
														}
													});

													volumeSlider.addEventListener("mousedown", function (event) {
														document.addEventListener("mousemove", updateVolume);
														document.addEventListener("mouseup", function () {
															document.removeEventListener("mousemove", updateVolume);
														});
														updateVolume(event); // Ensure volume is set on initial click
													});

													const updateVolume = (event) => {
														const sliderBounds = volumeSlider.getBoundingClientRect();
														const mouseYRelativeToSlider = sliderBounds.bottom - event.clientY;
														const sliderPosition = Math.min(
															Math.max(mouseYRelativeToSlider, 0),
															sliderBounds.height
														);
														const volume = sliderPosition / sliderBounds.height;

														// Check if volume is a finite number
														if (isFinite(volume)) {
															videoElement.volume = volume;
															volumeLine.style.height = `${volume * 100}%`;
															setVolumeIcon(volume);
														}
													};

													function setVolumeIcon(volume) {
														if (volume >= 0.8) {
															volumeToggleButton.className = "toggle-sound video-volume-high";
															volumeToggleButton.textContent = "volume_up";
														} else if (volume >= 0.4) {
															volumeToggleButton.className = "toggle-sound video-volume-medium";
															volumeToggleButton.textContent = "volume_down";
														} else if (volume > 0) {
															volumeToggleButton.className = "toggle-sound video-volume-low";
															volumeToggleButton.textContent = "volume_mute";
														} else {
															volumeToggleButton.className = "toggle-sound video-volume-muted";
															volumeToggleButton.textContent = "volume_off";
														}
													}

													function setVideoVolume(volume) {
														// Ensure volume is a finite number before setting
														if (isFinite(volume)) {
															videoElement.volume = volume;
															volumeLine.style.height = `${volume * 100}%`;
															setVolumeIcon(volume);
														}
													}

													if (videoElement.webkitSupportsPresentationMode && typeof videoElement.webkitSetPresentationMode === "function") {
														pictureInPictureButton.addEventListener("click", function () {
															pictureInPictureButton.classList.toggle("video-picture-in-picture-enter");
															videoElement.webkitSetPresentationMode(
																videoElement.webkitPresentationMode === "picture-in-picture" ? "inline" : "picture-in-picture"
															);
														});
													} else {
														pictureInPictureButton.style.display = "none";
													}

													if (window.WebKitPlaybackTargetAvailabilityEvent) {
														videoElement.addEventListener("webkitplaybacktargetavailabilitychanged", function (event) {
															switch (event.availability) {
																case "available":
																	airplayButton.style.display = "block";
																	break;
																default:
																	airplayButton.style.display = "none";
															}
														});
														airplayButton.addEventListener("click", function () {
															videoElement.webkitShowPlaybackTargetPicker();
														});
													} else {
														airplayButton.style.display = "none";
													}
												});

											</script>


										<?php endif; ?>

									</div>
								</div>
							</div>
						</div>
						<!--/Gallery Section-->

						<!--Ratings Section-->
						<div class="card ">
							<div class="card-header  align-items-center">
								<i class="feather-star"></i>
								<h4>Qiymətləndirmə</h4>
							</div>
							<div class="card-body">
								<div class="ratings-content">

									<?php

									$total = $listing['rating_count'] ?? 0;

									$stars = [
										5 => $listing['star_5'] ?? 0,
										4 => $listing['star_4'] ?? 0,
										3 => $listing['star_3'] ?? 0,
										2 => $listing['star_2'] ?? 0,
										1 => $listing['star_1'] ?? 0,
									];

									function percent($count, $total)
									{
										if ($total == 0)
											return 0;
										return round(($count / $total) * 100);
									}
									?>

									<div class="row">
										<div class="col-lg-3">
											<div class="ratings-info">

												<?php
												$userRating = 0;

												if (!empty($_SESSION['customer_id'])) {
													$stmt = $pdo->prepare("
        SELECT rating 
        FROM listings_reviews 
        WHERE listing_id = ? AND customer_id = ?
        LIMIT 1
    ");
													$stmt->execute([$listing['lid'], $_SESSION['customer_id']]);

													$userRating = (int) $stmt->fetchColumn();
												}
												?>

												<p class="ratings-score">
													<span><?= htmlspecialchars($avg); ?></span>/5
												</p>

												<hr>

												<p>Sizin qiymət: <strong><span
															id="userReview"><?= $userRating; ?></span></strong>
													/ 5</p>
												<!-- INTERACTIVE STARS -->
												<div class="rate-stars" data-listing="<?= (int) $listing['lid'] ?>"
													data-user-rating="<?= $userRating ?>">
													<?php for ($i = 1; $i <= 5; $i++): ?>
														<i class="fa-regular fa-star rate-star" data-value="<?= $i ?>"></i>
													<?php endfor; ?>
												</div>

												<p>Qiymətləndir</p>

												<style>
													.rate-stars i {
														font-size: 22px;
														cursor: pointer;
														color: #ccc;
														transition: 0.2s;
													}

													.rate-stars i.filled {
														color: #f4b400;
													}

													.rate-stars.loading {
														opacity: 0.6;
														pointer-events: none;
													}
												</style>
											</div>
										</div>
										<div class="col-lg-9">
											<div class="ratings-table table-responsive">
												<table class="">
													<?php foreach ([5, 4, 3, 2, 1] as $s): ?>
														<tr>
															<td class="star-ratings">
																<?php for ($i = 0; $i < $s; $i++): ?>
																	<i class="fas fa-star filled"></i>
																<?php endfor; ?>
															</td>

															<td class="scrore-width">
																<div class="progress">
																	<div class="progress-bar"
																		style="width: <?= percent($stars[$s], $total) ?>%">
																	</div>
																</div>
															</td>

															<td><?= $stars[$s] ?></td>
														</tr>
													<?php endforeach; ?>
												</table>

												<style>
													.progress {
														width: 100%;
														height: 8px;
														background: #eee;
														border-radius: 10px;
														overflow: hidden;
													}

													.progress-bar {
														height: 100%;
														background: #f4b400;
														/* star color */
														width: 0%;
														transition: width 0.3s ease;
													}
												</style>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--/Ratings Section-->



					</div>
					<div class="col-lg-3 theiaStickySidebar">
						<div class="rightsidebar">

							<div class="card">
								<h4><img src="<?= $base_url; ?>/assets/img/breifcase.svg" alt=""> Biznes info
								</h4>
								<div class="map-details">

									<ul class="info-list"> 
										<li>
											<div class="d-block text-center">
												<a
													href="<?= $base_url; ?>/market/<?= htmlspecialchars($listing['market_slug']); ?>/<?= $listing['market_id']; ?>">
													<img src="<?= $base_url; ?>/assets/img/market-logo/<?= htmlspecialchars($listing['market_logo']); ?>"
														alt="<?= htmlspecialchars($listing['market_name']); ?>"
														style="max-height: 70px; max-width: 100%;  object-fit: contain; margin-bottom: 10px;">
												</a>
												<br>
												<a class="text-decoration-underline"
													href="<?= $base_url; ?>/market/<?= htmlspecialchars($listing['market_slug']); ?>/<?= $listing['market_id']; ?>">
													<h5>
														<?= htmlspecialchars($listing['market_name']); ?> <i
															class="fa-solid fa-arrow-up-right-from-square"
															style="font-size: 13px;"></i>
													</h5>
												</a>
											</div>


										</li>

										<li><i class="feather-globe"></i>
											<?= $listing['country'] . " / " . $listing['city']; ?></li>

										<li><i class="feather-map-pin"></i> <?= $listing['address']; ?></li>


										<li><i class="feather-phone-call"></i>
											<a href="tel:<?= $listing['phone_number']; ?>">
												<?= $listing['phone_number']; ?>
											</a>
										</li>

										<li><i class="feather-mail"></i> <a
												href="mailto:<?= $listing['email']; ?>"><?= $listing['email']; ?></a>
										</li>


										<li class="socialicons pb-0">
											<?php foreach ($socials as $sm): ?>
												<a href="<?= htmlspecialchars($sm['social_url']); ?>" target="_blank"><i
														class="fab <?= htmlspecialchars($sm['social_icon']); ?>"></i>
												</a>
											<?php endforeach; ?>
										</li>
									</ul>
								</div>
							</div>


							<!-- <div class="card mb-0">
										<h4> <i class="feather-phone-call"></i> Contact Business</h4>
										<form class="contactbusinessform">
											<div class="form-set">
												<input type="text" class="form-control" placeholder="Name">
											</div>
											<div class="form-set">
												<input type="email" class="form-control" placeholder="Email Address">
											</div>
											<div class="form-set">
												<textarea rows="6" class="form-control"
													placeholder="Message"></textarea>
											</div>
											<div class="submit-section">
												<button class="btn btn-primary submit-btn" type="submit">Send
													Message</button>
											</div>
										</form>
									</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Details Main Section -->

		<?php include __DIR__ . '/inc/alert_modal.php'; ?>

		<!-- Şikayət Modalı -->
		<div class="modal fade" id="complaintModal" tabindex="-1">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">

					<div class="modal-header">
						<h5 class="modal-title">Şikayət et</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>

					<div class="modal-body">

						<form id="complaintForm">

							<input class="form-control mb-2" type="text" name="full_name" placeholder="Ad Soyad"
								required>
							<input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
							<textarea class="form-control mb-2" name="message" placeholder="Şikayət..."
								required></textarea>

							<input type="hidden" name="listing_id" value="<?= $listing['lid']; ?>">
							<input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
							<input type="text" name="website" style="display:none">

							<!-- <div class="g-recaptcha mb-2" data-sitekey="<?php //echo SITE_KEY; ?>"></div> -->

							<button class="btn btn-primary w-100" type="submit">Göndər</button>

						</form>
					</div>

				</div>
			</div>
		</div>
		<!-- Şikayət Modalı -->

		<!-- Mesaj Modalı -->
		<div class="modal fade" id="messageModal">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<form id="messageForm">
						<div class="modal-header">
							<h5 class="modal-title">Mesaj göndər</h5>
						</div>

						<div class="modal-body">
							<textarea name="message" rows="7" class="form-control"></textarea>
							<input type="hidden" name="listing_id" value="<?= $listing['lid']; ?>">

							<?php require_once __DIR__ . "/api/_csrf.php"; ?>
							<input type="hidden" name="csrf_token"
								value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
						</div>

						<div class="modal-footer">
							<button type="submit" class="btn btn-success">Göndər</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Mesaj MOdalı -->

		<!-- Footer -->
		<?php require_once __DIR__ . '/inc/footer.php'; ?>
		<!-- /Footer -->



	</div>
	<!--/Galler Slider Section-->



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

	<script src="<?= $base_url; ?>/assets/js/jquery-3.7.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="<?= $base_url; ?>/assets/js/bootstrap.bundle.min.js"></script>

	<!-- Select2 JS -->
	<script src="<?= $base_url; ?>/assets/plugins/select2/js/select2.min.js"></script>

	<!-- Aos -->
	<script src="<?= $base_url; ?>/assets/plugins/aos/aos.js"></script>

	<!-- Fearther JS -->
	<script src="<?= $base_url; ?>/assets/js/feather.min.js"></script>

	<!-- Top JS -->
	<script src="<?= $base_url; ?>/assets/js/backToTop.js"></script>

	<!-- Sticky Sidebar JS -->
	<script src="<?= $base_url; ?>/assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
	<script src="<?= $base_url; ?>/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>

	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<!-- Custom JS -->
	<script src="<?= $base_url; ?>/assets/js/script.js"></script>


	<script src="<?= $base_url; ?>/js/alert-modal.js"></script>


	<!-- Swiper JS -->
	<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

	<script>
		const swiper = new Swiper(".mySwiper", {
			slidesPerView: 3,
			spaceBetween: 10,
			loop: true,
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
			pagination: {
				el: ".swiper-pagination",
				clickable: true,
			},
			breakpoints: {
				768: { slidesPerView: 2 },
				480: { slidesPerView: 1 },
			},
		});
	</script>
	<script>
		document.querySelectorAll(".rate-stars").forEach(wrapper => {

			const stars = wrapper.querySelectorAll(".rate-star");

			let selectedRating = +wrapper.dataset.userRating || 0;
			let isLocked = selectedRating > 0; // əvvəl səs veribsə lock et

			function highlight(count) {
				stars.forEach(s => {
					const val = +s.dataset.value;

					if (val <= count) {
						s.classList.remove("fa-regular");
						s.classList.add("fa-solid");
					} else {
						s.classList.remove("fa-solid");
						s.classList.add("fa-regular");
					}
				});
			}

			// 🔥 ƏN VACİB HİSSƏ — səhifə açılan kimi işləyir
			highlight(selectedRating);

			// EVENTS
			stars.forEach(star => {

				star.addEventListener("mouseenter", () => {
					if (isLocked) return;
					highlight(star.dataset.value);
				});

				star.addEventListener("mouseleave", () => {
					if (isLocked) return;
					highlight(selectedRating);
				});

				star.addEventListener("click", async () => {

					if (isLocked) return;

					const rating = +star.dataset.value;
					const listingId = wrapper.dataset.listing;

					try {
						const res = await fetch("<?= $base_url; ?>/api/send/rate.php", {
							method: "POST",
							headers: { "Content-Type": "application/json" },
							body: JSON.stringify({
								listing_id: listingId,
								rating: rating
							})
						});

						const data = await res.json();

						if (data.status === "success") {
							selectedRating = rating;
							highlight(rating);
							isLocked = true;

							showError(data.message);

							document.querySelector(".ratings-score span").textContent = data.data.avg;
							document.getElementById("userReview").textContent = selectedRating;
						}

						else if (data.status === "already_voted") {
							showError(data.message);
							isLocked = true;
						}

						else if (data.status === "not_logged_in") {
							showError(data.message);
						}

						else {
							showError(data.message || "Xəta baş verdi");
						}

					} catch (err) {
						console.error(err);
						showError("Server xətası");
					}
				});
			});

		});
	</script>

	<script>
		document.addEventListener("click", async (e) => {
			const btn = e.target.closest(".add-favorite");
			if (!btn) return;

			e.preventDefault();



			const listingId = btn.dataset.listingId;
			const icon = btn.querySelector("i");

			try {
				const res = await fetch("<?= $base_url; ?>/api/send/favorites.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ listing_id: listingId })
				});

				const data = await res.json();

				// LOGIN YOXDUR
				if (data.status === "not_logged_in") {
					showError(data.message || "Davam etmək üçün daxil olun");

					return;
				}

				// ƏLAVƏ OLUNDU
				if (data.status === "added") {
					btn.classList.add("text-primary");

					btn.innerHTML = `
						<i class="fa-solid fa-heart"></i> <b>Seçilmişlərdədir</b>
					`;

					showError(data.message);
				}

				// SİLİNDİ
				else if (data.status === "removed") {
					btn.classList.remove("text-primary");
					btn.classList.remove("fw-bold");

					icon.classList.remove("fa-solid");
					icon.classList.add("fa-regular");

					btn.innerHTML = `
				<i class="fa-regular fa-heart"></i> Seçilmişlərdə saxla
			`;

					showError(data.message || "SEÇİLMİŞLƏRDƏN silindi");
				}

				else {
					showError(data.message || "Xəta baş verdi");
				}

			} catch (err) {
				console.error(err);
				showError("Server xətası");
			}
		});
	</script>

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

	<script>

		// submit
		document.getElementById("complaintForm").addEventListener("submit", async function (e) {
			e.preventDefault();

			// const captcha = grecaptcha.getResponse();

			// if (!captcha) {
			// 	console.log("Captcha təsdiqlə ❗");
			// 	return;
			// }

			const formData = new FormData(this);

			const res = await fetch("<?= $base_url; ?>/api/send/complaint_submit.php", {
				method: "POST",
				body: formData
			});

			const data = await res.json();

			if (data.success) {
				showError("Şikayət göndərildi ✅");

				const modal = bootstrap.Modal.getInstance(
					document.getElementById('complaintModal')
				);
				modal.hide();

				this.reset();
			} else {
				showError(data.error || "Xəta baş verdi");
			}
		});
	</script>

	<script>
		document.getElementById("messageForm").addEventListener("submit", function (e) {
			e.preventDefault();

			let formData = new FormData(this);

			for (let pair of formData.entries()) {
				console.log(pair[0], pair[1]);
			}

			fetch("<?= $base_url; ?>/api/send/message.php", {
				method: "POST",
				body: formData
			})
				.then(res => res.json())
				.then(data => {
					if (data.status === "success") {


						showError("Mesaj göndərildi");

						setTimeout(() => {
							window.location.href = "<?= $base_url; ?>/messages";
						}, 3000);

					} else {
						showError(data.message);
					}
				})
				.catch(() => {
					showError("Server xətası");
				});


		});
	</script>

</body>

</html>