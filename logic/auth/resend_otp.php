<?php
require_once '../../config/config.php';
require "../../vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_GET['email'] ?? '';
$type = $_GET['type'] ?? 'register';

if (!$email) die("Thiếu email");

// Lấy user_id
$stmt = $conn->prepare("SELECT user_id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    $user_id = $user['user_id'];
    $db_type = ($type == 'register') ? 'email_verification' : 'password_reset';

    // Xóa token cũ
    $conn->query("DELETE FROM user_tokens WHERE user_id = $user_id AND token_type = '$db_type'");

    // Tạo OTP mới
    $otp = rand(100000, 999999);
    $otp_hash = hash('sha256', $otp);
    $expires = date('Y-m-d H:i:s', time() + 900);

    $ins = $conn->prepare("INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) VALUES (?, ?, ?, ?)");
    $ins->bind_param("isss", $user_id, $otp_hash, $db_type, $expires);
    $ins->execute();

    // Gửi mail (Copy code mail ở trên)
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'edutech.ttcn@gmail.com';
    $mail->Password = 'whja jfkq kawd qkyy';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('no-reply@elearning.com', 'EduTech');
    $mail->addAddress($email); 
    $mail->isHTML(true);
    $mail->Subject = 'Gửi lại mã xác thực OTP';
    $mail->Body = "Mã OTP mới của bạn là: <b style='font-size: 20px; color: blue;'>$otp</b>";
    $mail->send();
}

// Quay lại trang nhập OTP
header("Location: " . BASE_URL . "otp.php?email=$email&type=$type&msg=resent");
?>