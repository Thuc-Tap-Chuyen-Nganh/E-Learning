<?php
require_once '../config/config.php'; 
// Lấy tên file hiện tại
$current_page = basename($_SERVER['PHP_SELF']);

// Đếm số giao dịch chờ duyệt
$pending_count = 0;
if (isset($_SESSION['admin_id'])) {
    $pending_query = $conn->query("SELECT COUNT(*) as count FROM payments WHERE status = 'pending'");
    $pending_count = $pending_query->fetch_assoc()['count'];
}

// Các trang con thuộc mục Khóa học
$course_pages = [
    'admin_courses.php',
    'admin_add_course.php',
    'admin_edit_course.php',
    'admin_course_details.php',
    'admin_chapter_details.php',
    'admin_quiz_manage.php'
];

// Các trang con thuộc mục Học viên
$student_pages = [
    'admin_students.php'
];
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?= BASE_URL ?>admin/admin_dashboard.php" class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>EduTech Admin</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Tổng quan</span>
                </a>
            </li>

            <li>
                <a href="admin_courses.php" class="<?php echo (in_array($current_page, $course_pages)) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-book"></i>
                    <span>Quản lý khóa học</span>
                </a>
            </li>

            <li>
                <a href="admin_students.php" class="<?php echo (in_array($current_page, $student_pages)) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i>
                    <span>Quản lý học viên</span>
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>admin/admin_payments.php" class="<?= $current_page == 'admin_payments.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>Quản lý thanh toán</span>
                    <?php if ($pending_count > 0): ?>
                        <span class="badge badge-danger"><?= $pending_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li>
                <a href="admin_reports.php" class="<?php echo ($current_page == 'admin_reports.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Báo cáo thống kê</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="../src/handlers/admin_logout.php" class="sidebar-logout-btn">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>