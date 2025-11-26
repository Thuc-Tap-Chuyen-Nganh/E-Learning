<?php
// src/admin_handlers/delete_chapter_handler.php

session_start();
require '../core/db_connect.php'; // Gọi kết nối CSDL

// Trả về JSON
header('Content-Type: application/json');

// 1. Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
    exit();
}

// 2. Chỉ chấp nhận POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ luồng input JSON (vì fetch gửi body dạng json hoặc formdata)
    // Ở đây mình sẽ dùng FormData từ JS nên dùng $_POST như bình thường
    if (!isset($_POST['chapter_id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID chương.']);
        exit();
    }

    $chapter_id = $_POST['chapter_id'];

    // 3. Thực hiện Xóa
    // Lưu ý: Nếu bạn đã thiết lập Khóa Ngoại (Foreign Key) trong CSDL là ON DELETE CASCADE
    // thì các bài học (lessons) trong chương này sẽ tự động bị xóa theo.
    // Nếu chưa, bạn có thể cần xóa lessons thủ công trước. Ở đây ta giả định CSDL đã chuẩn.
    
    $stmt = $conn->prepare("DELETE FROM chapters WHERE chapter_id = ?");
    $stmt->bind_param("i", $chapter_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: Không thể xóa chương này.']);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
}
?>