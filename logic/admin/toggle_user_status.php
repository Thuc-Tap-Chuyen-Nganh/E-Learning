<?php
session_start();
require '../../config/config.php';

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL ."admin/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Xác định trạng thái mới
    $new_status = ($action == 'ban') ? 'banned' : 'active';
    $msg = ($action == 'ban') ? 'Đã khóa tài khoản thành công.' : 'Đã mở khóa tài khoản thành công.';

    // Cập nhật CSDL
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'student'");
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        header("Location: " . BASE_URL ."admin/admin_students.php?msg=" . urlencode($msg));
        exit();
    } else {
        echo "Lỗi hệ thống.";
    }
} else {
    header("Location: " . BASE_URL ."admin/admin_students.php");
    exit();
}
?>