<?php
// src/admin_handlers/delete_course_handler.php

session_start();
require '../core/db_connect.php'; // Gọi $conn

// Báo cho trình duyệt biết chúng ta sẽ trả về JSON
header('Content-Type: application/json');

// 1. Bảo vệ: Chỉ admin mới được xóa
if (!isset($_SESSION['admin_id'])) {
    // Trả về lỗi JSON
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
    exit();
}

// 2. Chỉ chấp nhận phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Lấy ID từ FormData (AJAX)
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID khóa học.']);
        exit();
    }
    
    $course_id = $_POST['id'];

    // 4. Dùng Prepared Statement (lệnh DELETE)
    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);

    // 5. Thực thi và trả về JSON
    if ($stmt->execute()) {
        // Xóa thành công
        echo json_encode(['success' => true]);
    } else {
        // Lỗi (ví dụ: khóa ngoại, CSDL không cho xóa)
        echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: Không thể xóa khóa học này.']);
    }

    $stmt->close();
    $conn->close();

} else {
    // Nếu ai đó cố truy cập file này bằng GET
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
}
?>