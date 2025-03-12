<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'config.php';
require 'vendor/autoload.php'; // بارگذاری PHPMailer

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = 'لطفا ایمیل خود را وارد کنید.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'ایمیل وارد شده معتبر نیست.';
    } else {
        // چک کردن وجود ایمیل در دیتابیس
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if (!$stmt->fetch()) {
            $error = 'ایمیل وارد شده در سیستم وجود ندارد.';
        } else {
            // ایجاد کد یک‌بار مصرف (۶ رقمی)
            $otp = rand(100000, 999999);

            // ذخیره کد در جدول password_resets
            $stmt = $pdo->prepare('INSERT INTO password_resets (email, otp) VALUES (?, ?)');
            $stmt->execute([$email, $otp]);

            // ارسال ایمیل با استفاده از PHPMailer
            $mail = new PHPMailer(true);

            try {
                // تنظیمات سرور SMTP
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // آدرس سرور SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'aghp81@gmail.com'; // ایمیل شما
                $mail->Password = 'ivlqoescwfgfnibi'; // پسورد ایمیل شما
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // استفاده از TLS
                $mail->SMTPSecure = "tls";
                $mail->Port = 587; // پورت SMTP

                // تنظیمات ایمیل
                $mail->setFrom('laraveldevphp@gmail.com', 'abol'); // ایمیل و نام ارسال‌کننده
                $mail->addAddress($email); // ایمیل گیرنده
                $mail->isHTML(true); // فعال‌سازی محتوای HTML
                $mail->setLanguage("fa");
                $mail->CharSet = 'UTF-8'; // نمایش فارسی در عنوان ایمیل
                $mail->Subject = 'کد یک‌بار مصرف برای تغییر پسورد'; // موضوع ایمیل
                $mail->Body    = "کد یک‌بار مصرف شما: <strong>$otp</strong><br>این کد تنها ۱۰ دقیقه معتبر است."; // محتوای ایمیل

                $mail->send(); // ارسال ایمیل
                $success = 'کد یک‌بار مصرف به ایمیل شما ارسال شد.';
                echo "<script>
                    alert('کد یک‌بار مصرف به ایمیل شما ارسال شد');
                    window.location.href='verify_otp.php';
                    </script>";
            } catch (Exception $e) {
                $error = "خطا در ارسال ایمیل: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازیابی پسورد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">بازیابی پسورد</h2>
        <?php if ($error) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">ایمیل</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">دریافت کد یک‌بار مصرف</button>
        </form>
        <p class="mt-3"><a href="login.php">بازگشت به صفحه ورود</a></p>
    </div>
</body>

</html>