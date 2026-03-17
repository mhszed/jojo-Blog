<?php
// 功能代码完全保留，未做任何修改
session_start();
require_once __DIR__ . '/../inc/common.php';

if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

$pdo = db();
$username = $_SESSION['user'] ??$_cookie['user'];

try {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $validUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$validUser) {
        session_unset();
        session_destroy();
        setcookie('user', '', time() - 3600, '/');
        header('Location: ./login.php');
        exit;
    }
} catch (PDOException $e) {
    die('用户校验失败：' . $e->getMessage());
}

$userPosts = [];
try {
    $stmt = $pdo->prepare("SELECT id, blogtitle, blogdate FROM blog WHERE uid = :u ORDER BY blogdate DESC ");
    $stmt->execute([':u' => $username]);
    $userPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $userPosts = [];
}
?>

<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员首页</title>
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 基础重置与全局样式 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* 更流畅的过渡曲线 */
        }

        /* 页面背景优化：暗化+模糊，突出前景内容 */
        body {
            font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
            color: #2d3436;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.4)), /* 暗化遮罩 */
                        url("../img/853912.jpg") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* 导航栏美化：增强立体感与交互 */
        .nav {
            background: linear-gradient(135deg, rgba(41, 128, 185, 0.98), rgba(30, 96, 145, 0.98));
            padding: 14px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 2px solid #ffd700;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 25px;
            justify-content: flex-end;
            flex-wrap: wrap; /* 响应式换行 */
        }

        .user-profile-link {
            color: #ffd700;
            text-decoration: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .user-profile-link::before {
            content: '\f007';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #ffd700;
            font-size: 1.1rem;
        }

        .user-profile-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 400;
        }

        .nav a[href="./logout.php"] {
            border: 2px solid #ffd700;
            background-color: rgba(255, 215, 0, 0.05);
        }

        .nav a[href="./logout.php"]:hover {
            background-color: rgba(255, 215, 0, 0.15);
            color: #ffd700;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 215, 0, 0.2);
        }

        .nav a[href="./published.php"] {
            background-color: #ffd700;
            color: #1e6091 !important;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.2);
        }

        .nav a[href="./published.php"]:hover {
            background-color: #fff3cd;
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }

        /* 主体容器优化 */
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        /* 头部区域美化 */
        .hero {
            text-align: center;
            margin-bottom: 50px;
            padding: 35px 20px;
            background-color: rgba(255, 255, 255, 0.92);
            border: 2px solid #2980b9;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(41, 128, 185, 0.18);
            position: relative;
            overflow: hidden;
        }

        /* 装饰元素：角落金色星星 */
        .hero::before,
        .hero::after {
            content: '★';
            color: rgba(255, 215, 0, 0.3);
            position: absolute;
            font-size: 4rem;
        }

        .hero::before {
            top: -20px;
            right: -20px;
        }

        .hero::after {
            bottom: -20px;
            left: -20px;
            transform: rotate(180deg);
        }

        .hero h1 {
            color: #1e6091;
            margin-bottom: 15px;
            font-weight: 700;
            font-size: 2.2rem;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(30, 96, 145, 0.1);
        }

        .hero p {
            color: #636e72;
            font-size: 1.15rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }

        /* 博文卡片美化 */
        .post-card {
            background-color: rgba(255, 255, 255, 0.95);
            border: 2px solid #b8daff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 40px;
            position: relative;
        }

        .post-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2980b9, #ffd700);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .post-card h2 {
            color: #1e6091;
            font-size: 1.6rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e3f2fd;
        }

        .post-card h2::before {
            content: '\f1ea';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #2980b9;
            font-size: 1.4rem;
        }

        /* 博文列表美化 */
        .post-list {
            list-style: none;
            padding: 0;
        }

        .post-item {
            padding: 18px 0;
            border-bottom: 1px dashed #e3f2fd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-item:hover {
            background-color: rgba(235, 248, 255, 0.5);
            border-radius: 8px;
            padding-left: 10px;
        }

        .post-item a {
            color: #2d3436;
            text-decoration: none;
            font-size: 1.1rem;
            max-width: 65%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        .post-item a:hover {
            color: #2980b9;
            text-decoration: underline;
            text-underline-offset: 4px;
            text-decoration-thickness: 2px;
        }

        .post-mate {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .post-time {
            color: #636e72;
            font-size: 0.95rem;
            margin-right: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .post-time::before {
            content: '\f073';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #b8daff;
            font-size: 0.9rem;
        }

        /* 空状态美化 */
        .empty-post {
            text-align: center;
            padding: 60px 30px;
            color: #636e72;
            font-size: 1.1rem;
            background-color: rgba(235, 248, 255, 0.3);
            border-radius: 12px;
            margin: 20px 0;
        }

        .empty-post::before {
            content: '★';
            font-size: 3rem;
            display: block;
            margin-bottom: 20px;
            color: #b8daff;
            animation: pulse 2s infinite;
        }

        .empty-post a {
            color: #2980b9;
            text-decoration: none;
            margin-top: 15px;
            display: inline-block;
            font-weight: 600;
            font-size: 1.05rem;
            background-color: rgba(41, 128, 185, 0.1);
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid #b8daff;
        }

        .empty-post a:hover {
            background-color: #2980b9;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(41, 128, 185, 0.2);
        }

        /* 按钮组美化 */
        .post-actions {
            display: flex;
            gap: 12px;
        }

        .post-actions a {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .edit-btn {
            background-color: #fff3cd;
            color: #856404 !important;
            text-decoration: none !important;
            border: 1px solid #ffeeba;
        }

        .edit-btn:hover {
            background-color: #ffd700;
            color: #1e6091 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 215, 0, 0.2);
        }

        .delete-btn {
            background-color: #e3f2fd;
            color: #1976d2 !important;
            text-decoration: none !important;
            border: 1px solid #b8daff;
        }

        .delete-btn:hover {
            background-color: #2980b9;
            color: #fff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(41, 128, 185, 0.2);
        }

        /* 动画效果 */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }
            50% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 0.7;
            }
        }

        /* 响应式优化 */
        @media (max-width: 768px) {
            .nav {
                padding: 12px 15px;
            }

            .user-info {
                gap: 15px;
                justify-content: center;
            }

            .nav a {
                padding: 6px 12px;
                font-size: 0.9rem;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .post-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px 10px;
            }

            .post-item a {
                max-width: 100%;
                margin-bottom: 10px;
            }

            .post-mate {
                width: 100%;
                justify-content: space-between;
            }

            .post-time {
                margin-right: 0;
            }
        }

        @media (max-width: 480px) {
            .user-info {
                flex-direction: column;
                gap: 10px;
            }

            .hero {
                padding: 25px 15px;
            }

            .post-card {
                padding: 20px 15px;
            }

            .post-actions {
                gap: 8px;
            }

            .post-actions a {
                padding: 5px 10px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- 功能导航栏（未改动功能，仅美化样式） -->
    <header class="nav">
        <div class="user-info">
                  
            <a href="./delete_people.php">
                <i class="fa-solid fa-pen-to-square"></i> 其他用户
            </a>
            <a href="./message.php">
                <i class="fa-solid fa-pen-to-square"></i> 管理评论
            </a>
              <a href="../text.php">
                <i class="fa-solid fa-pen-to-square"></i> 去往前端
            </a>
        
            <a href="./logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> 退出登录
            </a>
            <a href="./published.php">
                <i class="fa-solid fa-pen-to-square"></i> 发表博文
            </a>
               <div class="user-profile-link" onclick="window.location.href='./profile.php'">
                欢迎，<?php echo htmlspecialchars($username); ?>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="hero">
            <h1>管理员系统首页</h1>
            <p>管理你的博文内容，查看发布记录，轻松编辑与维护个人创作</p>
        </div>

        <div class="post-card">
            <h2><i class="fa-solid fa-file-lines"></i> 我的博文</h2>
  
            <ul class="post-list">
                <?php if (!empty($userPosts)) : ?>
                    <?php foreach ($userPosts as $post ) : ?>
                        <li class="post-item">
                            <a href="../post_detail.php?id=<?php echo htmlspecialchars($post['id']); ?>">
                                <?php echo htmlspecialchars($post['blogtitle']); ?>
                            </a>
                            <div class="post-mate">
                                <span class="post-time">
                                    <?php echo htmlspecialchars($post['blogdate']); ?>
                                </span>
                                <div class="post-actions">
                                    <a href="./edit_post.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="edit-btn">
                                        <i class="fa-solid fa-pencil"></i> 编辑
                                    </a>
                                    <a href="./delete_post.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="delete-btn" onclick="return confirm('确定要删除这篇博文吗？删除后不可恢复！')">
                                        <i class="fa-solid fa-trash"></i> 删除
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?> 
                <?php else :?>
                    <li class="empty-post">
                        暂无发布的博文~ <br>
                        <a href="./published.php">
                            点击前往发表第一篇博文
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>   
    </main>
</body>
</html>