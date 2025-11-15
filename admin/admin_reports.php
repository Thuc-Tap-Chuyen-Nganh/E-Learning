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
    <title>Admin - Báo cáo thống kê</title>

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
                    <a href="admin_dashboard.php">
                        <i class="fa-solid fa-table-columns"></i>
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
                    <a href="admin_reports.php" class="active"> <i class="fa-solid fa-chart-pie"></i>
                        <span>Báo cáo thống kê</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Báo cáo thống kê</h1>
                <p>Theo dõi hiệu suất và phân tích dữ liệu</p>
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
                        <span class="card-title">Học viên mới</span>
                        <span class="card-value">78</span>
                        <span class="card-growth green">+19.2%</span> 
                        <span class="card-desc">So với tháng trước</span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Khóa học hoạt động</span>
                        <span class="card-value">24</span>
                        <span class="card-growth green">+8.3%</span> 
                        <span class="card-desc">So với tháng trước</span>
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
                        <span class="card-desc">So với tháng trước</span>
                    </div>
                    <div class="card-icon icon-orange">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </section>

            <section class="charts-grid">
                <div class="chart-container full-width-chart"> <h3>Doanh thu 6 tháng gần nhất</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Học viên mới theo tháng</h3>
                    <canvas id="newStudentsChart"></canvas>
                </div>
            </section>

            <section class="charts-grid" style="margin-top: 20px;">
                <div class="chart-container">
                    <h3>Phân bổ khóa học theo danh mục</h3>
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Top 5 khóa học phổ biến nhất</h3>
                    <canvas id="topCoursesChart"></canvas>
                </div>
            </section>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="js/admin_charts.js"></script>

</body>
</html>