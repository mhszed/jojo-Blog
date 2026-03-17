<?php
session_start();
// 基础安全头
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
include_once __DIR__ . '/inc/common.php';

$pdo = db();
$blog = null;
$msg = '';

if(isset($_GET['id'])){
    $blogid=(int)$_GET['id'];
    try{
        $stmt=$pdo->prepare("select * from blog where id = ? LIMIT 1");
        $stmt->execute([$blogid]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$blog){
            $msg = '博客已被删除';
        }
        
    }catch(PDOException $e){
        $msg='查询失败';
    }
}else{
    $msg='无效的请求';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $blog ? htmlspecialchars($blog['blogtitle']  ) : '博客详情' ?></title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 全局样式重置与背景设置 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", Arial, sans-serif;
        }
        body {
            padding: 0;
            max-width: 100%;
            min-height: 100vh;
            line-height: 1.8;
            /* 背景图设置 */
            background: url("./img/831062.png") no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        /* 导航栏美化 */
        .nav {
            background: rgba(30, 96, 145, 0.9);
            padding: 15px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: flex-start;
            gap: 20px;
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s ease;
            padding: 6px 12px;
            border-radius: 8px;
        }
        .nav a:hover {
            background: rgba(255, 215, 0, 0.2);
            transform: translateY(-2px);
        }

        /* 主体内容容器 */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* 博客详情卡片 */
        .blog-detail {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }
        .blog-detail::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #2980b9, #ffd700);
        }

        /* 博客标题 */
        .blog-title {
            font-size: 2.2rem;
            margin-bottom: 15px;
            color: #1e6091;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            padding-bottom: 10px;
        }
        .blog-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: #ffd700;
        }

        /* 博客元信息 */
        .blog-meta {
            color: #636e72;
            font-size: 0.95rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .blog-meta h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2980b9;
            font-size: 1.1rem;
        }

        /* 博客内容 */
        .blog-content {
            color: #333;
            font-size: 1.05rem;
            white-space: pre-wrap; /* 保留换行 */
            line-height: 1.8;
        }

        /* 错误提示 */
        .error {
            color: #dc3545;
            padding: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            background-color: #f8d7da;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* 操作按钮组 */
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .actions a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        .actions a:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
        }
        .actions a.edit {
            background-color: #ffd700;
            color: #1e6091;
        }
        .actions a.delete {
            background-color: #dc3545;
        }

        /* 响应式优化 */
        @media (max-width: 768px) {
            .nav {
                padding: 12px 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .blog-detail {
                padding: 25px 15px;
            }
            .blog-title {
                font-size: 1.8rem;
            }
        }
        /* 文本域美化（保留原有固定尺寸，增加视觉优化） */
        .fixed-size-textarea {
            width: 900px;
            height: 600px;
            resize: none;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 8px;
            font-size: 1rem;
            line-height: 1.8;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .fixed-size-textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
    </style>
</head>
<body>
   <div class="nav">
      <a href="./text.php"><i class="fa-solid fa-house"></i> 前端首页</a>
      <a href="./admin/index.php"><i class="fa-solid fa-user-gear"></i> 后台首页</a>
   </div>

   <div class="container">
    <!-- 博客详情 -->
    <?php if($msg): ?>
        <div class="error"><?php echo $msg; ?></div>
     <?php else: ?>
        <div class="blog-detail">
             <h1 class="blog-title"><?php echo htmlspecialchars($blog['blogtitle']);?></h1>
             <div class="blog-meta">
                 <h3><i class="fa-solid fa-user"></i> 作者</h3>
                 <?php echo htmlspecialchars($blog['uid']); ?>
                 <span><i class="fa-solid fa-clock"></i> 时间: <?php echo htmlspecialchars($blog['blogdate']); ?></span>
             </div>
            
             <div class="blog-content">
               <textarea name="content" class="fixed-size-textarea" disabled><?php echo htmlspecialchars($blog['blogcontent'] ?? ''); ?></textarea>
             </div>

            
        </div>
    <?php endif; ?>
   </div>
</body>
</html>