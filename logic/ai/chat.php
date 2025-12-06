<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

try {
    if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) throw new Exception("Thiếu API Key.");

    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = $input['message'] ?? '';
    if (empty($userMessage)) { echo json_encode(['reply' => '...']); exit; }

    // 1. Nạp Context kèm ID và Thumbnail
    $courses_context = "Danh sách khóa học hiện có:\n";
    // Lấy thêm ID và thumbnail
    $sql = "SELECT course_id, title, category, price FROM courses WHERE status='published'";
    $result = $conn->query($sql);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $price = $row['price'] == 0 ? "Miễn phí" : number_format($row['price']) . "đ";
            // Quan trọng: Dạy AI biết ID của từng khóa
            $courses_context .= "- ID: {$row['course_id']} | Tên: {$row['title']} ({$row['category']}) | Giá: $price\n";
        }
    }

    // 2. Prompt Engineering (Kỹ thuật ra lệnh)
    $system_prompt = "Bạn là EduBot. Trả lời ngắn gọn, thân thiện.
    Dữ liệu khóa học:
    $courses_context
    
    QUAN TRỌNG: Nếu bạn gợi ý một khóa học cụ thể cho người dùng, hãy thêm mã này vào CUỐI câu trả lời: [CID:ID_KHOA_HOC].
    Ví dụ: 'Bạn nên học khóa Python nhé. [CID:15]'.
    Chỉ đưa ra 1 mã khóa học phù hợp nhất.";

    // 3. Gọi API Gemini
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . GEMINI_API_KEY; // Dùng model mới nhất 2.0 Flash cho nhanh
    $data = [ "contents" => [ [ "parts" => [ ["text" => $system_prompt . "\n\nUser: " . $userMessage] ] ] ] ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $botReply = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Xin lỗi, tôi chưa hiểu ý bạn.";

    // 4. Xử lý Logic trích xuất ID khóa học
    $recommended_course = null;
    
    // Regex tìm chuỗi [CID:123]
    if (preg_match('/\[CID:(\d+)\]/', $botReply, $matches)) {
        $course_id = $matches[1];
        
        // Xóa cái mã [CID:...] khỏi lời thoại để người dùng không thấy nó
        $botReply = str_replace($matches[0], '', $botReply);

        // Query lấy thông tin chi tiết để vẽ Card
        $stmt = $conn->prepare("SELECT course_id, title, thumbnail, category, price FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($course = $res->fetch_assoc()) {
            // Xử lý ảnh và giá
            $course['image_url'] = get_course_image($course['thumbnail'], $course['category']);
            $course['price_fmt'] = $course['price'] == 0 ? 'Miễn phí' : number_format($course['price']) . 'đ';
            $recommended_course = $course;
        }
    }

    $botReply = nl2br(htmlspecialchars($botReply));

    // 5. Trả về JSON (Gồm lời thoại + Dữ liệu khóa học nếu có)
    echo json_encode([
        'reply' => $botReply,
        'course' => $recommended_course // Biến này có thể null hoặc là mảng dữ liệu khóa học
    ]);

} catch (Exception $e) {
    echo json_encode(['reply' => "⚠️ Lỗi: " . $e->getMessage()]);
}
?>