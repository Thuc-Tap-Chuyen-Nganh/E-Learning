<?php
require_once 'config/config.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quên mật khẩu | E-Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/forgot.css?v=<?= filemtime('assets/css/forgot.css') ?>">
</head>
<body>
  <div class="card">
    <form action="<?= BASE_URL ?>logic/auth/forgot_password.php" method="post" id="forgotForm">
      <div class="header">
        <h1><i class="fas fa-key"></i> Quên mật khẩu?</h1>
        <p>Nhập email để nhận liên kết đặt lại mật khẩu</p>
      </div>
      <div class="form">
        <div class="form-group">
          <label for="email">Email đã đăng ký</label>
          <input type="email" id="email" name="email" placeholder="example@elearning.com" />
        </div>
        <?php
        $message = "";
        $alert_class = "";
        $is_success_message = false;

        //Kiểm tra thông báo THÀNH CÔNG
        if (isset($_GET['status']) && $_GET['status'] == 'sent') {
          $message = "Nếu email tồn tại, link reset sẽ được gửi. 
          Vui lòng kiểm tra email!";
          $alert_class = "alert-success";
          $is_success_message = true;
        }

        //Kiểm tra thông báo LỖI 
        if (isset($_GET['error'])) {
          $message = "Có lỗi xảy ra, vui lòng thử lại.";
          $alert_class = "alert-danger";
        }
        ?>
        <!--Cái này error xử lý còn chưa ok (để sau)-->
        <div class="alert <?php echo $alert_class; ?>" id="errorMsg" style="<?php echo empty($message) ? 'display: none;' : ''; ?>">
          <?php echo $message; ?>
        </div>
        <button class="btn">Gửi yêu cầu</button>
        <a href="login.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
        </a>
      </div>
    </form>
  </div>
  <script src="assets/js/validate.js"></script>
</body>
</html>
