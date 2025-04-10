<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // بررسی نهایی قبل از حذف
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    
    // همچنین می‌توانید لاگ این عملیات را ذخیره کنید
    // $log_stmt = $pdo->prepare('INSERT INTO logs (admin_id, action) VALUES (?, ?)');
    // $log_stmt->execute([$_SESSION['user_id'], "حذف کاربر با ID $user_id"]);
}

header('Location: profile.php');
exit;
?>