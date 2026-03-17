<?php
session_start();
// 确保编码统一，避免隐藏字符导致解析错误
header('Content-Type: text/html; charset=utf-8');
include_once __DIR__ . '/../inc/common.php';

if(!isset($_SESSION['user']))
{
    header('location: ./login.php');
    exit;
}
$msg='';
$pdo=db();
if(isset($_GET['id'])){
    $postid=(int)$_GET['id'];
    $checkStmt = $pdo->prepare('SELECT * from blog WHERE id = ? LIMIT 1');
    $checkStmt->execute([$postid]);
    $blog = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$blog){
        $msg='文章不见了';
        header("Location: ./index.php?msg=" .urlencode($msg));
        exit;
    }

    if($blog['uid'] !=$_SESSION['user'] ){
          $msg = "无权限修改他人文章";
    header("Location: ./index.php?msg=" . urlencode($msg));
    exit;
    }


}else{
$msg='无参数';
}
if($_SERVER['REQUEST_METHOD']==='POST'){ // 修正为严格比较POST
    $text = $_POST['content'];
    try{
          $updateStmt = $pdo->prepare("
    UPDATE blog 
    SET blogcontent = ?, 
        blogdate = NOW()  -- 自动更新为当前时间
    WHERE id = ?
");
         $updateStmt->execute([$text,$postid]);
       echo "<script>alert('成功修改'); location.href='./index.php';</script>" ;
       exit; 
    }catch(PDOException $e){
                $msg = '修改失败：'  . $e->getMessage(); // 补充冒号，错误信息更清晰
           }  
    }


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑文章</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 背景图设置 */
        body {
            background: url("../img/792754.png") no-repeat center center fixed;
            background-size: cover;
            position: relative;
            margin: 0;
            padding: 0;
            color: #333;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.85);
            z-index: -1;
        }

        /* 导航栏美化 */
        .nav {
            background-color: rgba(51, 51, 51, 0.9);
            padding: 15px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .nav a:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        /* 容器美化 */
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* 错误提示美化 */
        .error {
            background-color: #f8d7da;
            color: #dc3545;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        /* 文章详情区块美化 */
        .blog-detail {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative;
            transition: box-shadow 0.3s ease;
        }
        .blog-detail:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .blog-detail::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #3498db, #2980b9);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .blog-title {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.3;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* 元信息美化 */
        .blog-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #7f8c8d;
            margin-bottom: 25px;
        }
        .blog-meta h3, .blog-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .blog-meta .fa-solid {
            color: #3498db;
        }

        /* 文本域美化（保留原有固定尺寸，增加视觉优化） */
        .fixed-size-textarea {
            width: 800px;
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

        /* 按钮美化 */
        button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        button:hover {
            background: linear-gradient(135deg, #2980b9, #1e6091);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* 动画效果 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 响应式适配 */
        @media (max-width: 768px) {
            .blog-detail {
                padding: 15px;
            }
            .fixed-size-textarea {
                width: 100%;
                height: 250px;
            }
            .blog-title {
                font-size: 1.5rem;
            }
            .nav {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
     <div class="nav">
      <a href="./index.php"><i class="fa-solid fa-pen-to-square"></i> 后台首页</a>
     </div>
     <div class="container">
        <?php if($msg): ?>
                <div class="error"><?php echo $msg; ?></div>
                <?php else: ?>
                <div class="blog-detail">
                <h1 class="blog-title"><?php echo htmlspecialchars($blog['blogtitle']);?> </h1>   
                </div>
                <div class="blog-meta">
                <h3><i class="fa-solid fa-user"></i> 作者</h3>
                <?php echo htmlspecialchars($blog['uid']); ?>
                <span><i class="fa-solid fa-clock"></i> 时间: <?php echo htmlspecialchars($blog['blogdate']); ?></span>
                <form method="post">
                    <div class="blog-content"  >
                          <textarea name="content" class="fixed-size-textarea"><?php echo htmlspecialchars($blog['blogcontent'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit"><i class="fa-solid fa-pen-to-square"></i> 确认修改</button>
                </form>
             </div>   
        <?php endif ?>   
     </div>
</body>
</html>