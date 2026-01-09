<?php
session_start();
require_once '../config/config.php'; 

// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

// ====================================================
// 1. TÍNH TOÁN KPI
// ====================================================

// A. Doanh thu tháng này
$current_month = date('Y-m');
$sql_rev = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'";
$rev_this_month = $conn->query($sql_rev)->fetch_assoc()['total'] ?? 0;

// B. Học viên mới tháng này
$sql_student = "SELECT COUNT(*) as total FROM users WHERE role = 'student' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'";
$students_this_month = $conn->query($sql_student)->fetch_assoc()['total'] ?? 0;

// C. Khóa học hoạt động
$sql_active_courses = "SELECT COUNT(*) as total FROM courses WHERE status = 'published'";
$active_courses = $conn->query($sql_active_courses)->fetch_assoc()['total'] ?? 0;

// D. Tỷ lệ hoàn thành
$sql_prog = "SELECT 
    (SELECT COUNT(*) FROM enrollments WHERE progress = 100) as completed,
    (SELECT COUNT(*) FROM enrollments) as total";
$prog_data = $conn->query($sql_prog)->fetch_assoc();
$completion_rate = ($prog_data['total'] > 0) ? round(($prog_data['completed'] / $prog_data['total']) * 100, 1) : 0;


// ====================================================
// 2. XỬ LÝ DỮ LIỆU BIỂU ĐỒ
// ====================================================

// --- CHART 1 & 2: Dữ liệu 6 tháng gần nhất ---
$months_labels = [];
$revenue_data = [];
$students_data = [];

for ($i = 5; $i >= 0; $i--) {
    $month_key = date('Y-m', strtotime("-$i months"));
    $month_label = date('m/Y', strtotime("-$i months"));
    
    $months_labels[] = $month_label;

    // Doanh thu
    $q_rev = "SELECT SUM(amount) as total FROM payments WHERE status='completed' AND DATE_FORMAT(created_at, '%Y-%m') = '$month_key'";
    $res_rev = $conn->query($q_rev)->fetch_assoc();
    $revenue_data[] = $res_rev['total'] ?? 0;

    // Học viên
    $q_stu = "SELECT COUNT(*) as total FROM users WHERE role='student' AND DATE_FORMAT(created_at, '%Y-%m') = '$month_key'";
    $res_stu = $conn->query($q_stu)->fetch_assoc();
    $students_data[] = $res_stu['total'] ?? 0;
}

// --- CHART 3: Phân bổ danh mục ---
$cat_labels = [];
$cat_data = [];
$q_cat = "SELECT category, COUNT(*) as count FROM courses GROUP BY category";
$res_cat = $conn->query($q_cat);
while($row = $res_cat->fetch_assoc()) {
    $cat_labels[] = $row['category'];
    $cat_data[] = $row['count'];
}

// --- CHART 4: Top 5 Khóa học ---
$top_labels = [];
$top_data = [];
$q_top = "SELECT c.title, COUNT(e.enrollment_id) as total 
          FROM enrollments e 
          JOIN courses c ON e.course_id = c.course_id 
          GROUP BY e.course_id 
          ORDER BY total DESC LIMIT 5";
$res_top = $conn->query($q_top);
while($row = $res_top->fetch_assoc()) {
    $top_labels[] = mb_strimwidth($row['title'], 0, 30, "..."); // Cắt ngắn tên nếu quá dài
    $top_data[] = $row['total'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="../favicon.ico">
    <link rel="stylesheet" href="css/admin_styles.css?v=<?= time() ?>">
    
    <style>
        /* Tinh chỉnh riêng cho layout biểu đồ */
        .chart-box {
            position: relative; 
            height: 300px; /* Chiều cao cố định cho khung chứa biểu đồ */
            width: 100%;
        }
    </style>
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Báo cáo thống kê</h1>
                <p>Theo dõi hiệu suất và phân tích dữ liệu</p>
            </div>
        </header>

        <main class="main-content">
            
            <section class="stat-cards-grid kpi-4-cols">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Doanh thu tháng này</span>
                        <span class="card-value"><?= number_format($rev_this_month, 0, ',', '.') ?>đ</span>
                        <span class="card-desc">Tính trên đơn đã thanh toán</span>
                    </div>
                    <div class="card-icon icon-green">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Học viên mới</span>
                        <span class="card-value"><?= $students_this_month ?></span>
                        <span class="card-desc">Đăng ký trong tháng này</span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Khóa học hoạt động</span>
                        <span class="card-value"><?= $active_courses ?></span>
                        <span class="card-desc">Đang được công khai</span>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fa-solid fa-book-reader"></i>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tỷ lệ hoàn thành</span>
                        <span class="card-value"><?= $completion_rate ?>%</span>
                        <span class="card-desc">Trung bình toàn hệ thống</span>
                    </div>
                    <div class="card-icon icon-orange">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </section>

            <section class="charts-grid">
                <div class="chart-container full-width-chart"> 
                    <h3>Doanh thu 6 tháng gần nhất</h3>
                    <div class="chart-box">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="chart-container">
                    <h3>Học viên mới theo tháng</h3>
                    <div class="chart-box">
                        <canvas id="newStudentsChart"></canvas>
                    </div>
                </div>
            </section>

            <section class="charts-grid" style="margin-top: 20px;">
                <div class="chart-container">
                    <h3>Phân bổ khóa học theo danh mục</h3>
                    <div class="chart-box" style="height: 300px; display: flex; justify-content: center;">
                        <?php if(empty($cat_data)): ?>
                            <p style="align-self: center; color:#666;">Chưa có dữ liệu danh mục</p>
                        <?php else: ?>
                            <canvas id="pieChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="chart-container">
                    <h3>Top 5 khóa học phổ biến nhất</h3>
                    <div class="chart-box">
                        <?php if(empty($top_data)): ?>
                            <p style="text-align:center; padding-top:120px; color:#666;">Chưa có lượt đăng ký nào</p>
                        <?php else: ?>
                            <canvas id="topCoursesChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            // Dữ liệu từ PHP
            const monthLabels = <?= json_encode($months_labels) ?>;
            const revenueData = <?= json_encode($revenue_data) ?>;
            const studentsData = <?= json_encode($students_data) ?>;
            const catLabels = <?= json_encode($cat_labels) ?>;
            const catData = <?= json_encode($cat_data) ?>;
            const topLabels = <?= json_encode($top_labels) ?>;
            const topData = <?= json_encode($top_data) ?>;

            // Cấu hình chung để biểu đồ không bị méo (Quan trọng: maintainAspectRatio: false)
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false, 
                plugins: {
                    legend: { display: false } // Ẩn chú thích mặc định cho gọn
                }
            };

            // --- 1. Biểu đồ đường (Doanh thu) ---
            const revenueChartCtx = document.getElementById('revenueChart');
            if (revenueChartCtx) {
                new Chart(revenueChartCtx, {
                    type: 'line',
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Doanh thu (VNĐ)',
                            data: revenueData,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0,123,255,0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // --- 2. Biểu đồ cột (Học viên mới) ---
            const newStudentsChartCtx = document.getElementById('newStudentsChart');
            if (newStudentsChartCtx) {
                new Chart(newStudentsChartCtx, {
                    type: 'bar',
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Học viên mới',
                            data: studentsData,
                            backgroundColor: '#8e2de2',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                    }
                });
            }

            // --- 3. Biểu đồ tròn (Danh mục) ---
            const pieChartCtx = document.getElementById('pieChart');
            if (pieChartCtx && catData.length > 0) {
                new Chart(pieChartCtx, {
                    type: 'doughnut',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            data: catData,
                            backgroundColor: ['#007bff', '#8e2de2', '#dc3545', '#fd7e14', '#20c997', '#6610f2'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { position: 'right' } // Pie chart cần legend bên phải
                        }
                    }
                });
            }

            // --- 4. Biểu đồ thanh ngang (Top khóa học) ---
            const topCoursesChartCtx = document.getElementById('topCoursesChart');
            if (topCoursesChartCtx && topData.length > 0) {
                new Chart(topCoursesChartCtx, {
                    type: 'bar',
                    data: {
                        labels: topLabels,
                        datasets: [{
                            label: 'Học viên',
                            data: topData,
                            backgroundColor: '#dc3545',
                            borderRadius: 4,
                            barPercentage: 0.6 // Làm thanh mỏng lại chút
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Chuyển thành thanh ngang
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
                    }
                });
            }
        });
    </script>

</body>
</html>

