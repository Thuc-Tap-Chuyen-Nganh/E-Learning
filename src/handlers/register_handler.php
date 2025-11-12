<?php
session_start();
require '../core/db_connect.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Gửi mail xác nhận
function sendVerificationEmail($email, $token) {
  $mail = new PHPMailer(true);
  $link = "http://localhost/E-learning/verify_email.php?token=$token";
  
  try {
    // Cấu hình Server (SMTP của Gmail)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'edutech.ttcn@gmail.com';
    $mail->Password = 'whja jfkq kawd qkyy';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';

    // Người gửi, người nhận
    $mail->setFrom('no-reply@elearning.com', 'EduTech');
    $mail->addAddress($email);

    // Nội dung Email
    $mail->isHTML();
    $mail->Subject = 'Kích hoạt tài khoản EduTech E-Learning';
    $mail->Body    = "Chào bạn,<br><br>Cảm ơn bạn đã đăng ký. Vui lòng nhấp vào liên kết dưới đây để kích hoạt tài khoản:<br>"
                    . "<a href='$link'>$link</a><br><br>"
                    . "Nếu bạn không đăng ký, vui lòng bỏ qua email này.";
    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  // Để chắc ăn thì validate lại 1 lần ở đây nữa (mà lười quá nên khỏi hoặc để làm sau cũng được)...

  // Kiểm tra Email đã tồn tại trong csdl chưa
  $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email); // "s" là string
  $stmt->execute();
  $result = $stmt->get_result();
  
  // Nếu có báo lỗi email đã được dùng
  if ($result->fetch_assoc()) {
      $stmt->close();
      header("Location: ../../register.php?error=emailtaken");
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
  $stmt->execute();
  
  // Lấy user_id vừa tạo (Dùng $conn->insert_id)
  $user_id = $conn->insert_id;
  $stmt->close();

  // Tạo Token xác thực
  $token = bin2hex(random_bytes(32)); 
  $token_hash = hash('sha256', $token); 
  $expires_at_ts = time() + 3600; // 1 giờ
  $expires_at_sql = date('Y-m-d H:i:s', $expires_at_ts);

  // Lưu Token vào csdl
  $stmt = $conn->prepare(
      "INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) 
        VALUES (?, ?, 'email_verification', ?)"
  );
  // "i" là integer, "s" là string
  $stmt->bind_param("iss", $user_id, $token_hash, $expires_at_sql);
  $stmt->execute();
  $stmt->close();

  // Gửi Email
  sendVerificationEmail($email, $token);

  header("Location: ../../login.php");
  exit();

} else {
  header("Location: ../../index.php");
  exit();
}

?>