<?php
// admin/admin_edit_course.php

session_start();
require '../src/core/db_connect.php'; // Gọi $conn

// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 1. LẤY ID TỪ URL
if (!isset($_GET['id'])) {
    header("Location: admin_courses.php?error=missing_id");
    exit();
}
$course_id = $_GET['id'];

// 2. LẤY DỮ LIỆU CŨ CỦA KHÓA HỌC
$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

// Nếu không tìm thấy khóa học
if (!$course) {
    header("Location: admin_courses.php?error=not_found");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Thêm khóa học mới</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                <li><a href="admin_dashboard.php"><i class="fa-solid fa-table-columns"></i> <span>Tổng quan</span></a></li>
                <li><a href="admin_courses.php" class="active"><i class="fa-solid fa-book"></i> <span>Quản lý khóa học</span></a></li>
                <li><a href="admin_students.php"><i class="fa-solid fa-users"></i> <span>Quản lý học viên</span></a></li>
                <li><a href="admin_reports.php"><i class="fa-solid fa-chart-pie"></i> <span>Báo cáo thống kê</span></a></li>
            </ul>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Thêm khóa học mới</h1>
                <p>Điền thông tin chi tiết cho khóa học</p>
            </div>
            <a href="../src/admin_handlers/admin_logout.php" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </a>
        </header>

        <main class="main-content">
            <div class="form-container">
                <form action="../src/admin_handlers/edit_course_handler.php" method="POST" class="course-form">
                    
                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">

                    <div class="form-group">
                        <label for="title">Tên khóa học</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($course['description']); ?></textarea>
                    </div>

                    <div class="form-group form-group-small">
                        <label for="price_display">Giá (VNĐ)</label>
                        <input type="text" id="price_display" min="0" value="<?php echo $course['price']; ?>" required>
                        <input type="hidden" id="price" name="price" value="<?php echo $course['price']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Danh mục</label>
                        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($course['category']); ?>" required>
                    </div>

                    <div class="form-group form-group-small">
                        <label for="status">Trạng thái</label>
                        <div class="select-wrapper">
                            <select id="status" name="status">
                                <option value="draft" <?php if($course['status'] == 'draft') echo 'selected'; ?>>Nháp (Draft)</option>
                                <option value="published" <?php if($course['status'] == 'published') echo 'selected'; ?>>Công bố (Published)</option>
                                <option value="archived" <?php if($course['status'] == 'archived') echo 'selected'; ?>>Lưu trữ (Archived)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="admin_courses.php" class="btn btn-secondary">Hủy</a>
                    </div>

                </form>
            </div>
        </main>
    </div>

    // Định dang ôn nhập liệu "Giá"
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceDisplay = document.getElementById('price_display');
            const priceHidden = document.getElementById('price');

            // Định dạng ban đầu
            formatPrice(priceDisplay.value);

            // Thêm sự kiện 'input' để theo dõi mỗi khi người dùng gõ
            priceDisplay.addEventListener('input', function(e) {
                formatPrice(e.target.value);
            });

            function formatPrice(value) {
                // 1. Loại bỏ tất cả dấu chấm (,) và ký tự không phải số
                let numericValue = value.replace(/[^0-9]/g, '');

                // 2. Gán giá trị số thực (đã làm sạch) cho ô ẩn
                if (numericValue === '') {
                    numericValue = '0'; // Tránh lỗi NaN
                }
                priceHidden.value = numericValue;

                // 3. Định dạng giá trị hiển thị với dấu chấm (vi-VN)
                let formattedValue = new Intl.NumberFormat('vi-VN').format(numericValue);
                
                // 4. Cập nhật lại giá trị cho ô hiển thị
                priceDisplay.value = formattedValue;
            }
        });
    </script>
</body>
</html>