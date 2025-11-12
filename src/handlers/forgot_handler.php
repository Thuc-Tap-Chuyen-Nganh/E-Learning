<?php
  require "../core/db_connect.php";
  require "../../vendor/autoload.php";

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  // Hàm gửi email xác nhận reset passwd
  function sendPasswordResetEmail($email,$token){
    $mail = new PHPMailer(true);
    $link = "http://localhost/E-Learning/reset_password.php?token=$token";

    try{
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

      $mail->isHTML();
      $mail->Subject = 'Yêu cầu reset mật khẩu E-Learning';
      $mail->Body    = "Chúng tôi nhận được yêu cầu reset mật khẩu cho tài khoản của bạn.<br>"
                      . "Vui lòng nhấp vào link sau để đặt mật khẩu mới: <a href='$link'>$link</a><br>"
                      . "Link này sẽ hết hạn sau 30 phút.<br>"
                      . "Nếu bạn không yêu cầu, vui lòng bỏ qua email này.";
      $mail->send();
      return true;
    } catch(Exception $e){
      return false;
    }
  }

  if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email = $_POST['email'];
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header("Location: ../../forgot.php");
      exit();
    }

    // Tìm email có trong cơ sở dữ liệu để lấy user
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? AND status='active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Nếu tìm được user thì xóa token cũ đi, tạo mới và insert token mới vào csdl
    if ($user) {
      $user_id = $user['user_id'];

      // Xóa cũ
      $stmt_del = $conn->prepare("DELETE FROM user_tokens WHERE user_id = ? AND token_type = 'password_reset'");
      $stmt_del->bind_param("i", $user_id);
      $stmt_del->execute();
      $stmt_del->close();

      // Tạo mới
      $token = bin2hex(random_bytes(32));
      $token_hash = hash('sha256', $token);
      $expires_at_sql = date('Y-m-d H:i:s', time()+1800);

      // Insert vào csdl
      $stmt_ins = $conn->prepare("INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) VALUES (?, ?, 'password_reset', ?)");
      $stmt_ins->bind_param("iss", $user_id, $token_hash, $expires_at_sql);
      $stmt_ins->execute();
      $stmt_ins->close();

      // Gửi mail
      sendPasswordResetEmail($email, $token);
    }

    header("Location: ../../forgot.php?status=sent");
    exit();
  }
  else{
    header("Location: ../../index.php");
    exit();
  }
?>