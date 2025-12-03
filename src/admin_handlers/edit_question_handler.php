<?php
// src/admin_handlers/edit_question_handler.php
session_start();
require '../core/db_connect.php';

if (!isset($_SESSION['admin_id'])) exit('Unauthorized');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_id = $_POST['question_id'];
    $lesson_id = $_POST['lesson_id']; // Để redirect về đúng chỗ
    
    $question_text = $_POST['question_text'];
    $opt_a = $_POST['option_a'];
    $opt_b = $_POST['option_b'];
    $opt_c = $_POST['option_c'];
    $opt_d = $_POST['option_d'];
    $correct = $_POST['correct_option'];

    // Cập nhật CSDL
    $stmt = $conn->prepare("
        UPDATE questions 
        SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? 
        WHERE question_id = ?
    ");
    
    // "ssssssi"
    $stmt->bind_param("ssssssi", $question_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct, $question_id);

    if ($stmt->execute()) {
        header("Location: ../../admin/admin_quiz_manage.php?lesson_id=$lesson_id&status=updated");
    } else {
        header("Location: ../../admin/admin_quiz_manage.php?lesson_id=$lesson_id&error=db_error");
    }
}
?>