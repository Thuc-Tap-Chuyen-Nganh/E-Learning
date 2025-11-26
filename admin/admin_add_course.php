<?php
session_start();
// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
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

    <?php include 'templates/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Thêm khóa học mới</h1>
                <p>Điền thông tin chi tiết cho khóa học</p>
            </div>
        </header>

        <main class="main-content">
            <div class="form-container">
                <form action="../src/admin_handlers/add_course_handler.php" method="POST" class="course-form">
                    
                    <div class="form-group">
                        <label for="title">Tên khóa học</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" rows="5"></textarea>
                    </div>

                    <div class="form-group form-group-small">
                        <label for="price_display">Giá (VNĐ)</label>
                        <input type="text" id="price_display" min="0" required>
                        <input type="hidden" id="price" name="price" value="0">
                    </div>

                    <div class="form-group">
                        <label for="category">Danh mục</label>
                        <input type="text" id="category" name="category" required>
                    </div>

                    <div class="form-group form-group-small">
                        <label for="status">Trạng thái</label>
                        <div class="select-wrapper">
                            <select id="status" name="status">
                                <option value="draft">Nháp (Draft)</option>
                                <option value="published">Công bố (Published)</option>
                                <option value="archived">Lưu trữ (Archived)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Lưu khóa học</button>
                        <a href="admin_courses.php" class="btn btn-secondary">Hủy</a>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- Định dang ô nhập liệu "Giá" -->
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