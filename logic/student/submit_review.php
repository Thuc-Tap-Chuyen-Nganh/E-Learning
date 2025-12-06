<?php
session_start();
require_once '../../config/config.php';

// 1. Bảo vệ
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $course_id = intval($_POST['course_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // Validate
    if ($rating < 1 || $rating > 5) {
        header("Location: " . BASE_URL . "course_detail.php?id=$course_id&error=invalid_rating");
        exit();
    }

    // 2. Kiểm tra xem học viên đã đăng ký khóa này chưa (Chỉ học viên mới được review)
    $check_enroll = $conn->query("SELECT enrollment_id FROM enrollments WHERE user_id = $user_id AND course_id = $course_id");
    if ($check_enroll->num_rows == 0) {
        die("Bạn phải đăng ký khóa học mới được đánh giá.");
    }

    // 3. Kiểm tra xem đã review chưa (Mỗi người chỉ 1 lần, nếu rồi thì UPDATE)
    $check_review = $conn->query("SELECT review_id FROM reviews WHERE user_id = $user_id AND course_id = $course_id");
    
    if ($check_review->num_rows > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE reviews SET rating = ?, comment = ?, created_at = NOW() WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("isii", $rating, $comment, $user_id, $course_id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, course_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $course_id, $rating, $comment);
    }
    $stmt->execute();

    // 4. TÍNH TOÁN LẠI ĐIỂM TRUNG BÌNH 
    $sql_avg = "SELECT AVG(rating) as avg_rate, COUNT(*) as total FROM reviews WHERE course_id = $course_id";
    $stats = $conn->query($sql_avg)->fetch_assoc();
    
    $new_avg = round($stats['avg_rate'], 1); // Làm tròn 1 số thập phân (VD: 4.5)
    $new_count = $stats['total'];

    // Cập nhật ngược lại vào bảng courses
    $conn->query("UPDATE courses SET avg_rating = $new_avg, review_count = $new_count WHERE course_id = $course_id");

    header("Location: " . BASE_URL . "course_detail.php?id=$course_id&review_status=success");
    exit();
}
?>