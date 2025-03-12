<?php
require 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // اعتبارسنجی فیلدها
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'لطفا تمام فیلدها را پر کنید.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'ایمیل وارد شده معتبر نیست.';
    } elseif ($password !== $confirm_password) {
        $error = 'پسوردها مطابقت ندارند.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error = 'پسورد باید حداقل ۸ کاراکتر و شامل حروف بزرگ، کوچک، عدد و کاراکترهای خاص باشد.';
    } else {
        // چک کردن وجود ایمیل در دیتابیس
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'این ایمیل قبلا ثبت شده است.';
        } else {
            // هش کردن پسورد و ذخیره کاربر در دیتابیس
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hashed_password]);
            header('Location: login.php');
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
    <title>ثبت نام</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">ثبت نام</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">نام</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">ایمیل</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">رمز عبور</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="form-text text-muted">
                    پسورد باید حداقل ۸ کاراکتر و شامل حروف بزرگ، کوچک، عدد و کاراکترهای خاص باشد.
                </small>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">تکرار رمز عبور</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">ثبت نام</button>
        </form>
        <p class="mt-3">قبلا ثبت نام کرده‌اید؟ <a href="login.php">وارد شوید</a></p>
    </div>
</body>
</html>