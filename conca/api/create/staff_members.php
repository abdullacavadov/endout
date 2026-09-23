<?php
declare(strict_types=1);
require_once("../../inc/config.php");
require_once("../../inc/admin_auth.php");
require_admin_permission($pdo, 'staff');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Sorğu metodu yanlışdır.');
admin_verify_csrf($_POST['csrf'] ?? null);

$full_name=trim((string)($_POST['full_name']??''));$email=trim((string)($_POST['email']??''));$phone=trim((string)($_POST['phone']??''));$role=trim((string)($_POST['role']??''));$password=(string)($_POST['password']??'');
if($full_name===''||$email===''||$phone===''||$role===''||$password==='') exit('Bütün məcburi sahələri doldurun.');
if(!filter_var($email,FILTER_VALIDATE_EMAIL)) exit('Email formatı yanlışdır.');
if(strlen($password)<8) exit('Şifrə ən azı 8 simvol olmalıdır.');
if(!admin_is_super($pdo) && in_array(strtolower($role),['super admin','superadmin','super_admin'],true)) exit('Super Admin rolu yalnız Super Admin tərəfindən verilə bilər.');

try{
 $check=$pdo->prepare("SELECT id FROM tbl_user WHERE email=? LIMIT 1");$check->execute([$email]);if($check->fetch())exit('Bu email artıq istifadə olunur.');
 $check=$pdo->prepare("SELECT role_name FROM tbl_roles WHERE role_name=? AND role_is_active='Aktiv' LIMIT 1");$check->execute([$role]);if(!$check->fetch())exit('Aktiv rol seçilməyib.');
 $hash=password_hash($password,PASSWORD_DEFAULT);
 $st=$pdo->prepare("INSERT INTO tbl_user(full_name,email,phone,role,password,status) VALUES(?,?,?,?,?,'Active')");
 $st->execute([$full_name,$email,$phone,$role,$hash]);$id=(int)$pdo->lastInsertId();
 if(isset($_FILES['photo'])&&$_FILES['photo']['error']===UPLOAD_ERR_OK){
   if($_FILES['photo']['size']>2*1024*1024)exit('Şəkil maksimum 2 MB ola bilər.');
   $mime=(new finfo(FILEINFO_MIME_TYPE))->file($_FILES['photo']['tmp_name']);
   $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'][$mime]??null;if(!$ext)exit('Yalnız JPG, PNG və GIF şəkillərinə icazə verilir.');
   $name='avatar_'.bin2hex(random_bytes(8)).'.'.$ext;$dir=__DIR__.'/../../assets/img/avatar/';if(!is_dir($dir))mkdir($dir,0775,true);
   if(!move_uploaded_file($_FILES['photo']['tmp_name'],$dir.$name))exit('Fayl yüklənmədi.');
   $pdo->prepare("UPDATE tbl_user SET photo=? WHERE id=?")->execute([$name,$id]);
 }
 admin_audit($pdo,'staff.created','staff',$id,null,['full_name'=>$full_name,'email'=>$email,'role'=>$role]);
 echo 'success';
}catch(Throwable $e){error_log('staff create failed: '.$e->getMessage());echo 'Server xətası baş verdi.';}
