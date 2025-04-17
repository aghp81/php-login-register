<?php
require 'config.php';

// تنظیمات reCAPTCHA
// define('RECAPTCHA_SITE_KEY', '6LdJyRErAAAAAPbSQJOd6LjKhXv37DQHfZfb6Zwr');
// define('RECAPTCHA_SECRET_KEY', '6LdJyRErAAAAAPqAQmEpnAIMNXJInTZDmbGaIV73');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']); // نقش کاربر

    // اعتبارسنجی فیلدها
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
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
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hashed_password, $role]);

            $success = 'ثبت‌نام شما با موفقیت انجام شد. <a href="login.php">وارد شوید</a>';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">ثبت نام</h2>
        <?php if ($error) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
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
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            <!-- check box for show password -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="showPassword">
                <label class="form-check-label" for="showPassword">نمایش رمز عبور</label>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">تکرار رمز عبور</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="bi bi-eye-slash"></i><!-- آیکون چشم بسته (Bootstrap Icons) -->
                    </button>
                </div>
            </div>


            <div class="mb-3">
                <label for="role" class="form-label">نقش</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="user">کاربر عادی</option>
                    <option value="editor">ویرایشگر</option>
                    <option value="admin">مدیر</option>
                </select>
            </div>
            <div class="g-recaptcha mb-3" data-sitekey="SITE_KEY"></div>

            <button type="submit" class="btn btn-primary">ثبت نام</button>
        </form>
        <p class="mt-3">قبلا ثبت نام کرده‌اید؟ <a href="login.php">وارد شوید</a></p>
    </div>

    <!-- نمایش پسورد -->

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });
    </script>

    <!-- check box for show password -->
    <script>
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.type = this.checked ? 'text' : 'password';
            });
        });
    </script>

    <script>
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.type = this.checked ? 'text' : 'password';
            });
        });
    </script>
</body>

</html>