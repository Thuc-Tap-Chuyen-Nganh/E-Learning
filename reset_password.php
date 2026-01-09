<?php
session_start();
require_once 'config/config.php';

// 1. KIỂM TRA QUYỀN TRUY CẬP (SESSION TỪ BƯỚC OTP)
// Nếu chưa qua bước OTP thành công thì đá về trang quên mật khẩu
if (!isset($_SESSION['allow_reset_password']) || !isset($_SESSION['reset_email'])) {
    header("Location: " . BASE_URL . "forgot.php");
    exit();
}

$error_message = "";

// 2. XỬ LÝ KHI NGƯỜI DÙNG BẤM "LƯU MẬT KHẨU" (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $email = $_SESSION['reset_email'];

    // Validate dữ liệu
    if (empty($password) || empty($confirm)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin.";
    } elseif (strlen($password) < 6) {
        $error_message = "Mật khẩu phải có ít nhất 6 ký tự.";
    } elseif ($password !== $confirm) {
        $error_message = "Mật khẩu nhập lại không khớp.";
    } else {
        // --- LOGIC CẬP NHẬT MẬT KHẨU ---
        
        // Băm mật khẩu mới
        $new_hash = password_hash($password, PASSWORD_DEFAULT);

        // Cập nhật vào DB theo Email (lấy từ session)
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_hash, $email);

        if ($stmt->execute()) {
            // Thành công -> Xóa session quyền reset để không cho back lại
            unset($_SESSION['allow_reset_password']);
            unset($_SESSION['reset_email']);

            // Chuyển về trang đăng nhập kèm thông báo
            header("Location: " . BASE_URL . "login.php?status=reset_success");
            exit();
        } else {
            $error_message = "Lỗi hệ thống. Vui lòng thử lại sau.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt lại mật khẩu | EduTech</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/login.css?v=<?= time() ?>">
</head>
<body>
  <div class="card">
    <form action="" method="post">
      <div class="header">
        <h1><i class="fas fa-lock"></i> Đặt lại mật khẩu</h1>
        <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
      </div>
      
      <div class="form">
        <div class="form-group">
          <label for="password">Mật khẩu mới</label>
          <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự" required />
        </div>
        
        <div class="form-group">
          <label for="confirm">Nhập lại mật khẩu</label>
          <input type="password" id="confirm" name="confirm" placeholder="Xác nhận mật khẩu" required />
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
            </div>
        <?php endif; ?>
        
        <button type="submit" class="btn">Lưu mật khẩu mới</button>
      </div>
    </form>
  </div>
</body>
</html>
