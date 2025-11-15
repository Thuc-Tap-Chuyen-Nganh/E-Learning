<?php
session_start();
require '../core/db_connect.php';

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu
    // Lấy thêm course_id từ trường <input type="hidden">
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    // 2. Validation
    if (empty($title) || empty($category) || empty($course_id)) {
        header("Location: ../../admin/admin_edit_course.php?id=$course_id&error=empty");
        exit();
    }

    // 3. Dùng Prepared Statement (lệnh UPDATE)
    $stmt = $conn->prepare(
        "UPDATE courses 
         SET title = ?, description = ?, price = ?, category = ?, status = ?
         WHERE course_id = ?"
    );
    
    // "ssissi" = String, String, Integer, String, String, Integer (cho course_id)
    $stmt->bind_param("ssissi", $title, $description, $price, $category, $status, $course_id);

    // 4. Thực thi
    if ($stmt->execute()) {
        // Cập nhật thành công
        header("Location: ../../admin/admin_courses.php?status=updated");
        exit();
    } else {
        // Lỗi
        header("Location: ../../admin/admin_edit_course.php?id=$course_id&error=db_error");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: ../../admin/admin_courses.php");
    exit();
}
?>