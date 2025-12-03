<?php
// src/student_handlers/mark_completed.php
session_start();
require '../core/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$lesson_id = intval($_POST['lesson_id']);
$course_id = intval($_POST['course_id']);

// 1. Kiểm tra xem đã hoàn thành chưa
$check = $conn->query("SELECT progress_id FROM lesson_progress WHERE user_id = $user_id AND lesson_id = $lesson_id");

if ($check->num_rows == 0) {
    // 2. Nếu chưa -> Insert vào bảng lesson_progress
    $stmt = $conn->prepare("INSERT INTO lesson_progress (user_id, lesson_id, course_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $lesson_id, $course_id);
    $stmt->execute();
}

// 3. TÍNH TOÁN TIẾN ĐỘ (%)
// Tổng số bài học của khóa
$res_total = $conn->query("
    SELECT COUNT(*) as total FROM lessons l 
    JOIN chapters c ON l.chapter_id = c.chapter_id 
    WHERE c.course_id = $course_id
");
$total_lessons = $res_total->fetch_assoc()['total'];

// Số bài đã học của user trong khóa này
$res_done = $conn->query("SELECT COUNT(*) as done FROM lesson_progress WHERE user_id = $user_id AND course_id = $course_id");
$lessons_done = $res_done->fetch_assoc()['done'];

// Tính phần trăm
$percent = ($total_lessons > 0) ? round(($lessons_done / $total_lessons) * 100) : 0;

// 4. Cập nhật vào bảng enrollments
$update_enroll = $conn->prepare("UPDATE enrollments SET progress = ? WHERE user_id = ? AND course_id = ?");
$update_enroll->bind_param("iii", $percent, $user_id, $course_id);
$update_enroll->execute();

echo json_encode([
    'success' => true,
    'new_progress' => $percent,
    'message' => 'Đã hoàn thành bài học'
]);
?>