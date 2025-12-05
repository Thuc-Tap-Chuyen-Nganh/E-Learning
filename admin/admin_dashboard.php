<?php
session_start();
require_once '../config/config.php'; 

// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

// === 1. TÍNH TOÁN KPI (THỐNG KÊ CƠ BẢN) ===

// A. Tổng doanh thu (Chỉ tính các giao dịch 'completed')
$res_revenue = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'");
$total_revenue = $res_revenue->fetch_assoc()['total'] ?? 0;

// B. Tổng học viên
$res_students = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
$total_students = $res_students->fetch_assoc()['total'];

// C. Khóa học đang hoạt động
$res_courses = $conn->query("SELECT COUNT(*) as total FROM courses WHERE status = 'published'");
$active_courses = $res_courses->fetch_assoc()['total'];

// D. Tỷ lệ hoàn thành (Số khóa đã học xong / Tổng lượt đăng ký)
$res_progress = $conn->query("SELECT 
    (SELECT COUNT(*) FROM enrollments WHERE progress = 100) as completed,
    (SELECT COUNT(*) FROM enrollments) as total");
$prog_data = $res_progress->fetch_assoc();
$completion_rate = ($prog_data['total'] > 0) ? round(($prog_data['completed'] / $prog_data['total']) * 100, 1) : 0;


// === 2. DỮ LIỆU BIỂU ĐỒ DOANH THU (30 NGÀY QUA) ===
$chart_data = [];
$date_labels = [];
// Tạo mảng 30 ngày gần nhất
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_labels[] = date('d/m', strtotime("-$i days")); // Label hiển thị
    $chart_data[$date] = 0; // Mặc định 0
}

// Query doanh thu theo ngày
$sql_chart = "SELECT DATE(created_at) as pay_date, SUM(amount) as daily_total 
              FROM payments 
              WHERE status = 'completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
              GROUP BY DATE(created_at)";
$res_chart = $conn->query($sql_chart);

while ($row = $res_chart->fetch_assoc()) {
    if (isset($chart_data[$row['pay_date']])) {
        $chart_data[$row['pay_date']] = $row['daily_total'];
    }
}
// Chuyển về mảng index để JS dùng
$chart_values = array_values($chart_data);


// === 3. HOẠT ĐỘNG GẦN ĐÂY (5 Giao dịch mới nhất) ===
$sql_activity = "SELECT u.username, c.title, p.created_at, p.amount 
                 FROM payments p
                 JOIN users u ON p.user_id = u.user_id
                 JOIN courses c ON p.course_id = c.course_id
                 WHERE p.status = 'completed'
                 ORDER BY p.created_at DESC LIMIT 5";
$res_activity = $conn->query($sql_activity);


// === 4. KHÓA HỌC HÀNG ĐẦU (Top 3 Doanh thu) ===
$sql_top = "SELECT c.title, COUNT(p.payment_id) as student_count, SUM(p.amount) as revenue
            FROM payments p
            JOIN courses c ON p.course_id = c.course_id
            WHERE p.status = 'completed'
            GROUP BY p.course_id
            ORDER BY revenue DESC LIMIT 3";
$res_top = $conn->query($sql_top);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tổng quan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/admin_styles.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Tổng quan</h1>
                <p>Chào mừng quay trở lại! Đây là tổng quan về hệ thống</p>
            </div>
        </header>

        <main class="main-content">
            
            <section class="stat-cards-grid kpi-4-cols">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng doanh thu</span>
                        <span class="card-value"><?= format_currency($total_revenue) ?></span>
                        <span class="card-desc">Tất cả thời gian</span>
                    </div>
                    <div class="card-icon icon-green">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng học viên</span>
                        <span class="card-value"><?= number_format($total_students) ?></span>
                        <span class="card-desc">Tài khoản học viên</span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Khóa học hoạt động</span>
                        <span class="card-value"><?= $active_courses ?></span>
                        <span class="card-desc">Đang mở bán</span>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fa-solid fa-book-reader"></i>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tỷ lệ hoàn thành</span>
                        <span class="card-value"><?= $completion_rate ?>%</span>
                        <span class="card-desc">Học viên tốt nghiệp</span>
                    </div>
                    <div class="card-icon icon-orange">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </section>

            <section class="chart-container full-width" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 20px;">Biểu đồ doanh thu (30 ngày qua)</h3>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </section>

            <section class="dashboard-grid-2-col">
                
                <div class="activity-feed">
                    <h3>Giao dịch gần đây</h3>
                    <ul>
                        <?php if ($res_activity->num_rows > 0): ?>
                            <?php while($act = $res_activity->fetch_assoc()): 
                                $initial = strtoupper(substr($act['username'], 0, 1));
                            ?>
                                <li>
                                    <div class="avatar" style="background: #3b82f6; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px;">
                                        <?= $initial ?>
                                    </div>
                                    <div class="activity-text">
                                        <strong><?= htmlspecialchars($act['username']) ?></strong> đã mua <strong><?= htmlspecialchars($act['title']) ?></strong>
                                        <br>
                                        <span style="font-size: 12px; color: #666;"><?= date('d/m H:i', strtotime($act['created_at'])) ?> • <span style="color: #16a34a; font-weight: 600;">+<?= number_format($act['amount']) ?>đ</span></span>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: #666; font-style: italic;">Chưa có giao dịch nào.</p>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="top-courses-list">
                    <h3>Khóa học doanh thu cao</h3>
                    <ul>
                        <?php if ($res_top->num_rows > 0): ?>
                            <?php $rank = 1; while($top = $res_top->fetch_assoc()): ?>
                                <li>
                                    <div class="course-info">
                                        <h4 style="display: flex; align-items: center; gap: 8px;">
                                            <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 12px;">#<?= $rank++ ?></span>
                                            <?= htmlspecialchars($top['title']) ?>
                                        </h4>
                                        <span style="font-size: 13px; color: #666;"><?= $top['student_count'] ?> lượt mua</span>
                                        <span class="revenue" style="display: block; color: #16a34a; font-weight: 600;">Doanh thu: <?= number_format($top['revenue']) ?>đ</span>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: #666;">Chưa có dữ liệu.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>

        </main>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Dữ liệu từ PHP
        const labels = <?= json_encode($date_labels) ?>;
        const data = <?= json_encode($chart_values) ?>;

        new Chart(ctx, {
            type: 'bar', // Hoặc 'line' nếu thích
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>