<?php
// Gọi file cấu hình (đã bao gồm kết nối DB và session)
require_once 'config/config.php'; 

// --- 1. LẤY SỐ LIỆU THỐNG KÊ (REALTIME) ---
// Đếm tổng học viên
$res_students = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
$count_students = $res_students->fetch_row()[0];

// Đếm tổng khóa học đã xuất bản
$res_courses = $conn->query("SELECT COUNT(*) FROM courses WHERE status = 'published'");
$count_courses = $res_courses->fetch_row()[0];

// Đếm tổng lượt đăng ký (Học viên đang học)
$res_enrolls = $conn->query("SELECT COUNT(*) FROM enrollments");
$count_enrolls = $res_enrolls->fetch_row()[0];

// --- 2. LẤY 3 KHÓA HỌC MỚI NHẤT CHO PHẦN "NỔI BẬT" ---
$sql_featured = "SELECT * FROM courses WHERE status = 'published' ORDER BY course_id DESC LIMIT 3";
$result_featured = $conn->query($sql_featured);

// --- 3. LẤY TOP 3 DANH MỤC PHỔ BIẾN CHO HERO QUICK LINKS ---
$sql_top_hero = "SELECT c.category, COUNT(e.enrollment_id) as enroll_count FROM enrollments e JOIN courses c ON e.course_id = c.course_id WHERE c.status = 'published' GROUP BY c.category ORDER BY enroll_count DESC LIMIT 3";
$result_top_hero = $conn->query($sql_top_hero);

// --- 4. LẤY TOP 4 DANH MỤC PHỔ BIẾN CHO CATEGORIES SECTION ---
$sql_top_categories = "SELECT c.category, COUNT(e.enrollment_id) as enroll_count FROM enrollments e JOIN courses c ON e.course_id = c.course_id WHERE c.status = 'published' GROUP BY c.category ORDER BY enroll_count DESC LIMIT 4";
$result_top_categories = $conn->query($sql_top_categories);

// --- 5. MAP ICONS CHO CATEGORIES ---
$category_icons = [
    'Lập trình Web' => ['class' => 'icon-code', 'icon' => 'fa-code'],
    'Lập trình Mobile' => ['class' => 'icon-mobile', 'icon' => 'fa-mobile-screen'],
    'Data Science' => ['class' => 'icon-data', 'icon' => 'fa-database'],
    'An ninh mạng' => ['class' => 'icon-security', 'icon' => 'fa-shield-halved'],
    'AI' => ['class' => 'icon-ai', 'icon' => 'fa-brain'],
    'Cloud' => ['class' => 'icon-cloud', 'icon' => 'fa-cloud'],
    'default' => ['class' => 'icon-code', 'icon' => 'fa-code']
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Học công nghệ không giới hạn</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="favicon.ico">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/index.css?v=<?= time() ?>">
</head>
<body>

    <?php require 'includes/header.php'; ?>

    <main>
        <section class="hero-new">
            <div class="container">
                <div class="hero-badge">
                    <i class="fa-solid fa-graduation-cap"></i> 
                    Tham gia cùng <strong><?= number_format($count_students) ?>+</strong> học viên tài năng
                </div>
                
                <h1 class="hero-title">
                    Học Kỹ năng Công nghệ <br>
                    <span class="highlight">Mọi lúc, Mọi nơi</span>
                </h1>
                
                <p class="hero-subtitle">
                    Làm chủ các kỹ năng công nghệ hàng đầu với <strong><?= $count_courses ?></strong> khóa học chuyên sâu về Lập trình, Khoa học dữ liệu, AI, và nhiều hơn nữa.
                </p>

                <div class="hero-search-container">
                    <form action="courses.php" method="GET" class="hero-search-form">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="q" placeholder="Bạn muốn học gì hôm nay? (Ví dụ: React, Python...)" required>
                        <button type="submit">Tìm kiếm</button>
                    </form>
                </div>

                <div class="hero-quick-links">
                    <span>Phổ biến:</span>
                    <?php if ($result_top_hero->num_rows > 0): ?>
                        <?php while($cat = $result_top_hero->fetch_assoc()): ?>
                            <a href="courses.php?category=<?= urlencode($cat['category']) ?>"><?= htmlspecialchars($cat['category']) ?></a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="custom-shape-divider-bottom">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                </svg>
            </div>
        </section>

        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-blue"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($count_students) ?></h3>
                            <p>Học viên đăng ký</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-purple"><i class="fa-solid fa-book-bookmark"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($count_courses) ?></h3>
                            <p>Khóa học video</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-pink"><i class="fa-solid fa-chalkboard-user"></i></div>
                        <div class="stat-info">
                            <h3><?= number_format($count_enrolls) ?></h3>
                            <p>Lượt tham gia học</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-green"><i class="fa-solid fa-star"></i></div>
                        <div class="stat-info">
                            <h3>4.9/5</h3>
                            <p>Đánh giá trung bình</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section categories">
            <div class="container">
                <div class="section-header center">
                    <h2>Khám phá Danh mục phổ biến</h2>
                    <p>Chọn từ các lĩnh vực công nghệ hot nhất hiện nay</p>
                </div>

                <div class="categories-grid">
                    <?php if ($result_top_categories->num_rows > 0): ?>
                        <?php while($cat = $result_top_categories->fetch_assoc()): ?>
                            <?php
                            $icon_data = $category_icons[$cat['category']] ?? $category_icons['default'];
                            ?>
                            <a href="courses.php?category=<?= urlencode($cat['category']) ?>" class="cat-card">
                                <div class="cat-icon <?= $icon_data['class'] ?>"><i class="fa-solid <?= $icon_data['icon'] ?>"></i></div>
                                <h3><?= htmlspecialchars($cat['category']) ?></h3>
                            </a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="section courses-featured bg-light">
            <div class="container">
                <div class="section-header flex-between">
                    <div>
                        <h2>Khóa học mới nhất</h2>
                        <p>Cập nhật kiến thức mới nhất mỗi ngày</p>
                    </div>
                    <a href="courses.php" class="view-all-btn">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="course-grid">
                    <?php if ($result_featured->num_rows > 0): ?>
                        <?php while($row = $result_featured->fetch_assoc()): ?>
                            <div class="course-card">
                                <div class="course-thumb">
                                    <img src="<?= get_course_image($row['thumbnail'], $row['category']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                                    <span class="badge badge-beginner">Mới</span>
                                </div>
                                <div class="course-body">
                                    <div class="course-cat"><?= htmlspecialchars($row['category']) ?></div>
                                    
                                    <h3 class="course-title">
                                        <a href="course_detail.php?id=<?= $row['course_id'] ?>">
                                            <?= htmlspecialchars($row['title']) ?>
                                        </a>
                                    </h3>
                                    
                                    <div class="course-instructor">
                                        <img src="assets/images/EdutechTeam.png" alt="Instructor">
                                        <span>EduTech Team</span>
                                    </div>
                                    
                                    <div class="course-rating">
                                        <span class="rating-val"><?= $row['avg_rating'] > 0 ? $row['avg_rating'] : '5.0' ?></span>
                                        <div class="stars" style="color: #f59e0b; font-size: 12px;">
                                            <?php
                                                // Nếu chưa có đánh giá nào thì hiện 5 sao ảo cho đẹp
                                                $rating_display = $row['avg_rating'] > 0 ? round($row['avg_rating']) : 5;
                                                for($k=1; $k<=5; $k++) {
                                                    echo $k <= $rating_display ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star" style="color:#ccc;"></i>';
                                                }
                                            ?>
                                        </div>
                                        <span class="rating-count" style="font-size: 12px; color: #777;">(<?= $row['review_count'] ?> đánh giá)</span>
                                    </div>
                                    
                                    <div class="course-footer">
                                        <div class="course-meta">
                                            <span><i class="fa-regular fa-clock"></i> Online</span>
                                            <span><i class="fa-solid fa-video"></i> Video</span>
                                        </div>
                                        <div class="course-price">
                                            <?= format_currency($row['price']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="grid-column: 1/-1; text-align: center;">Chưa có khóa học nào được xuất bản.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="cta-bottom">
            <div class="container">
                <div class="cta-content">
                    <h2>Bắt đầu hành trình học tập ngay hôm nay</h2>
                    <p>Tham gia cộng đồng học tập và bắt đầu làm chủ các kỹ năng công nghệ mới nhất.</p>
                    <div class="cta-buttons">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="register.php" class="btn btn-white">Đăng ký miễn phí</a>
                        <?php else: ?>
                            <a href="student/my_courses.php" class="btn btn-white">Vào bàn học</a>
                        <?php endif; ?>
                        <a href="courses.php" class="btn btn-outline-white">Xem lộ trình</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php require 'includes/footer.php'; ?>

</body>
</html>
