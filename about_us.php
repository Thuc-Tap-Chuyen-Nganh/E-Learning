<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi | EduTech</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="favicon.ico">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/index.css?v=<?= time() ?>">
</head>
<body>

    <?php require 'includes/header.php'; ?>

    <main>
        <section class="section about-hero">
            <div class="container">
                <div class="about-content">
                    <h1>Về EduTech</h1>
                </div>
            </div>
        </section>

        <section class="section about-creator bg-light">
            <div class="container">
                <div class="creator-profile">
                    <div class="creator-avatar">
                        <img src="https://ui-avatars.com/api/?name=Trần+Thiên+Triệu&background=667eea&color=fff&size=150" alt="Trần Thiên Triệu">
                    </div>
                    <div class="creator-info">
                        <h2>Người Tạo Dự Án</h2>
                        <h3>Trần Thiên Triệu</h3>
                        <p class="creator-id">Mã Sinh Viên: DH52201647</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section about-creator bg-light">
            <div class="container">
                <div class="creator-profile">
                    <div class="creator-avatar">
                        <img src="https://ui-avatars.com/api/?name=Trần+Thiên+Triệu&background=667eea&color=fff&size=150" alt="Trần Thiên Triệu">
                    </div>
                    <div class="creator-info">
                        <h2>Người Tạo Dự Án</h2>
                        <h3>Lê Thanh Việt</h3>
                        <p class="creator-id">Mã Sinh Viên: DH52201750</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-bottom">
            <div class="container">
                <div class="cta-content">
                    <h2>Tham Gia Cộng Đồng EduTech</h2>
                    <p>Hãy cùng nhau học tập và phát triển kỹ năng công nghệ.</p>
                    <div class="cta-buttons">
                        <a href="register.php" class="btn btn-white">Đăng Ký Ngay</a>
                        <a href="courses.php" class="btn btn-outline-white">Xem Khóa Học</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require 'includes/footer.php'; ?>

</body>
</html>
