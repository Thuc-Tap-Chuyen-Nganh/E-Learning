<?php
require "src/core/db_connect.php";

$token = $_GET['token']??'';
$error_message = "";
$show_form = false;

if (empty($token)) {
  $error_message = "Link không hợp lệ!";
} else{
  $token_hash = hash('sha256', $token);
  $stmt = $conn->prepare("SELECT * FROM user_tokens WHERE token_hash = ? AND token_type = 'password_reset'");
  $stmt->bind_param("s", $token_hash);
  $stmt->execute();
  $result = $stmt->get_result();
  $token_data = $result->fetch_assoc();
  $stmt->close();

  if (!$token_data) 
    $error_message = "Link không hợp lệ hoặc đã được sử dụng.";
  elseif (strtotime($token_data['expires_at']) < time()) {
    $error_message = "Link đã hết hạn. Vui lòng yêu cầu link mới.";
    $stmt_del = $conn->prepare("DELETE FROM user_tokens WHERE token_hash = ?");
    $stmt_del->bind_param("s", $token_hash);
    $stmt_del->execute();
    $stmt_del->close();
  }
  else 
    $show_form = true;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt lại mật khẩu | EduTech</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
  <div class="card">
    <?php if ($show_form): ?>
    <form action="src/handlers/reset_handler.php" method="post" id="resetForm">
      <div class="header">
        <h1><i class="fas fa-unlock-alt"></i> Đặt mật khẩu mới</h1>
        <p>Vui lòng nhập mật khẩu mới của bạn</p>
      </div>
      <div class="form">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
        <div class="form-group">
          <label for="password">Mật khẩu mới</label>
          <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự" />
        </div>
        <div class="form-group">
          <label for="confirm">Nhập lại mật khẩu mới</label>
          <input type="password" id="confirm" name="confirm" placeholder="Nhập lại mật khẩu" />
        </div>
        <div class="error" id="errorMsg">
          <?php
            // Hiển thị lỗi từ handler (nếu có)
            if (isset($_GET['error']) && $_GET['error'] == 'mismatch') {
              echo "Mật khẩu nhập lại không khớp!";
            }
          ?>
        </div>
        
        <button type="submit" class="btn">Lưu mật khẩu</button>
      </div>
    </form>
    <?php else: ?>
      <div class="header">
        <h1><i class="fas fa-exclamation-triangle"></i> Lỗi</h1>
      </div>
      <div class="form" style="text-align: center;">
        <p class="error" style="display:block;"><?php echo $error_message; ?></p>
        <a href="forgot.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Quay lại trang Quên mật khẩu
        </a>
      </div>
    <?php endif; ?>
  </div>
  <script src="public/js/validate.js"></script>
</body>
</html>