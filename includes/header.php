<?php
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['username'] ?? 'Học viên';
$user_avatar = $_SESSION['avatar'] ?? null;
$show_avatar = !empty($user_avatar) && file_exists(BASE_PATH . $user_avatar);
$avatar_url = $show_avatar ? BASE_URL . $user_avatar : "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=random&color=fff";
?>

<header class="header">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php" class="logo">
            <div class="logo-icon"><i class="fa-solid fa-book-open"></i></div>
            <span>EduTech</span>
        </a>
        
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="<?= BASE_URL ?>index.php">Trang chủ</a></li>
                <li><a href="<?= BASE_URL ?>courses.php">Khóa học</a></li>
                
                <?php if ($is_logged_in): ?>
                    <li><a href="<?= BASE_URL ?>student/my_courses.php">My EduTech</a></li>
                <?php endif; ?>
            </ul>

            <div class="auth-buttons">
                <?php if ($is_logged_in): ?>
                    <div class="user-header-profile" style="display: flex; align-items: center; gap: 10px;">
                        <a href="<?= BASE_URL ?>student/my_courses.php" class="user-info-header" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 600; font-size: 14px;">Hi, <?= htmlspecialchars($user_name) ?></span>
                            <img src="<?= $avatar_url ?>" style="width: 36px; height: 36px; border-radius: 50%; border: 2px solid #8e2de2; object-fit: cover;" alt="User">
                        </a>
                        
                        <a href="<?= BASE_URL ?>logic/auth/logout.php" class="btn-logout" title="Đăng xuất" 
                           style="padding: 5px 10px; border: 1px solid #fee2e2; background: #fee2e2; color: #dc2626; border-radius: 5px;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login.php" class="login-text">Đăng nhập</a>
                    <a href="<?= BASE_URL ?>register.php" class="btn btn-primary">Đăng ký</a>
                <?php endif; ?>
            </div>
        </nav>
        
        <div class="hamburger-icon" id="hamburger-btn">
            <i class="fa-solid fa-bars"></i>
        </div>
    </div>
</header>

<div class="mobile-nav-container" id="mobile-nav-container">
    <div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>
    <nav class="mobile-nav">
        <ul class="mobile-nav-links">
            <li><a href="<?= BASE_URL ?>index.php">Trang chủ</a></li>
            <li><a href="<?= BASE_URL ?>courses.php">Khóa học</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="<?= BASE_URL ?>student/my_courses.php">My EduTech</a></li>
            <?php endif; ?>
        </ul>
        <div class="mobile-auth-buttons">
            <?php if ($is_logged_in): ?>
                <a href="<?= BASE_URL ?>logic/auth/logout.php" class="btn btn-secondary" style="color: red;">Đăng xuất</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>login.php" class="btn btn-secondary">Đăng nhập</a>
                <a href="<?= BASE_URL ?>register.php" class="btn btn-primary">Đăng ký</a>
            <?php endif; ?>
        </div>
    </nav>
</div>