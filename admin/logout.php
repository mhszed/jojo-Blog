<?php
// 启动会话（必须在最顶部，无前置输出）
session_start();


if(isset($SESSION['user'])){
    unset($_SESSION['user']);
}
session_unset();
session_destroy();

setcookie('user', $res['username'],time()-3600,'/','',false,true);



header('Location: ./login.php'); 
exit;
?>