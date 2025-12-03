<?php
// src/handlers/update_profile_handler.php
session_start();
require '../core/db_connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username']);

    // 2. Validate
    if (empty($username)) {
        header("Location: ../../student/profile.php?error=empty");
        exit();
    }

    // 3. Cập nhật vào CSDL
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
    $stmt->bind_param("si", $username, $user_id);

    if ($stmt->execute()) {
        // Cập nhật lại session để hiển thị tên mới ngay lập tức
        $_SESSION['username'] = $username;
        
        header("Location: ../../student/profile.php?status=success");
        exit();
    } else {
        header("Location: ../../student/profile.php?error=db_error");
        exit();
    }
} else {
    header("Location: ../../student/profile.php");
    exit();
}
?>