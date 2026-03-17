<?php
session_start();
// 确保编码统一，避免隐藏字符导致解析错误
header('Content-Type: text/html; charset=utf-8');
include_once __DIR__ . '/../inc/common.php';

if(!isset($_SESSION['user']))
{
    header('location: ./login.php');
}
$msg='';
$pdo=db();
if(isset($_GET['id'])){
    $postid=(int)$_GET['id'];
#判断请求的id是否为session的uid,仅仅博客作者可删除
    $checkStmt = $pdo->prepare('SELECT uid from blog WHERE id = ? LIMIT 1');
    $checkStmt->execute([$postid]);
    $checkfetch = $checkStmt->fetch(PDO::FETCH_ASSOC);
    if($checkfetch['uid'] !=$_SESSION['user'] ){
          $msg = "无权限删除他人文章";
    header("Location: ./index.php?msg=" . urlencode($msg));
    exit;
    }
    try{
    $stmt = $pdo->prepare('DELETE FROM blog WHERE id = ?');
    $stmt->execute([$postid]);
    #urlencode($msg) 对中文提示信息进行 URL 编码（避免中文在 URL 中出现乱码），再通过 URL 参数 ?msg= 传递给首页
    $msg="文章成功删除";
     // 修正跳转URL格式：用?msg=传递参数，避免乱码
    header("Location:./index.php?msg=" .urlencode($msg));
    exit;
    }catch(PDOException $e){
    $msg= '??' .$e->getMessage();
    }
}else{
$msg='无参数';
}
header('location:./index.php?msg='.urlencode($msg));
exit;
?>