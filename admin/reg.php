<?php
include_once __DIR__ . '/../inc/common.php';

$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    
     $u = trim($_POST['username']);
     $p1   = trim($_POST['password']);
     $p2   = trim($_POST['password2']);
     $e   = trim($_POST['email']);   
     if(empty($u) || empty($p1) || empty($p2) || empty($e)){
            $msg = '信息输入完整';
       
     }else if($p1 !== $p2){
         $msg = '俩次输入不同';
     }else{
          try{ 
            $pdo = db();
            if($pdo)
            {
                $stmt = $pdo->prepare("select id from users where username = :u limit 1");
                $stmt->execute([':u'=>$u]);
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                if($res)
                {
                    $msg = '这里满员了，你逃不掉了';
                       
            
                }elseif(strlen($p1)<6){
                    $msg = '就这么短？？？';
                }else
                {
                   $stmt= $pdo->prepare("insert into users (username,password,email,level) values (?,?,?,0)");
                   $stmt->execute([$u,MD5($p1),$e]);
                   $res = $stmt->rowCount();
                   if ($res>0){
                   echo "<script>alert('注册成功！我不做人了，JOJO'); location.href='./login.php';</script>";
                   exit;}
                }
            }else{
                $msg = '数据库连接失败';
                exit;
            }
           }catch(PDOException $e){
                $msg = '注册失败'  . $e->getMessage();
           }  
     }   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        /* 全局样式 - 仅添加背景和风格化样式 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            /* 背景图设置：使用指定图片，全屏覆盖且居中 */
            background-image: url("../img/775940.png");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', serif; /* 衬线字体增强复古感 */
            position: relative;
        }

        /* 半透明遮罩：增强文字可读性，不影响背景质感 */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }

        /* 导航栏样式调整 */
        .nav {
            background-color: rgba(30, 30, 30, 0.9);
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #888;
        }

        .nav .filed {
            color: #ddd;
            font-size: 0.95rem;
        }

        .nav .links a {
            color: #fff;
            text-decoration: none;
            padding: 6px 12px;
            border: 1px solid #888;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .nav .links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #fff;
        }

        /* 主体容器 */
        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 0 20px;
        }

        /* 标题区域 */
        .hero {
            text-align: center;
            margin-bottom: 30px;
        }

        .hero h1 {
            color: #f0f0f0;
            font-size: 1.8rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            letter-spacing: 0.5px;
        }

        /* 表单面板 */
        .panel {
            background-color: rgba(20, 20, 20, 0.85);
            border: 1px solid #555;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        /* 错误信息 */
        .panel p {
            color: #ff9999;
            text-align: center;
            margin-bottom: 20px;
            padding: 8px;
            border: 1px solid #663333;
            border-radius: 3px;
        }

        /* 表单字段 */
        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            color: #ddd;
            font-size: 0.95rem;
        }

        .field input {
            width: 100%;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #555;
            border-radius: 3px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .field input:focus {
            outline: none;
            border-color: #bbb;
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* 提交按钮 */
        .btn.btn-primary {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: 1px solid #666;
            border-radius: 3px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn.btn-primary:hover {
            background-color: #444;
            border-color: #999;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
   <header class="nav">
    <div class="filed">已有账号？</div>
    <div class="links"><a href="./login.php">登录</a></div>
 </header>
 <main class="container">
    <div class="hero"><h1>注册</h1></div>
    <div class="panel">
      <?php if($msg) echo '<p>'.$msg.'</p>'; ?>
      <form method="post">
        <div class="field"><label>用户名</label><input name="username" required /></div>
        <div class="field"><label>密码</label><input name="password" type="password" required /></div>
        <div class="field"><label>再次输入密码</label><input name="password2" type="password" required /></div>
        <div class="field"><label>邮箱</label><input name="email" type="email" required /></div>
        <button class="btn btn-primary" type="submit">注册</button>
      </form>
    </div>
  </main>
</body>
</html>