<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Đăng nhập | E-Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
  <div class="card">
    <form action="src/handlers/login_handler.php" method="post" id="loginForm">
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
          if ($error == "notverified") $error_message = "Tài khoản chưa xác thực email!";
          elseif ($error == "banned") $error_message = "Tài khoản đã bị cấm!";
          elseif ($error == "wrongcreds") $error_message = "Email hoặc mật khẩu không khớp!";
        }
        ?>
        <div class="error" id="errorMsg"><?php echo $error_message; ?></div>
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
  <script src="public/js/validate.js"></script>
</body>
</html>


