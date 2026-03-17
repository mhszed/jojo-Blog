<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>米斯达弹窗</title>
    <style>
        /* 遮罩层 */
        .alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* 弹窗容器 */
        .alert-box {
            width: 300px;
            padding: 30px;
            border-radius: 8px;
            background-image: url("./img/4444.jpg"); /* 确保图片路径正确（如img文件夹下的4444.jpg） */
            background-size: cover;
            background-position: center;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* 弹窗文本 */
        .alert-text {
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            margin-bottom: 25px;
            padding: 10px;
        }

        /* 按钮容器（用于并排显示按钮） */
        .button-group {
            display: flex;
            gap: 10px; /* 按钮间距 */
            justify-content: center;
        }

        /* 按钮样式 */
        .alert-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        /* 关闭按钮 */
        .alert-close {
            background-color: #ffd700;
            color: #8c0000;
        }

        .alert-close:hover {
            background-color: #fff;
        }

        /* 返回按钮 */
        .alert-back {
            background-color: #8c0000;
            color: #ffd700;
        }

        .alert-back:hover {
            background-color: #b40000;
        }
    </style>
</head>
<body>
    <!-- 自定义弹窗 -->
    <div class="alert-overlay" id="customAlert">
        <div class="alert-box">
            <div class="alert-text">米斯达:?????</div>
            <!-- 按钮组：关闭弹窗 + 返回test2.php -->
            <div class="button-group">
                <button class="alert-btn alert-close" onclick="window.location.href='./index.php'">关闭</button>
                <button class="alert-btn alert-back" onclick="window.location.href='./test2.php'">返回</button>
            </div>
        </div>
    </div>

    <script>
        // 页面加载后自动显示弹窗
        window.onload = function() {
            document.getElementById("customAlert").style.display = "flex";
        };


    </script>
</body>
</html>