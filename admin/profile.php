<?php
session_start();
require_once __DIR__ . '/../inc/common.php';

// 未登录强制跳转
if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

$username = $_SESSION['user'] ;
$pdo = db();
$msg = '';

// 初始读取用户当前信息
$stmt = $pdo->prepare("SELECT password,email,level FROM users WHERE username = :u LIMIT 1");
$stmt->execute([':u' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// 处理资料修改提交
try{
 if ($_SERVER['REQUEST_METHOD']==='POST'){
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    if(!empty($old_password) && !empty($new_password)){  
      if(md5($old_password) !== $user['password']){
        $msg = '密码错误';
      }else{
        if(!empty($new_password)&& strlen($new_password)>=6){
            $stmt = $pdo->prepare("UPDATE users SET password = :p WHERE username = :u ");
            $stmt->execute([':p'=>md5($new_password),':u'=>$username]);
            $msg = '<span style="color: green;">密码修改成功！重新登陆</span>';
            header('Location: ./login.php');
            exit;
           }else{
              $msg = '新密码至少6位';
           }
      }
    }else{
             $msg = '输入完整信息';
         }
 }} catch (PDOException $e){
    $msg = '更新失败' . $e->getMessage();
 }
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改个人资料</title>
    <style>
        /* 全局样式与背景图设置 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", Arial, sans-serif;
        }
        body {
            background: url("../img/770392.jpg") no-repeat center center fixed;
            background-size: cover;
            color: #333;
            line-height: 1.6;
        }

        /* 导航栏美化 */
        .nav {
            background: rgba(50, 50, 50, 0.8);
            padding: 15px 30px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        .nav .links a {
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav .links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* 容器与主体布局 */
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .hero {
            text-align: center;
            margin-bottom: 30px;
        }
        .hero h1 {
            color: #fff;
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* 面板与表单美化 */
        .panel {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .field input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .field input[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        .field input:focus {
            outline: none;
            border-color: #4a6fa5;
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.2);
        }

        /* 密码修改区域 */
        .password-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #4a6fa5;
        }
        .password-section h4 {
            margin: 0 0 15px 0;
            color: #333;
        }

        /* 按钮美化 */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #4a6fa5;
            color: #fff;
        }
        .btn-primary:hover {
            background: #3a5a85;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(74, 111, 165, 0.3);
        }

        /* 错误提示 */
        .error-msg {
            padding: 12px 15px;
            background: #f8d7da;
            color: #dc3545;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }

        /* 响应式优化 */
        @media (max-width: 768px) {
            .nav {
                padding: 12px 20px;
            }
            .container {
                margin: 20px auto;
            }
            .panel {
                padding: 20px;
            }
            .field input {
                padding: 10px 12px;
            }
            .btn {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <header class="nav">
        <div class="links">
            <a href="./index.php">返回首页</a>
        </div>
    </header>

    <main class="container">
        <div class="hero">
            <h1>个人资料</h1>
        </div>
        <div class="panel">
            <?php if ($msg) echo '<div class="error-msg">' . $msg . '</div>'; ?>

            <form method="post">
                <!-- 用户名不可修改（显示只读） -->
                <div class="field">
                    <label>用户名</label>
                    <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
                </div>
                <div class="field">
                    <label>用户等级</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['level']) ?>" readonly>
                </div>
                <div class="field">
                    <label>用户邮箱</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['email']) ?>" readonly>
                </div>
                <!-- 密码修改区域 -->
                <div class="password-section">
                    <h4>修改密码</h4>
                    <div class="field">
                        <label>旧密码</label>
                        <input type="password" name="old_password" placeholder="输入旧密码验证">
                    </div>
                    <div class="field">
                        <label>新密码</label>
                        <input type="password" name="new_password" placeholder="输入新密码">
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">保存修改</button>
            </form>
        </div>
    </main>
</body>
</html>