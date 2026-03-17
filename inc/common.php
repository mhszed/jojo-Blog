<?php
// 仅包含数据库连接函数，无session_start()！
function db() {
    $host = 'localhost';
    $dbname = 'root'; // 替换为实际数据库名
    $username = 'root'; // PhpStudy默认账号
    $password = '123456'; // 替换为实际密码
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
        
    } catch(PDOException $e) {
        die("数据库连接失败：" . $e->getMessage());
        return false;
    }
}


?>