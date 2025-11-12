<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <title>Đăng nhập | Kích hoạt tài khoản Edutech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body {
        background-color: #E6E6FA;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        }
        .card {
        width: 100%;
        max-width: 420px;
        background: linear-gradient(
            90deg,
            #8e2de2 0%, /* Tím đậm */
            #4a00e0 40%, /* Tím/xanh */
            #0099f7 100% /* Xanh dương */
        );
        color: white;
        text-align: center;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        overflow: hidden;
        padding: 30px 20px;
        }
        .card h1 { font-size: 20px; margin-bottom: 8px; }
        .card p { opacity: 0.9; }
    </style>
</head>
<body>
    <?php
    require 'src/core/db_connect.php';

    // Lấy token từ URL
    if (!isset($_GET['token'])) {
        die("Token không hợp lệ hoặc bị thiếu.");
    }
    $token = $_GET['token'];

    // Băm token
    $token_hash = hash('sha256', $token);

    // Tìm token trong CSDL
    $stmt = $conn->prepare(
        "SELECT * FROM user_tokens 
        WHERE token_hash = ? AND token_type = 'email_verification'"
    );
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $token_data = $result->fetch_assoc();
    $stmt->close();

    if (!$token_data) {
        die("Token không hợp lệ. Vui lòng thử lại.");
    }

    // Kiểm tra token hết hạn
    if (strtotime($token_data['expires_at']) < time()) {
        // Xóa token hết hạn
        $stmt = $conn->prepare("DELETE FROM user_tokens WHERE token_id = ?");
        $stmt->bind_param("i", $token_data['token_id']);
        $stmt->execute();
        $stmt->close();
        die("Token đã hết hạn. Vui lòng đăng ký lại.");
    }

    // Token hợp lệ -> Kích hoạt tài khoản
    $user_id = $token_data['user_id'];
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Xóa token đã sử dụng
    $stmt = $conn->prepare("DELETE FROM user_tokens WHERE token_id = ?");
    $stmt->bind_param("i", $token_data['token_id']);
    $stmt->execute();
    $stmt->close();

    // Thông báo thành công
    header("refresh:3;url=index.php"); // Chuyển về trang đăng nhập
    echo 
    '<div class="card">
        <h1><i class="fas fa-user-graduate"></i> Kích hoạt tài khoản thành công!</h1>
        <p>Bạn sẽ được chuyển hướng đến trang đăng nhập sau 3 giây...</p>
    </div>';
    ?>
</body>
</html>