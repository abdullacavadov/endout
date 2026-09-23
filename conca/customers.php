<?php
require_once "inc/config.php";
require_once "inc/admin_auth.php";
require_admin_permission($pdo, 'customers');

$q = trim((string)($_GET['q'] ?? ''));
$status = (string)($_GET['status'] ?? '');
$type = (string)($_GET['type'] ?? '');
$where=[]; $params=[];
if ($q !== '') { $where[]="(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR CAST(id AS CHAR)=?)"; $like="%$q%"; array_push($params,$like,$like,$like,$q); }
if (in_array($status,['pending','active','blocked'],true)) { $where[]="status=?"; $params[]=$status; }
if (in_array($type,['partner','simple'],true)) { $where[]="type=?"; $params[]=$type; }
$sql="SELECT id,full_name,email,phone,type,status,phone_verified,email_verified,created_at FROM customers";
if($where) $sql.=" WHERE ".implode(" AND ",$where);
$sql.=" ORDER BY id DESC LIMIT 100";
$stmt=$pdo->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
$csrf=admin_csrf_token();
?>
<!doctype html><html lang="az"><head><?php include "inc/head.php"; ?></head><body><div class="app-main"><div id="app-wrapper" class="app-wrapper d-flex flex-column align-items-stretch min-vh-100"><?php include "inc/sidebar.php"; ?><?php include "inc/navbar.php"; ?><div class="app-content-wrapper py-13"><div class="container-fluid"><div class="page-header pb-7 d-flex justify-content-between align-items-center"><div><h2 class="fw-semibold fs-7">Müştərilər və Vendorlar</h2><div class="text-muted">İstifadəçi statusu, tipi və hesab təhlükəsizliyi</div></div><a class="btn btn-outline-primary" href="api/management/export.php?type=customers">CSV export</a></div>
<form class="row g-3 mb-5"><div class="col-md-4"><input class="form-control" name="q" value="<?=admin_h($q)?>" placeholder="Ad, email, telefon və ya ID"></div><div class="col-md-3"><select class="form-control" name="type"><option value="">Bütün tiplər</option><option value="partner" <?=$type==='partner'?'selected':''?>>Vendor</option><option value="simple" <?=$type==='simple'?'selected':''?>>Müştəri</option></select></div><div class="col-md-3"><select class="form-control" name="status"><option value="">Bütün statuslar</option><option value="active" <?=$status==='active'?'selected':''?>>Aktiv</option><option value="blocked" <?=$status==='blocked'?'selected':''?>>Blok</option><option value="pending" <?=$status==='pending'?'selected':''?>>Gözləyir</option></select></div><div class="col-md-2"><button class="btn btn-primary w-100">Axtar</button></div></form>
<div class="pure-card rounded-custom card-bg shadow-custom"><div class="pure-card-body table-responsive"><table class="table align-middle"><thead><tr><th>ID</th><th>Ad</th><th>Əlaqə</th><th>Tip</th><th>Status</th><th>Doğrulama</th><th>Əməliyyat</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td>#<?=$r['id']?></td><td><a href="customer-view.php?id=<?=$r['id']?>"><?=admin_h($r['full_name'])?></a></td><td><?=admin_h($r['email'])?><br><?=admin_h($r['phone'])?></td><td><?= $r['type']==='partner'?'Vendor':'Müştəri' ?></td><td><span class="badge <?=$r['status']==='active'?'bg-success':($r['status']==='blocked'?'bg-danger':'bg-warning')?>"><?=admin_h($r['status'])?></span></td><td><?=((int)$r['phone_verified']?'Tel ✓':'Tel —')?> / <?=((int)$r['email_verified']?'Email ✓':'Email —')?></td><td><form method="post" action="api/management/customer_action.php" class="d-flex gap-2"><input type="hidden" name="csrf" value="<?=$csrf?>"><input type="hidden" name="id" value="<?=$r['id']?>"><select name="status" class="form-control form-control-sm"><option value="active" <?=$r['status']==='active'?'selected':''?>>Aktiv</option><option value="blocked" <?=$r['status']==='blocked'?'selected':''?>>Blok</option><option value="pending" <?=$r['status']==='pending'?'selected':''?>>Gözləyir</option></select><button class="btn btn-sm btn-primary">Tətbiq et</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div></div><?php include "inc/footer.php"; ?></div></div><?php include "inc/scripts_url.php"; ?></body></html>
