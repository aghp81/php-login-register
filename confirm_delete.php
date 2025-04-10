<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

require 'config.php';

if (!isset($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$user_id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأیید حذف کاربر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">تأیید حذف کاربر</h5>
                <p>آیا مطمئن هستید می‌خواهید کاربر <strong><?= htmlspecialchars($user['name']) ?></strong> را حذف کنید؟</p>
                
                <form action="delete_user.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn btn-danger">بله، حذف شود</button>
                    <a href="profile.php" class="btn btn-secondary">خیر، انصراف</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>