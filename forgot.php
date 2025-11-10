<!-- forgot.html -->
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
    <div class="header">
      <h1><i class="fas fa-key"></i> Quên mật khẩu?</h1>
      <p>Nhập email để nhận liên kết đặt lại mật khẩu</p>
    </div>
    <div class="form">
      <div class="form-group">
        <label for="email">Email đã đăng ký</label>
        <input type="email" id="email" placeholder="example@elearning.com" />
      </div>
      <button class="btn">Gửi hướng dẫn</button>
      <a href="login.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
      </a>
    </div>
  </div>
    <script>
    function handleSubmit() {
      const email = document.getElementById('email').value.trim();
      document.getElementById('email-error').style.display = 'none';
      
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('email-error').textContent = email ? 'Email không hợp lệ' : 'Vui lòng nhập email';
        document.getElementById('email-error').style.display = 'block';
        return;
      }

      alert(`✉️ Đã gửi liên kết khôi phục mật khẩu đến:\n${email}\n(Vui lòng kiểm tra hộp thư trong 5 phút)`);
      window.location.href = 'index.html';
    }
  </script>
</body>

</html>
