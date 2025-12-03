<?php
// src/handlers/change_password_handler.php
session_start();
require '../core/db_connect.php';

// 1. Bảo vệ
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Validate cơ bản
    if (empty($current_password) || empty($new_password)) {
        header("Location: ../../student/profile.php?pass_error=empty");
        exit();
    }

    if (strlen($new_password) < 6) {
        header("Location: ../../student/profile.php?pass_error=short");
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("Location: ../../student/profile.php?pass_error=mismatch");
        exit();
    }

    // 3. Kiểm tra MẬT KHẨU CŨ có đúng không
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($current_password, $user['password_hash'])) {
        // 4. Mật khẩu cũ đúng -> Tiến hành cập nhật
        
        // Hash mật khẩu mới (Tận dụng logic của reset_handler)
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $new_hash, $user_id);

        if ($update_stmt->execute()) {
            header("Location: ../../student/profile.php?pass_status=success");
            exit();
        } else {
            header("Location: ../../student/profile.php?pass_error=db_error");
            exit();
        }

    } else {
        // Mật khẩu cũ sai
        header("Location: ../../student/profile.php?pass_error=wrongcurrent");
        exit();
    }

} else {
    header("Location: ../../student/profile.php");
    exit();
}
?>