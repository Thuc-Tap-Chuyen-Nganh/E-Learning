<?php
session_start();
require_once '../../config/config.php'; 

// Bảo vệ: Chỉ admin mới được thực hiện
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

// Chỉ chạy khi phương thức là POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu từ form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    // 2. Validation 
    if (empty($title) || empty($category)) {
        header("Location: " . BASE_URL . "admin/admin_add_course.php?error=empty");
        exit();
    }

    // Xử lý Upload Ảnh
    $thumbnail_path = null; // Mặc định là null

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['thumbnail']['name'];
        $filetype = $_FILES['thumbnail']['type'];
        $filesize = $_FILES['thumbnail']['size'];
        
        // Lấy đuôi file
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Validate đuôi file và kích thước (ví dụ < 5MB)
        if (in_array($ext, $allowed) && $filesize < 5000000) {
            // Đặt tên file mới để tránh trùng (time + random)
            $new_filename = time() . "_" . rand(1000, 9999) . "." . $ext;
            
            // Đường dẫn lưu file vật lý
            // Lưu ý: BASE_PATH đã được định nghĩa trong config.php
            $upload_dir = BASE_PATH . 'assets/uploads/courses/';
            
            // Kiểm tra và di chuyển file
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $new_filename)) {
                // Lưu đường dẫn tương đối vào DB
                $thumbnail_path = 'assets/uploads/courses/' . $new_filename;
            }
        }
    }

    // 3. Dùng Prepared Statement (An toàn)
    $stmt = $conn->prepare(
        "INSERT INTO courses (title, description, thumbnail, price, category, status)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    // "ssiss" = String, String, Integer, String, String
    $stmt->bind_param("sssiss", $title, $description, $thumbnail_path, $price, $category, $status);

    // 4. Thực thi
    if ($stmt->execute()) {
        // Thêm thành công, chuyển hướng về trang quản lý
        header("Location: " . BASE_URL . "admin/admin_courses.php?status=added");
        exit();
    } else {
        // Lỗi
        header("Location: " . BASE_URL . "admin/admin_add_course.php?error=db_error");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    // Nếu truy cập trực tiếp file này
    header("Location: " . BASE_URL . "admin/admin_courses.php");
    exit();
}
?>