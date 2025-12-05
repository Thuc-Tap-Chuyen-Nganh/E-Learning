<?php
// logic/admin/course_delete.php
session_start();
require_once '../../config/config.php'; // Gọi config

// Trả về JSON (vì file này được gọi bằng AJAX)
header('Content-Type: application/json');

// 1. Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
    exit();
}

// 2. Chỉ chấp nhận POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID khóa học.']);
        exit();
    }
    
    $course_id = $_POST['id'];

    // --- BƯỚC MỚI: LẤY ẢNH ĐỂ XÓA ---
    $stmt_get = $conn->prepare("SELECT thumbnail FROM courses WHERE course_id = ?");
    $stmt_get->bind_param("i", $course_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result()->fetch_assoc();
    $thumbnail_path = $result['thumbnail'] ?? null;
    $stmt_get->close();
    // --------------------------------

    // 3. Xóa trong CSDL
    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);

    if ($stmt->execute()) {
        
        // --- BƯỚC MỚI: XÓA FILE ẢNH VẬT LÝ ---
        // Nếu có ảnh và file đó tồn tại trên ổ cứng
        if ($thumbnail_path && file_exists(BASE_PATH . $thumbnail_path)) {
            unlink(BASE_PATH . $thumbnail_path);
        }
        // --------------------------------------

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: Không thể xóa khóa học này.']);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
}
?>