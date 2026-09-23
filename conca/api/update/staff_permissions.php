<?php
declare(strict_types=1);
require_once("../../inc/config.php");require_once("../../inc/admin_auth.php");require_admin_permission($pdo,'staff');
if($_SERVER['REQUEST_METHOD']!=='POST')exit('Sorğu metodu yanlışdır.');admin_verify_csrf($_POST['csrf']??null);
$id=(int)($_POST['pid']??0);$permission=trim((string)($_POST['permission']??''));$name=trim((string)($_POST['name']??''));if($id<=0||$name===''||!preg_match('/^[a-z][a-z0-9_.-]{1,80}$/',$permission))exit('Məlumatları düzgün doldurun.');
$st=$pdo->prepare("SELECT * FROM tbl_permissions WHERE id=? LIMIT 1");$st->execute([$id]);$old=$st->fetch(PDO::FETCH_ASSOC);if(!$old)exit('İcazə tapılmadı.');
$st=$pdo->prepare("UPDATE tbl_permissions SET permission=?,name=? WHERE id=?");$st->execute([$permission,$name,$id]);admin_audit($pdo,'permission.updated','permission',$id,$old,['permission'=>$permission,'name'=>$name]);echo 'success';
