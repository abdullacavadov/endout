<?php
declare(strict_types=1);
require_once '../../inc/config.php';require_once '../../inc/admin_auth.php';require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('POST tələb olunur.');admin_verify_csrf($_POST['csrf']??null);
$id=(int)($_POST['pid']??0);$st=$pdo->prepare("SELECT * FROM tbl_permissions WHERE id=? LIMIT 1");$st->execute([$id]);$old=$st->fetch(PDO::FETCH_ASSOC);if(!$old)exit('İcazə tapılmadı.');
$st=$pdo->prepare("SELECT COUNT(*) FROM tbl_roles WHERE role_permissions LIKE ?");$st->execute(['%\''.$old['permission'].'\'%']);if((int)$st->fetchColumn()>0)exit('Bu icazə aktiv rollarda istifadə olunur.');
$pdo->prepare("UPDATE tbl_permissions SET permission_is_active='Deaktiv' WHERE id=?")->execute([$id]);admin_audit($pdo,'permission.deactivated','permission',$id,$old,['permission_is_active'=>'Deaktiv']);header("Location: ../../staff-permissions.php");exit;
