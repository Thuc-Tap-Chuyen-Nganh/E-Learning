<?php
session_start(); // Dùng session để quản lý phiên đăng nhập
require_once '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Tìm người dùng bằng email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Kiểm tra email có tồn tại và mật khẩu có khớp không
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Mật khẩu khớp! Kiểm tra 'status'
        if ($user['status'] == 'pending') {
            header("Location: " . BASE_URL . "login.php?error=notverified");
            exit();
        }
        if ($user['status'] == 'banned') {
            header("Location: " . BASE_URL . "login.php?error=banned");
            exit();
        }
        if ($user['status'] == 'active') {
            // ĐĂNG NHẬP THÀNH CÔNG
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['avatar'] = $user['avatar'];
            
            header("Location: " . BASE_URL . "student/my_courses.php");
            exit();
        }

    } else {
        // Email không tồn tại HOẶC mật khẩu sai
        header("Location: " . BASE_URL . "login.php?error=wrongcreds");
        exit();
    }

} else {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>