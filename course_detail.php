<?php
session_start();
require_once 'config/config.php';

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

// 5. LẤY DANH SÁCH REVIEW
$reviews_query = $conn->query("
    SELECT r.*, u.username, u.avatar 
    FROM reviews r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.course_id = $course_id 
    ORDER BY r.created_at DESC
");
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

    <link rel="stylesheet" href="assets/css/index.css?v=<?= filemtime('assets/css/index.css') ?>">
    <link rel="stylesheet" href="assets/css/course_detail.css?v=<?= time() ?>">
</head>
<body>

    <?php require 'includes/header.php'; ?>

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
                            <span class="rating-number"><?= $course['avg_rating'] ?? '5.0' ?></span>
                            <div class="stars" style="color: #f59e0b; margin-top: 5px;">
                                <?php
                                    $stars = round($course['avg_rating'] ?? 0);
                                    for($i=1; $i<=5; $i++) {
                                        echo $i <= $stars ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                    }
                                ?>
                            </div>
                        </div>
                        <span class="students-count"><i class="fa-solid fa-user-group"></i></span>
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
                            <span>Tổng thời lượng <?php echo format_time($total_seconds); ?></span>
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
                                    <span><i class="fa-solid fa-star"></i> 4.9 Xếp hạng</span>
                                    <span><i class="fa-solid fa-play-circle"></i> Nhiều Khóa học</span>
                                </div>
                                <p class="bio">
                                    Chúng tôi là đội ngũ giảng viên tâm huyết, cam kết mang đến những kiến thức công nghệ chất lượng cao và thực tế nhất cho cộng đồng.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-section" id="reviews">
                        <h3>Đánh giá từ học viên (<?= $course['review_count'] ?? 0 ?>)</h3>
                        
                        <div class="review-summary" style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; background: #f9fafb; padding: 20px; border-radius: 12px;">
                            <div class="big-rating" style="text-align: center;">
                                <div style="font-size: 48px; font-weight: 800; color: #b45309; line-height: 1;"><?= $course['avg_rating'] ?? '5.0' ?></div>
                                <div class="stars" style="color: #f59e0b; margin-top: 5px;">
                                    <?php
                                        $stars = round($course['avg_rating'] ?? 0);
                                        for($i=1; $i<=5; $i++) {
                                            echo $i <= $stars ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                        }
                                    ?>
                                </div>
                                <div style="font-size: 13px; color: #666; margin-top: 5px;">Điểm trung bình</div>
                            </div>
                            
                            <?php if ($is_enrolled): ?>
                                <div style="flex-grow: 1; border-left: 1px solid #ddd; padding-left: 20px;">
                                    <h4 style="margin-bottom: 10px;">Viết đánh giá của bạn</h4>
                                    <form action="<?= BASE_URL ?>logic/student/submit_review.php" method="POST">
                                        <input type="hidden" name="course_id" value="<?= $course_id ?>">
                                        
                                        <div class="rating-input" style="margin-bottom: 10px;">
                                            <div class="star-rating">
                                                <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 sao"><i class="fa-solid fa-star"></i></label>
                                                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 sao"><i class="fa-solid fa-star"></i></label>
                                                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 sao"><i class="fa-solid fa-star"></i></label>
                                                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 sao"><i class="fa-solid fa-star"></i></label>
                                                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 sao"><i class="fa-solid fa-star"></i></label>
                                            </div>
                                        </div>
                                        
                                        <textarea name="comment" placeholder="Chia sẻ cảm nhận của bạn..." required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 10px;"></textarea>
                                        <button type="submit" class="btn btn-primary" style="padding: 8px 20px; font-size: 14px;">Gửi đánh giá</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="review-list">
                            <?php if ($reviews_query->num_rows > 0): ?>
                                <?php while ($rv = $reviews_query->fetch_assoc()): 
                                    $rv_avatar = !empty($rv['avatar']) && file_exists(BASE_PATH.$rv['avatar']) ? BASE_URL.$rv['avatar'] : "https://ui-avatars.com/api/?name=".urlencode($rv['username']);
                                ?>
                                    <div class="review-item">
                                        <div class="review-user">
                                            <img src="<?= $rv_avatar ?>" class="user-avatar" style="object-fit:cover;">
                                            <div class="user-info">
                                                <h5 style="margin: 0;"><?= htmlspecialchars($rv['username']) ?></h5>
                                                <div class="stars" style="font-size: 12px;">
                                                    <?php for($k=1; $k<=5; $k++) echo $k <= $rv['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star" style="color:#ddd;"></i>'; ?>
                                                </div>
                                            </div>
                                            <span class="review-time"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></span>
                                        </div>
                                        <p class="review-text" style="margin-top: 10px; color: #444;"><?= nl2br(htmlspecialchars($rv['comment'])) ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p style="color: #666; font-style: italic;">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <div class="course-sidebar-wrapper">
                    <div class="course-sidebar">
                        <div class="preview-video">
                            <img src="<?php echo get_course_image($course['thumbnail'], $course['category']); ?>" alt="Course Preview">
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
                            <?php else: ?>
                                <?php if ($course['price'] == 0): ?>
                                    <a href="<?= BASE_URL ?>logic/student/enroll.php?course_id=<?= $course_id ?>" class="btn btn-primary btn-full">
                                        Đăng ký miễn phí
                                    </a>
                                <?php else: ?>
                                    <a href="checkout.php?course_id=<?= $course_id ?>" class="btn btn-primary btn-full">
                                        Mua khóa học ngay
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="guarantee">Đảm bảo chất lượng nội dung</p>

                            <div class="includes-box">
                                <h4>Khóa học bao gồm:</h4>
                                <ul>
                                    <li><i class="fa-solid fa-video"></i> <?php echo format_time($total_seconds); ?> video bài giảng</li>
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

    <?php require 'includes/footer.php'; ?>

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