<?php
// logic/auth/update_profile.php
require_once '../../config/config.php'; 

// 1. Bảo vệ
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username']);

    if (empty($username)) {
        header("Location: " . BASE_URL . "student/profile.php?error=empty");
        exit();
    }

    // 2. Xử lý Upload Avatar
    $avatar_sql_part = ""; 
    $params = [$username];
    $types = "s";

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Giới hạn 2MB
        if (in_array($ext, $allowed) && $_FILES['avatar']['size'] < 2000000) {
            
            // --- XÓA ẢNH CŨ ---
            // Lấy đường dẫn ảnh hiện tại từ DB
            $stmt_old = $conn->prepare("SELECT avatar FROM users WHERE user_id = ?");
            $stmt_old->bind_param("i", $user_id);
            $stmt_old->execute();
            $res_old = $stmt_old->get_result()->fetch_assoc();
            $old_avatar_path = $res_old['avatar'] ?? null;

            // Nếu có ảnh cũ và file đó tồn tại -> Xóa
            if ($old_avatar_path && file_exists(BASE_PATH . $old_avatar_path)) {
                unlink(BASE_PATH . $old_avatar_path); // Hàm xóa file của PHP
            }
            // ---------------------------

            $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
            $upload_dir = BASE_PATH . 'assets/uploads/avatars/';
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_dir . $new_filename)) {
                $avatar_path = 'assets/uploads/avatars/' . $new_filename;
                
                $avatar_sql_part = ", avatar = ?";
                $params[] = $avatar_path;
                $types .= "s";

                $_SESSION['avatar'] = $avatar_path;
            }
        }
    }

    // Thêm ID vào cuối
    $params[] = $user_id;
    $types .= "i";

    // 3. Cập nhật DB
    $sql = "UPDATE users SET username = ? $avatar_sql_part WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        header("Location: " . BASE_URL . "student/profile.php?status=success");
    } else {
        header("Location: " . BASE_URL . "student/profile.php?error=db_error");
    }
}
?>