<?php
session_start();
require_once '../../config/config.php'; 

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $chapter_id = $_POST['chapter_id'];
    $course_id = $_POST['course_id']; // Để redirect về đúng chỗ
    $title = $_POST['title'];
    $sort_order = $_POST['sort_order'];

    if (empty($title) || empty($chapter_id)) {
        header("Location: " . BASE_URL . "admin/admin_course_details.php?course_id=$course_id&error=empty");
        exit();
    }

    // Cập nhật CSDL
    $stmt = $conn->prepare("UPDATE chapters SET title = ?, sort_order = ? WHERE chapter_id = ?");
    $stmt->bind_param("sii", $title, $sort_order, $chapter_id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/admin_course_details.php?course_id=$course_id&status=updated");
    } else {
        header("Location: " . BASE_URL . "admin/admin_course_details.php?course_id=$course_id&error=db_error");
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: " . BASE_URL . "admin/admin_courses.php");
    exit();
}
?>