<?php
session_start();
require_once '../config/config.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. XỬ LÝ DỮ LIỆU BIỂU ĐỒ (7 NGÀY QUA)
// Khởi tạo mảng dữ liệu cho 7 ngày gần nhất (Mặc định là 0)
$chart_labels = []; // Nhãn ngày (T2, T3...)
$chart_data = [];   // Dữ liệu giờ học
$week_map = [];     // Map trung gian để gán dữ liệu

// Tạo khung 7 ngày
for ($i = 6; $i >= 0; $i--) {
    $timestamp = strtotime("-$i days");
    $date_key = date('Y-m-d', $timestamp);
    $label = date('d/m', $timestamp); // Ví dụ: 05/12
    
    $chart_labels[] = $label;
    $week_map[$date_key] = 0;
}

// Truy vấn tổng thời gian học theo ngày
$sql_chart = "SELECT 
                DATE(lp.completed_at) as study_date, 
                SUM(l.duration) as total_minutes
              FROM lesson_progress lp
              JOIN lessons l ON lp.lesson_id = l.lesson_id
              WHERE lp.user_id = ? 
              AND lp.completed_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
              GROUP BY DATE(lp.completed_at)";

$stmt_chart = $conn->prepare($sql_chart);
$stmt_chart->bind_param("i", $user_id);
$stmt_chart->execute();
$res_chart = $stmt_chart->get_result();

while ($row = $res_chart->fetch_assoc()) {
    $date = $row['study_date'];
    // Quy đổi phút sang giờ (làm tròn 1 số lẻ)
    $hours = round($row['total_minutes'] / 60, 1);
    if (isset($week_map[$date])) {
        $week_map[$date] = $hours;
    }
}

// Chuyển dữ liệu từ Map sang Array chuẩn để đưa vào Chart.js
foreach ($week_map as $val) {
    $chart_data[] = $val;
}

// Tính tổng giờ học trong tuần này
$weekly_hours = array_sum($chart_data);


// 3. LẤY DANH SÁCH KHÓA HỌC & TIẾN ĐỘ CHI TIẾT
$sql = "SELECT 
            c.title, c.category, e.progress, e.enrolled_at
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.user_id = ?
        ORDER BY e.enrolled_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$courses_data = [];
$total_completed = 0;
$total_progress_sum = 0;
$count = 0;

while ($row = $result->fetch_assoc()) {
    $courses_data[] = $row;
    if ($row['progress'] == 100) $total_completed++;
    $total_progress_sum += $row['progress'];
    $count++;
}

$avg_progress = $count > 0 ? round($total_progress_sum / $count) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến độ học tập - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/index.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css?v=<?= time() ?>">
</head>
<body>

    <?php require '../includes/header.php'; ?>

    <div class="student-layout container">
        
        <?php require '../includes/student_sidebar.php'; ?>

        <main class="student-content">
            
            <div class="section-header">
                <h2>Tổng quan tiến độ</h2>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
                    <div>
                        <h4><?= $total_completed ?></h4>
                        <p>Khóa đã xong</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon blue"><i class="fa-solid fa-layer-group"></i></div>
                    <div>
                        <h4><?= $count ?></h4>
                        <p>Đang tham gia</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon purple"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div>
                        <h4><?= $weekly_hours ?>h</h4> 
                        <p>Giờ học (7 ngày)</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon orange"><i class="fa-solid fa-fire"></i></div>
                    <div>
                        <h4><?= $avg_progress ?>%</h4>
                        <p>Tiến độ TB</p>
                    </div>
                </div>
            </div>

            <div class="chart-section">
                <div class="chart-header">
                    <h3>Hoạt động học tập</h3>
                    <select class="chart-filter">
                        <option>7 ngày qua</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="learningChart"></canvas>
                </div>
            </div>

            <div class="section-header" style="margin-top: 30px;">
                <h2>Chi tiết khóa học</h2>
            </div>

            <div class="progress-list-container">
                <?php if (count($courses_data) > 0): ?>
                    <?php foreach ($courses_data as $course): ?>
                        <div class="progress-item">
                            <div class="prog-info">
                                <h4 class="prog-title"><?= htmlspecialchars($course['title']) ?></h4>
                                <span class="prog-cat"><?= htmlspecialchars($course['category']) ?></span>
                            </div>
                            
                            <div class="prog-bar-wrapper">
                                <div class="prog-stats">
                                    <span><?= $course['progress'] ?>% Hoàn thành</span>
                                    <?php if ($course['progress'] == 100): ?>
                                        <span class="status-tag completed"><i class="fa-solid fa-check"></i> Xong</span>
                                    <?php else: ?>
                                        <span class="status-tag ongoing">Đang học</span>
                                    <?php endif; ?>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-bar" style="width: <?= $course['progress'] ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #666; width: 100%;">
                        Bạn chưa đăng ký khóa học nào.
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <?php require '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('learningChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(142, 45, 226, 0.5)'); 
        gradient.addColorStop(1, 'rgba(142, 45, 226, 0.05)'); 

        // Dữ liệu từ PHP
        const labels = <?= json_encode($chart_labels) ?>;
        const dataPoints = <?= json_encode($chart_data) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Giờ học',
                    data: dataPoints,
                    borderColor: '#8e2de2',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#8e2de2',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] },
                        ticks: { stepSize: 0.5 } 
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>