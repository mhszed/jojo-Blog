<?php
session_start();
// 1. 补充安全响应头（避免样式失效+防护XSS）
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
include_once __DIR__ . '/../inc/common.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

$username = $_SESSION['user'];
$pdo = db();
$msg = '';
$msgType = '';

#处理提交
#使用俩个判断是担心其他的post误触
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['published'])) {
    $blogtitle = trim($_POST['title']);
    $blogcontent = trim($_POST['content']);
// 正则替换：去除首行的所有空格/制表符
$blogcontent = preg_replace('/^[\s\t]+/', '', $blogcontent);
    if (empty($blogtitle) || empty($blogcontent)) {
        $msg = '输入不完整哦';
        $msgType = 'error';
    } elseif (strlen($blogtitle) > 200) {
        $msg = '标题长度不能超过200字！';
        $msgType = 'error';
    } elseif (strlen($blogcontent) > 5000) {
        $msg = '内容长度不能超过5000字！';
        $msgType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO blog (blogtitle,blogcontent,uid) VALUES (?,?,?)");
            $stmt->execute([$blogtitle, $blogcontent, $username]);
             echo "<script>alert('发布成功，嘞呀嘞daze'); location.href='./index.php';</script>";
            exit;
        } catch (PDOException $e) {
            $msg = '失败了' . $e->getMessage();
            $msgType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发表博客</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            /* 背景图优化：暗化遮罩提升前景可读性 */
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.4)),
                        url("../img/770392.jpg") no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 20px;
            position: relative;
            min-height: 100vh;
        }

        /* 顶部导航栏美化 */
        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background: linear-gradient(135deg, #2980b9, #1e6091);
            border-radius: 12px;
            margin-bottom: 30px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .top .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 1.05rem;
        }

        .top .user-info::before {
            content: '\f007';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #ffd700;
        }

        .top a {
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 1rem;
        }

        .top a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .top a::before {
            content: '\f015';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
        }

        /* 发表博客容器美化 */
        .publish {
            max-width: 900px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            padding: 35px;
            position: relative;
            overflow: hidden;
        }

        .publish::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2980b9, #ffd700);
        }

        /* 表单标题 */
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #1e6091;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .form-header h2::before,
        .form-header h2::after {
            content: '\f1ea';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #ffd700;
        }

        /* 表单样式优化 */
        .form-group {
            margin-bottom: 28px;
        }

        .title, .content {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-weight: 500;
            color: #1e6091;
            font-size: 1.1rem;
        }

        .title::before {
            content: '\f02b';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #2980b9;
        }

        .content::before {
            content: '\f15c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #2980b9;
        }

        .intitle {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 1.05rem;
            color: #333;
        }

        .intitle:focus {
            outline: none;
            border-color: #2980b9;
            box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.15);
        }

        .incontent {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 1.05rem;
            line-height: 1.8;
            resize: vertical;
            min-height: 300px;
            color: #333;
            font-family: inherit;
        }

        .incontent:focus {
            outline: none;
            border-color: #2980b9;
            box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.15);
        }

        .form-hint {
            font-size: 0.85rem;
            color: #636e72;
            margin-left: 2px;
        }

        /* 按钮美化 */
        .btn {
            background: linear-gradient(135deg, #2980b9, #1e6091);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(41, 128, 185, 0.15);
        }

        .btn::before {
            content: '\f091';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1e6091, #154360);
            box-shadow: 0 6px 16px rgba(41, 128, 185, 0.25);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 3px 8px rgba(41, 128, 185, 0.2);
        }

        /* 提示信息样式优化 */
        .msg {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .msg::before {
            content: '';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
        }

        .msg.success {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
            border: 1px solid #2ecc71;
        }

        .msg.success::before {
            content: '\f058';
            color: #27ae60;
        }

        .msg.error {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid #ec7063;
        }

        .msg.error::before {
            content: '\f057';
            color: #e74c3c;
        }

        /* 响应式优化 */
        @media (max-width: 768px) {
            .publish {
                padding: 25px 20px;
            }

            .top {
                padding: 12px 15px;
                gap: 15px;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }

            .intitle, .incontent {
                padding: 12px 15px;
            }

            .btn {
                padding: 12px 25px;
                font-size: 1rem;
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .top {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .incontent {
                min-height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="publish">
        <div class="top">
            <div class="user-info"><?php echo htmlspecialchars($username); ?></div>
            <a href="./index.php">首页</a>
        </div>

        <div class="form-header">
            <h2>发表新博文</h2>
        </div>

        <form action="" method="post">
            <?php if ($msg) : ?>
                <div class="msg <?php echo $msgType; ?>"><?php echo $msg; ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label class="title">标题</label>
                <input type="text" name="title" class="intitle" placeholder="请输入博文标题">
                <div class="form-hint">提示：标题长度不超过200字</div>
            </div>

            <div class="form-group">
                <label class="content">内容</label>
                <textarea class="incontent" name="content" placeholder="请输入博文内容"></textarea>
                <div class="form-hint">提示：内容长度不超过5000字，支持换行</div>
            </div>

            <input type="submit" value="发布" class="btn" name="published">
        </form>
    </div>
</body>
</html>