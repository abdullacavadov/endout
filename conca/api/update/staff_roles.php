<?php
declare(strict_types=1);
require_once("../../inc/config.php");require_once("../../inc/admin_auth.php");require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('Sorğu metodu yanlışdır.');admin_verify_csrf($_POST['csrf']??null);
$id=(int)($_POST['role_id']??0);$name=trim((string)($_POST['role_name']??''));$perms=array_values(array_filter(array_map('trim',$_POST['role_permissions']??[])));
if($id<=0||$name===''||!$perms)exit('Məlumatları düzgün doldurun.');
$st=$pdo->prepare("SELECT * FROM tbl_roles WHERE role_id=? LIMIT 1");$st->execute([$id]);$old=$st->fetch(PDO::FETCH_ASSOC);if(!$old)exit('Rol tapılmadı.');
$oldSuper=in_array(strtolower($old['role_name']),['super admin','superadmin','super_admin'],true);$newSuper=in_array(strtolower($name),['super admin','superadmin','super_admin'],true);
if(($oldSuper||$newSuper)&&!admin_is_super($pdo))exit('Super Admin rolunu yalnız Super Admin dəyişə bilər.');
$valid=$pdo->query("SELECT permission FROM tbl_permissions WHERE permission_is_active='Aktiv'")->fetchAll(PDO::FETCH_COLUMN);foreach($perms as $perm)if($perm!=='*'&&!in_array($perm,$valid,true))exit('Naməlum icazə: '.$perm);
if(in_array('*',$perms,true)&&!admin_is_super($pdo))exit('Wildcard icazə verilmir.');
$encoded=implode(',',array_map(fn($x)=>"'".$x."'",$perms));$pdo->prepare("UPDATE tbl_roles SET role_name=?,role_permissions=? WHERE role_id=?")->execute([$name,$encoded,$id]);
admin_audit($pdo,'role.updated','role',$id,$old,['role_name'=>$name,'permissions'=>$perms]);echo 'success';
