<?php
session_start();
require_once __DIR__ . '/inc/common.php';

$pdo = db();
$messages = [];
$error = '';
$n = 10;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = isset($_POST['n']) ? (int)$_POST['n'] : 10;
    
    if ($n == 4) {
        echo "<script>alert('米斯达:?????');location.href='/4444.php';</script>";
        exit;
    }
    
    if ($n <= 0 || $n > 100) {
        $error = '请输入1-100之间的数字！';
    }
}   

if(1){
    try{
        $stmt=$pdo->prepare("SELECT id, nickname, content, contact, create_time FROM message ORDER BY create_time DESC LIMIT ?");
 #这样预处理第一个参数1表示 SQL 中第 1 个?，PDO::PARAM_INT强制参数为整数类型，确保生成的 SQL 是LIMIT 10（无引号）。   
        $stmt->bindParam(1, $n, PDO::PARAM_INT); 
    $stmt->execute(); // 这里不再传递参数，而是通过bindParam绑定
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }catch (PDOException $e) {
        $error = '留言加载失败：' . $e->getMessage();
    }
}else{
    $error='？？？';
}
?>

<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOJO主题留言板 - 分享你的奇妙感悟</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 基础样式 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial Black', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #0d0d0d; /* 深黑背景 */
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(180, 0, 0, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 215, 0, 0.05) 0%, transparent 30%);
            color: #f0f0f0;
            line-height: 1.6;
            padding: 20px;
        }

        /* 导航栏 */
        .nav {
            background: linear-gradient(135deg, #b40000 0%, #8c0000 100%);
            border: 2px solid #ffd700; /* 金色边框 */
            border-radius: 8px;
            padding: 12px 30px;
            margin: 0 auto 30px;
            max-width: 1200px;
            box-shadow: 0 4px 0 #660000, 0 0 15px rgba(255, 215, 0, 0.3);
        }

        .nav-container {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            color: #ffd700;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-shadow: 2px 2px 0 #000;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 215, 0, 0.1);
            border-color: #ffd700;
            transform: translateY(-2px);
        }

        /* 页面标题 */
        .page-title {
            text-align: center;
            margin: 0 auto 40px;
            max-width: 800px;
            padding: 20px;
            background-color: #1a0000;
            border: 3px solid #ffd700;
            border-radius: 8px;
            box-shadow: 0 6px 0 #660000, 0 0 20px rgba(255, 215, 0, 0.2);
        }

        .page-title h1 {
            color: #ffd700;
            font-size: 2.5rem;
            text-shadow: 3px 3px 0 #b40000, 5px 5px 0 #000;
            letter-spacing: 2px;
            transform: skew(-5deg); /* 倾斜效果，JOJO标志性排版 */
        }

        /* 主容器 */
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* 表单样式 */
        form {
            background-color: #1a0000;
            border: 2px solid #b40000;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 4px 0 #660000;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            color: #ffd700;
            margin-bottom: 8px;
            font-size: 1.1rem;
            text-shadow: 1px 1px 0 #000;
        }

        .field input {
            width: 100%;
            padding: 12px;
            background-color: #0d0d0d;
            border: 2px solid #b40000;
            border-radius: 4px;
            color: #f0f0f0;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .field input:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(180deg, #ffd700 0%, #e6c200 100%);
            color: #8c0000;
            border: 2px solid #b40000;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 0 #660000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 0 #660000;
            background: linear-gradient(180deg, #fff380 0%, #ffd700 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 0 #660000;
        }

        /* 错误提示 */
        .error {
            background-color: rgba(180, 0, 0, 0.2);
            border: 2px solid #b40000;
            border-radius: 4px;
            color: #ff9999;
            padding: 12px;
            margin: 0 0 20px;
            text-align: center;
            font-weight: bold;
        }

        /* 留言列表 */
        .message-list-card {
            background-color: #1a0000;
            border: 2px solid #b40000;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 0 #660000;
        }

        .list-title {
            color: #ffd700;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #b40000;
            text-shadow: 2px 2px 0 #000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .list-title i {
            color: #b40000;
        }

        /* 留言项 */
        .message-item {
            background-color: #0d0d0d;
            border: 1px solid #b40000;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .message-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #b40000 0%, #ffd700 100%);
        }

        .message-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
            border-color: #ffd700;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .message-nickname {
            color: #ffd700;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-nickname i {
            color: #b40000;
        }

        .message-time {
            color: #999;
            font-size: 0.9rem;
        }

        .message-content {
            color: #f0f0f0;
            margin-bottom: 15px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            line-height: 1.8;
        }

        .message-contact {
            color: #ccc;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-contact i {
            color: #b40000;
        }

        /* 无留言状态 */
        .no-messages {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .no-messages i {
            font-size: 3rem;
            color: #b40000;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* 响应式调整 */
        @media (max-width: 600px) {
            .page-title h1 {
                font-size: 1.8rem;
            }
            .message-header {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <header class="nav">
        <div class="nav-container">
            <a href="./index.php" class="nav-link">返回主页</a>
        </div>
    </header>

    <!-- 页面标题 -->
    <div class="page-title">
        <h1>JOJO 奇妙留言板</h1>
    </div>

    <!-- 主容器 -->
    <div class="container">
        <!-- 错误提示 -->
        <?php if ($error) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- 查询表单 -->
        <form method="post">
            <div class="field">
                <label>你想查询几条，不可以是4哦</label>
                <input name="n" type="text" value="<?php echo $n; ?>" />
            </div>
            <button class="btn btn-primary" type="submit">查询</button>
        </form>

        <!-- 留言列表 -->
        <div class="message-list-card">
            <h2 class="list-title"><i class="fa-solid fa-comments"></i> 他人的奇妙感悟</h2>

            <?php if (!empty($messages)) : ?>
                <?php foreach ($messages as $msg) : ?>
                    <div class="message-item">
                        <div class="message-header">
                            <div class="message-nickname">
                                <i class="fa-solid fa-user"></i>
                                <?php echo htmlspecialchars($msg['nickname']); ?>
                            </div>
                            <div class="message-time">
                                <?php echo htmlspecialchars($msg['create_time']); ?>
                            </div>
                        </div>
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($msg['content'])); ?>
                        </div>
                        <div class="message-contact">
                            <i class="fa-solid fa-envelope"></i>
                            <?php echo htmlspecialchars($msg['contact']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="no-messages">
                    <i class="fa-solid fa-comment-slash"></i>
                    暂无留言～<br>
                    快来成为第一个留下感悟的人吧！
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>