<?php
require_once "inc/config.php"; require_once "inc/admin_auth.php"; require_admin_permission($pdo,'reports');
$stats=[
'customers'=>(int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn(),
'vendors'=>(int)$pdo->query("SELECT COUNT(*) FROM customers WHERE type='partner'")->fetchColumn(),
'listings'=>(int)$pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn(),
'active_listings'=>(int)$pdo->query("SELECT COUNT(*) FROM listings WHERE status='active'")->fetchColumn(),
'pending_listings'=>(int)$pdo->query("SELECT COUNT(*) FROM listings WHERE status='moderation'")->fetchColumn(),
'tenders'=>(int)$pdo->query("SELECT COUNT(*) FROM tenders")->fetchColumn(),
'complaints'=>(int)$pdo->query("SELECT COUNT(*) FROM listing_complaints WHERE status IN ('new','reviewed')")->fetchColumn(),
'active_subs'=>(int)$pdo->query("SELECT COUNT(*) FROM customer_subs WHERE status='active'")->fetchColumn(),
];
$revenue=(float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid'")->fetchColumn();
?>
<!doctype html><html lang="az"><head><?php include "inc/head.php"; ?></head><body><div class="app-main"><div id="app-wrapper" class="app-wrapper d-flex flex-column align-items-stretch min-vh-100"><?php include "inc/sidebar.php"; ?><?php include "inc/navbar.php"; ?><div class="app-content-wrapper py-13"><div class="container-fluid"><div class="page-header pb-7"><h2 class="fw-semibold fs-7">Hesabatlar</h2></div><div class="row g-4"><?php foreach(['customers'=>'Müştərilər','vendors'=>'Vendorlar','listings'=>'Elanlar','active_listings'=>'Aktiv elanlar','pending_listings'=>'Moderasiya gözləyən','tenders'=>'Tenderlər','complaints'=>'Açıq şikayətlər','active_subs'=>'Aktiv abunəliklər'] as $k=>$label):?><div class="col-md-3"><div class="pure-card card-bg shadow-custom rounded-custom"><div class="pure-card-body"><div class="text-muted"><?=$label?></div><div class="fs-7 fw-bold"><?=$stats[$k]?></div></div></div></div><?php endforeach;?><div class="col-md-3"><div class="pure-card card-bg shadow-custom rounded-custom"><div class="pure-card-body"><div class="text-muted">Ödənilmiş ödənişlər</div><div class="fs-7 fw-bold"><?=number_format($revenue,2)?> AZN</div></div></div></div></div><div class="mt-5 d-flex gap-2 flex-wrap"><a class="btn btn-outline-primary" href="api/management/export.php?type=customers">Müştərilər CSV</a><a class="btn btn-outline-primary" href="api/management/export.php?type=listings">Elanlar CSV</a><a class="btn btn-outline-primary" href="api/management/export.php?type=payments">Ödənişlər CSV</a><a class="btn btn-outline-primary" href="api/management/export.php?type=subscriptions">Abunəliklər CSV</a></div></div></div><?php include "inc/footer.php"; ?></div></div><?php include "inc/scripts_url.php"; ?></body></html>
