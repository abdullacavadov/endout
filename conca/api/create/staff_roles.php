<?php
declare(strict_types=1);
require_once("../../inc/config.php");require_once("../../inc/admin_auth.php");require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('Sorğu metodu yanlışdır.');admin_verify_csrf($_POST['csrf']??null);
$name=trim((string)($_POST['role_name']??''));$perms=array_values(array_filter(array_map('trim',$_POST['role_permissions']??[])));
if($name===''||!$perms)exit('Rol adı və ən azı bir icazə seçilməlidir.');
if(in_array(strtolower($name),['super admin','superadmin','super_admin'],true)&&!admin_is_super($pdo))exit('Super Admin rolu yalnız Super Admin yarada bilər.');
$valid=[];$stmt=$pdo->query("SELECT permission FROM tbl_permissions WHERE permission_is_active='Aktiv'");foreach($stmt->fetchAll(PDO::FETCH_COLUMN) as $x)$valid[(string)$x]=true;
foreach($perms as $perm){if($perm==='*'&&!admin_is_super($pdo))exit('Wildcard icazə verilmir.');if($perm!=='*'&&!isset($valid[$perm]))exit('Naməlum icazə: '.$perm);}
$encoded=implode(',',array_map(fn($x)=>"'".$x."'",$perms));
try{$st=$pdo->prepare("INSERT INTO tbl_roles(role_name,role_permissions) VALUES(?,?)");$st->execute([$name,$encoded]);$id=(int)$pdo->lastInsertId();admin_audit($pdo,'role.created','role',$id,null,['role_name'=>$name,'permissions'=>$perms]);echo 'success';}catch(Throwable $e){error_log('role create failed: '.$e->getMessage());echo 'Server xətası baş verdi.';}
