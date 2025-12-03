<?php
session_start();
require_once 'src/core/db_connect.php'; // Kết nối CSDL

// === 1. XỬ LÝ LỌC & TÌM KIẾM ===
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Xây dựng câu truy vấn cơ bản
// Chỉ lấy khóa học đã Công bố (Published)
$sql = "SELECT * FROM courses WHERE status = 'published'";
$params = [];
$types = "";

// Nếu có tìm kiếm
if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Nếu có lọc theo danh mục
if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

// Sắp xếp mới nhất
$sql .= " ORDER BY course_id DESC";

// === 2. PHÂN TRANG (PAGINATION) ===
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 9; // Số khóa học mỗi trang
$offset = ($page - 1) * $limit;

// Tách câu lệnh để đếm tổng số dòng (cho phân trang)
// (Ở đây làm đơn giản: Query lấy hết trước rồi array_slice, 
// nhưng cách chuẩn là dùng SELECT COUNT(*) trước. Để code gọn ta làm query trực tiếp)
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$all_results = $stmt->get_result();
$total_courses = $all_results->num_rows;
$total_pages = ceil($total_courses / $limit);

// Bây giờ thêm LIMIT OFFSET vào SQL chính để lấy dữ liệu trang hiện tại
$sql_limit = $sql . " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt_final = $conn->prepare($sql_limit);
$stmt_final->bind_param($types, ...$params);
$stmt_final->execute();
$result_courses = $stmt_final->get_result();

// === 3. HÀM HELPER: LẤY ẢNH THEO DANH MỤC (VÌ DB CHƯA CÓ ẢNH) ===
function get_course_image($category) {
    $cat = strtolower($category);
    if (strpos($cat, 'web') !== false) return 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500&q=80'; // Ảnh code
    if (strpos($cat, 'data') !== false) return 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&q=80'; // Ảnh biểu đồ
    if (strpos($cat, 'design') !== false || strpos($cat, 'thiết kế') !== false) return 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&q=80'; // Ảnh nghệ thuật
    if (strpos($cat, 'mobile') !== false) return 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=500&q=80'; // Ảnh điện thoại
    if (strpos($cat, 'security') !== false || strpos($cat, 'bảo mật') !== false) return 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=500&q=80'; // Ảnh hacker
    return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&q=80'; // Ảnh mặc định (Laptop)
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Khóa học - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/courses.css?v=<?= time() ?>">
</head>
<body>

    <?php require 'src/templates/header.php'; ?>

    <main>
        <section class="courses-banner">
            <div class="container">
                <div class="banner-content">
                    <span class="sub-title">Tất cả khóa học</span>
                    <h1>Khám phá thư viện khóa học CNTT toàn diện</h1>
                    
                    <div class="search-bar-wrapper">
                        <form action="courses.php" method="GET" class="search-form">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="q" placeholder="Tìm kiếm khóa học..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit">Tìm kiếm</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="courses-layout-section">
            <div class="container">
                <div class="layout-grid">
                    
                    <aside class="sidebar-filter">
                        <div class="filter-header">
                            <h3><i class="fa-solid fa-sliders"></i> Bộ lọc</h3>
                            <a href="courses.php" class="reset-filter">Đặt lại</a>
                        </div>

                        <div class="filter-group">
                            <h4>Danh mục</h4>
                            <ul class="filter-list">
                                <li><a href="courses.php" class="<?= $category_filter == '' ? 'active' : '' ?>">Tất cả</a></li>
                                <li><a href="courses.php?category=Lập trình Web" class="<?= $category_filter == 'Lập trình Web' ? 'active' : '' ?>">Lập trình Web</a></li>
                                <li><a href="courses.php?category=Lập trình Mobile" class="<?= $category_filter == 'Lập trình Mobile' ? 'active' : '' ?>">Lập trình Mobile</a></li>
                                <li><a href="courses.php?category=Data Science" class="<?= $category_filter == 'Data Science' ? 'active' : '' ?>">Data Science</a></li>
                                <li><a href="courses.php?category=Thiết kế" class="<?= $category_filter == 'Thiết kế' ? 'active' : '' ?>">Thiết kế UI/UX</a></li>
                            </ul>
                        </div>

                        <div class="filter-group">
                            <h4>Trình độ</h4>
                            <ul class="filter-list">
                                <li><a href="#">Tất cả trình độ</a></li>
                                <li><a href="#">Người mới (Beginner)</a></li>
                                <li><a href="#">Nâng cao (Advanced)</a></li>
                            </ul>
                        </div>
                    </aside>

                    <div class="course-list-content">
                        <div class="list-top-bar">
                            <p class="result-count">Hiển thị <strong><?php echo $result_courses->num_rows; ?></strong> trên tổng <strong><?php echo $total_courses; ?></strong> khóa học</p>
                            <div class="sort-box">
                                <select class="sort-select">
                                    <option>Mới nhất</option>
                                    <option>Giá thấp đến cao</option>
                                </select>
                            </div>
                        </div>

                        <div class="course-grid">
                            <?php if ($result_courses->num_rows > 0): ?>
                                <?php while($row = $result_courses->fetch_assoc()): ?>
                                    
                                    <div class="course-card">
                                        <div class="course-thumb">
                                            <img src="<?php echo get_course_image($row['category']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                            <span class="badge badge-beginner">Mọi trình độ</span>
                                        </div>
                                        <div class="course-body">
                                            <div class="course-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                                            
                                            <h3 class="course-title">
                                                <a href="course_detail.php?id=<?php echo $row['course_id']; ?>">
                                                    <?php echo htmlspecialchars($row['title']); ?>
                                                </a>
                                            </h3>
                                            
                                            <div class="course-instructor">
                                                <img src="https://ui-avatars.com/api/?name=Edu+Tech&background=random" alt="Instructor">
                                                <span>EduTech Team</span>
                                            </div>
                                            
                                            <div class="course-rating">
                                                <span class="rating-val">5.0</span>
                                                <div class="stars">
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                    <i class="fa-solid fa-star"></i>
                                                </div>
                                                <span class="rating-count">(Mới)</span>
                                            </div>
                                            
                                            <div class="course-footer">
                                                <div class="course-meta">
                                                    <span><i class="fa-solid fa-video"></i> Online</span>
                                                </div>
                                                <div class="course-price">
                                                    <?php if($row['price'] == 0): ?>
                                                        <span style="color: #16a34a;">Miễn phí</span>
                                                    <?php else: ?>
                                                        <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                            <?php else: ?>
                                <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                                    <img src="public/images/empty-search.png" alt="" style="width: 100px; margin: 0 auto 20px; opacity: 0.5;">
                                    <h3>Không tìm thấy khóa học nào</h3>
                                    <p>Thử tìm kiếm từ khóa khác hoặc đặt lại bộ lọc.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <a href="?page=<?php echo max(1, $page - 1); ?>&q=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($category_filter); ?>" 
                               class="page-link <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                               <i class="fa-solid fa-chevron-left"></i>
                            </a>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&q=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($category_filter); ?>" 
                                   class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                                   <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <a href="?page=<?php echo min($total_pages, $page + 1); ?>&q=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($category_filter); ?>" 
                               class="page-link <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                               <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php require 'src/templates/footer.php'; ?>

</body>
</html>