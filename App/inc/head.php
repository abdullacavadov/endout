<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

<?php
function buildMeta($type, $data = [])
{

	$meta = [
		'title' => 'Endout',
		'description' => 'Default description',
		'image' => BASE_URL . '/assets/img/default.jpg',
		'url' => BASE_URL,
		'type' => 'website'
	];

	switch ($type) {

		case 'listing':
			$meta['title'] = $data['title'] ?? 'Elan';
			$meta['description'] = $data['description'] ?? '';
			$meta['url'] = BASE_URL . '/listing/' . $data['slug'] . '/' . $data['id'];;
			$meta['type'] = 'article';
			break;

		// case 'listings':
		// 	$meta['title'] = $data['title'];
		// 	$meta['description'] = 'Saytdakı bütün elanları kəşf et';
		// 	$meta['url'] = BASE_URL . '/listings?main=' . $data['main'] . '&mid=' . $data['mid'] . '/&sub=' . $data['sub'];
		// 	break;

		// case 'home':
		// 	$meta['title'] = 'Ana səhifə';
		// 	$meta['description'] = 'Ən son elanlar və xidmətlər';
		// 	break;
	}

	return $meta;
}

$meta = buildMeta($pageType ?? 'home', $metaData ?? []);

?>

<title><?= htmlspecialchars($meta['title']) ?></title>

<meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= htmlspecialchars($meta['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta['description']) ?>">
<meta property="og:image" content="<?= $meta['image'] ?>">
<meta property="og:url" content="<?= $meta['url'] ?>">
<meta property="og:type" content="<?= $meta['type'] ?>">

<!-- Favicon -->
<link rel="shortcut icon" href="<?= $base_url ?>/assets/img/favicon.png">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/bootstrap.min.css">

<!-- Fontawesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
	integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
	crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Select2 CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/plugins/select2/css/select2.min.css">

<!-- Aos CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/plugins/aos/aos.css">

<!-- Datetimepicker CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/bootstrap-datetimepicker.min.css">

<!-- Fearther CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/feather.css">

<!-- Owl carousel CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/owl.carousel.min.css">

<!-- Main CSS -->
<link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">

<!-- init-tel -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css" />


<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">


<style>
	.sticky-banner {
		position: sticky;
		top: 129px;
		height: calc(100vh - 129px);
		background-size: contain;
		background-position: center;
		background-repeat: no-repeat;
	}
</style>