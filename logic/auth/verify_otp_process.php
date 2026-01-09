<?php
session_start();
require_once '../../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    $type = $_POST['type']; // 'register' hoặc 'reset'

    // 1. Tìm user
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        header("Location: " . BASE_URL . "otp.php?email=$email&type=$type&error=invalid");
        exit();
    }

    $user_id = $user['user_id'];
    
    // 2. Kiểm tra OTP
    $otp_hash = hash('sha256', $otp); 
    
    // Mapping loại token
    $db_token_type = ($type == 'register') ? 'email_verification' : 'password_reset';

    $stmt = $conn->prepare("SELECT * FROM user_tokens WHERE user_id = ? AND token_hash = ? AND token_type = ?");
    $stmt->bind_param("iss", $user_id, $otp_hash, $db_token_type);
    $stmt->execute();
    $token_data = $stmt->get_result()->fetch_assoc();

    if (!$token_data) {
        header("Location: " . BASE_URL . "otp.php?email=$email&type=$type&error=invalid");
        exit();
    }

    // Kiểm tra hết hạn
    if (strtotime($token_data['expires_at']) < time()) {
        header("Location: " . BASE_URL . "otp.php?email=$email&type=$type&error=expired");
        exit();
    }

    // --- OTP HỢP LỆ ---

    // Xóa OTP đã dùng
    $del = $conn->prepare("DELETE FROM user_tokens WHERE user_id = ? AND token_type = ?");
    $del->bind_param("is", $user_id, $db_token_type);
    $del->execute();

    if ($type == 'register') {
        // Kích hoạt tài khoản
        $upd = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $upd->bind_param("i", $user_id);
        $upd->execute();
        
        // Chuyển lại về trang OTP với trạng thái success
        header("Location: " . BASE_URL . "otp.php?email=$email&type=register&status=success");
    } else {
        // Reset mật khẩu -> Tạo Session
        $_SESSION['allow_reset_password'] = true;
        $_SESSION['reset_email'] = $email;
        
        // Chuyển lại về trang OTP với trạng thái success
        header("Location: " . BASE_URL . "otp.php?email=$email&type=reset&status=success");
    }
    exit();

} else {
    header("Location: " . BASE_URL . "index.php");
}
?>