<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Học công nghệ không giới hạn</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
</head>
<body>

    <?php require 'src/templates/header.php'; ?>

    <div class="mobile-nav-container" id="mobile-nav-container">
        <div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>
        <nav class="mobile-nav">
            <div class="mobile-nav-header">
                <a href="#" class="logo"><span>EduTech</span></a>
                <div class="mobile-nav-close-btn" id="mobile-nav-close-btn"><i class="fa-solid fa-xmark"></i></div>
            </div>
            <ul class="mobile-nav-links">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="courses.php">Khóa học</a></li>
                <li><a href="student/my_courses.php">My EduTech</a></li>
            </ul>
            <div class="mobile-auth-buttons">
                <a href="login.php" class="btn btn-secondary">Đăng nhập</a>
                <a href="register.php" class="btn btn-primary">Đăng ký</a>
            </div>
        </nav>
    </div>

    <main>
        <section class="hero-new">
            <div class="container">
                <div class="hero-badge">
                    <i class="fa-solid fa-graduation-cap"></i> Tham gia cùng 500,000+ học viên toàn cầu
                </div>
                
                <h1 class="hero-title">
                    Học Kỹ năng Công nghệ <br>
                    <span class="highlight">Mọi lúc, Mọi nơi</span>
                </h1>
                
                <p class="hero-subtitle">
                    Làm chủ các kỹ năng công nghệ hàng đầu với các khóa học chuyên sâu về Lập trình, Khoa học dữ liệu, AI, và nhiều hơn nữa.
                </p>

                <div class="hero-search-container">
                    <form action="courses.php" method="GET" class="hero-search-form">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="q" placeholder="Bạn muốn học gì hôm nay? (Ví dụ: React, Python...)" required>
                        <button type="submit">Tìm kiếm</button>
                    </form>
                </div>

                <div class="hero-quick-links">
                    <span>Phổ biến:</span>
                    <a href="#">Web Development</a>
                    <a href="#">Python</a>
                    <a href="#">Data Science</a>
                </div>
            </div>
            
            <div class="custom-shape-divider-bottom">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                </svg>
            </div>
        </section>

        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-blue"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-info">
                            <h3>500K+</h3>
                            <p>Học viên đang học</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-purple"><i class="fa-solid fa-book-bookmark"></i></div>
                        <div class="stat-info">
                            <h3>1,200+</h3>
                            <p>Khóa học video</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-pink"><i class="fa-solid fa-chalkboard-user"></i></div>
                        <div class="stat-info">
                            <h3>250+</h3>
                            <p>Giảng viên chuyên gia</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-green"><i class="fa-solid fa-star"></i></div>
                        <div class="stat-info">
                            <h3>4.8/5</h3>
                            <p>Đánh giá trung bình</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section categories">
            <div class="container">
                <div class="section-header center">
                    <h2>Khám phá Danh mục phổ biến</h2>
                    <p>Chọn từ hàng trăm khóa học thuộc nhiều lĩnh vực công nghệ khác nhau</p>
                </div>

                <div class="categories-grid">
                    <a href="#" class="cat-card">
                        <div class="cat-icon icon-code"><i class="fa-solid fa-code"></i></div>
                        <h3>Lập trình Web</h3>
                        <p>142 khóa học</p>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-icon icon-mobile"><i class="fa-solid fa-mobile-screen"></i></div>
                        <h3>Lập trình Mobile</h3>
                        <p>89 khóa học</p>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-icon icon-data"><i class="fa-solid fa-database"></i></div>
                        <h3>Khoa học dữ liệu</h3>
                        <p>76 khóa học</p>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-icon icon-security"><i class="fa-solid fa-shield-halved"></i></div>
                        <h3>An ninh mạng</h3>
                        <p>54 khóa học</p>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-icon icon-ai"><i class="fa-solid fa-brain"></i></div>
                        <h3>AI & Machine Learning</h3>
                        <p>68 khóa học</p>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-icon icon-cloud"><i class="fa-solid fa-cloud"></i></div>
                        <h3>Điện toán đám mây</h3>
                        <p>92 khóa học</p>
                    </a>
                </div>
            </div>
        </section>

        <section class="section courses-featured bg-light">
            <div class="container">
                <div class="section-header flex-between">
                    <div>
                        <h2>Khóa học nổi bật</h2>
                        <p>Được tuyển chọn kỹ lưỡng bởi các chuyên gia</p>
                    </div>
                    <a href="courses.php" class="view-all-btn">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="course-grid">
                    <div class="course-card">
                        <div class="course-thumb">
                            <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500&q=80" alt="Web Dev">
                            <span class="badge badge-beginner">Người mới</span>
                        </div>
                        <div class="course-body">
                            <div class="course-cat">Lập trình Web</div>
                            <h3 class="course-title"><a href="#">Fullstack Web Development Bootcamp 2025</a></h3>
                            <div class="course-instructor">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                                <span>Nguyễn Văn A</span>
                            </div>
                            <div class="course-rating">
                                <span class="rating-val">4.8</span>
                                <div class="stars">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <span class="rating-count">(12,450)</span>
                            </div>
                            <div class="course-footer">
                                <div class="course-meta">
                                    <span><i class="fa-regular fa-clock"></i> 52h</span>
                                    <span><i class="fa-solid fa-video"></i> 140 bài</span>
                                </div>
                                <div class="course-price">499.000đ</div>
                            </div>
                        </div>
                    </div>

                    <div class="course-card">
                        <div class="course-thumb">
                            <img src="https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?w=500&q=80" alt="Data Science">
                            <span class="badge badge-intermediate">Trung cấp</span>
                        </div>
                        <div class="course-body">
                            <div class="course-cat">Data Science</div>
                            <h3 class="course-title"><a href="#">Data Science & Machine Learning Masterclass</a></h3>
                            <div class="course-instructor">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User">
                                <span>Trần Thị B</span>
                            </div>
                            <div class="course-rating">
                                <span class="rating-val">4.9</span>
                                <div class="stars">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <span class="rating-count">(8,920)</span>
                            </div>
                            <div class="course-footer">
                                <div class="course-meta">
                                    <span><i class="fa-regular fa-clock"></i> 68h</span>
                                    <span><i class="fa-solid fa-video"></i> 210 bài</span>
                                </div>
                                <div class="course-price">599.000đ</div>
                            </div>
                        </div>
                    </div>

                    <div class="course-card">
                        <div class="course-thumb">
                            <img src="https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=500&q=80" alt="Cyber">
                            <span class="badge badge-advanced">Nâng cao</span>
                        </div>
                        <div class="course-body">
                            <div class="course-cat">Cybersecurity</div>
                            <h3 class="course-title"><a href="#">Ethical Hacking & Bảo mật Chuyên nghiệp</a></h3>
                            <div class="course-instructor">
                                <img src="https://randomuser.me/api/portraits/men/85.jpg" alt="User">
                                <span>Lê Hoàng C</span>
                            </div>
                            <div class="course-rating">
                                <span class="rating-val">4.7</span>
                                <div class="stars">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <span class="rating-count">(6,540)</span>
                            </div>
                            <div class="course-footer">
                                <div class="course-meta">
                                    <span><i class="fa-regular fa-clock"></i> 45h</span>
                                    <span><i class="fa-solid fa-video"></i> 98 bài</span>
                                </div>
                                <div class="course-price">650.000đ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-bottom">
            <div class="container">
                <div class="cta-content">
                    <h2>Bắt đầu hành trình học tập ngay hôm nay</h2>
                    <p>Tham gia cộng đồng học tập và bắt đầu làm chủ các kỹ năng công nghệ mới nhất.</p>
                    <div class="cta-buttons">
                        <a href="register.php" class="btn btn-white">Đăng ký miễn phí</a>
                        <a href="courses.php" class="btn btn-outline-white">Xem lộ trình</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php require 'src/templates/footer.php'; ?>

    <script src="public/js/main.js?v=<?php echo filemtime('public/js/main.js'); ?>"></script>
</body>
</html>