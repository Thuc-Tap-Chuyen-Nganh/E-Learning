<?php
// admin/admin_courses.php

session_start();
require '../src/core/db_connect.php'; // Gọi file kết nối CSDL

// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// === LẤY TỪ KHÓA TÌM KIẾM (NẾU CÓ) ===
// Dùng '??' để gán giá trị rỗng nếu không tồn tại
$search_term = $_GET['search'] ?? '';

// === 1. LẤY SỐ LIỆU THỐNG KÊ ===
// Lấy tổng số khóa học
$total_result = $conn->query("SELECT COUNT(*) as total_courses FROM courses");
$total_courses = $total_result->fetch_assoc()['total_courses'];

// Lấy số khóa học đang hoạt động (giả sử 'published' là hoạt động)
$active_result = $conn->query("SELECT COUNT(*) as active_courses FROM courses WHERE status = 'published'");
$active_courses = $active_result->fetch_assoc()['active_courses'];

// (Bạn sẽ cần một câu lệnh SQL phức tạp hơn để lấy 'Tổng học viên' sau này)

// === 2. LẤY DANH SÁCH KHÓA HỌC ===
if (!empty($search_term)) {
    // NẾU CÓ TÌM KIẾM
    $like_term = "%" . $search_term . "%"; // Thêm dấu % để tìm kiếm (LIKE)
    
    // Tìm ở cột 'title' hoặc 'category'
    $stmt = $conn->prepare(
        "SELECT * FROM courses 
         WHERE title LIKE ? OR category LIKE ? 
         ORDER BY course_id DESC"
    );
    $stmt->bind_param("ss", $like_term, $like_term);
    $stmt->execute();
    $courses_result = $stmt->get_result();
    
} else {
    // NẾU KHÔNG TÌM KIẾM DANH SÁCH TẤT CẢ CÁC KHÓA HỌC
    $courses_result = $conn->query("SELECT * FROM courses ORDER BY course_id DESC");
}

// Helper để định dạng trạng thái
function format_status($status) {
    switch ($status) {
        case 'published':
            return '<span class="status status-active">Đang hoạt động</span>';
        case 'draft':
            return '<span class="status status-draft">Nháp</span>';
        case 'archived':
            return '<span class="status status-inactive">Lưu trữ</span>';
        default:
            return '<span class="status">' . htmlspecialchars($status) . '</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý khóa học</title>

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
                    <a href="admin_courses.php" class="active"> <i class="fa-solid fa-book"></i>
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
                    <a href="admin_reports.php"> 
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Báo cáo thống kê</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Quản lý khóa học</h1>
                <p>Quản lý và theo dõi tất cả các khóa học</p>
            </div>
            <a href="../src/admin_handlers/admin_logout.php" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </a>
        </header>

        <main class="main-content">
            <section class="stat-cards-grid">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng khóa học</span>
                        <span class="card-value"><?php echo $total_courses ?></span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-book"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Đang hoạt động</span>
                        <span class="card-value"><?php echo $active_courses ?></span>
                    </div>
                    <div class="card-icon icon-green">
                        <i class="fa-solid fa-book-reader"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng học viên</span>
                        <span class="card-value">755</span>
                    </div>
                    <div class="card-icon icon-purple">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
            </section>

            <section class="table-container">
                <div class="table-header">
                    <h2>Danh sách khóa học</h2>
                    <div class="table-controls">
                        <form action="admin_courses.php" method="GET" class="search-form">
                            <div class="search-box">
                                <i class="fa-solid fa-search"></i>
                                <input 
                                    type="text" 
                                    name="search" 
                                    placeholder="Tìm kiếm khóa học..."
                                    value="<?php echo htmlspecialchars($search_term); ?>"
                                >
                            </div>
                        </form>
                        <a href="admin_add_course.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i>
                            <span>Thêm khóa học</span>
                        </a>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên khóa học</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // === 3. VÒNG LẶP HIỂN THỊ KHÓA HỌC ===
                        if ($courses_result->num_rows > 0) {
                            while($row = $courses_result->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo number_format($row['price'], 0, ',', '.'); ?>₫</td>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo format_status($row['status']); ?></td>
                                    <td>
                                        <a href="admin_edit_course.php?id=<?php echo $row['course_id']; ?>" class="btn-icon btn-edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <button class="btn-icon btn-delete delete-btn" 
                                                data-id="<?php echo $row['course_id']; ?>"
                                                data-title="<?php echo htmlspecialchars($row['title']); ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            // Nếu không có khóa học nào
                            echo '<tr><td colspan="5" style="text-align: center;">Chưa có khóa học nào.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <h2>Xác nhận xóa</h2>
            <p>Bạn có chắc chắn muốn xóa khóa học này không? Hành động này không thể hoàn tác.</p>
            <div class="modal-actions">
                <button id="modalCancelBtn" class="btn btn-secondary">Hủy</button>
                <button id="modalConfirmBtn" class="btn btn-danger">Xóa</button>
            </div>
        </div>
    </div>
    <script src="js/admin_courses.js"></script>
</body>
</html>