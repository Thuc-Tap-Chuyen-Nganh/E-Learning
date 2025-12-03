<?php
// src/handlers/enroll_handler.php
session_start();
require '../core/db_connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Lưu lại trang hiện tại để login xong quay lại (Tính năng nâng cao, tạm thời chưa cần)
    header("Location: ../../login.php");
    exit();
}

// 2. Kiểm tra dữ liệu đầu vào
if (!isset($_GET['course_id'])) {
    header("Location: ../../courses.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id']);

// 3. Kiểm tra xem đã đăng ký chưa (Tránh trùng lặp)
$check_stmt = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE user_id = ? AND course_id = ?");
$check_stmt->bind_param("ii", $user_id, $course_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    // 4. Nếu CHƯA đăng ký -> Thêm vào bảng enrollments
    // progress mặc định là 0
    $enroll_stmt = $conn->prepare("INSERT INTO enrollments (user_id, course_id, progress) VALUES (?, ?, 0)");
    $enroll_stmt->bind_param("ii", $user_id, $course_id);
    
    if ($enroll_stmt->execute()) {
        // Đăng ký thành công -> Chuyển hướng đến trang "Khóa học của tôi" hoặc "Vào học"
        // Tạm thời chuyển về trang My Courses để thấy kết quả
        header("Location: ../../student/my_courses.php");
        exit();
    } else {
        echo "Lỗi hệ thống: " . $conn->error;
    }
} else {
    // 5. Nếu ĐÃ đăng ký rồi -> Chuyển hướng vào học luôn (tránh báo lỗi)
    header("Location: ../../student/my_courses.php");
    exit();
}
?>