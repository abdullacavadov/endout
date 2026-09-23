<?php
declare(strict_types=1);
require_once("../../inc/config.php");
require_once("../../inc/admin_auth.php");
require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('Sorğu metodu yanlışdır.');
admin_verify_csrf($_POST['csrf']??null);
$id=(int)($_POST['member_id']??0);$full=trim((string)($_POST['full_name']??''));$email=trim((string)($_POST['email']??''));$phone=trim((string)($_POST['phone']??''));$role=trim((string)($_POST['role']??''));
if($id<=0||$full===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$phone===''||$role==='')exit('Məlumatları düzgün doldurun.');
$st=$pdo->prepare("SELECT id,full_name,email,phone,role,status FROM tbl_user WHERE id=? LIMIT 1");$st->execute([$id]);$old=$st->fetch(PDO::FETCH_ASSOC);if(!$old)exit('Heyət üzvü tapılmadı.');
if($id===(int)($_SESSION['user']['id']??0)&&$role!==$old['role'])exit('Öz rolunuzu dəyişə bilməzsiniz.');
if(!$old['role']||!admin_is_super($pdo)){
 if(in_array(strtolower($role),['super admin','superadmin','super_admin'],true)&&strtolower($old['role'])!=='super admin')exit('Super Admin rolu yalnız Super Admin tərəfindən verilə bilər.');
}
$st=$pdo->prepare("SELECT id FROM tbl_user WHERE email=? AND id<>? LIMIT 1");$st->execute([$email,$id]);if($st->fetch())exit('Bu email artıq istifadə olunur.');
$st=$pdo->prepare("UPDATE tbl_user SET full_name=?,email=?,phone=?,role=? WHERE id=?");$st->execute([$full,$email,$phone,$role,$id]);
admin_audit($pdo,'staff.updated','staff',$id,$old,['full_name'=>$full,'email'=>$email,'phone'=>$phone,'role'=>$role]);
echo 'success';
