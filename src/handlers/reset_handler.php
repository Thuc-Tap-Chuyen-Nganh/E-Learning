<?php
require "../core/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validate mật khẩu
    if (empty($password) || empty($confirm)) {
        header("Location: ../../reset_password.php?token=$token&error=empty");
        exit();
    }
    if ($password !== $confirm) {
        header("Location: ../../reset_password.php?token=$token&error=mismatch");
        exit();
    }
    if (strlen($password) < 6) {
        header("Location: ../../reset_password.php?token=$token&error=short");
        exit();
    }

    // XÁC THỰC TOKEN (lần cuối, rất quan trọng)
    $token_hash = hash('sha256', $token);
    $stmt = $conn->prepare(
        "SELECT * FROM user_tokens 
         WHERE token_hash = ? AND token_type = 'password_reset'"
    );
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $token_data = $result->fetch_assoc();
    $stmt->close();

    // Kiểm tra token còn hạn không (lần nữa)
    if (!$token_data || strtotime($token_data['expires_at']) < time()) {
        // Nếu token hết hạn hoặc không hợp lệ, đá về trang forgot
        header("Location: ../../forgot.php?error=invalidtoken");
        exit();
    }

    // MỌI THỨ HỢP LỆ -> Đổi mật khẩu
    try {
        $user_id = $token_data['user_id'];
        
        // Băm mật khẩu mới
        $new_password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Cập nhật mật khẩu mới'
        $stmt_update = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt_update->bind_param("si", $new_password_hash, $user_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Xóa token đã sử dụng
        $stmt_delete = $conn->prepare("DELETE FROM user_tokens WHERE token_id = ?");
        $stmt_delete->bind_param("i", $token_data['token_id']);
        $stmt_delete->execute();
        $stmt_delete->close();

        // XONG! Chuyển về trang login với thông báo thành công (NÀy làm tạm trước vậy, chưa xử lý bên login.php)
        header("Location: ../../login.php?status=reset_success");
        exit();

    } catch (Exception $e) {
        // (Này cũng tạm)
        header("Location: ../../forgot.php?error=dberror");
        exit();
    }

} else {
    header("Location: ../../index.php");
    exit();
}
?>