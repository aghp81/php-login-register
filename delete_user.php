<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

require 'config.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // حذف کاربر از دیتابیس
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    
    header('Location: profile.php');
    exit;
}
?>