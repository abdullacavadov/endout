<?php
declare(strict_types=1);
require_once '../../inc/config.php';require_once '../../inc/admin_auth.php';require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('POST tələb olunur.');admin_verify_csrf($_POST['csrf']??null);
$id=(int)($_POST['rid']??0);$st=$pdo->prepare("SELECT * FROM tbl_roles WHERE role_id=? LIMIT 1");$st->execute([$id]);$old=$st->fetch(PDO::FETCH_ASSOC);if(!$old)exit('Rol tapılmadı.');
if(in_array(strtolower($old['role_name']),['super admin','superadmin','super_admin'],true))exit('Super Admin rolu silinə bilməz.');
$st=$pdo->prepare("SELECT COUNT(*) FROM tbl_user WHERE role=? AND status='Active'");$st->execute([$old['role_name']]);if((int)$st->fetchColumn()>0)exit('Bu rol aktiv heyət üzvlərində istifadə olunur. Əvvəlcə rolları dəyişin.');
$pdo->prepare("UPDATE tbl_roles SET role_is_active='Deaktiv' WHERE role_id=?")->execute([$id]);admin_audit($pdo,'role.deactivated','role',$id,$old,['role_is_active'=>'Deaktiv']);header("Location: ../../staff-roles.php");exit;
