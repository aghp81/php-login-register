<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل کاربری</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- فونت فارسی -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <!-- استایل‌های سفارشی -->
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-right: 250px; /* فاصله از سایدبار */
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- سایدبار -->
    <div class="sidebar">
        <h4 class="text-center text-white">داشبورد</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#">پروفایل</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">تنظیمات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">خروج</a>
            </li>
        </ul>
    </div>

    <!-- محتوای اصلی -->
    <div class="main-content">
        <h1 class="mb-4">پروفایل کاربری</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">اطلاعات کاربر</h5>
                <p class="card-text">نام: <strong><?php echo htmlspecialchars($user['name']); ?></strong></p>
                <p class="card-text">ایمیل: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
                <a href="#" class="btn btn-primary">ویرایش پروفایل</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>