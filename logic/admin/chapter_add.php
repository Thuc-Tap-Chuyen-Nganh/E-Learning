<?php
session_start();
require_once '../../config/config.php'; 

// === BẢO VỆ ===
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $sort_order = $_POST['sort_order'];

    // 2. Validation
    if (empty($title) || empty($course_id)) {
        header("Location: " . BASE_URL . "admin/admin_course_details.php?course_id=$course_id&error=empty");
        exit();
    }

    // 3. Chèn vào CSDL
    $stmt = $conn->prepare("INSERT INTO chapters (course_id, title, sort_order) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $course_id, $title, $sort_order);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/admin_course_details.php?course_id=$course_id&status=added");
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