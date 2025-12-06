<?php
session_start();
require_once '../config/config.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. LẤY THÔNG TIN USER
$stmt = $conn->prepare("SELECT username, email, avatar FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Xử lý dữ liệu hiển thị
$username = $user['username'];
$email = $user['email'];
$current_avatar = $user['avatar']; 
// Nếu có avatar thật thì dùng, không thì dùng 2 chữ cái đầu
$show_img = !empty($current_avatar) && file_exists(BASE_PATH . $current_avatar);
$user_initial = strtoupper(substr($username, 0, 2));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php require '../includes/header.php'; ?>

    <div class="student-layout container">
        
        <?php require '../includes/student_sidebar.php'; ?>

        <main class="student-content">
            
            <div class="section-header">
                <h2>Cài đặt tài khoản</h2>
            </div>

            <div class="profile-card" style="margin-bottom: 30px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                    Thông tin cá nhân
                </h3>

                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <i class="fa-solid fa-check-circle"></i> Cập nhật thông tin thành công!
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Có lỗi xảy ra. Vui lòng thử lại.
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>logic/auth/update_profile.php" method="POST" class="profile-form" enctype="multipart/form-data">
                    <div class="profile-avatar-section">
                        <div class="avatar-preview">
                            <?php if ($show_img): ?>
                                <img src="<?= BASE_URL . $current_avatar ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <span><?php echo $user_initial; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="avatar-info">
                            <h3><?php echo htmlspecialchars($username); ?></h3>
                            <p style="color: #64748b; margin-bottom: 10px;">Học viên tại EduTech</p>
                            
                            <label for="avatar_upload" style="cursor: pointer; color: #8e2de2; font-weight: 600; font-size: 14px;">
                                <i class="fa-solid fa-camera"></i> Tải ảnh mới
                            </label>
                            <input type="file" id="avatar_upload" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Họ và tên hiển thị</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Địa chỉ Email</label>
                        <div style="position: relative;">
                            <input type="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b; padding-right: 40px;">
                            <i class="fa-solid fa-lock" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 25px;">Lưu thông tin</button>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                    Bảo mật & Mật khẩu
                </h3>

                <?php if (isset($_GET['pass_status']) && $_GET['pass_status'] == 'success'): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <i class="fa-solid fa-check-circle"></i> Đổi mật khẩu thành công! Vui lòng đăng nhập lại vào lần tới.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['pass_error'])): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <i class="fa-solid fa-circle-exclamation"></i> 
                        <?php 
                            if($_GET['pass_error'] == 'wrongcurrent') echo "Mật khẩu hiện tại không đúng.";
                            elseif($_GET['pass_error'] == 'mismatch') echo "Mật khẩu nhập lại không khớp.";
                            elseif($_GET['pass_error'] == 'short') echo "Mật khẩu mới phải trên 6 ký tự.";
                            else echo "Có lỗi xảy ra khi đổi mật khẩu.";
                        ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>logic/auth/change_password.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="••••••••">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="new_password">Mật khẩu mới</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Nhập lại mật khẩu mới</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="background-color: #334155; color: white; border: none; padding: 10px 25px;">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <?php require '../includes/footer.php'; ?>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    // Tìm thẻ img hoặc tạo mới nếu đang hiển thị chữ
                    const container = document.querySelector('.avatar-preview');
                    container.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>