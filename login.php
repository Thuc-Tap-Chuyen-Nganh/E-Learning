<?php
require_once 'config/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Đăng nhập | E-Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/login.css?v=<?= filemtime('assets/css/login.css') ?>">
</head>
<body>
  <div class="card">
    <form action="<?= BASE_URL ?>logic/auth/login.php" method="post" id="loginForm">
      <div class="header">
        <h1><i class="fas fa-user-graduate"></i> Đăng nhập</h1>
        <p>Vào hệ thống E-Learning để học lập trình</p>
      </div>
      <div class="form">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="example@elearning.com" />
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input type="password" id="password" name="password" placeholder="••••••••" />
        </div>
        <a href="forgot.php" class="link">Quên mật khẩu?</a>
        <?php
        $error_message = ""; 
        if (isset($_GET['error'])){
          $error = $_GET['error'];
          if ($error == "notverified"){
            $email_resend = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
            $error_message = "Tài khoản chưa xác thực! <a href='" . BASE_URL . "logic/auth/resend_otp.php?email=$email_resend&type=register' class='link'>Gửi mã kích hoạt ngay</a>";
          }
          elseif ($error == "banned") $error_message = "Tài khoản đã bị cấm!";
          elseif ($error == "wrongcreds") $error_message = "Email hoặc mật khẩu không khớp!";
        }
        ?>
        <div class="alert alert-danger" id="errorMsg" style="<?php echo empty($error_message) ? 'display: none;' : ''; ?>"><?php echo $error_message; ?></div>
        <button class="btn">Đăng nhập</button>
        <div class="register-link">
          Chưa có tài khoản? <a href="register.php" class="link">Đăng ký ngay</a>        
        </div>
        <a href="index.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Quay lại trang chủ
        </a>
      </div>
    </form>
    
  </div>
  <script src="assets/js/validate.js"></script>
</body>
</html>



