<?php
session_start();
require_once '../src/core/db_connect.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. LẤY THÔNG TIN USER (Sidebar)
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();
$username = $user_info['username'];
$email = $user_info['email'];
$user_initial = strtoupper(substr($username, 0, 2));

// 3. LẤY DỮ LIỆU TIẾN ĐỘ THẬT
// Lưu ý: Chúng ta cần tính tiến độ dựa trên bảng enrollments
$sql = "SELECT 
            c.title, c.category, e.progress, e.enrolled_at
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.user_id = ?
        ORDER BY e.progress DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$courses_data = [];
$total_completed = 0;
$total_progress = 0;
$count = 0;

while ($row = $result->fetch_assoc()) {
    $courses_data[] = $row;
    // Coi như hoàn thành nếu progress = 100
    if ($row['progress'] == 100) {
        $total_completed++;
    }
    $total_progress += $row['progress'];
    $count++;
}

$avg_progress = $count > 0 ? round($total_progress / $count) : 0;

// Giả lập dữ liệu biểu đồ (Vì chưa có bảng tracking log từng ngày)
$chart_data = [2.5, 3.0, 1.5, 4.0, 2.0, 5.0, 3.5]; // Giờ học 7 ngày qua
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến độ học tập - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../public/css/index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../public/css/student_dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php require '../src/templates/header.php'; ?>

    <div class="student-layout container">
        
        <aside class="student-sidebar">
            <div class="user-widget">
                <div class="user-avatar-large">
                    <span><?php echo $user_initial; ?></span> 
                </div>
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($username); ?></h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>

            <nav class="student-menu">
                <ul>
                    <li><a href="my_courses.php"><i class="fa-solid fa-book-open"></i> Khóa học của tôi</a></li>
                    <li><a href="progress.php" class="active"><i class="fa-solid fa-chart-simple"></i> Tiến độ học tập</a></li>
                    <li><a href="certificates.php"><i class="fa-solid fa-certificate"></i> Chứng chỉ</a></li>
                    <li><a href="profile.php"><i class="fa-regular fa-user"></i> Hồ sơ cá nhân</a></li>
                </ul>
            </nav>
        </aside>

        <main class="student-content">
            
            <div class="section-header">
                <h2>Tổng quan tiến độ</h2>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
                    <div>
                        <h4><?php echo $total_completed; ?></h4>
                        <p>Khóa đã xong</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon blue"><i class="fa-solid fa-layer-group"></i></div>
                    <div>
                        <h4><?php echo $count; ?></h4>
                        <p>Đang tham gia</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon purple"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div>
                        <h4>--</h4> <p>Giờ học (Tuần)</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon orange"><i class="fa-solid fa-fire"></i></div>
                    <div>
                        <h4><?php echo $avg_progress; ?>%</h4>
                        <p>Tiến độ TB</p>
                    </div>
                </div>
            </div>

            <div class="chart-section">
                <div class="chart-header">
                    <h3>Hoạt động học tập (Giả lập)</h3>
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
                                <h4 class="prog-title"><?php echo htmlspecialchars($course['title']); ?></h4>
                                <span class="prog-cat"><?php echo htmlspecialchars($course['category']); ?></span>
                            </div>
                            
                            <div class="prog-bar-wrapper">
                                <div class="prog-stats">
                                    <span><?php echo $course['progress']; ?>% Hoàn thành</span>
                                    <?php if ($course['progress'] == 100): ?>
                                        <span class="status-tag completed"><i class="fa-solid fa-check"></i> Xong</span>
                                    <?php else: ?>
                                        <span class="status-tag ongoing">Đang học</span>
                                    <?php endif; ?>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-bar" style="width: <?php echo $course['progress']; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666;">Bạn chưa đăng ký khóa học nào.</p>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Cấu hình biểu đồ
        const ctx = document.getElementById('learningChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(142, 45, 226, 0.5)'); 
        gradient.addColorStop(1, 'rgba(142, 45, 226, 0.05)'); 

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                datasets: [{
                    label: 'Giờ học',
                    data: <?php echo json_encode($chart_data); ?>,
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
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>