<?php
// src/admin_handlers/add_chapter_handler.php

session_start();
require '../core/db_connect.php'; // Gọi CSDL

// === BẢO VỆ ===
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description']; // Lưu ý: Bảng chapters cần có cột này, nếu chưa có hãy thêm vào hoặc bỏ qua
    $sort_order = $_POST['sort_order'];

    // 2. Validation
    if (empty($title) || empty($course_id)) {
        header("Location: ../../admin/admin_course_details.php?course_id=$course_id&error=empty");
        exit();
    }

    // 3. Chèn vào CSDL
    // Lưu ý: Đảm bảo bảng 'chapters' của bạn có các cột này: course_id, title, sort_order
    // Nếu bạn muốn lưu 'description', hãy chắc chắn bảng có cột đó.
    // Giả sử bảng chapters chỉ có: chapter_id, course_id, title, sort_order
    
    // NẾU KHÔNG CÓ CỘT DESCRIPTION TRONG CSDL:
    // $stmt = $conn->prepare("INSERT INTO chapters (course_id, title, sort_order) VALUES (?, ?, ?)");
    // $stmt->bind_param("isi", $course_id, $title, $sort_order);

    // NẾU CÓ CỘT DESCRIPTION:
    // Hãy chạy lệnh SQL này để thêm cột nếu chưa có: ALTER TABLE chapters ADD COLUMN description TEXT;
    $stmt = $conn->prepare("INSERT INTO chapters (course_id, title, description, sort_order) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $course_id, $title, $description, $sort_order);

    if ($stmt->execute()) {
        header("Location: ../../admin/admin_course_details.php?course_id=$course_id&status=added");
    } else {
        header("Location: ../../admin/admin_course_details.php?course_id=$course_id&error=db_error");
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: ../../admin/admin_courses.php");
    exit();
}
?>