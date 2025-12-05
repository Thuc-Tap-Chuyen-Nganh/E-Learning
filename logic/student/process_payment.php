<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: " . BASE_URL);
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = intval($_POST['course_id']);
$amount = floatval($_POST['amount']);
$transaction_code = $_POST['transaction_code']; // Lấy mã DH... từ form

// 1. Lưu vào bảng PAYMENTS (Trạng thái mặc định là 'pending')
$stmt = $conn->prepare("INSERT INTO payments (user_id, course_id, amount, transaction_code, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("iids", $user_id, $course_id, $amount, $transaction_code);

if ($stmt->execute()) {
    // 2. Chuyển hướng về trang khóa học của tôi với thông báo chờ duyệt
    header("Location: " . BASE_URL . "student/my_courses.php?status=pending_approval");
    exit();
} else {
    echo "Lỗi: " . $conn->error;
}
?>