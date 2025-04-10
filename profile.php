<?php
session_start(); // شروع session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // اگر کاربر وارد نشده باشد، به صفحه لاگین هدایت می‌شود
    exit;
}

require 'config.php';

// دریافت اطلاعات کاربر فعلی
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$current_user = $stmt->fetch();

// بررسی وجود user_role در session
$user_role = $_SESSION['user_role'] ?? 'user'; // اگر user_role وجود نداشت، مقدار پیش‌فرض 'user' در نظر گرفته می‌شود

// دریافت لیست همه کاربران (فقط برای ادمین)
$users = [];
if ($current_user['role'] == 'admin') {
    $stmt = $pdo->query('SELECT * FROM users');
    $users = $stmt->fetchAll();
}
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
            margin-right: 250px;
            /* فاصله از سایدبار */
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
            <?php if ($user_role == 'admin') : ?>
                <li class="nav-item">
                    <a class="nav-link" href="#">مدیریت کاربران</a>
                </li>
            <?php endif; ?>
            <?php if ($current_user['role'] == 'admin' || $current_user['role'] == 'editor') : ?>
                <li class="nav-item">
                    <a class="nav-link" href="#">مدیریت محتوا</a>
                </li>
                
            <?php endif; ?>
            <li class="nav-item">
                    <a class="nav-link" href="reset_password.php"> تغییر پسورد</a>
                </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">خروج</a>
            </li>
        </ul>
    </div>

    <!-- محتوای اصلی -->
    <div class="main-content">
        <h1 class="mb-4">پروفایل کاربری</h1>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">اطلاعات کاربر</h5>
                <p class="card-text">نام: <strong><?php echo htmlspecialchars($current_user['name']); ?></strong></p>
                <p class="card-text">ایمیل: <strong><?php echo htmlspecialchars($current_user['email']); ?></strong></p>
                <p class="card-text">نقش: <strong><?php echo htmlspecialchars($current_user['role']); ?></strong></p>
                <a href="#" class="btn btn-primary">ویرایش پروفایل</a>
            </div>
        </div>

        <!-- بخش مدیریت کاربران (فقط برای ادمین) -->
        <?php if ($current_user['role'] == 'admin') : ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">لیست کاربران</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>نام</th>
                                <th>ایمیل</th>
                                <th>نقش</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">ویرایش</a>
                                        <!-- <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('آیا مطمئن هستید می‌خواهید این کاربر را حذف کنید؟')">
                                            حذف
                                        </a> -->
                                        <!-- روش امن تر برای حذف کاربر -->
                                        <a href="confirm_delete.php?id=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('آیا مطمئن هستید می‌خواهید این کاربر را حذف کنید؟')">
                                            حذف
                                        </a>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>