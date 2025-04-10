<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    die("دسترسی غیرمجاز");
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    try {
        $user_id = (int)$_POST['user_id'];
        
        // بررسی وجود کاربر
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $check_stmt->execute([$user_id]);
        
        if ($check_stmt->rowCount() > 0) {
            // جلوگیری از حذف خود مدیر
            if ($user_id == $_SESSION['user_id']) {
                die("شما نمی‌توانید حساب خود را حذف کنید");
            }
            
            $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delete_stmt->execute([$user_id]);
            
            if ($delete_stmt->rowCount() > 0) {
                $_SESSION['success'] = "کاربر با موفقیت حذف شد";
            } else {
                $_SESSION['error'] = "عملیات حذف انجام نشد";
            }
        } else {
            $_SESSION['error'] = "کاربر مورد نظر وجود ندارد";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "خطای پایگاه داده: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "درخواست نامعتبر";
}

header("Location: profile.php");
exit;
?>