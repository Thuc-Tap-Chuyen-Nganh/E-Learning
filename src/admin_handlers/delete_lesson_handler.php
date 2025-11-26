<?php
// src/admin_handlers/delete_lesson_handler.php

session_start();
require '../core/db_connect.php';

// Trả về JSON
header('Content-Type: application/json');

// 1. Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
    exit();
}

// 2. Chỉ chấp nhận POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['lesson_id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID bài học.']);
        exit();
    }

    $lesson_id = $_POST['lesson_id'];

    // 3. Thực hiện Xóa
    $stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: Không thể xóa bài học này.']);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
}
?>