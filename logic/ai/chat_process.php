<?php
// file: logic/ai/chat_process.php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../../config/config.php'; // Đảm bảo đường dẫn đúng

// Khởi tạo lịch sử nếu chưa có
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// --- CASE 1: LẤY LỊCH SỬ (Khi load trang) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'history' => $_SESSION['chat_history']
    ]);
    exit;
}

// --- CASE 2: XÓA LỊCH SỬ (Tùy chọn) ---
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    $_SESSION['chat_history'] = [];
    echo json_encode(['status' => 'cleared']);
    exit;
}
// --- CASE 3: XỬ LÝ CHAT (POST) ---
try {
    if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
        throw new Exception("Chưa cấu hình API Key");
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = $input['message'] ?? '';

    if (empty($userMessage)) {
        echo json_encode(['reply' => '...']); 
        exit;
    }

    // 1. Lưu câu hỏi của User vào Session
    $_SESSION['chat_history'][] = [
        'role' => 'user',
        'content' => $userMessage,
        'type' => 'text'
    ];

    // 2. Lấy dữ liệu khóa học (Context)
    $courses_data = "DANH SÁCH KHÓA HỌC:\n";
    $sql = "SELECT course_id, title, price, description FROM courses WHERE status = 'published'";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $price = ($row['price'] == 0) ? "Miễn phí" : number_format($row['price']) . "đ";
        $desc = mb_substr(strip_tags($row['description']), 0, 150);
        $courses_data .= "- ID: {$row['course_id']} | Tên: {$row['title']} | Giá: $price | Info: $desc\n";
    }

    // 3. Chuẩn bị nội dung gửi Gemini
    // System Prompt
    $contents = [];
    $system_prompt = "Bạn là Trợ lý tuyển sinh ảo (EduBot).
    Nhiệm vụ: Dựa vào nhu cầu của khách, hãy tư vấn khóa học phù hợp nhất từ danh sách dưới đây.
    
    {$courses_data}

    QUY TẮC QUAN TRỌNG:
    1. Trả lời ngắn gọn, thân thiện, xưng là 'EduBot'.
    2. Nếu tìm thấy khóa học phù hợp, hãy giới thiệu về nó và KẾT THÚC câu trả lời bằng mã: [REC_ID:ID_KHOA_HOC].
    3. Ví dụ: 'Bạn nên học khóa Lập trình Web nhé, rất hợp với người mới. [REC_ID:15]'
    4. Chỉ gợi ý 1 khóa học tốt nhất mỗi lần.
    5. Trả lời sao cho vừa đủ dưới 750 output token";

    // 5. Chuẩn bị dữ liệu gửi sang Gemini (Multi-turn chat)
    $contents = [];
    
    // Đưa kiến thức hệ thống vào ngữ cảnh (dùng role user đầu tiên để mồi)
    $contents[] = [
        "role" => "user",
        "parts" => [["text" => $system_prompt]]
    ];
    $contents[] = [
        "role" => "model",
        "parts" => [["text" => "Chào bạn, tôi là EduBot. Tôi đã nắm rõ danh sách khóa học. Tôi có thể giúp gì cho bạn?"]]
    ];

    // Đưa lịch sử chat từ Session vào (Lấy 10 tin gần nhất để tiết kiệm token)
    $recent_history = array_slice($_SESSION['chat_history'], -10);
    foreach ($recent_history as $msg) {
        // Chỉ lấy các tin nhắn text để gửi cho AI (bỏ qua thông tin card)
        if ($msg['type'] === 'text') {
            $role = ($msg['role'] == 'user') ? 'user' : 'model';
            $contents[] = [
                "role" => $role,
                "parts" => [["text" => $msg['content']]]
            ];
        }
    }

    // Thêm câu hỏi hiện tại
    $contents[] = [
        "role" => "user",
        "parts" => [["text" => $userMessage]]
    ];

    // 6. Gọi API Gemini
    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;
    
    $curl = curl_init($api_url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            "contents" => $contents,
            "generationConfig" => ["temperature" => 0.7, "maxOutputTokens" => 800]
        ]),
        CURLOPT_SSL_VERIFYPEER => false // Tắt verify SSL nếu chạy local
    ]);
    
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    $ai_reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Xin lỗi, tôi chưa hiểu.";

    // 5. Xử lý Card & Lưu câu trả lời của Bot vào Session
    $course_card = null;
    if (preg_match('/\[REC_ID:(\d+)\]/', $ai_reply, $matches)) {
        $rec_id = $matches[1];
        $ai_reply = str_replace($matches[0], '', $ai_reply); // Xóa mã

        // Lấy thông tin card
        $stmt = $conn->prepare("SELECT course_id, title, thumbnail, price FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $rec_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($course = $res->fetch_assoc()) {
             $imgUrl = 'assets/uploads/courses/' . $course['thumbnail']; 
             if (empty($course['thumbnail']) || !file_exists('../../' . $imgUrl)) {
                 $imgUrl = 'assets/images/default_course_img.PNG';
             }
             
             $course_card = [
                'title' => $course['title'],
                'price' => ($course['price'] == 0) ? 'Miễn phí' : number_format($course['price']) . 'đ',
                'image' => $imgUrl,
                'link' => 'course_detail.php?id=' . $course['course_id']
            ];
        }
    }

    // Format text
    $ai_reply_formatted = nl2br(htmlspecialchars(trim($ai_reply)));

    // Lưu phản hồi text của Bot vào Session
    $_SESSION['chat_history'][] = [
        'role' => 'bot',
        'content' => $ai_reply_formatted,
        'type' => 'text'
    ];

    // Nếu có Card, lưu Card vào Session luôn để load lại vẫn thấy
    if ($course_card) {
        $_SESSION['chat_history'][] = [
            'role' => 'bot',
            'content' => $course_card, // Lưu mảng dữ liệu card
            'type' => 'card'
        ];
    }

    echo json_encode([
        'reply' => $ai_reply_formatted,
        'card' => $course_card
    ]);

} catch (Exception $e) {
    echo json_encode(['reply' => "Lỗi: " . $e->getMessage()]);
}
?>