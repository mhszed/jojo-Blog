<?php
require_once __DIR__ . '/inc/common.php';
session_start();
$pdo = db();
$allPosts = [];
try {
    $stmt = $pdo->prepare('SELECT b.id,   
            b.blogtitle AS title, 
            b.blogdate, 
            u.username AS author 
        FROM blog b
        LEFT JOIN users u ON b.uid = u.username 
        WHERE b.blogtitle IS NOT NULL 
        ORDER BY b.blogdate DESC 
        LIMIT 10 ');
    $stmt->execute();
    $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $allPosts = [];
}
?>

<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOJO主题博客 - 首页</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            background-color: #050505;
            background-image: 
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 50m-40 0a40 40 0 1 0 80 0a40 40 0 1 0 -80 0' fill='none' stroke='%23333' stroke-width='0.5'/%3E%3C/svg%3E"),
                url("data:image/svg+xml,%3Csvg width='200' height='200' viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10 10L190 190M20 180L180 20M50 10L150 190M70 190L130 10M100 10L100 190M130 10L70 190M150 190L50 10M180 20L20 180M190 190L10 10' fill='none' stroke='%23444' stroke-width='0.3'/%3E%3C/svg%3E"),
                radial-gradient(circle at 20% 30%, rgba(230, 0, 0, 0.25) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 215, 0, 0.2) 0%, transparent 50%);
            font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
            color: #fdfdfd;
            min-height: 100vh;
            padding-bottom: 80px;
            overflow-x: hidden;
            perspective: 1000px;
        }

        .nav {
            background: linear-gradient(135deg, #880000 0%, #550000 50%, #880000 100%);
            padding: 22px 40px;
            box-shadow: 
                0 8px 30px rgba(150, 0, 0, 0.9),
                0 0 25px rgba(255, 215, 0, 0.5),
                inset 0 3px 8px rgba(255, 255, 255, 0.15),
                inset 0 -3px 8px rgba(0, 0, 0, 0.7);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 4px solid;
            border-image: linear-gradient(90deg, transparent, #ffd700, #ffb300, #ffd700, transparent) 1;
        }

        body.scroll .nav {
            padding: 15px 40px;
            transform: scale(0.98);
            box-shadow: 
                0 5px 25px rgba(150, 0, 0, 0.9),
                0 0 20px rgba(255, 215, 0, 0.4);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .nav-link {
            color: #ffd700;
            font-size: 1.5rem;
            font-weight: 900;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 18px;
            text-shadow: 0 0 12px rgba(255, 215, 0, 0.9), 4px 4px 8px rgba(0, 0, 0, 0.95);
            padding: 15px 30px;
            border-radius: 10px;
            border: 3px solid transparent;
            letter-spacing: 3px;
            text-transform: uppercase;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.1) 50%, rgba(0, 0, 0, 0.3) 100%);
            box-shadow: 
                inset 0 2px 5px rgba(255, 255, 255, 0.15),
                0 3px 5px rgba(0, 0, 0, 0.5);
            -webkit-text-stroke: 0.5px #ffb300;
        }

        .nav-link:hover {
            background: linear-gradient(45deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0.4) 50%, rgba(255, 215, 0, 0.2) 100%);
            transform: translateY(-6px) scale(1.05) rotateX(5deg);
            border-color: #ffd700;
            box-shadow: 
                0 0 25px rgba(255, 215, 0, 0.85),
                inset 0 2px 5px rgba(255, 255, 255, 0.25),
                0 5px 10px rgba(0, 0, 0, 0.7);
            text-shadow: 0 0 20px rgba(255, 215, 0, 1), 4px 4px 8px rgba(0, 0, 0, 0.95);
        }

        .nav-link::before {
            content: '\f0e7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            transform: rotate(22deg) scale(1.4);
            color: #ffd700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 1);
            animation: iconPulse 4s ease-in-out infinite alternate;
            filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.8));
        }

        .main-image {
            width: 100%;
            padding: 60px 20px;
            text-align: center;
            position: relative;
            margin-bottom: 50px;
            perspective: 1500px;
        }

        .main-image-outer {
            max-width: 1050px;
            margin: 0 auto;
            position: relative;
            transform-style: preserve-3d;
            animation: mainFloat 8s ease-in-out infinite;
        }

        .main-image-wrapper {
            padding: 20px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 25px;
            box-shadow: 
                inset 0 0 40px rgba(0, 0, 0, 0.8),
                0 0 50px rgba(255, 215, 0, 0.15);
            position: relative;
            transform-style: preserve-3d;
        }

        .volume-light {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 25px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            z-index: -1;
            animation: lightShift 10s ease-in-out infinite alternate;
        }

        .stand-aura {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110%;
            height: 110%;
            border-radius: 22px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.35) 0%, rgba(255, 69, 0, 0.1) 70%, transparent 85%);
            z-index: -2;
            animation: auraPulse 5s ease-in-out infinite;
            filter: blur(15px);
        }

        .particle-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 25px;
            z-index: -3;
            overflow: hidden;
        }

        .main-image img {
            max-width: 100%;
            max-height: 75vh;
            object-fit: contain;
            border: 12px solid;
            border-image: linear-gradient(135deg, #ffd700, #ffb300, #ffd700, #ff9900, #ffd700, #ffb300, #ffd700) 1;
            border-radius: 18px;
            box-shadow: 
                0 0 40px rgba(255, 215, 0, 0.85),
                0 12px 60px rgba(0, 0, 0, 0.98),
                inset 0 0 30px rgba(0, 0, 0, 0.7),
                inset 0 1px 5px rgba(255, 255, 255, 0.2);
            transition: transform 6s ease-in-out, box-shadow 5s ease, filter 4s ease;
            background-color: #0f0f0f;
            padding: 20px;
            filter: contrast(1.1) saturate(1.2) brightness(1.05);
            transform: translateZ(10px);
            backface-visibility: hidden;
        }

        .main-image img:hover {
            transform: translateZ(20px) scale(1.08) rotate(1deg);
            box-shadow: 
                0 0 60px rgba(255, 215, 0, 0.95),
                0 15px 70px rgba(0, 0, 0, 0.98),
                inset 0 0 35px rgba(0, 0, 0, 0.75),
                0 0 200px rgba(255, 215, 0, 0.35);
            filter: contrast(1.2) saturate(1.3) brightness(1.1);
        }

        .main-image-wrapper::before {
            content: '';
            position: absolute;
            top: -25px;
            left: -25px;
            right: -25px;
            bottom: -25px;
            border: 4px dashed;
            border-image: linear-gradient(45deg, #ffd700, #ffb300, #ffd700) 1;
            border-radius: 35px;
            opacity: 0.85;
            z-index: -4;
            animation: 
                dash 20s linear infinite,
                dashFade 8s ease-in-out infinite,
                dashRotate 25s linear infinite;
            transform: translateZ(-5px);
        }

        .main-image-wrapper::after {
            content: 'JOJO\'S BIZARRE ADVENTURE';
            position: absolute;
            bottom: -65px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffd700;
            font-size: 1.6rem;
            font-weight: 900;
            text-shadow: 0 0 15px rgba(255, 215, 0, 0.9), 4px 4px 8px rgba(0, 0, 0, 0.98);
            letter-spacing: 8px;
            text-transform: uppercase;
            background: linear-gradient(45deg, #330000 0%, #110000 50%, #330000 100%);
            padding: 15px 50px;
            border-radius: 50px;
            border: 3px solid;
            border-image: linear-gradient(45deg, #ffd700, #ffb300, #ffd700) 1;
            box-shadow: 
                0 8px 20px rgba(0, 0, 0, 0.95),
                inset 0 2px 5px rgba(255, 255, 255, 0.15),
                0 0 25px rgba(255, 215, 0, 0.6);
            -webkit-text-stroke: 1px #ffb300;
            animation: textFloat 7s ease-in-out infinite reverse;
        }

        .main-image-slogan {
            position: absolute;
            top: -45px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffd700;
            font-size: 1.3rem;
            font-weight: 800;
            text-shadow: 0 0 15px rgba(255, 215, 0, 0.95), 3px 3px 6px rgba(0, 0, 0, 0.98);
            letter-spacing: 4px;
            background: linear-gradient(45deg, #330000 0%, #110000 50%, #330000 100%);
            padding: 12px 45px;
            border-radius: 40px;
            border: 3px solid rgba(255, 215, 0, 0.9);
            box-shadow: 
                0 5px 15px rgba(0, 0, 0, 0.95),
                inset 0 2px 5px rgba(255, 255, 255, 0.15),
                0 0 20px rgba(255, 215, 0, 0.7);
            -webkit-text-stroke: 0.8px #ffb300;
            animation: 
                textFloat 6s ease-in-out infinite,
                textGlow 4s ease-in-out infinite;
            transform: translateX(-50%) translateZ(5px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
            background-color: #2d0b0b;
            border: 2px solid #ffd700;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        }

        .hero h1 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 2.2rem;
            text-shadow: 3px 3px 0 #000;
        }

        .hero p {
            color: #f0c0c0;
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .post-card {
            background-color: #2c0e0e;
            border: 2px solid #9b2226;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            padding: 25px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .post-card::before {
            content: '';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-top: 3px solid #ffd700;
            border-right: 3px solid #ffd700;
        }

        .post-card h2 {
            color: #ffd700;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-shadow: 2px 2px 0 #000;
        }

        .post-card h2::before {
            content: '\f1ea';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            color: #e63946;
            transform: rotate(-5deg);
        }

        .post-list {
            list-style: none;
            padding: 0;
        }

        .post-item {
            padding: 15px 0;
            border-bottom: 1px dashed #5c1a1a;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-title {
            flex: 1;
            min-width: 250px;
            margin-bottom: 10px;
        }

        .post-title a {
            color: #f1faee;
            text-decoration: none;
            font-size: 1.1rem;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 1px 1px 0 #000;
            transition: all 0.3s;
        }

        .post-title a:hover {
            color: #ffd700;
            letter-spacing: 1px;
        }

        .post-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            color: #d1bebe;
            font-size: 0.9rem;
            width: 100%;
        }

        .post-author::before {
            content: '\f007';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            margin-right: 5px;
            color: #e63946;
        }

        .post-time::before {
            content: '\f073';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            margin-right: 5px;
            color: #e63946;
        }

        .empty-post {
            text-align: center;
            padding: 50px 0;
            color: #d1bebe;
            font-size: 1.2rem;
        }

        .empty-post::before {
            content: '\f256';
            font-family: 'Font Awesome 6 Free';
            font-weight: 600;
            font-size: 3rem;
            display: block;
            margin-bottom: 20px;
            color: #5c1a1a;
            transform: rotate(10deg);
        }

        @keyframes mainFloat {
            0%, 100% { transform: rotateX(2deg) rotateY(3deg); }
            50% { transform: rotateX(-2deg) rotateY(-3deg); }
        }

        @keyframes lightShift {
            0% { background-position: 30% 30%; }
            100% { background-position: 70% 70%; }
        }

        @keyframes auraPulse {
            0%, 100% { opacity: 0.4; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
        }

        @keyframes iconPulse {
            0% { transform: rotate(22deg) scale(1.4); }
            100% { transform: rotate(28deg) scale(1.6); }
        }

        @keyframes textFloat {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-12px); }
        }

        @keyframes textGlow {
            0%, 100% { text-shadow: 0 0 15px rgba(255, 215, 0, 0.9), 3px 3px 6px rgba(0, 0, 0, 0.98); }
            50% { text-shadow: 0 0 30px rgba(255, 215, 0, 1), 3px 3px 8px rgba(0, 0, 0, 0.98); }
        }

        @keyframes dash {
            to { stroke-dashoffset: -3000; }
        }

        @keyframes dashFade {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 0.95; }
        }

        @keyframes dashRotate {
            to { transform: rotate(360deg); }
        }

        img {
            opacity: 0;
            animation: fadeIn 2.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateZ(-50px) scale(0.9) rotateY(15deg); filter: blur(10px); }
            to { opacity: 1; transform: translateZ(0) scale(1) rotateY(0); filter: blur(0); }
        }

        .main-image img {
            animation-delay: 0.8s;
        }

        @media (max-width: 768px) {
            .main-image {
                padding: 40px 15px;
            }

            .main-image-outer {
                max-width: 100%;
            }

            .main-image-wrapper {
                padding: 15px;
            }

            .main-image img {
                border-width: 8px;
                padding: 15px;
            }

            .main-image-wrapper::after {
                font-size: 1.1rem;
                padding: 12px 30px;
                bottom: -50px;
                letter-spacing: 4px;
                border-width: 2px;
            }

            .main-image-slogan {
                font-size: 1.1rem;
                padding: 10px 30px;
                top: -35px;
                letter-spacing: 2px;
                border-width: 2px;
            }

            .nav {
                padding: 18px 30px;
                border-bottom-width: 3px;
            }

            body.scroll .nav {
                padding: 12px 30px;
                transform: scale(0.97);
            }

            .nav-link {
                font-size: 1.3rem;
                padding: 12px 25px;
                gap: 15px;
                border-width: 2px;
            }

            .container {
                padding: 0 15px;
            }

            .hero {
                padding: 15px;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .post-card {
                padding: 20px;
            }

            .post-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .post-meta {
                margin-top: 10px;
                gap: 15px;
                font-size: 0.85rem;
            }
        }
      .message-board-btn {
    text-align: center;
    margin: 40px 0;
  }
  .jojo-btn {
    display: inline-block;
    color: #ffd700;
    font-size: 1.3rem;
    font-weight: 900;
    text-decoration: none;
    padding: 18px 45px;
    border-radius: 50px;
    border: 3px solid #ffd700;
    text-shadow: 0 0 12px rgba(255, 215, 0, 0.9), 2px 2px 4px rgba(0, 0, 0, 0.95);
    background: linear-gradient(135deg, #880000 0%, #550000 50%, #880000 100%);
    box-shadow: 
      0 8px 20px rgba(150, 0, 0, 0.9),
      0 0 20px rgba(255, 215, 0, 0.6),
      inset 0 3px 5px rgba(255, 255, 255, 0.15),
      inset 0 -3px 5px rgba(0, 0, 0, 0.7);
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    letter-spacing: 2px;
    text-transform: uppercase;
    -webkit-text-stroke: 0.3px #ffb300;
  }
  .jojo-btn:hover {
    transform: translateY(-8px) scale(1.05) rotateX(5deg);
    box-shadow: 
      0 12px 30px rgba(150, 0, 0, 0.9),
      0 0 30px rgba(255, 215, 0, 0.8),
      inset 0 3px 5px rgba(255, 255, 255, 0.25);
    border-color: #ff9900;
    text-shadow: 0 0 20px rgba(255, 215, 0, 1);
  }
  .jojo-btn i {
    margin-right: 12px;
    transform: rotate(15deg);
  }
  /* 响应式按钮 */
  @media (max-width: 768px) {
    .jojo-btn {
      font-size: 1.1rem;
      padding: 15px 35px;
    }
  }
    </style>
    <script>
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                document.body.classList.add('scroll');
            } else {
                document.body.classList.remove('scroll');
            }
        });

        const mainImage = document.querySelector('.main-image img');
        const mainOuter = document.querySelector('.main-image-outer');
        if (mainImage && mainOuter) {
            document.addEventListener('mousemove', (e) => {
                const x = (e.clientX / window.innerWidth - 0.5) * 10;
                const y = (e.clientY / window.innerHeight - 0.5) * 10;
                mainOuter.style.transform = `rotateX(${y * 0.3}deg) rotateY(${x * 0.3}deg)`;
            });
        }

        function createParticles() {
            const container = document.querySelector('.particle-container');
            if (!container) return;

            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                const size = Math.random() * 3 + 1;
                const left = Math.random() * 100;
                const top = Math.random() * 100;
                const delay = Math.random() * 10;
                const duration = Math.random() * 15 + 10;

                particle.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    background: rgba(255, 215, 0, ${Math.random() * 0.5 + 0.3});
                    border-radius: 50%;
                    left: ${left}%;
                    top: ${top}%;
                    animation: particles ${duration}s linear ${delay}s infinite;
                    filter: blur(1px);
                `;

                container.appendChild(particle);
            }
        }

        window.addEventListener('load', createParticles);
    </script>
</head>
<body>
    <header class="nav">
        <div class="nav-container">
            <a href="./admin/index.php" class="nav-link">你也想发布文章？进入博客后台</a>
        </div>
    </header>

    <main class="main-image">
        <div class="main-image-outer">
            <div class="main-image-wrapper">
                <div class="particle-container"></div>
                <div class="volume-light"></div>
                <div class="stand-aura"></div>
                <div class="main-image-slogan">欧拉~</div>
                <img src="./img/659513.jpg" alt="JOJO的奇妙冒险" title="JOJO的奇妙冒险">
            </div>
        </div>
    </main>

    <main class="container">
        <div class="hero">
            <h1>欢迎来到博客</h1>
            <p>分享关于你的奇妙冒险</p>
        </div>

        <div class="post-card">
            <h2><i class="fa-solid fa-file-lines"></i> 最新博文</h2>
            <ul class="post-list">
                <?php if (!empty($allPosts)) : ?>
                    <?php foreach ($allPosts as $post) : ?>
                        <li class="post-item">
                            <div class="post-title">
                                <a href="./post_detail.php?id=<?php echo htmlspecialchars($post['id']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </div>
                            <div class="post-meta">
                                <span class="post-author">
                                    <?php echo htmlspecialchars($post['author'] ?? '匿名替身使者'); ?>
                                </span>
                                <span class="post-time">
                                    <?php echo $post['blogdate'] 
                                        ? htmlspecialchars(date($post['blogdate'])) : '未知时间'; 
                                    ?>
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li class="empty-post">
                        暂无博文发布...<br>
                        快来成为第一个分享JOJO感悟的人！
                    </li>
                <?php endif; ?>

            </ul>
            <div class="message-board-btn">
               <a href="../message_board.php" class="jojo-btn">
              <i class="fa-solid fa-comments"></i> 前往留言板
            </a>
</div>
        </div>
    </main>
</body>
</html>