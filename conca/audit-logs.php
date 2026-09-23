<?php
require_once "inc/config.php";
require_once "inc/admin_auth.php";

require_admin_permission($pdo, 'audit');

$q = trim((string) ($_GET['q'] ?? ''));
$rows = [];
$dbError = null;

$where = [];
$params = [];

if ($q !== '') {
    $where[] = '(a.action LIKE ? OR a.entity_type LIKE ? OR u.full_name LIKE ?)';
    $like = "%{$q}%";
    array_push($params, $like, $like, $like);
}

$sql = "SELECT a.*, u.full_name AS admin_name
        FROM admin_audit_logs a
        LEFT JOIN tbl_user u ON u.id = a.admin_id";

if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY a.id DESC LIMIT 200";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Admin audit log page failed: ' . $e->getMessage());
    $dbError = 'Audit log cədvəli hazır deyil. Zəhmət olmasa admin migration faylını tətbiq edin.';
}
?>
<!doctype html>
<html lang="az">
<head><?php include "inc/head.php"; ?></head>
<body>
<div class="app-main">
<div id="app-wrapper" class="app-wrapper d-flex flex-column align-items-stretch min-vh-100">
<?php include "inc/sidebar.php"; ?>
<?php include "inc/navbar.php"; ?>

<div class="app-content-wrapper py-13">
<div class="container-fluid">

<div class="page-header pb-7">
    <h2 class="fw-semibold fs-7">Admin Audit Log</h2>
</div>

<form class="mb-5">
    <input class="form-control" name="q" value="<?= admin_h($q) ?>" placeholder="Əməliyyat, obyekt və ya admin">
    <button class="btn btn-primary mt-2">Axtar</button>
</form>

<?php if ($dbError): ?>
    <div class="alert alert-warning">
        <?= admin_h($dbError) ?>
        <div class="mt-2">
            <code>database/migrations/2026_09_24_admin_management.sql</code>
        </div>
    </div>
<?php else: ?>
<div class="pure-card card-bg shadow-custom rounded-custom">
<div class="pure-card-body table-responsive">
<table class="table">
<thead>
<tr>
    <th>Tarix</th>
    <th>Admin</th>
    <th>Əməliyyat</th>
    <th>Obyekt</th>
    <th>IP</th>
    <th>Dəyişiklik</th>
</tr>
</thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr>
    <td><?= admin_h($r['created_at']) ?></td>
    <td><?= admin_h($r['admin_name'] ?? '#' . $r['admin_id']) ?></td>
    <td><code><?= admin_h($r['action']) ?></code></td>
    <td>
        <?= admin_h($r['entity_type'] ?? '-') ?>
        <?= !empty($r['entity_id']) ? '#' . (int) $r['entity_id'] : '' ?>
    </td>
    <td><?= admin_h($r['ip_address'] ?? '-') ?></td>
    <td>
        <details>
            <summary>Göstər</summary>
            <pre><?= admin_h(json_encode([
                'old' => json_decode($r['old_values'] ?? 'null', true),
                'new' => json_decode($r['new_values'] ?? 'null', true),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
        </details>
    </td>
</tr>
<?php endforeach; ?>

<?php if (!$rows): ?>
<tr>
    <td colspan="6" class="text-center py-5">Audit qeydi yoxdur.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<?php endif; ?>

</div>
</div>

<?php include "inc/footer.php"; ?>
</div>
</div>

<?php include "inc/scripts_url.php"; ?>
</body>
</html>
