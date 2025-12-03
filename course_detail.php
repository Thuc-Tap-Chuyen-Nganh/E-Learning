<?php
session_start();
require_once 'src/core/db_connect.php';

// 1. LẤY ID KHÓA HỌC TỪ URL
if (!isset($_GET['id'])) {
    header("Location: courses.php");
    exit();
}
$course_id = intval($_GET['id']);

// 2. LẤY THÔNG TIN KHÓA HỌC
$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ? AND status = 'published'");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("Khóa học không tồn tại hoặc chưa được công bố.");
}

// 3. KIỂM TRA TRẠNG THÁI ĐĂNG KÝ (Nếu user đã login)
$is_enrolled = false;
if (isset($_SESSION['user_id'])) {
    $check_enroll = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $check_enroll->bind_param("ii", $_SESSION['user_id'], $course_id);
    $check_enroll->execute();
    if ($check_enroll->get_result()->num_rows > 0) {
        $is_enrolled = true;
    }
}

// 4. LẤY CHƯƠNG VÀ BÀI HỌC (Curriculum)
// Lấy danh sách chương
$chapters_query = $conn->query("SELECT * FROM chapters WHERE course_id = $course_id ORDER BY sort_order ASC");
$chapters_data = [];
$total_lessons = 0;
$total_seconds = 0; // Tính bằng phút trong DB, sau đó quy đổi

while ($chap = $chapters_query->fetch_assoc()) {
    $chap_id = $chap['chapter_id'];
    // Lấy bài học của chương này
    // Lưu ý: Cần chắc chắn bảng lessons có cột sort_order. Nếu chưa có, đổi thành ORDER BY lesson_id ASC
    $lessons_query = $conn->query("SELECT * FROM lessons WHERE chapter_id = $chap_id ORDER BY sort_order ASC");
    
    $lessons = [];
    while ($less = $lessons_query->fetch_assoc()) {
        $lessons[] = $less;
        $total_lessons++;
        $total_seconds += ($less['duration']); // duration lưu phút
    }
    
    $chap['lessons'] = $lessons;
    $chapters_data[] = $chap;
}

// Helper tính giờ phút
function format_total_time($minutes) {
    if ($minutes < 60) return $minutes . " phút";
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return "{$h} giờ {$m} phút";
}

// Helper lấy ảnh (Copy từ courses.php để đồng bộ)
function get_course_image($category) {
    $cat = strtolower($category);
    if (strpos($cat, 'web') !== false) return 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500&q=80';
    if (strpos($cat, 'data') !== false) return 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&q=80';
    if (strpos($cat, 'design') !== false) return 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&q=80';
    if (strpos($cat, 'mobile') !== false) return 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=500&q=80';
    if (strpos($cat, 'security') !== false) return 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=500&q=80';
    return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&q=80';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/course_detail.css?v=<?= time() ?>">
</head>
<body>

    <?php require 'src/templates/header.php'; ?>

    <main>
        <section class="course-hero">
            <div class="container hero-container">
                <div class="hero-content">
                    <div class="breadcrumbs">
                        <a href="index.php">Trang chủ</a> <i class="fa-solid fa-chevron-right"></i>
                        <a href="courses.php">Khóa học</a> <i class="fa-solid fa-chevron-right"></i>
                        <span><?php echo htmlspecialchars($course['category']); ?></span>
                    </div>
                    
                    <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <p class="course-desc">
                        <?php 
                            // Cắt ngắn mô tả nếu quá dài
                            echo htmlspecialchars($course['description']); 
                        ?>
                    </p>

                    <div class="course-meta-row">
                        <span class="badge-bestseller">Phổ biến</span>
                        <div class="rating-stars">
                            <span class="rating-number">5.0</span>
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            <span class="rating-count">(Mới)</span>
                        </div>
                        <span class="students-count"><i class="fa-solid fa-user-group"></i> Dành cho mọi người</span>
                    </div>

                    <div class="course-instructor-info">
                        Được tạo bởi <a href="#">EduTech Team</a> • Cập nhật mới nhất
                    </div>

                    <div class="course-lang">
                        <i class="fa-solid fa-globe"></i> Tiếng Việt
                    </div>
                </div>
            </div>
        </section>

        <section class="course-main-section">
            <div class="container layout-grid">
                
                <div class="course-left-col">
                    
                    <div class="what-learn-box">
                        <h3>Bạn sẽ học được gì?</h3>
                        <ul class="learn-list">
                            <li><i class="fa-solid fa-check"></i> Nắm vững kiến thức nền tảng và nâng cao</li>
                            <li><i class="fa-solid fa-check"></i> Thực hành qua các dự án thực tế</li>
                            <li><i class="fa-solid fa-check"></i> Tư duy giải quyết vấn đề logic</li>
                            <li><i class="fa-solid fa-check"></i> Kỹ năng làm việc với công nghệ mới nhất</li>
                        </ul>
                    </div>

                    <div class="course-curriculum">
                        <h3>Nội dung khóa học</h3>
                        <div class="curriculum-stats">
                            <span><?php echo count($chapters_data); ?> Chương</span> • 
                            <span><?php echo $total_lessons; ?> Bài học</span> • 
                            <span>Tổng thời lượng <?php echo format_total_time($total_seconds); ?></span>
                        </div>

                        <div class="accordion">
                            <?php if (!empty($chapters_data)): ?>
                                <?php foreach ($chapters_data as $chapter): ?>
                                    <div class="accordion-item">
                                        <div class="accordion-header" onclick="toggleAccordion(this)">
                                            <span class="accordion-title">
                                                <i class="fa-solid fa-chevron-down"></i> 
                                                <?php echo htmlspecialchars($chapter['title']); ?>
                                            </span>
                                            <span class="accordion-meta">
                                                <?php echo count($chapter['lessons']); ?> bài
                                            </span>
                                        </div>
                                        
                                        <div class="accordion-body" style="display: none;">
                                            <ul>
                                                <?php foreach ($chapter['lessons'] as $lesson): ?>
                                                    <li>
                                                        <div>
                                                            <?php 
                                                                // Icon theo loại
                                                                if ($lesson['lesson_type'] == 'video') echo '<i class="fa-regular fa-circle-play"></i>';
                                                                elseif ($lesson['lesson_type'] == 'quiz') echo '<i class="fa-solid fa-circle-question"></i>';
                                                                else echo '<i class="fa-regular fa-file-lines"></i>';
                                                            ?>
                                                            
                                                            <span><?php echo htmlspecialchars($lesson['title']); ?></span>
                                                        </div>
                                                        
                                                        <span class="time">
                                                            <?php echo $lesson['duration'] > 0 ? $lesson['duration'].'p' : ''; ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Nội dung đang được cập nhật...</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="instructor-section">
                        <h3>Giảng viên</h3>
                        <div class="instructor-profile">
                            <img src="https://ui-avatars.com/api/?name=Edu+Tech&background=random" alt="Instructor">
                            <div class="instructor-details">
                                <h4><a href="#">EduTech Team</a></h4>
                                <p class="job-title">Đội ngũ chuyên gia công nghệ</p>
                                <div class="instructor-stats">
                                    <span><i class="fa-solid fa-star"></i> 5.0 Xếp hạng</span>
                                    <span><i class="fa-solid fa-play-circle"></i> Nhiều Khóa học</span>
                                </div>
                                <p class="bio">
                                    Chúng tôi là đội ngũ giảng viên tâm huyết, cam kết mang đến những kiến thức công nghệ chất lượng cao và thực tế nhất cho cộng đồng.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-section">
                        <h3>Đánh giá từ học viên</h3>
                        <div class="review-item">
                            <div class="review-user">
                                <div class="user-avatar">HV</div>
                                <div class="user-info">
                                    <h5>Học viên EduTech</h5>
                                    <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                                </div>
                                <span class="review-time">Gần đây</span>
                            </div>
                            <p class="review-text">Khóa học rất bổ ích, nội dung sát thực tế.</p>
                        </div>
                    </div>

                </div>

                <div class="course-sidebar-wrapper">
                    <div class="course-sidebar">
                        <div class="preview-video">
                            <img src="<?php echo get_course_image($course['category']); ?>" alt="Course Preview">
                            <div class="play-btn"><i class="fa-solid fa-play"></i></div>
                            <div class="preview-text">Xem giới thiệu</div>
                        </div>

                        <div class="sidebar-content">
                            <div class="price-box">
                                <?php if($course['price'] == 0): ?>
                                    <span class="current-price" style="color: #16a34a;">Miễn phí</span>
                                <?php else: ?>
                                    <span class="current-price"><?php echo number_format($course['price'], 0, ',', '.'); ?>đ</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="time-left"><i class="fa-regular fa-clock"></i> Truy cập trọn đời</div>

                            <?php if ($is_enrolled): ?>
                                <a href="student/learning.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary btn-full" style="background: #16a34a;">
                                    <i class="fa-solid fa-play"></i> Vào học ngay
                                </a>
                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <a href="src/handlers/enroll_handler.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary btn-full">
                                    Đăng ký ngay
                                </a>
                            <?php else: ?>
                                <a href="src/handlers/enroll_handler.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary btn-full">
                                    Đăng ký ngay
                                </a>
                            <?php endif; ?>
                            <p class="guarantee">Đảm bảo chất lượng nội dung</p>

                            <div class="includes-box">
                                <h4>Khóa học bao gồm:</h4>
                                <ul>
                                    <li><i class="fa-solid fa-video"></i> <?php echo format_total_time($total_seconds); ?> video bài giảng</li>
                                    <li><i class="fa-solid fa-mobile-screen"></i> Truy cập trên Mobile</li>
                                    <li><i class="fa-solid fa-infinity"></i> Truy cập trọn đời</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <?php require 'src/templates/footer.php'; ?>

    <script>
        function toggleAccordion(header) {
            const body = header.nextElementSibling;
            const icon = header.querySelector('.fa-chevron-down');
            
            if (body.style.display === "none") {
                body.style.display = "block";
                header.style.background = "#eef1f3";
                if(icon) icon.style.transform = "rotate(180deg)";
            } else {
                body.style.display = "none";
                header.style.background = "#f7f9fa";
                if(icon) icon.style.transform = "rotate(0deg)";
            }
        }
    </script>

</body>
</html>