<?php
session_start();
require_once __DIR__ . '/../inc/common.php';

#随机csrf
if($_SERVER['REQUEST_METHOD']==='GET'){
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;
}

$pdo = db();
$msg = '';
if($pdo){

}else{
   
  $msg ='数据库连接失败';
  exit;
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = trim($_POST['username']);
  $p = trim($_POST['password']);
  $submitted_csrf_token = $_POST['csrf_token'];
  $session_token = $_SESSION['csrf_token'];
  // 非空校验
  if (empty($u) || empty($p)) {
      $msg = '用户名和密码不能为空';
      exit;
  }

  if(empty($submitted_csrf_token) || $submitted_csrf_token !== $session_token){
      $msg = 'csrf???';
      exit;
  }
  $stmt = $pdo->prepare("SELECT *  FROM users WHERE username = :u AND password = :w LIMIT 1" );
  $stmt->execute([':u'=>$u, ':w'=>MD5($p)]);
  $res = $stmt->fetch(PDO::FETCH_ASSOC);

  if($res ){
      $_SESSION['user'] = $res['username'];
      #防止xss得到cookie
      setcookie('user', $res['username'],time()+3600,'/','',false,true);
      header('LOCATION: ./INDEX.PHP');
       unset($_SESSION['csrf_token']);
       exit;
  }else
  {
      $msg = '用户名或密码错误';
  }
}else{
 $msg = '';
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        /* 全局样式 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            /* 背景图设置 - 全屏覆盖且居中 */
            background-image: url("../img/834598.png");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', sans-serif;
            /* 半透明遮罩增强文字可读性 */
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        /* 导航栏样式 */
        .nav {
            background-color: rgba(20, 20, 40, 0.8);
            padding: 15px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            overflow: hidden
        }

        .nav .links {
            text-align: right;
            float: left;
            padding-right:40px ;
        }

        .nav .links a {
            color: #ffd700; /* 金色文字，JOJO标志性色 */
            text-decoration: none;
            font-size: 1rem;
            padding: 8px 16px;
            border: 1px solid #ffd700;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .nav .links a:hover {
            background-color: rgba(255, 215, 0, 0.2);
            color: #fff;
        }
        .nav .links2 {
            text-align: left;
        }

        .nav .links2 a {
            color: #ffd700; /* 金色文字，JOJO标志性色 */
            text-decoration: none;
            font-size: 1rem;
            padding: 8px 16px;
            border: 1px solid #ffd700;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .nav .links2 a:hover {
            background-color: rgba(255, 215, 0, 0.2);
            color: #fff;
        }
        /* 主体容器 */
        .container {
            max-width: 500px;
            margin: 80px auto;
            padding: 0 20px;
        }

        /* 标题区域 */
        .hero {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            color: #ffd700;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            letter-spacing: 1px;
        }

        /* 表单面板 */
        .panel {
            background-color: rgba(30, 30, 50, 0.9);
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }

        /* 错误信息 */
        .panel p {
            color: #ff4d4d;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ff4d4d;
            border-radius: 4px;
        }

        /* 表单字段 */
        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: #f0f0f0;
            font-weight: 500;
        }

        .field input {
            width: 100%;
            padding: 12px;
            border: 2px solid #4a4a8a;
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .field input:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
        }

        /* 提交按钮 */
        .btn.btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #ffd700;
            color: #1a1a2e;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn.btn-primary:hover {
            background-color: #fff;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.7);
            transform: translateY(-2px);
        }

        /* CSRF隐藏域不影响样式 */
        input[type="hidden"] {
            display: none;
        }
    </style>
</head>
<body>
 <header class="nav">
    <div class="links"><a href="./reg.php">注册</a></div>
      <div class="links"><a href="../text.php">返回首页</a></div>
 </header>

   
 <main class="container">
    <div class="hero"><h1>登录</h1></div>
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>用户名</label><input name="username" required /></div>
        <div class="field"><label>密码</label><input name="password" type="password" required /></div>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button class="btn btn-primary" type="submit">登录</button>
      </form>

    </div>
</main>

</body>
</html>