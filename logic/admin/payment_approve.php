<?php
// logic/admin/payment_approve.php
session_start();
require_once '../../config/config.php';

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $payment_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Lấy thông tin thanh toán để biết user_id và course_id
    $stmt_get = $conn->prepare("SELECT user_id, course_id FROM payments WHERE payment_id = ?");
    $stmt_get->bind_param("i", $payment_id);
    $stmt_get->execute();
    $pay_info = $stmt_get->get_result()->fetch_assoc();

    if (!$pay_info) die("Không tìm thấy đơn hàng.");

    if ($action == 'approve') {
        // 1. Cập nhật Payment -> completed
        $conn->query("UPDATE payments SET status = 'completed' WHERE payment_id = $payment_id");

        // 2. Kích hoạt khóa học (Thêm vào enrollments)
        // Kiểm tra xem đã enroll chưa để tránh lỗi
        $user_id = $pay_info['user_id'];
        $course_id = $pay_info['course_id'];
        
        $check = $conn->query("SELECT enrollment_id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO enrollments (user_id, course_id, progress) VALUES ($user_id, $course_id, 0)");
        }

        header("Location: " . BASE_URL . "admin/admin_payments.php?status=approved");

    } elseif ($action == 'reject') {
        // Cập nhật Payment -> failed
        $conn->query("UPDATE payments SET status = 'failed' WHERE payment_id = $payment_id");
        header("Location: " . BASE_URL . "admin/admin_payments.php?status=rejected");
    }

} else {
    header("Location: " . BASE_URL . "admin/admin_payments.php");
}
?>