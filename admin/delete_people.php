<?php
// 功能代码完全保留，仅修复SQL语法错误（原WHERE后多了AND）
session_start();
require_once __DIR__ . '/../inc/common.php';

if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

$pdo = db();
$username = $_SESSION['user'];
$error = '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
$stmt->execute([':u' => $username]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$res || $res['level'] < 1) {
    echo "<script>alert('小面包，level太低了'); location.href='./index.php';</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users u WHERE u.username IS NOT NULL AND u.id>1 ORDER BY u.id");
    $stmt->execute();
    $use = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = '加载失败：' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* JOJO主题强化：红金黑高对比度+指定背景图片 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial Black', 'Microsoft YaHei', sans-serif;
        }

        body {
            /* 核心修改：替换为指定背景图片 img\774141.png */
            background-image: 
                url('../img/774141.png'), /* 指定背景图片路径 */
                radial-gradient(circle at 20% 30%, rgba(180, 0, 0, 0.15) 0%, transparent 25%),
                radial-gradient(circle at 80% 70%, rgba(255, 215, 0, 0.08) 0%, transparent 35%),
                repeating-linear-gradient(45deg, rgba(255, 215, 0, 0.02) 0px, rgba(255, 215, 0, 0.02) 2px, transparent 2px, transparent 10px);
            background-color: #0a0a0a; /* 兜底背景色 */
            background-repeat: no-repeat; /* 图片不重复 */
            background-size: cover; /* 图片覆盖整个页面 */
            background-position: center; /* 图片居中 */
            background-attachment: fixed; /* 图片固定，滚动不跟随 */
            color: #f5f5f5;
            line-height: 1.8;
            padding: 20px;
            min-height: 100vh;
            /* 半透明遮罩：避免图片过亮影响文字可读性 */
            position: relative;
        }

        /* 遮罩层：在背景图片和内容之间增加半透明黑色层，提升文字对比度 */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* 透明度0.7，可调整 */
            z-index: -1; /* 位于背景图片之上，内容之下 */
        }

        /* 导航栏：强化金属边框和阴影，JOJO标志性红金渐变 */
        .nav {
            background: linear-gradient(135deg, #b40000 0%, #990000 50%, #660000 100%);
            border: 3px solid #ffd700;
            border-radius: 12px;
            padding: 15px 30px;
            margin: 0 auto 40px;
            max-width: 1400px;
            box-shadow: 0 6px 0 #440000, 0 0 20px rgba(255, 215, 0, 0.4);
            position: relative;
            overflow: hidden;
            z-index: 10; /* 确保导航栏在最上层 */
        }

        .nav::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav:hover::after {
            left: 100%;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .nav-link {
            color: #ffd700;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid transparent;
            border-radius: 8px;
            text-shadow: 2px 2px 0 #000, 3px 3px 0 #440000;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-link:hover {
            background-color: rgba(255, 215, 0, 0.2);
            border-color: #ffd700;
            transform: translateY(-3px);
            box-shadow: 0 4px 0 #ffd700;
        }

        .admin-tag {
            color: #ffd700;
            text-shadow: 1px 1px 0 #b40000, 2px 2px 0 #000;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-tag i {
            color: #ff4500;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* 页面标题：JOJO标志性倾斜文字，强化阴影层次 */
        .page-title {
            text-align: center;
            margin: 0 auto 50px;
            max-width: 900px;
            padding: 25px 30px;
            background-color: #1a0000;
            border: 4px solid #ffd700;
            border-radius: 12px;
            box-shadow: 0 8px 0 #660000, 0 0 25px rgba(255, 215, 0, 0.3);
            transform: skew(-3deg);
            position: relative;
            z-index: 10;
        }

        .page-title h1 {
            color: #ffd700;
            font-size: 2.8rem;
            text-shadow: 3px 3px 0 #b40000, 6px 6px 0 #000, 0 0 10px rgba(255, 215, 0, 0.5);
            letter-spacing: 3px;
            transform: skew(3deg);
            margin-bottom: 10px;
        }

        .page-title p {
            color: #f0f0f0;
            text-shadow: 1px 1px 0 #000;
            font-size: 1.1rem;
            transform: skew(3deg);
        }

        /* 主容器：居中+留白，提升呼吸感 */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 10; /* 确保内容在遮罩之上 */
        }

        /* 错误提示：强化警示感 */
        .error {
            background-color: rgba(180, 0, 0, 0.3);
            border: 3px solid #ff4500;
            border-radius: 8px;
            color: #ffcccc;
            padding: 15px;
            margin: 0 0 30px;
            text-align: center;
            font-weight: bold;
            font-size: 1.05rem;
            box-shadow: 0 4px 0 #660000;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .error i {
            color: #ff4500;
            font-size: 1.5rem;
        }

        /* 用户列表卡片：强化边框和内边距，提升质感 */
        .user-list-card {
            background-color: #1a0000;
            border: 3px solid #b40000;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 0 #660000, 0 0 15px rgba(180, 0, 0, 0.2);
            margin-bottom: 50px;
            background-color: rgba(26, 0, 0, 0.85); /* 卡片半透明背景，透出背景图片 */
            backdrop-filter: blur(2px); /* 轻微模糊，提升质感 */
        }

        .list-title {
            color: #ffd700;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #b40000;
            text-shadow: 2px 2px 0 #000;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
        }

        .list-title i {
            color: #ff4500;
            font-size: 1.8rem;
        }

        /* 用户列表表头：强化对比色，提升可读性 */
        .user-table-header {
            display: grid;
            grid-template-columns: 80px 220px 320px 120px 140px;
            gap: 15px;
            padding: 15px 25px;
            background-color: rgba(45, 11, 11, 0.9);
            border-radius: 8px 8px 0 0;
            font-weight: bold;
            color: #ffd700;
            text-shadow: 1px 1px 0 #000;
            font-size: 1.05rem;
            border-bottom: 2px solid #b40000;
        }

        /* 用户列表项：优化hover效果，增加分隔线 */
        .user-list {
            list-style: none;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        .user-item {
            display: grid;
            grid-template-columns: 80px 220px 320px 120px 140px;
            gap: 15px;
            padding: 18px 25px;
            background-color: rgba(13, 13, 13, 0.9);
            border-bottom: 1px solid #2d0b0b;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .user-item:nth-child(even) {
            background-color: rgba(17, 17, 17, 0.9);
        }

        .user-item:hover {
            background-color: rgba(34, 34, 34, 0.95);
            transform: translateX(8px);
            box-shadow: inset 0 0 15px rgba(255, 215, 0, 0.1);
        }

        .user-item:last-child {
            border-bottom: none;
        }

        /* 用户信息样式：优化文字对比 */
        .user-id {
            color: #ffd700;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .user-name {
            color: #f5f5f5;
            font-size: 1.1rem;
            text-shadow: 1px 1px 0 #000;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-name i {
            color: #b40000;
            font-size: 0.9rem;
        }

        .user-email {
            color: #cccccc;
            font-size: 0.95rem;
            word-break: break-all;
        }

        .user-level {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-shadow: 1px 1px 0 #000;
            font-size: 0.95rem;
        }

        .level-0 {
            background-color: #444444;
            color: #ffffff;
            border: 1px solid #666666;
        }

        .level-1 {
            background-color: #b40000;
            color: #ffd700;
            border: 1px solid #ff4500;
        }

        .level-2 {
            background-color: #ff4500;
            color: #ffffff;
            border: 1px solid #ffd700;
        }

        .level-3 {
            background-color: #ffd700;
            color: #b40000;
            border: 1px solid #ffaa00;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }

        /* 删除按钮：JOJO风格红色按钮，强化交互反馈 */
        .delete-form {
            margin: 0;
            width: 100%;
        }

        .delete-btn {
            width: 100%;
            background: linear-gradient(135deg, #b40000 0%, #990000 100%);
            color: #ffd700;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 8px 15px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            font-size: 1rem;
            text-shadow: 1px 1px 0 #000;
            box-shadow: 0 4px 0 #660000;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #ff4500 0%, #b40000 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 0 #440000, 0 0 10px rgba(255, 69, 0, 0.3);
        }

        .delete-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 2px 0 #440000;
        }

        .delete-btn i {
            font-size: 1.1rem;
        }

        /* 无用户状态：JOJO风格文案，提升趣味性 */
        .empty-user {
            text-align: center;
            padding: 60px 30px;
            color: #999;
            background-color: rgba(13, 13, 13, 0.9);
            border-radius: 8px;
            border: 2px dashed #2d0b0b;
        }

        .empty-user i {
            font-size: 4rem;
            color: #b40000;
            margin-bottom: 20px;
            opacity: 0.6;
            transition: all 0.5s;
        }

        .empty-user:hover i {
            opacity: 1;
            transform: rotate(10deg) scale(1.1);
        }

        .empty-user p {
            font-size: 1.2rem;
            margin-bottom: 15px;
            text-shadow: 1px 1px 0 #000;
        }

        .empty-user span {
            color: #ffd700;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* 响应式优化：适配手机/平板，避免布局错乱 */
        @media (max-width: 1024px) {
            .user-table-header, .user-item {
                grid-template-columns: 70px 180px 250px 100px 120px;
            }
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 15px;
            }

            .page-title h1 {
                font-size: 2.2rem;
            }

            .user-table-header, .user-item {
                grid-template-columns: 60px 140px 1fr 100px;
                grid-template-areas:
                    "id name email btn"
                    "level level level level";
                gap: 10px;
            }

            .user-table-header .id-col { grid-area: id; }
            .user-table-header .name-col { grid-area: name; }
            .user-table-header .email-col { grid-area: email; }
            .user-table-header .operate-col { grid-area: btn; }
            .user-table-header .level-col { 
                grid-area: level; 
                text-align: left;
                padding-top: 5px;
            }

            .user-item .user-id { grid-area: id; }
            .user-item .user-name { grid-area: name; }
            .user-item .user-email { grid-area: email; }
            .user-item .user-level { grid-area: level; margin-top: 5px; }
            .user-item .operate-col { grid-area: btn; }

            .delete-btn {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                transform: skew(0);
                padding: 20px 15px;
            }

            .page-title h1 {
                font-size: 1.8rem;
                transform: skew(0);
            }

            .user-table-header, .user-item {
                grid-template-columns: 50px 1fr;
                grid-template-areas:
                    "id name"
                    "email email"
                    "level level"
                    "btn btn";
            }

            .operate-col {
                margin-top: 10px;
            }

            .delete-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <header class="nav">
        <div class="nav-container">
            <a href="./index.php" class="nav-link">
                <i class="fa-solid fa-arrow-left"></i> 返回主页面
            </a>
            <div class="admin-tag">
                <i class="fa-solid fa-crown"></i>
                管理员：<?php echo htmlspecialchars($username); ?>
            </div>
        </div>
    </header>

    <!-- 页面标题 -->
    <div class="page-title">
        <h1>管理</h1>
    </div>

    <!-- 主内容区 -->
    <main class="container">
        <div class="user-list-card">
            <!-- 错误提示 -->
            <?php if ($error) : ?>
                <div class="error">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- 列表标题 -->
            <div class="list-title">
                <i class="fa-solid fa-users-gear"></i>
                列表
            </div>

            <!-- 表头 -->
            <div class="user-table-header">
                <div class="id-col">编号</div>
                <div class="name-col">姓名</div>
                <div class="email-col">联络方式</div>
                <div class="operate-col">操作</div>
            </div>

            <!-- 用户列表 -->
            <ul class="user-list">
                <?php if (!empty($use)) : ?>
                    <?php foreach ($use as $user) : ?>
                        <li class="user-item">
                            <div class="user-id"><?php echo htmlspecialchars($user['id']); ?></div>
                            <div class="user-name">
                                <i class="fa-solid fa-user-secret"></i>
                                <?php echo htmlspecialchars($user['username'] ?? '匿名'); ?>
                            </div>
                            <div class="user-email">
                                <?php echo $user['email'] ? htmlspecialchars($user['email']) : '未登记联络方式'; ?>
                            </div>


                            <div class="operate-col">
                                <form action="./delete_user.php" method="post" class="delete-form">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['username']); ?>">
                                    <button type="submit" class="delete-btn" >
                                        <i class="fa-solid fa-trash"></i> 删除
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li class="empty-user">
                        <i class="fa-solid fa-mask"></i>
                        <p>暂无觉醒替身的使者...</p>
                        <span>等待第一个替身觉醒者出现！</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </main>
</body>
</html>