<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$lesson_id = intval($_POST['lesson_id']);
$course_id = intval($_POST['course_id']);
$answers = isset($_POST['answers']) ? $_POST['answers'] : []; // Mảng: [question_id => selected_option]

// 1. Lấy đáp án đúng từ Database
$stmt = $conn->prepare("SELECT question_id, correct_option FROM questions WHERE lesson_id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

$total_questions = 0;
$correct_count = 0;
$correction_sheet = []; // Bảng chữa bài gửi về cho client

while ($row = $result->fetch_assoc()) {
    $qid = $row['question_id'];
    $correct = $row['correct_option'];
    
    $user_choice = isset($answers[$qid]) ? $answers[$qid] : null;
    
    // Chấm điểm
    $is_correct = ($user_choice === $correct);
    if ($is_correct) {
        $correct_count++;
    }

    // Tạo dữ liệu chữa bài
    $correction_sheet[$qid] = [
        'is_correct' => $is_correct,
        'correct_option' => $correct, // Gửi đáp án đúng về để hiện lên
        'user_choice' => $user_choice
    ];
    
    $total_questions++;
}

// 2. Tính điểm
if ($total_questions > 0) {
    $score_percent = round(($correct_count / $total_questions) * 100);
} else {
    $score_percent = 0;
}

// Quy định: Trên 70% là ĐẠT
$is_passed = ($score_percent >= 70);

// 3. Nếu ĐẠT -> Lưu vào lesson_progress (Giống logic mark_completed.php)
if ($is_passed) {
    // Check xem đã lưu chưa
    $check = $conn->query("SELECT progress_id FROM lesson_progress WHERE user_id = $user_id AND lesson_id = $lesson_id");
    if ($check->num_rows == 0) {
        $stmt_ins = $conn->prepare("INSERT INTO lesson_progress (user_id, lesson_id, course_id) VALUES (?, ?, ?)");
        $stmt_ins->bind_param("iii", $user_id, $lesson_id, $course_id);
        $stmt_ins->execute();
        
        // Cập nhật % tiến độ khóa học (Copy logic từ mark_completed)
        // ... (Để code gọn, ta có thể bỏ qua bước tính lại % enrollments ở đây, 
        // hoặc bạn có thể copy đoạn tính toán từ mark_completed.php vào đây)
        
        // Tái sử dụng logic tính % enrollments:
        $res_total = $conn->query("SELECT COUNT(*) as total FROM lessons l JOIN chapters c ON l.chapter_id = c.chapter_id WHERE c.course_id = $course_id");
        $total_lessons_course = $res_total->fetch_assoc()['total'];
        $res_done = $conn->query("SELECT COUNT(*) as done FROM lesson_progress WHERE user_id = $user_id AND course_id = $course_id");
        $lessons_done = $res_done->fetch_assoc()['done'];
        $new_percent = ($total_lessons_course > 0) ? round(($lessons_done / $total_lessons_course) * 100) : 0;
        
        $conn->query("UPDATE enrollments SET progress = $new_percent WHERE user_id = $user_id AND course_id = $course_id");
    }
}

// 4. Trả kết quả về cho JS
echo json_encode([
    'success' => true,
    'score_percent' => $score_percent,
    'correct_count' => $correct_count,
    'total_questions' => $total_questions,
    'is_passed' => $is_passed,
    'correction' => $correction_sheet
]);
?>