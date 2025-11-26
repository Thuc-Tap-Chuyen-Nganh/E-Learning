<?php
// src/admin_handlers/edit_lesson_handler.php

session_start();
require '../core/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lesson_id = $_POST['lesson_id'];
    $chapter_id = $_POST['chapter_id']; // Để redirect
    $title = $_POST['title'];
    $lesson_type = $_POST['lesson_type'];
    
    // Biến mặc định
    $video_url = null;
    $content = null;
    $duration = 0;

    if (empty($title) || empty($lesson_id)) {
        header("Location: ../../admin/admin_chapter_details.php?chapter_id=$chapter_id&error=empty");
        exit();
    }

    // Xử lý dữ liệu theo loại
    if ($lesson_type == 'video') {
        $video_url = $_POST['content_url'];
        $duration = $_POST['duration'];
    } elseif ($lesson_type == 'text') {
        $content = $_POST['content_text'];
        // Với bài text, ta có thể giữ duration = 0 hoặc cho admin nhập nếu muốn
    } elseif ($lesson_type == 'quiz') {
        $duration = $_POST['duration']; // Quiz cũng cần thời gian làm bài
    }

    // CẬP NHẬT CSDL
    $stmt = $conn->prepare("
        UPDATE lessons 
        SET title = ?, lesson_type = ?, video_url = ?, content = ?, duration = ? 
        WHERE lesson_id = ?
    ");
    
    // "ssssii" -> string, string, string, string, int, int
    $stmt->bind_param("ssssii", $title, $lesson_type, $video_url, $content, $duration, $lesson_id);

    if ($stmt->execute()) {
        header("Location: ../../admin/admin_chapter_details.php?chapter_id=$chapter_id&status=updated");
    } else {
        header("Location: ../../admin/admin_chapter_details.php?chapter_id=$chapter_id&error=db_error");
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: ../../admin/admin_courses.php");
    exit();
}
?>