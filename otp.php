<?php
require_once 'config/config.php';
$email = $_GET['email'] ?? '';
$type = $_GET['type'] ?? 'register';
$status = $_GET['status'] ?? '';
$error = $_GET['error'] ?? '';

// Xác định link chuyển hướng tiếp theo
if ($type == 'register') {
    $next_url = BASE_URL . "login.php?status=activated";
    $success_msg = "Kích hoạt tài khoản thành công!";
} else {
    $next_url = BASE_URL . "reset_password.php";
    $success_msg = "Xác thực thành công! Đang chuyển hướng...";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Xác thực OTP | EduTech</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/login.css?v=<?= time() ?>">
  <style>
    /* CSS cho thông báo thành công */
    .alert-success-box {
        text-align: center;
        padding: 20px;
        animation: fadeIn 0.5s;
    }
    .icon-success {
        font-size: 50px;
        color: #28a745;
        margin-bottom: 15px;
    }
    .success-text {
        font-size: 18px;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 10px;
    }
    .redirect-text {
        color: #666;
        font-size: 14px;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Ẩn form khi thành công */
    .hidden { display: none; }
  </style>
</head>
<body>
  <div class="card">
    
    <?php if ($status == 'success'): ?>
        <div class="alert-success-box">
            <div class="icon-success"><i class="fas fa-check-circle"></i></div>
            <div class="success-text"><?= $success_msg ?></div>
            <div class="redirect-text">
                Tự động chuyển trang sau <span id="countdown" style="font-weight:bold; color:#8e2de2;">3</span>s...
            </div>
        </div>
        <script>
            let seconds = 3;
            const countdownEl = document.getElementById('countdown');
            const interval = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = "<?= $next_url ?>";
                }
            }, 1000);
        </script>

    <?php else: ?>
        <form action="<?= BASE_URL ?>logic/auth/verify_otp_process.php" method="post">
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> Xác thực OTP</h1>
            <p>Mã xác thực đã được gửi đến email:<br><strong><?= htmlspecialchars($email) ?></strong></p>
        </div>
        
        <div class="form">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
            
            <div class="form-group">
                <input type="text" id="otp" name="otp" placeholder="••••••" maxlength="6" 
                    style="text-align: center; letter-spacing: 8px; font-weight: bold; font-size: 1.5rem; height: 50px;" required />
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" style="display:block; text-align: center; margin-bottom: 15px;">
                    <?= $error == 'invalid' ? 'Mã OTP không chính xác!' : ($error == 'expired' ? 'Mã OTP đã hết hạn!' : 'Có lỗi xảy ra.') ?>
                </div>
            <?php endif; ?>

            <button class="btn">Xác nhận</button>
            
            <div class="register-link" style="margin-top: 15px;">
                Chưa nhận được mã? <a href="<?= BASE_URL ?>logic/auth/resend_otp.php?email=<?= $email ?>&type=<?= $type ?>" class="link">Gửi lại</a>
            </div>
        </div>
        </form>
    <?php endif; ?>

  </div>
</body>
</html>
