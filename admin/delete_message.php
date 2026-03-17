<?php
session_start();
// 确保编码统一，避免隐藏字符导致解析错误
header('Content-Type: text/html; charset=utf-8');
include_once __DIR__ . '/../inc/common.php';

if(!isset($_SESSION['user'])|| $_SESSION['user']!='admin')
{
    header('location: ./login.php');
}
$msg='';
$pdo=db();
if(isset($_POST['message_id'])){
    $a=$_POST['message_id'];
    $checkStmt = $pdo->prepare('DELETE  from message WHERE id = ? ');
    $checkStmt->execute([$a]);


    try{
    $stmt = $pdo->prepare('DELETE FROM users WHERE username = ? limit 1');
    $stmt->execute([$a]);
    #urlencode($msg) 对中文提示信息进行 URL 编码（避免中文在 URL 中出现乱码），再通过 URL 参数 ?msg= 传递给首页
    $msg="成功删除";
     // 修正跳转URL格式：用?msg=传递参数，避免乱码
    header("Location:./message.php?msg=" .urlencode($msg));
    exit;
    }catch(PDOException $e){
    $msg= '??' .$e->getMessage();
    }
}else{
$msg='无参数';
}
header('location:./delete_message.php?msg='.urlencode($msg));
exit;
?>