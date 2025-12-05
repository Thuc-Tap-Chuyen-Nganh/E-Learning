<?php
// logic/admin/course_edit.php
session_start();
require_once '../../config/config.php';

// 1. Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    // 2. Xử lý Upload Ảnh Mới (Nếu có)
    $thumbnail_sql_part = ""; 
    $params = [$title, $description, $price, $category, $status];
    $types = "ssiss";

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['thumbnail']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $_FILES['thumbnail']['size'] < 5000000) { // 5MB
            
            // --- XÓA ẢNH CŨ ---
            // Lấy ảnh cũ từ DB trước
            $stmt_old = $conn->prepare("SELECT thumbnail FROM courses WHERE course_id = ?");
            $stmt_old->bind_param("i", $course_id);
            $stmt_old->execute();
            $res_old = $stmt_old->get_result()->fetch_assoc();
            $old_thumbnail = $res_old['thumbnail'] ?? null;

            // Nếu có ảnh cũ và file tồn tại -> Xóa
            if ($old_thumbnail && file_exists(BASE_PATH . $old_thumbnail)) {
                unlink(BASE_PATH . $old_thumbnail);
            }
            // ---------------------------

            $new_filename = time() . "_" . rand(1000, 9999) . "." . $ext;
            $upload_dir = BASE_PATH . 'assets/uploads/courses/';
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $new_filename)) {
                $thumbnail_path = 'assets/uploads/courses/' . $new_filename;
                
                // Thêm vào câu lệnh update
                $thumbnail_sql_part = ", thumbnail = ?";
                $params[] = $thumbnail_path;
                $types .= "s";
            }
        }
    }

    // Thêm course_id vào cuối mảng tham số cho WHERE
    $params[] = $course_id;
    $types .= "i";

    // 3. CẬP NHẬT CSDL
    $sql = "UPDATE courses 
            SET title = ?, description = ?, price = ?, category = ?, status = ? $thumbnail_sql_part
            WHERE course_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params); // Rải mảng tham số

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "admin/admin_courses.php?status=updated");
    } else {
        header("Location: " . BASE_URL . "admin/admin_edit_course.php?id=$course_id&error=db_error");
    }
}
?>