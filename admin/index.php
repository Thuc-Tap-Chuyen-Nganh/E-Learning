<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech Admin Login</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <div class="login-card">
        <div class="login-icon">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        
        <h1>EduTech Admin</h1>
        <p class="subtitle">Đăng nhập vào hệ thống quản trị</p>

        <form action="../src/admin_handlers/admin_login_handler.php" method="POST"> <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="admin@elearning.com" required/>
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="********" required>
            </div>

            <?php
            // Hiển thị lỗi (nếu có)
            $error_message = ""; 
            if (isset($_GET['error']) && $_GET['error'] == 'wrongcreds') {
                $error_message = "Email hoặc mật khẩu không đúng!";
            }
            ?>

            <div class="error" id="errorMsg"><?php echo $error_message; ?></div>

            <button type="submit" class="login-btn">Đăng nhập</button>
        </form>
        
    </div>

</body>
</html>