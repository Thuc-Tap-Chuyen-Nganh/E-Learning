<?php
// src/templates/header.php

// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === XỬ LÝ ĐƯỜNG DẪN ===
// Kiểm tra xem file đang chạy có nằm trong thư mục 'student' không
// basename(dirname(...)) lấy tên thư mục cha của file hiện tại
$in_student_folder = (basename(dirname($_SERVER['PHP_SELF'])) == 'student');

// Nếu đang ở trong student, đường dẫn về gốc là '../', ngược lại là ''
$path = $in_student_folder ? '../' : '';

// Kiểm tra trạng thái đăng nhập
$is_logged_in = isset($_SESSION['user_id']);
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Học viên';
?>

<header class="header">
    <div class="container">
        <a href="<?= $path ?>index.php" class="logo">
            <div class="logo-icon"><i class="fa-solid fa-book-open"></i></div>
            <span>EduTech</span>
        </a>
        
        <nav class="navbar">
            <ul class="nav-links">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                
                <li><a href="<?= $path ?>index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Trang chủ</a></li>
                <li><a href="<?= $path ?>courses.php" class="<?= $current_page == 'courses.php' ? 'active' : '' ?>">Khóa học</a></li>
                
                <?php if ($is_logged_in): ?>
                    <li><a href="<?= $path ?>student/my_courses.php" class="<?= $current_page == 'my_courses.php' ? 'active' : '' ?>">My EduTech</a></li>
                <?php endif; ?>
            </ul>

            <div class="auth-buttons">
                <?php if ($is_logged_in): ?>
                    <div class="user-header-profile" style="display: flex; align-items: center; gap: 10px;">
                        <a href="<?= $path ?>student/my_courses.php" class="user-info-header" style="display: flex; align-items: center; gap: 8px; color: inherit; text-decoration: none;">
                            <span style="font-weight: 600; font-size: 14px;">Hi, <?= htmlspecialchars($user_name) ?></span>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=random&color=fff" 
                                 style="width: 36px; height: 36px; border-radius: 50%; border: 2px solid #8e2de2;" 
                                 alt="User">
                        </a>
                        
                        <a href="<?= $path ?>src/handlers/logout_handler.php" class="btn-logout" title="Đăng xuất" 
                           style="padding: 5px 10px; border: 1px solid #fee2e2; background: #fee2e2; color: #dc2626; border-radius: 5px; font-size: 14px;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= $path ?>login.php" class="login-text">Đăng nhập</a>
                    <a href="<?= $path ?>register.php" class="btn btn-primary">Đăng ký</a>
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
        <div class="mobile-nav-header">
            <a href="<?= $path ?>index.php" class="logo"><span>EduTech</span></a>
            <div class="mobile-nav-close-btn" id="mobile-nav-close-btn"><i class="fa-solid fa-xmark"></i></div>
        </div>
        <ul class="mobile-nav-links">
            <li><a href="<?= $path ?>index.php">Trang chủ</a></li>
            <li><a href="<?= $path ?>courses.php">Khóa học</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="<?= $path ?>student/my_courses.php">My EduTech</a></li>
            <?php endif; ?>
        </ul>
        <div class="mobile-auth-buttons">
            <?php if ($is_logged_in): ?>
                <div style="padding: 10px 0; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=random" style="width: 30px; border-radius: 50%;" alt="">
                    Chào, <?= htmlspecialchars($user_name) ?>
                </div>
                <a href="<?= $path ?>src/handlers/logout_handler.php" class="btn btn-secondary" style="color: #dc2626; border-color: #dc2626; margin-top: 10px; text-align: center;">Đăng xuất</a>
            <?php else: ?>
                <a href="<?= $path ?>login.php" class="btn btn-secondary">Đăng nhập</a>
                <a href="<?= $path ?>register.php" class="btn btn-primary">Đăng ký</a>
            <?php endif; ?>
        </div>
    </nav>
</div>