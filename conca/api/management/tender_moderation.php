<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/admin_auth.php';
require_admin_permission($pdo, 'moderation');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
admin_verify_csrf($_POST['csrf'] ?? null);

$id = (int) ($_POST['id'] ?? 0);
$status = (string) ($_POST['status'] ?? '');
$reason = trim((string) ($_POST['reason'] ?? ''));

if ($id <= 0 || !in_array($status, ['active','pending','cancelled'], true)) {
    http_response_code(422); exit('Yanlış tender statusu.');
}
if ($status === 'cancelled' && $reason === '') {
    http_response_code(422); exit('Ləğv səbəbi tələb olunur.');
}

$stmt = $pdo->prepare("SELECT id, status FROM tenders WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$old = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$old) { http_response_code(404); exit('Tender tapılmadı.'); }

$stmt = $pdo->prepare("UPDATE tenders SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);
admin_audit($pdo, 'tender.moderated', 'tender', $id, $old, ['status'=>$status,'reason'=>$reason]);

header('Location: ../tenders.php?updated=1');
exit;
