<?php
declare(strict_types=1);
require_once("../../inc/config.php");require_once("../../inc/admin_auth.php");require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('Sorğu metodu yanlışdır.');admin_verify_csrf($_POST['csrf']??null);
$name=trim((string)($_POST['permissions_name']??''));$permission=trim((string)($_POST['permission']??''));if($name===''||$permission==='')exit('Bütün sahələri doldurun.');if(!preg_match('/^[a-z][a-z0-9_.-]{1,80}$/',$permission))exit('Permission formatı yanlışdır.');
$st=$pdo->prepare("SELECT id FROM tbl_permissions WHERE permission=? LIMIT 1");$st->execute([$permission]);if($st->fetch())exit('Bu icazə artıq mövcuddur.');
$st=$pdo->prepare("INSERT INTO tbl_permissions(name,permission) VALUES(?,?)");$st->execute([$name,$permission]);$id=(int)$pdo->lastInsertId();admin_audit($pdo,'permission.created','permission',$id,null,['name'=>$name,'permission'=>$permission]);echo 'success';
