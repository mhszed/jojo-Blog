<?php
require_once __DIR__ . '/inc/common.php';
session_start();
$pdo = db();
$messages = [];
$error = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
   $n= trim($_POST['nickname']);
   $c= trim($_POST['content']);
   $e= trim($_POST['contact']);

if(!empty($n) &&!empty($c)&&!empty($e)){
    try{
        $stmt=$pdo->prepare("insert into message (nickname, content, contact, create_time) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$n,$c,$e]);
        $error='留言成功';
    }catch (PDOException $e) {
            $error = '留言失败：' . $e->getMessage();
}
}else{
    $error='输入完啊';

}
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOJO 奇妙留言板</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            background-color: #1a1a1a;
            color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 215, 0, 0.1) 0%, transparent 15%),
                radial-gradient(circle at 80% 70%, rgba(140, 0, 0, 0.1) 0%, transparent 15%);
            padding: 20px;
        }

        /* 导航栏：JOJO红色调 */
        .nav {
            background: linear-gradient(135deg, #c02929 0%, #8c0000 100%);
            padding: 16px 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(200, 0, 0, 0.4);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: #ffd700;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 0.95rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            border: 1px solid transparent;
        }

        .nav-link:hover {
            background-color: rgba(255, 215, 0, 0.2);
            border-color: #ffd700;
            color: #fff;
        }

        /* 页面标题 */
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #2d0b0b;
            border: 2px solid #ffd700;
            border-radius: 8px;
            max-width: 800px;
            margin: 0 auto 40px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
        }

        .page-title h1 {
            color: #ffd700;
            font-size: 2rem;
            text-shadow: 2px 2px 0 #000;
            letter-spacing: 1px;
        }

        /* 主容器 */
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* 留言表单卡片 */
        .message-form-card {
            background-color: #2c0e0e;
            border: 2px solid #9b2226;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            padding: 30px;
        }

        .form-title {
            color: #ffd700;
            font-size: 1.5rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-shadow: 2px 2px 0 #000;
        }

        .form-title::before {
            content: '\f1ea';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #e63946;
            transform: rotate(-5deg);
        }

        /* 提示信息 */
        .alert-error {
            padding: 12px 15px;
            background-color: rgba(230, 57, 70, 0.2);
            border: 1px solid #e63946;
            border-radius: 4px;
            color: #ff9999;
            margin-bottom: 20px;
            text-align: center;
        }

        /* 表单组 */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #f0c0c0;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid #5c1a1a;
            border-radius: 6px;
            color: #fff;
            font-size: 1rem;
            resize: vertical;
        }

        .form-control:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
        }

        textarea.form-control {
            min-height: 150px;
            line-height: 1.8;
        }

        .form-hint {
            font-size: 0.85rem;
            color: #999;
            margin-top: 5px;
            display: block;
        }

        /* 提交按钮 */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #ffd700;
            color: #8c0000 !important;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-shadow: none;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: #fff;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.7);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- 导航栏（包含返回主页按钮） -->
    <header class="nav">
        <div class="nav-container">
            <a href="./index.php" class="nav-link">返回主页</a>
            <a href="./test2.php" class="nav-link">其他人的留言</a>
        </div>
    </header>

    <!-- 页面标题 -->
    <div class="page-title">
        <h1>JOJO 奇妙留言板</h1>
    </div>

    <!-- 主容器：表单 + 留言列表 -->
    <div class="container">
        <!-- 留言表单 -->
        <div class="message-form-card">
            <h2 class="form-title"><i class="fa-solid fa-pen-fancy"></i> 留下你的感悟</h2>
            <?php if ($error) : ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nickname">昵称 <span style="color: #e63946;">*</span></label>
                    <input type="text" id="nickname" name="nickname" class="form-control" placeholder="请输入你的昵称（必填）"  >
                </div>

                <div class="form-group">
                    <label for="content">留言内容 <span style="color: #e63946;">*</span></label>
                    <textarea id="content" name="content" class="form-control" placeholder="分享你对博客的看法..."></textarea>
                    <div class="form-hint">提示：内容支持换行，请勿发表违规言论</div>
                </div>

                <div class="form-group">
                    <label for="contact">联系方式<span style="color: #e63946;">*</span></label>
                    <input type="text" id="contact" name="contact" class="form-control" placeholder="如邮箱、QQ（选填，方便他人联系你）" ">
                </div>

                <button type="submit" class="btn-submit">提交留言</button>
            </form>
        </div>
    </div>
</body>
</html>
