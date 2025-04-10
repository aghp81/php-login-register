<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

require 'config.php';

// دریافت اطلاعات کاربر برای ویرایش
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// پردازش فرم ویرایش
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
    $stmt->execute([$name, $email, $role, $user_id]);
    
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش کاربر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">ویرایش کاربر</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">نام</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">ایمیل</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">نقش</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>مدیر</option>
                    <option value="editor" <?= $user['role'] == 'editor' ? 'selected' : '' ?>>ویرایشگر</option>
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>کاربر عادی</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
            <a href="profile.php" class="btn btn-secondary">انصراف</a>
        </form>
    </div>
</body>
</html>