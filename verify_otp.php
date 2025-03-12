<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);

    if (empty($email) || empty($otp)) {
        $error = 'لطفا ایمیل و کد یک‌بار مصرف را وارد کنید.';
    } else {
        // چک کردن اعتبار کد یک‌بار مصرف
        $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE email = ? AND otp = ? AND created_at >= NOW() - INTERVAL 10 MINUTE');
        $stmt->execute([$email, $otp]);
        $reset_request = $stmt->fetch();

        if (!$reset_request) {
            $error = 'کد یک‌بار مصرف نامعتبر یا منقضی شده است.';
        } else {
            // هدایت کاربر به صفحه تغییر پسورد
            header("Location: update_password.php?email=" . urlencode($email));
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأیید کد یک‌بار مصرف</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">تأیید کد یک‌بار مصرف</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">ایمیل</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="otp" class="form-label">کد یک‌بار مصرف</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary">تأیید کد</button>
        </form>
    </div>
</body>
</html>