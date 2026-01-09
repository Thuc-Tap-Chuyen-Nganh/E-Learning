<?php
  require "../../config/config.php";
  require "../../vendor/autoload.php";

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Validate Email
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header("Location: " . BASE_URL . "forgot.php");
      exit();
    }

    // 1. Tìm user có email này và đang active
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? AND status='active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close(); // Đóng statement cũ

    // Nếu tìm thấy user
    if ($user) {
      $user_id = $user['user_id'];

      // 2. Xóa token cũ (nếu có)
      $stmt_del = $conn->prepare("DELETE FROM user_tokens WHERE user_id = ? AND token_type = 'password_reset'");
      $stmt_del->bind_param("i", $user_id);
      $stmt_del->execute();
      $stmt_del->close();
      
      // --- [ĐÃ SỬA] Xóa dòng $user_id = $stmt->insert_id; gây lỗi ở đây ---

      // 3. TẠO OTP MỚI
      $otp = rand(100000, 999999);
      $otp_hash = hash('sha256', $otp);
      $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 phút

      // 4. Lưu OTP vào Database
      $stmt_token = $conn->prepare("INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) VALUES (?, ?, 'password_reset', ?)");
      $stmt_token->bind_param("iss", $user_id, $otp_hash, $expires_at);
      $stmt_token->execute();
      $stmt_token->close();

      // 5. Gửi Email
      // --- [ĐÃ SỬA] Bỏ dòng require autoload bị lặp ---
      $mail = new PHPMailer(true);
      try {
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
          $mail->Subject = 'Mã xác thực đặt lại mật khẩu';
          $mail->Body    = "Xin chào,<br>Mã OTP đặt lại mật khẩu của bạn là: <b style='font-size: 20px; color: blue;'>$otp</b><br>Mã có hiệu lực trong 15 phút.";
          
          $mail->send();

          // CHUYỂN HƯỚNG SANG TRANG NHẬP OTP
          // --- [ĐÃ SỬA] type=reset để phân biệt với đăng ký ---
          header("Location: " . BASE_URL . "otp.php?email=" . urlencode($email) . "&type=reset");
          exit();
      }
      catch (Exception $e){
          // Log lỗi nếu cần thiết
      }
    }

    // Để bảo mật (tránh dò email), dù có user hay không cũng báo là "đã gửi" hoặc chuyển hướng
    // Nếu mail gửi thất bại hoặc user không tồn tại, code sẽ chạy xuống đây
    header("Location: " . BASE_URL . "forgot.php?status=sent");
    exit();
  }
  else{
    header("Location: " . BASE_URL . "index.php");
    exit();
  }
?>