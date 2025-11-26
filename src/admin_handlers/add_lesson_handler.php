<?php
session_start();
require '../core/db_connect.php';

// === BẢO VỆ ===
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu chung
    $chapter_id = $_POST['chapter_id'];
    $title = $_POST['title'];
    $lesson_type = $_POST['lesson_type'];
    
    // Khởi tạo các biến nội dung là NULL trước
    $video_url = null;
    $content = null;
    $duration = 0;

    // 2. Validation cơ bản
    if (empty($title) || empty($chapter_id)) {
        header("Location: ../../admin/admin_chapter_details.php?chapter_id=$chapter_id&error=empty");
        exit();
    }

    // 3. Xử lý dữ liệu riêng theo từng loại
    if ($lesson_type == 'video') {
        $video_url = $_POST['content_url'];
        $duration = $_POST['duration'];
        
        // Nếu người dùng nhập link youtube dạng https://www.youtube.com/watch?v=ID
        // Bạn có thể cần xử lý để lấy ID hoặc embed link (Tùy nhu cầu hiển thị)
        
    } elseif ($lesson_type == 'text') {
        // Lấy nội dung từ CKEditor
        $content = $_POST['content_text'];
        // Bài đọc thường không có duration cố định, hoặc bạn có thể ước lượng (vd: 5 phút)
        $duration = 5; 

    } elseif ($lesson_type == 'quiz') {
        // Quiz chưa có nội dung gì, chỉ tạo bài học rỗng
        // Sau này thêm câu hỏi vào bảng 'questions'
        $duration = 15; // Mặc định thời gian làm bài (có thể cho chỉnh sửa sau)
    }

    // 4. INSERT vào CSDL
    // Câu lệnh SQL này phải khớp với cấu trúc bảng lessons bạn đã tạo/sửa
    $stmt = $conn->prepare("
        INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    // "issssi" -> int, string, string, string, string, int
    $stmt->bind_param("issssi", $chapter_id, $title, $lesson_type, $video_url, $content, $duration);

    if ($stmt->execute()) {
        header("Location: ../../admin/admin_chapter_details.php?chapter_id=$chapter_id&status=added");
    } else {
        // Debug lỗi nếu cần: echo $stmt->error;
        header("Location: ../../admin/admin_chapter_details.php?chapter_id=$chapter_id&error=db_error");
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: ../../admin/admin_courses.php");
    exit();
}
?>