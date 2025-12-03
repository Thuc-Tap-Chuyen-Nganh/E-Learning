<?php
// src/admin_handlers/delete_question_handler.php
session_start();
require '../core/db_connect.php';

if (!isset($_SESSION['admin_id'])) exit('Unauthorized');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_id = $_POST['question_id'];
    $lesson_id = $_POST['lesson_id'];

    $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();

    header("Location: ../../admin/admin_quiz_manage.php?lesson_id=$lesson_id&status=deleted");
}
?>