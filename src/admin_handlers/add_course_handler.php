<?php
session_start();
require '../core/db_connect.php'; 

// Bảo vệ: Chỉ admin mới được thực hiện
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin/login.php");
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

    // 2. Validation (Bạn có thể thêm validation kỹ hơn)
    if (empty($title) || empty($category)) {
        header("Location: ../../admin/admin_add_course.php?error=empty");
        exit();
    }

    // 3. Dùng Prepared Statement (An toàn)
    $stmt = $conn->prepare(
        "INSERT INTO courses (title, description, price, category, status) 
         VALUES (?, ?, ?, ?, ?)"
    );
    
    // "ssiss" = String, String, Integer, String, String
    $stmt->bind_param("ssiss", $title, $description, $price, $category, $status);

    // 4. Thực thi
    if ($stmt->execute()) {
        // Thêm thành công, chuyển hướng về trang quản lý
        header("Location: ../../admin/admin_courses.php?status=added");
        exit();
    } else {
        // Lỗi
        header("Location: ../../admin/admin_add_course.php?error=db_error");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    // Nếu truy cập trực tiếp file này
    header("Location: ../../admin/admin_courses.php");
    exit();
}
?>