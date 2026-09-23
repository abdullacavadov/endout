<?php
declare(strict_types=1);
require_once '../../inc/config.php';require_once '../../inc/admin_auth.php';require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('POST tələb olunur.');admin_verify_csrf($_POST['csrf']??null);
$id=(int)($_POST['mid']??0);if($id<=0)exit('Yanlış ID.');if($id===(int)($_SESSION['user']['id']??0))exit('Öz hesabınızı silə bilməzsiniz.');
$st=$pdo->prepare("SELECT id,full_name,email,role,status FROM tbl_user WHERE id=? LIMIT 1");$st->execute([$id]);$old=$st->fetch(PDO::FETCH_ASSOC);if(!$old)exit('Heyət üzvü tapılmadı.');
if(in_array(strtolower($old['role']),['super admin','superadmin','super_admin'],true)&&!admin_is_super($pdo))exit('Super Admin hesabını dəyişmək üçün Super Admin icazəsi lazımdır.');
$pdo->prepare("UPDATE tbl_user SET status='Deactive' WHERE id=?")->execute([$id]);
admin_audit($pdo,'staff.suspended','staff',$id,$old,['status'=>'Deactive']);
header("Location: ../../staff-members.php");exit;
