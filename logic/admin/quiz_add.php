<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['admin_id'])) exit('Unauthorized');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lesson_id = $_POST['lesson_id'];
    $question_text = $_POST['question_text'];
    $opt_a = $_POST['option_a'];
    $opt_b = $_POST['option_b'];
    $opt_c = $_POST['option_c'];
    $opt_d = $_POST['option_d'];
    $correct = $_POST['correct_option'];

    $stmt = $conn->prepare("INSERT INTO questions (lesson_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $lesson_id, $question_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL ."admin/admin_quiz_manage.php?lesson_id=$lesson_id&status=added");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>