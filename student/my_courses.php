<?php
session_start();
require_once '../config/config.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. LẤY THÔNG TIN USER (Cho Header/Sidebar)
// (Phần này đã được xử lý trong session và sidebar, nhưng query lại để chắc chắn)
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();

// 3. TÍNH TOÁN THỐNG KÊ TỔNG QUAN
// A. Tổng thời gian đã học (dựa trên lesson_progress)
$sql_time = "SELECT SUM(l.duration) as total_minutes 
             FROM lesson_progress lp 
             JOIN lessons l ON lp.lesson_id = l.lesson_id 
             WHERE lp.user_id = ?";
$stmt_time = $conn->prepare($sql_time);
$stmt_time->bind_param("i", $user_id);
$stmt_time->execute();
$time_res = $stmt_time->get_result()->fetch_assoc();
$total_minutes_learned = $time_res['total_minutes'] ?? 0;
$hours_learned = round($total_minutes_learned / 60, 1);

// 4. LẤY DANH SÁCH KHÓA HỌC & TÍNH CÁC CHỈ SỐ KHÁC
$sql_courses = "SELECT 
            c.course_id, c.title, c.thumbnail, c.category, c.price,
            e.enrolled_at, e.progress
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.user_id = ?
        ORDER BY e.enrolled_at DESC";

$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->bind_param("i", $user_id);
$stmt_courses->execute();
$result_courses = $stmt_courses->get_result();

// Đưa dữ liệu vào mảng để dễ xử lý thống kê trước khi loop HTML
$my_courses = [];
$total_progress_sum = 0;
$certificates_count = 0;

while ($row = $result_courses->fetch_assoc()) {
    $my_courses[] = $row;
    $total_progress_sum += $row['progress'];
    
    // Nếu hoàn thành 100% thì tính là 1 chứng chỉ
    if ($row['progress'] == 100) {
        $certificates_count++;
    }
}

$total_enrolled = count($my_courses);
$avg_progress = ($total_enrolled > 0) ? round($total_progress_sum / $total_enrolled) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khóa học của tôi - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php require '../includes/header.php'; ?>

    <div class="student-layout container">
        
        <?php require '../includes/student_sidebar.php'; ?>

        <main class="student-content">
            
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-icon blue"><i class="fa-solid fa-book"></i></div>
                    <div>
                        <h4><?php echo $total_enrolled; ?></h4>
                        <p>Khóa đang học</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon purple"><i class="fa-regular fa-clock"></i></div>
                    <div>
                        <h4><?php echo $hours_learned; ?></h4>
                        <p>Giờ đã học</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon green"><i class="fa-solid fa-award"></i></div>
                    <div>
                        <h4><?php echo $certificates_count; ?></h4>
                        <p>Chứng chỉ</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon orange"><i class="fa-solid fa-arrow-trend-up"></i></div>
                    <div>
                        <h4><?php echo $avg_progress; ?>%</h4>
                        <p>Tiến độ TB</p>
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h2>Khóa học của tôi</h2>
                <a href="<?= BASE_URL ?>courses.php" class="link-primary">Tìm thêm khóa học</a>
            </div>

            <div class="course-list-vertical">
                
                <?php if ($total_enrolled > 0): ?>
                    <?php foreach($my_courses as $course): ?>
                        <div class="course-item-card">
                            <div class="course-thumb">
                                <img src="<?php echo get_course_image($course['thumbnail'], $course['category']); ?>" alt="Course">
                            </div>
                            <div class="course-details">
                                <h3 style="margin-top: 0;"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p class="last-accessed">Đăng ký ngày: <?php echo date('d/m/Y', strtotime($course['enrolled_at'])); ?></p>
                                
                                <div class="progress-wrapper">
                                    <div class="progress-labels">
                                        <span>Tiến độ</span>
                                        <span><?php echo $course['progress']; ?>%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-bar" style="width: <?php echo $course['progress']; ?>%"></div>
                                    </div>
                                    <p class="lessons-count">
                                        <?php if ($course['progress'] == 100): ?>
                                            <span style="color: #16a34a; font-weight: bold;"><i class="fa-solid fa-check-circle"></i> Đã hoàn thành</span>
                                        <?php else: ?>
                                            Đã hoàn thành <?php echo $course['progress']; ?>% nội dung
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="course-actions">
                                    <?php if ($course['progress'] == 100): ?>
                                        <a href="<?= BASE_URL ?>student/certificates.php" class="btn btn-primary" style="background: #fbbf24; color: #78350f; border: none;">
                                            <i class="fa-solid fa-certificate"></i> Xem chứng chỉ
                                        </a>
                                        
                                        <a href="<?= BASE_URL ?>student/learning.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-secondary" style="text-decoration: none; color: #333;">
                                            Ôn tập lại
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>student/learning.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary" style="text-decoration: none; color: white;">
                                            <i class="fa-solid fa-play"></i> Tiếp tục học
                                        </a>
                                        
                                        <a href="<?= BASE_URL ?>course_detail.php?id=<?php echo $course['course_id']; ?>" class="btn btn-secondary" style="text-decoration: none; color: #333;">
                                            Chi tiết
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; background: white; border-radius: 16px;">
                        <img src="<?= BASE_URL ?>assets/images/empty-box.png" alt="" style="width: 80px; opacity: 0.5; margin-bottom: 20px;">
                        <h3>Bạn chưa đăng ký khóa học nào.</h3>
                        <p style="color: #666; margin-bottom: 20px;">Hãy khám phá các khóa học thú vị ngay hôm nay!</p>
                        <a href="<?= BASE_URL ?>courses.php" class="btn btn-primary" style="text-decoration: none; color: white; padding: 10px 25px; border-radius: 50px;">Khám phá ngay</a>
                    </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

    <?php require '../includes/footer.php'; ?>

</body>
</html>