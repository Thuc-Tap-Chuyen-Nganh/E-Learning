<?php
session_start();
require_once '../../config/config.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  // Để chắc ăn thì validate lại 1 lần ở đây nữa (mà lười quá nên khỏi hoặc để làm sau cũng được)...

  // Kiểm tra Email đã tồn tại trong csdl chưa
  $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email); 
  $stmt->execute();
  $result = $stmt->get_result();
  
  // Nếu có báo lỗi email đã được dùng
  if ($result->fetch_assoc()) {
      $stmt->close();
      header("Location: " . BASE_URL . "register.php?error=emailtaken");
      exit();
  }
  $stmt->close(); // Đóng statement

  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // Thêm người dùng vào csdl
  $stmt = $conn->prepare(
      "INSERT INTO users (username, email, password_hash, status) 
        VALUES (?, ?, ?, 'pending')"
  );
  $stmt->bind_param("sss", $username, $email, $password_hash);
  
  if ($stmt->execute()) {
      $user_id = $stmt->insert_id;

      // TẠO OTP
      $otp = rand(100000, 999999);
      $otp_hash = hash('sha256', $otp);
      $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 phút

      // Lưu OTP
      $stmt_token = $conn->prepare("INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) VALUES (?, ?, 'email_verification', ?)");
      $stmt_token->bind_param("iss", $user_id, $otp_hash, $expires_at);
      $stmt_token->execute();

      // Gửi Email
      require "../../vendor/autoload.php";
      $mail = new PHPMailer(true);
      try {
          // Cấu hình mail server (Giữ nguyên cấu hình cũ của bạn)
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'edutech.ttcn@gmail.com';
          $mail->Password = 'whja jfkq kawd qkyy'; // Mật khẩu ứng dụng
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
          $mail->Port = 465;
          $mail->CharSet = 'UTF-8';

          $mail->setFrom('no-reply@elearning.com', 'EduTech');
          $mail->addAddress($email, $name);
          $mail->isHTML(true);
          $mail->Subject = 'Mã xác thực đăng ký tài khoản';
          $mail->Body    = "Xin chào $name,<br>Mã xác thực (OTP) của bạn là: <b style='font-size: 20px; color: blue;'>$otp</b><br>Mã có hiệu lực trong 15 phút.";
          
          $mail->send();
      } catch (Exception $e) {
          // Log lỗi nếu cần
      }

      // CHUYỂN HƯỚNG SANG TRANG NHẬP OTP
      header("Location: " . BASE_URL . "otp.php?email=" . urlencode($email) . "&type=register");
      exit();
  }
} else {
  header("Location: " . BASE_URL . "index.php");
  exit();
}

?>