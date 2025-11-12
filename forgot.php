<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quên mật khẩu | E-Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="public/css/forgot.css">
</head>
<body>
  <div class="card">
    <form action="src/handlers/forgot_handler.php" method="post" id="forgotForm">
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
        $is_success_message = false;

        //Kiểm tra thông báo THÀNH CÔNG
        if (isset($_GET['status']) && $_GET['status'] == 'sent') {
          $message = "Nếu email tồn tại, link reset sẽ được gửi. 
          Vui lòng kiểm tra email!";
          $is_success_message = true;
        }

        //Kiểm tra thông báo LỖI 
        if (isset($_GET['error'])) {
          $message = "Có lỗi xảy ra, vui lòng thử lại.";
        }
        ?>
        <!--Cái này error xử lý còn chưa ok (để sau)-->
        <div class="error" id="errorMsg" style="<?php echo $is_success_message ? 'color: green;' : ''; ?>">
          <?php echo $message; ?>
        </div>
        <button class="btn">Gửi yêu cầu</button>
        <a href="login.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
        </a>
      </div>
    </form>
  </div>
  <script src="public/js/validate.js"></script>
</body>
</html>