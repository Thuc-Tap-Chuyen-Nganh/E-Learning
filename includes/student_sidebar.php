<?php
// includes/student_sidebar.php
$sb_username = $_SESSION['username'] ?? 'Học viên';
$sb_email = $_SESSION['email'] ?? '';
$sb_avatar = $_SESSION['avatar'] ?? null;
$sb_show_avatar = !empty($sb_avatar) && file_exists(BASE_PATH . $sb_avatar);
$sb_avatar_url = $sb_show_avatar ? BASE_URL . $sb_avatar : null;
$sb_initial = strtoupper(substr($sb_username, 0, 2));
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="student-sidebar">
    <div class="user-widget">
        <div class="user-avatar-large">
            <?php if ($sb_avatar_url): ?>
                <img src="<?= $sb_avatar_url ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
                <span><?= $sb_initial ?></span> 
            <?php endif; ?>
        </div>
        <div class="user-info">
            <h3><?= htmlspecialchars($sb_username) ?></h3>
            <p><?= htmlspecialchars($sb_email) ?></p>
        </div>
    </div>

    <nav class="student-menu">
        <ul>
            <li>
                <a href="<?= BASE_URL ?>student/my_courses.php" class="<?= $current_page == 'my_courses.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-book-open"></i> Khóa học của tôi
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>student/progress.php" class="<?= $current_page == 'progress.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-simple"></i> Tiến độ học tập
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>student/certificates.php" class="<?= $current_page == 'certificates.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-certificate"></i> Chứng chỉ
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>student/profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">
                    <i class="fa-regular fa-user"></i> Hồ sơ cá nhân
                </a>
            </li>
        </ul>
    </nav>
</aside>