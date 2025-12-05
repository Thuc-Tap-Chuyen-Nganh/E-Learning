<?php
require_once 'config/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Đăng ký | E-Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/register.css?v=<?= filemtime('assets/css/register.css') ?>">
</head>
<body>
  <div class="card">
    <div class="header">
      <h1><i class="fas fa-user-plus"></i> Đăng ký</h1>
      <p>Tạo tài khoản để bắt đầu học lập trình</p>
    </div>
    <div class="form">
      <form action="<?= BASE_URL ?>logic/auth/register.php" method="post" id="registerForm">
        <div class="form-group">
          <label for="name">Họ và tên</label>
          <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="example@elearning.com" />
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự" />
        </div>
        <div class="form-group">
          <label for="confirm">Nhập lại mật khẩu</label>
          <input type="password" id="confirm" name="confirm" placeholder="Nhập lại mật khẩu" />
        </div>
        <?php
        $error_message = ""; 
        if (isset($_GET['error'])){
          $error = $_GET['error'];
          if ($error == "emailtaken") $error_message = "Email này đã được sử dụng!";
        }
        ?>
        <div class="error" id="errorMsg"><?php echo $error_message; ?></div>
        <button type="submit" class="btn">Tạo tài khoản</button>
        <a href="index.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Quay lại trang chủ
        </a>
      </form>
    </div>
  </div>
  <script src="assets/js/validate.js"></script>
</body>
</html>