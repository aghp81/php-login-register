<?php
require 'config.php';

$error = '';
$success = '';

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (empty($password) || empty($confirm_password)) {
            $error = 'لطفا تمام فیلدها را پر کنید.';
        } elseif ($password !== $confirm_password) {
            $error = 'پسوردها مطابقت ندارند.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $error = 'پسورد باید حداقل ۸ کاراکتر و شامل حروف بزرگ، کوچک، عدد و کاراکترهای خاص باشد.';
        } else {
            // به‌روزرسانی پسورد در جدول users
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
            $stmt->execute([$hashed_password, $email]);

            // حذف کد یک‌بار مصرف از جدول password_resets
            $stmt = $pdo->prepare('DELETE FROM password_resets WHERE email = ?');
            $stmt->execute([$email]);

            $success = 'پسورد شما با موفقیت تغییر یافت. <a href="login.php">وارد شوید</a>';
            echo "<script>
                    alert('پسورد شما با موفقیت تغییر یافت');
                    window.location.href='login.php';
                    </script>";
        }
    }
} else {
    $error = 'درخواست نامعتبر است.';
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغییر پسورد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">تغییر پسورد</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">پسورد جدید</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">تکرار پسورد جدید</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">تغییر پسورد</button>
        </form>
    </div>
</body>
</html>