<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Học công nghệ không giới hạn</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="public/css/index.css">

</head>
<body>

    <header class="header">
        <div class="container">
            <a href="#" class="logo">
                <i class="fa-solid fa-book-open-reader"></i>
                <span>EduTech</span>
            </a>
            
            <nav class="navbar">
                <ul class="nav-links">
                    <li><a href="#">Khóa học</a></li>
                    <li><a href="#">Về chúng tôi</a></li>
                    <li><a href="#">Liên hệ</a></li>
                </ul>
                <div class="auth-buttons">
                    <a href="login.php" class="login-btn">Đăng nhập</a>
                    <a href="register.php" class="btn btn-primary">Đăng ký</a>
                </div>
            </nav>
            
            <div class="hamburger-icon" id="hamburger-btn">
                <i class="fa-solid fa-bars"></i>
            </div>
            
        </div>
    </header>

    <div class="mobile-nav-container" id="mobile-nav-container">
        <div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>
        
        <nav class="mobile-nav">
            <div class="mobile-nav-header">
                <a href="#" class="logo">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span>EduTech</span>
                </a>
                <div class="mobile-nav-close-btn" id="mobile-nav-close-btn">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
            
            <ul class="mobile-nav-links">
                <li><a href="#">Khóa học</a></li>
                <li><a href="#">Về chúng tôi</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
            
            <div class="mobile-auth-buttons">
                <a href="login.php" class="btn btn-secondary">Đăng nhập</a>
                <a href="register.php" class="btn btn-primary">Đăng ký</a>
            </div>
        </nav>
    </div>


    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Học công nghệ không giới hạn</h1>
                    <p>Nền tảng học trực tuyến hàng đầu với hơn 1000+ khóa học về lập trình, công nghệ thông tin và kỹ năng số. Bắt đầu hành trình của bạn ngay hôm nay!</p>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-primary">Bắt đầu học ngay</a>
                        <a href="#" class="btn btn-secondary">Khám phá khóa học</a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat">
                            <h3>50K+</h3>
                            <p>Học viên</p>
                        </div>
                        <div class="stat">
                            <h3>1000+</h3>
                            <p>Khóa học</p>
                        </div>
                        <div class="stat">
                            <h3>95%</h3>
                            <p>Hài lòng</p>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzOTAwOTB8MHwxfHNlYXJjaHwxfHxlLWxlYXJuaW5nJTIwbGFwdG9wfGVufDB8fHx8MTcwMTIwNjQ3NHww&ixlib=rb-4.0.3&q=80&w=1080" alt="Học lập trình online">
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="features-header">
                    <h2>Tại sao chọn EduTech?</h2>
                    <p>Chúng tôi cung cấp trải nghiệm học tập toàn diện với công nghệ hiện đại</p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="icon"><i class="fa-solid fa-book"></i></div>
                        <h3>Khóa học đa dạng</h3>
                        <p>Hơn 1000+ khóa học về lập trình, công nghệ và kỹ năng số</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fa-solid fa-users"></i></div>
                        <h3>Cộng đồng sôi động</h3>
                        <p>Kết nối với hàng nghìn học viên và chuyên gia công nghệ</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fa-solid fa-certificate"></i></div>
                        <h3>Chứng chỉ uy tín</h3>
                        <p>Nhận chứng chỉ được công nhận sau khi hoàn thành khóa học</p>
                    </div>
                    <div class="feature-card">
                        <div class="icon"><i class="fa-solid fa-chart-line"></i></div>
                        <h3>Học tập linh hoạt</h3>
                        <p>Học mọi lúc, mọi nơi với nền tảng trực tuyến hiện đại</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta">
            <div class="container">
                <h2>Sẵn sàng bắt đầu học?</h2>
                <p>Tham gia cùng hàng nghìn học viên đang phát triển kỹ năng công nghệ</p>
                <a href="register.php" class="btn btn-light">Đăng ký miễn phí</a>
            </div>
        </section>

    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a href="#" class="logo">
                        <i class="fa-solid fa-book-open-reader"></i>
                        <span>EduTech</span>
                    </a>
                    <p>Nền tảng học trực tuyến hàng đầu Việt Nam</p>
                </div>
                
                <div class="footer-links">
                    <h4>Sản phẩm</h4>
                    <ul>
                        <li><a href="#">Khóa học</a></li>
                        <li><a href="#">Chứng chỉ</a></li>
                        <li><a href="#">Giảng viên</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Công ty</h4>
                    <ul>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Trung tâm trợ giúp</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Điều khoản</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="public/js/main.js"></script>

</body>
</html>