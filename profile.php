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
    <title>پروفایل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">پروفایل کاربری</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">نام: <?php echo htmlspecialchars($user['name']); ?></h5>
                <p class="card-text">ایمیل: <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="card-text"> <a href="reset_password.php">تغییر رمز عبور</a></p>
                <a href="logout.php" class="btn btn-danger">خروج</a>
            </div>
        </div>
    </div>
</body>
</html>