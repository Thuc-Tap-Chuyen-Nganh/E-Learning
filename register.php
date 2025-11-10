<!-- register.html -->
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Đăng ký | E-Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="public/css/register.css">
</head>
<body>
  <div class="card">
    <div class="header">
      <h1><i class="fas fa-user-plus"></i> Đăng ký</h1>
      <p>Tạo tài khoản để bắt đầu học lập trình</p>
    </div>
    <div class="form">
      <div class="form-group">
        <label for="name">Họ và tên</label>
        <input type="text" id="name" placeholder="Nguyễn Văn A" />
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="example@elearning.com" />
      </div>
      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" placeholder="Tối thiểu 6 ký tự" />
      </div>
      <div class="form-group">
        <label for="confirm">Nhập lại mật khẩu</label>
        <input type="password" id="confirm" placeholder="Nhập lại mật khẩu" />
      </div>
      <button class="btn">Tạo tài khoản</button>
      <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
      </a>
    </div>
  </div>
  <script>
    // Nhận thông báo từ register.html
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('registered') && urlParams.get('registered') === 'true') {
      document.getElementById('success-msg').style.display = 'block';
      const email = urlParams.get('email');
      if (email) document.getElementById('email').value = email;
    }
    function togglePassword() {
      const pw = document.getElementById('password');
      const icon = document.getElementById('toggle-pw');
      if (pw.type === 'password') {
        pw.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        pw.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
    function handleLogin() {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      let hasError = false;
      document.querySelectorAll('.error').forEach(e => e.style.display = 'none');
      if (!email) {
        document.getElementById('email-error').textContent = 'Vui lòng nhập email';
        document.getElementById('email-error').style.display = 'block';
        hasError = true;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('email-error').textContent = 'Email không hợp lệ';
        document.getElementById('email-error').style.display = 'block';
        hasError = true;
      }
      if (!password) {
        document.getElementById('password-error').style.display = 'block';
        hasError = true;
      }
      if (!hasError) {
        alert(`✅ Đăng nhập thành công!\nChào mừng bạn: ${email}`);
      }
    }
  </script>
</body>

</html>
