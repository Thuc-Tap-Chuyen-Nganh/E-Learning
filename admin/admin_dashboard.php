<?php
session_start();
// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tổng quan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="admin_dashboard.php" class="logo">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>EduTech Admin</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="admin_dashboard.php" class="active"> <i class="fa-solid fa-table-columns"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
                <li>
                    <a href="admin_courses.php"> 
                        <i class="fa-solid fa-book"></i>
                        <span>Quản lý khóa học</span>
                    </a>
                </li>
                <li>
                    <a href="admin_students.php"> 
                        <i class="fa-solid fa-users"></i>
                        <span>Quản lý học viên</span>
                    </a>
                </li>
                <li>
                    <a href="admin_reports.php"> 
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Báo cáo thống kê</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Tổng quan</h1>
                <p>Chào mừng quay trở lại! Đây là tổng quan về hệ thống</p>
            </div>
            <a href="../src/admin_handlers/admin_logout.php" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </a>
        </header>

        <main class="main-content">
            <section class="stat-cards-grid kpi-4-cols">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Doanh thu tháng này</span>
                        <span class="card-value">58,000,000₫</span>
                        <span class="card-growth green">+12.5%</span> 
                        <span class="card-desc">So với tháng trước</span>
                    </div>
                    <div class="card-icon icon-green">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng học viên</span>
                        <span class="card-value">1,234</span>
                        <span class="card-growth green">+78</span> 
                        <span class="card-desc">Học viên mới tháng này</span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Khóa học đang hoạt động</span>
                        <span class="card-value">24</span>
                        <span class="card-growth green">+2</span> 
                        <span class="card-desc">Khóa học mới</span>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fa-solid fa-book-reader"></i>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tỷ lệ hoàn thành</span>
                        <span class="card-value">67%</span>
                        <span class="card-growth green">+5.1%</span> 
                        <span class="card-desc">Tăng so với tháng trước</span>
                    </div>
                    <div class="card-icon icon-orange">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </section>

            <section class="chart-container full-width">
                <h3>Doanh thu 30 ngày qua (triệu đồng)</h3>
                <div class="horizontal-bar-chart">
                    <div class="bar-item">
                        <span class="bar-label">01/06</span>
                        <div class="bar-wrapper"><div class="bar" style="width: 60%;"></div></div>
                        <span class="bar-value">8.5tr</span>
                    </div>
                     <div class="bar-item">
                        <span class="bar-label">05/06</span>
                        <div class="bar-wrapper"><div class="bar" style="width: 65%;"></div></div>
                        <span class="bar-value">9.2tr</span>
                    </div>
                     <div class="bar-item">
                        <span class="bar-label">10/06</span>
                        <div class="bar-wrapper"><div class="bar" style="width: 70%;"></div></div>
                        <span class="bar-value">10.1tr</span>
                    </div>
                     <div class="bar-item">
                        <span class="bar-label">15/06</span>
                        <div class="bar-wrapper"><div class="bar" style="width: 85%;"></div></div>
                        <span class="bar-value">11.8tr</span>
                    </div>
                     <div class="bar-item">
                        <span class="bar-label">20/06</span>
                        <div class="bar-wrapper"><div class="bar" style="width: 68%;"></div></div>
                        <span class="bar-value">9.8tr</span>
                    </div>
                    <div class="bar-item">
                        <span class="bar-label">25/06</span>
                        <div class="bar-wrapper"><div class="bar" style="width: 100%;"></div></div>
                        <span class="bar-value">13.2tr</span>
                    </div>
                </div>
            </section>

            <section class="dashboard-grid-2-col">
                <div class="activity-feed">
                    <h3>Hoạt động gần đây</h3>
                    <ul>
                        <li>
                            <div class="avatar avatar-n">N</div>
                            <div class="activity-text">
                                <strong>Nguyễn Văn Nam</strong> đã đăng ký <strong>React & TypeScript</strong>
                                <span>5 phút trước</span>
                            </div>
                        </li>
                        <li>
                            <div class="avatar avatar-m">T</div>
                            <div class="activity-text">
                                <strong>Trần Thị Mai</strong> đã hoàn thành <strong>Python cơ bản</strong>
                                <span>15 phút trước</span>
                            </div>
                        </li>
                         <li>
                            <div class="avatar avatar-a">L</div>
                            <div class="activity-text">
                                <strong>Lê Hoàng Anh</strong> đã đăng ký <strong>Digital Marketing</strong>
                                <span>1 giờ trước</span>
                            </div>
                        </li>
                         <li>
                            <div class="avatar avatar-l">P</div>
                            <div class="activity-text">
                                <strong>Phạm Thị Lan</strong> đã hoàn thành <strong>UI/UX Design</strong>
                                <span>2 giờ trước</span>
                            </div>
                        </li>
                         <li>
                            <div class="avatar avatar-m">H</div>
                            <div class="activity-text">
                                <strong>Hoàng Văn Minh</strong> đã đăng ký <strong>Quản trị dự án</strong>
                                <span>3 giờ trước</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="top-courses-list">
                    <h3>Khóa học hàng đầu</h3>
                    <ul>
                        <li>
                            <div class="course-info">
                                <h4>React & TypeScript cơ bản</h4>
                                <span>245 học viên</span>
                                <span class="revenue">Doanh thu: <strong>367,500,000₫</strong></span>
                            </div>
                            <span class="tag tag-green">Cao</span>
                        </li>
                         <li>
                            <div class="course-info">
                                <h4>Python cho người mới bắt đầu</h4>
                                <span>189 học viên</span>
                                <span class="revenue">Doanh thu: <strong>226,800,000₫</strong></span>
                            </div>
                            <span class="tag tag-green">Cao</span>
                        </li>
                         <li>
                            <div class="course-info">
                                <h4>Digital Marketing nâng cao</h4>
                                <span>156 học viên</span>
                                <span class="revenue">Doanh thu: <strong>312,000,000₫</strong></span>
                            </div>
                            <span class="tag tag-yellow">Trung bình</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="stat-cards-grid bottom-stats">
                 <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Học viên đang học</span>
                        <span class="card-value">892</span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Khóa học hoàn thành</span>
                        <span class="card-value">342</span>
                    </div>
                    <div class="card-icon icon-green">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Đang chờ duyệt</span>
                        <span class="card-value">12</span>
                    </div>
                    <div class="card-icon icon-orange">
                        <i class="fa-solid fa-exclamation-circle"></i>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>