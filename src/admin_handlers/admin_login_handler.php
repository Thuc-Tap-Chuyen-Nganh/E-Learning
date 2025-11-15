<?php
session_start();
require '../core/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../../admin/login.php?error=empty");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Chỉ kiểm tra 'active' (admin thì không 'pending')
        if ($user['status'] == 'active') {

            // Tạo một session riêng cho Admin
            $_SESSION['admin_id'] = $user['user_id'];
            $_SESSION['admin_name'] = $user['username'];
            
            // Chuyển hướng đến admin dashboard
            header("Location: ../../admin/admin_dashboard.php"); 
            exit();
        } 

    } else {
        // Email không tồn tại, sai mật khẩu, HOẶC không phải admin
        header("Location: ../../admin/index.php?error=wrongcreds");
        exit();
    }

} else {
    header("Location: ../../admin/login.php");
    exit();
}
?>