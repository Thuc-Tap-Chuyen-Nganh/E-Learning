<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['word_file'])) {
    try {
        $file = $_FILES['word_file']['tmp_name'];
        
        // 1. Dùng PHPWord để đọc file .docx
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file);

        // 2. Sử dụng HTML Writer để chuyển đổi sang HTML
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        
        // 3. Lưu HTML vào bộ nhớ đệm (buffer) thay vì file
        ob_start();
        $xmlWriter->save('php://output');
        $html_content = ob_get_contents();
        ob_end_clean();

        // 4. Xử lý sạch chuỗi HTML (PHPWord hay sinh ra thẻ body/html thừa)
        // Chỉ lấy nội dung trong thẻ body
        if (preg_match("/<body[^>]*>(.*?)<\/body>/is", $html_content, $matches)) {
            $clean_html = $matches[1];
        } else {
            $clean_html = $html_content;
        }

        // Trả về JSON
        echo json_encode(['success' => true, 'html' => $clean_html]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi đọc file: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Không có file được gửi lên.']);
}
?>