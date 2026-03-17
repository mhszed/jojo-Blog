<?php
require_once __DIR__ . '/inc/common.php';
session_start();
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
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); /* 顶级缓动，模拟物理运动 */
        }

        /* 背景：复古网点纸+金属划痕，JOJO原著质感 */
        body {
            background-color: #050505;
            background-image: 
                /* 网点纸纹理 */
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 50m-40 0a40 40 0 1 0 80 0a40 40 0 1 0 -80 0' fill='none' stroke='%23333' stroke-width='0.5'/%3E%3C/svg%3E"),
                /* 金属划痕纹理 */
                url("data:image/svg+xml,%3Csvg width='200' height='200' viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10 10L190 190M20 180L180 20M50 10L150 190M70 190L130 10M100 10L100 190M130 10L70 190M150 190L50 10M180 20L20 180M190 190L10 10' fill='none' stroke='%23444' stroke-width='0.3'/%3E%3C/svg%3E"),
                /* 渐变光效 */
                radial-gradient(circle at 20% 30%, rgba(230, 0, 0, 0.25) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 215, 0, 0.2) 0%, transparent 50%);
            font-family: 'Segoe UI', 'Microsoft YaHei', 'Arial Black', sans-serif;
            color: #fdfdfd;
            min-height: 100vh;
            padding-bottom: 120px;
            overflow-x: hidden;
            perspective: 1000px; /* 开启3D透视，让元素有空间感 */
        }

        /* 导航栏：复古金属铭牌质感，带磨损和反光 */
        .nav {
            background: linear-gradient(135deg, #880000 0%, #550000 50%, #880000 100%);
            background-image: 
                linear-gradient(135deg, #880000 0%, #550000 50%, #880000 100%),
                /* 金属拉丝纹理 */
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h100v100H0V0zm10 10h80v80H10V10z' fill='none' stroke='%23440000' stroke-width='1'/%3E%3Cpath d='M20 20h20v20H20V20zm20 20h20v20H40V40zm20 20h20v20H60V60z' fill='none' stroke='%23440000' stroke-width='0.5'/%3E%3C/svg%3E");
            padding: 22px 40px;
            box-shadow: 
                0 8px 30px rgba(150, 0, 0, 0.9),
                0 0 25px rgba(255, 215, 0, 0.5),
                inset 0 3px 8px rgba(255, 255, 255, 0.15), /* 上反光 */
                inset 0 -3px 8px rgba(0, 0, 0, 0.7); /* 下阴影 */
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 4px solid;
            border-image: linear-gradient(90deg, transparent, #ffd700, #ffb300, #ffd700, transparent) 1;
            transform: translateY(0) translateZ(0);
            transition: transform 0.3s ease;
        }

        /* 导航栏滚动时缩小，增强空间感 */
        body.scroll .nav {
            padding: 15px 40px;
            transform: translateY(0) translateZ(0) scale(0.98);
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
            /* 文字金属浮雕效果 */
            -webkit-text-stroke: 0.5px #ffb300;
        }

        .nav-link:hover {
            background: linear-gradient(45deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0.4) 50%, rgba(255, 215, 0, 0.2) 100%);
            transform: translateY(-6px) scale(1.05) rotateX(5deg); /* 3D hover效果 */
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
            /* 图标金属质感 */
            filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.8));
        }

        /* 主图片区域：3D空间+体积光+粒子特效，替身觉醒既视感 */
        .main-image {
            width: 100%;
            padding: 60px 20px;
            text-align: center;
            position: relative;
            margin-bottom: 50px;
            perspective: 1500px;
        }

        /* 主图外层容器：3D空间载体 */
        .main-image-outer {
            max-width: 1050px;
            margin: 0 auto;
            position: relative;
            transform-style: preserve-3d;
            animation: mainFloat 8s ease-in-out infinite;
        }

        /* 主图内层容器：实体质感+体积光 */
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

        /* 体积光：模拟阳光穿透效果 */
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

        /* 替身光环：外层能量场 */
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

        /* 粒子特效容器：模拟替身能量粒子 */
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

        /* 主图：实体金属边框+反光磨损 */
        .main-image img {
            max-width: 100%;
            max-height: 75vh;
            object-fit: contain;
            border: 12px solid;
            /* 高级金属边框：拉丝+渐变+磨损 */
            border-image: linear-gradient(135deg, #ffd700, #ffb300, #ffd700, #ff9900, #ffd700, #ffb300, #ffd700) 1;
            border-radius: 18px;
            box-shadow: 
                0 0 40px rgba(255, 215, 0, 0.85), /* 核心发光 */
                0 12px 60px rgba(0, 0, 0, 0.98), /* 深沉投影 */
                inset 0 0 30px rgba(0, 0, 0, 0.7), /* 内阴影 */
                0 0 150px rgba(255, 215, 0, 0.25), /* 远距离柔光 */
                /* 金属边框反光 */
                inset 0 1px 5px rgba(255, 255, 255, 0.2);
            transition: transform 6s ease-in-out, box-shadow 5s ease, filter 4s ease;
            background-color: #0f0f0f;
            padding: 20px;
            filter: contrast(1.1) saturate(1.2) brightness(1.05);
            transform: translateZ(10px); /* 3D提升，突出于容器 */
            backface-visibility: hidden;
        }

        /* 鼠标跟随：主图轻微旋转，模拟替身感知 */
        .main-image img:hover {
            transform: translateZ(20px) scale(1.08) rotate(1deg);
            box-shadow: 
                0 0 60px rgba(255, 215, 0, 0.95),
                0 15px 70px rgba(0, 0, 0, 0.98),
                inset 0 0 35px rgba(0, 0, 0, 0.75),
                0 0 200px rgba(255, 215, 0, 0.35);
            filter: contrast(1.2) saturate(1.3) brightness(1.1);
        }

        /* 主图虚线边框：滚动+呼吸+3D旋转 */
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

        /* 主图英文标题：金属铭牌质感 */
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
            /* 文字金属浮雕+反光 */
            -webkit-text-stroke: 1px #ffb300;
            animation: textFloat 7s ease-in-out infinite reverse;
        }

        /* 主图标语：3D金属牌质感 */
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
            /* 文字金属浮雕 */
            -webkit-text-stroke: 0.8px #ffb300;
            animation: 
                textFloat 6s ease-in-out infinite,
                textGlow 4s ease-in-out infinite;
            transform: translateX(-50%) translateZ(5px);
        }

        /* 子图片区域：3D卡片+hover爆发特效 */
        .sub-images {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            perspective: 1200px;
        }

        .sub-image-item {
            flex: 1;
            min-width: 280px;
            text-align: center;
            position: relative;
            padding: 25px;
            border-radius: 20px;
            background: linear-gradient(45deg, #111 0%, #050505 50%, #111 100%);
            box-shadow: 
                0 8px 25px rgba(0, 0, 0, 0.95),
                inset 0 0 20px rgba(0, 0, 0, 0.8),
                0 0 10px rgba(255, 215, 0, 0.1);
            border: 2px solid rgba(255, 215, 0, 0.3);
            overflow: hidden;
            transform-style: preserve-3d;
            transform: translateZ(0) rotateY(0deg);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* 子图hover：3D旋转+能量爆发 */
        .sub-image-item:hover {
            transform: translateZ(20px) rotateY(5deg) scale(1.03);
            border-color: #ffd700;
            box-shadow: 
                0 12px 35px rgba(0, 0, 0, 0.95),
                inset 0 0 25px rgba(0, 0, 0, 0.8),
                0 0 30px rgba(255, 215, 0, 0.5);
        }

        /* 子图能量光环：hover扩散 */
        .sub-image-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 250%;
            height: 250%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.3) 0%, rgba(255, 69, 0, 0.1) 70%, transparent 85%);
            z-index: 0;
            transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1);
            filter: blur(20px);
        }

        .sub-image-item:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }

        /* 子图图片：实体质感+反光 */
        .sub-image-item img {
            width: 100%;
            max-width: 350px;
            height: auto;
            object-fit: cover;
            border: 6px solid #ffd700;
            border-radius: 12px;
            box-shadow: 
                0 0 20px rgba(255, 215, 0, 0.5),
                0 10px 30px rgba(0, 0, 0, 0.98),
                inset 0 0 20px rgba(0, 0, 0, 0.6),
                inset 0 1px 3px rgba(255, 255, 255, 0.1);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            background-color: #0f0f0f;
            padding: 10px;
            position: relative;
            z-index: 1;
            filter: contrast(1.1) saturate(1.15);
            transform: translateZ(5px);
        }

        .sub-image-item:hover img {
            transform: translateZ(15px) scale(1.15) rotate(3deg);
            box-shadow: 
                0 0 40px rgba(255, 215, 0, 0.9),
                0 15px 40px rgba(0, 0, 0, 0.98),
                inset 0 0 25px rgba(0, 0, 0, 0.7),
                inset 0 1px 3px rgba(255, 255, 255, 0.15);
            border-color: #e63946;
            filter: contrast(1.25) saturate(1.3) brightness(1.1);
        }

        .image-caption {
            margin-top: 25px;
            font-size: 1.2rem;
            color: #ffd700;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.98);
            letter-spacing: 3px;
            font-weight: 800;
            text-transform: capitalize;
            padding: 12px 0;
            border-top: 3px dashed rgba(255, 215, 0, 0.7);
            border-bottom: 3px dashed rgba(255, 215, 0, 0.7);
            position: relative;
            z-index: 1;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0.7) 100%);
            border-radius: 12px;
            /* 文字金属浮雕 */
            -webkit-text-stroke: 0.5px #ffb300;
        }

        /* 粒子动画：动态生成能量粒子（CSS模拟） */
        @keyframes particles {
            0% { transform: translateY(100%) translateX(random(100%)) scale(random(0.5, 1.5)); opacity: 0; }
            50% { opacity: random(0.3, 0.7); }
            100% { transform: translateY(-100%) translateX(random(100%)) scale(random(0.5, 1.5)); opacity: 0; }
        }

        /* 核心动画库：细腻、有物理感 */
        /* 主图3D浮动 */
        @keyframes mainFloat {
            0%, 100% { transform: rotateX(2deg) rotateY(3deg); }
            50% { transform: rotateX(-2deg) rotateY(-3deg); }
        }

        /* 体积光位移 */
        @keyframes lightShift {
            0% { background-position: 30% 30%; }
            100% { background-position: 70% 70%; }
        }

        /* 替身光环呼吸 */
        @keyframes auraPulse {
            0%, 100% { opacity: 0.4; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
        }

        /* 图标脉冲+旋转 */
        @keyframes iconPulse {
            0% { transform: rotate(22deg) scale(1.4); }
            100% { transform: rotate(28deg) scale(1.6); }
        }

        /* 文字浮动 */
        @keyframes textFloat {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-12px); }
        }

        /* 文字发光呼吸 */
        @keyframes textGlow {
            0%, 100% { text-shadow: 0 0 15px rgba(255, 215, 0, 0.9), 3px 3px 6px rgba(0, 0, 0, 0.98); }
            50% { text-shadow: 0 0 30px rgba(255, 215, 0, 1), 3px 3px 8px rgba(0, 0, 0, 0.98); }
        }

        /* 虚线滚动+旋转 */
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

        /* 加载动画：3D渐入+模糊 */
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

        .sub-image-item img {
            animation-delay: calc(0.3s * var(--item-index));
        }

        /* 响应式优化：高级感不打折 */
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

            .sub-images {
                gap: 30px;
            }

            .sub-image-item {
                min-width: 100%;
                padding: 20px;
                border-width: 1px;
            }

            .sub-image-item img {
                max-width: 100%;
                border-width: 5px;
                padding: 8px;
            }

            .nav-link {
                font-size: 1.3rem;
                padding: 12px 25px;
                gap: 15px;
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
        }
    </style>
    <script>
        // 1. 滚动时导航栏缩小效果
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                document.body.classList.add('scroll');
            } else {
                document.body.classList.remove('scroll');
            }
        });

        // 2. 主图鼠标跟随：模拟替身感知互动
        const mainImage = document.querySelector('.main-image img');
        const mainOuter = document.querySelector('.main-image-outer');
        if (mainImage && mainOuter) {
            document.addEventListener('mousemove', (e) => {
                const x = (e.clientX / window.innerWidth - 0.5) * 10; // 水平偏移量
                const y = (e.clientY / window.innerHeight - 0.5) * 10; // 垂直偏移量
                // 轻微旋转，不夸张
                mainOuter.style.transform = `rotateX(${y * 0.3}deg) rotateY(${x * 0.3}deg)`;
            });
        }

        // 3. 粒子特效：动态生成JOJO风格能量粒子（JS实现，更真实）
        function createParticles() {
            const container = document.querySelector('.particle-container');
            if (!container) return;

            // 生成50个粒子
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                // 随机粒子大小、位置、动画延迟
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

        // 页面加载完成后创建粒子
        window.addEventListener('load', createParticles);
    </script>
</head>
<body>
    <!-- 导航栏：点击跳转到text.php -->
    <header class="nav">
        <div class="nav-container">
            <a href="./text.php" class="nav-link">进入博客主页</a>
        </div>
    </header>

    <!-- 主图片区域：3D空间+体积光+粒子特效 -->
    <main class="main-image">
        <div class="main-image-outer">
            <div class="main-image-wrapper">
                <div class="particle-container"></div> <!-- 粒子特效容器 -->
                <div class="volume-light"></div> <!-- 体积光 -->
                <div class="stand-aura"></div> <!-- 替身光环 -->
                <div class="main-image-slogan">人类的赞歌就是勇气的赞歌，人类的伟大就是勇气的伟大</div>
                <img src="./img/755323.jpg" alt="JOJO的奇妙冒险" title="JOJO的奇妙冒险">
            </div>
        </div>
    </main>

    <!-- 新增图片组：3D卡片+hover爆发特效 -->
    <section class="sub-images">
        <!-- 第一张新增图片 -->
        <div class="sub-image-item" style="--item-index: 1">
            <img src="./img/833606.png" alt="JOJO主题图片1" title="JOJO主题图片1">
            <div class="image-caption">战斗潮流 乔瑟夫・乔斯达</div>
        </div>

        <!-- 第二张新增图片 -->
        <div class="sub-image-item" style="--item-index: 2">
            <img src="./img/774552.png" alt="JOJO主题图片2" title="JOJO主题图片2">
            <div class="image-caption">波波的不妙冒险</div>
        </div>

        <!-- 第三张新增图片 -->
        <div class="sub-image-item" style="--item-index: 3">
            <img src="./img/1081460.jpg" alt="JOJO主题图片3" title="JOJO主题图片3">
            <div class="image-caption">乔鲁诺乔巴纳・黄金体验镇魂曲</div>
        </div>

        <!-- 第四张新增图片：834598.png -->
        <div class="sub-image-item" style="--item-index: 4">
            <img src="./img/834598.png" alt="JOJO主题图片4" title="JOJO主题图片4">
            <div class="image-caption">东方仗助 · 疯狂钻石</div>
        </div>

        <!-- 新增的第五张图片：571002.jpg -->
        <div class="sub-image-item" style="--item-index: 5">
            <img src="./img/571002.jpg" alt="JOJO主题图片5" title="JOJO主题图片5">
            <div class="image-caption">四蛋·软又湿</div>
        </div>

        <div class="sub-image-item" style="--item-index: 6">
            <img src="./img/1319757.jpeg" alt="JOJO主题图片6" title="JOJO主题图片6">
            <div class="image-caption">石之海 · 宿命之战</div>
        </div>
    </section>
</body>
</html>