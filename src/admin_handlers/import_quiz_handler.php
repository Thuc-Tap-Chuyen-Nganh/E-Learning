<?php
// src/admin_handlers/import_quiz_handler.php
session_start();
require '../core/db_connect.php';

if (!isset($_SESSION['admin_id'])) exit('Unauthorized');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["quiz_file"])) {
    
    $lesson_id = $_POST['lesson_id'];
    $file = $_FILES["quiz_file"]["tmp_name"];

    if ($_FILES["quiz_file"]["size"] > 0) {
        
        // Mở file ở chế độ đọc (r)
        $handle = fopen($file, "r");
        
        // Chuẩn bị câu lệnh SQL (Prepared Statement để tối ưu hiệu năng khi lặp)
        $stmt = $conn->prepare("INSERT INTO questions (lesson_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $success_count = 0;

        // Đọc từng dòng
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Kiểm tra xem dòng này có đủ 6 cột không (Câu hỏi + 4 đáp án + Đúng)
            if (count($data) < 6) {
                continue; // Bỏ qua dòng lỗi
            }

            // Gán dữ liệu từ CSV vào biến
            // Lưu ý: data[0] là cột A, data[1] là cột B...
            // Đôi khi file CSV lưu tiếng Việt bị lỗi font, cần utf8_encode nếu cần thiết
            // Nhưng tốt nhất là lưu file CSV với encoding UTF-8 ngay từ đầu.
            
            $question_text = trim($data[0]);
            $opt_a = trim($data[1]);
            $opt_b = trim($data[2]);
            $opt_c = trim($data[3]);
            $opt_d = trim($data[4]);
            $correct = strtoupper(trim($data[5])); // Chuyển thành chữ hoa (a -> A)

            // Validate đáp án đúng phải là A, B, C hoặc D
            if (!in_array($correct, ['A', 'B', 'C', 'D'])) {
                continue; // Bỏ qua nếu đáp án sai format
            }

            // Thực thi Insert
            $stmt->bind_param("issssss", $lesson_id, $question_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct);
            if ($stmt->execute()) {
                $success_count++;
            }
        }

        fclose($handle);
        $stmt->close();
        $conn->close();

        // Chuyển hướng về trang cũ với thông báo
        header("Location: ../../admin/admin_quiz_manage.php?lesson_id=$lesson_id&status=imported&count=$success_count");
        exit();
    }
} else {
    echo "Không có file được tải lên.";
}
?>