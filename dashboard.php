<?php
  session_start();
  require "src/core/db_connect.php";

  $user_id = $_SESSION['user_id'];
  $username = $_SESSION['username'];

  if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $email = $user['email'];
  }

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-book-open-reader"></i>
                <span>EduTech</span>
            </a>
            <span class="logo-subtext">Dashboard</span>
        </div>

        <div class="user-profile">
            <div class="user-avatar"><?php echo strtoupper(substr($username,0,1)) ?></div>
            <span class="user-name"><?php echo $username ?></span>
            <span class="user-email"><?php echo $email ?></span>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="#" class="active">
                        <i class="fa-solid fa-table-columns"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-book"></i>
                        <span>Khóa học của tôi</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Tiến độ</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-gear"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="index.php"> <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="main-header">
            <h1 class="header-title">E-Learning Web Project</h1>
        </header>

        <div class="dashboard-container">
            <section class="overview-stats">
                <h2 class="section-title">Tổng quan</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon-wrapper icon-blue">
                            <i class="fa-solid fa-laptop-code"></i>
                        </div>
                        <h3>Khóa học đang học</h3>
                        <p class="stat-value">3</p>
                    </div>
                    <div class="stat-card">
                        <div class="icon-wrapper icon-purple">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h3>Giờ học tuần này</h3>
                        <p class="stat-value">12.5</p>
                    </div>
                    <div class="stat-card">
                        <div class="icon-wrapper icon-green">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h3>Chứng chỉ đạt được</h3>
                        <p class="stat-value">5</p>
                    </div>
                    <div class="stat-card">
                        <div class="icon-wrapper icon-orange">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h3>Tiến độ trung bình</h3>
                        <p class="stat-value">60%</p>
                    </div>
                </div>
            </section>

            <section class="my-courses">
                <h2 class="section-title">Khóa học của tôi</h2>
                <div class="courses-grid">
                    <div class="course-card">
                        <img src="https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=500" alt="Web Full-Stack">
                        <div class="card-content">
                            <h3>Lập trình Web Full-Stack</h3>
                            <div class="progress-info">
                                <span>Tiến độ</span>
                                <span>65%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 65%;"></div>
                            </div>
                            <p class="lesson-count">31/48 bài học</p>
                            <button class="btn btn-continue">
                                <i class="fa-solid fa-play"></i>
                                <span>React Hooks nâng cao</span>
                            </button>
                        </div>
                    </div>
                    <div class="course-card">
                        <img src="https://www.sandipuniversity.edu.in/blog/wp-content/uploads/2020/01/70847328_xl.jpg" alt="Data Science">
                        <div class="card-content">
                            <h3>Data Science & AI</h3>
                            <div class="progress-info">
                                <span>Tiến độ</span>
                                <span>35%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 35%;"></div>
                            </div>
                            <p class="lesson-count">20/56 bài học</p>
                            <button class="btn btn-continue">
                                <i class="fa-solid fa-play"></i>
                                <span>Machine Learning cơ bản</span>
                            </button>
                        </div>
                    </div>
                    <div class="course-card">
                        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=500" alt="Teamwork">
                        <div class="card-content">
                            <h3>Kỹ năng làm việc nhóm</h3>
                            <div class="progress-info">
                                <span>Tiến độ</span>
                                <span>80%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 80%;"></div>
                            </div>
                            <p class="lesson-count">19/24 bài học</p>
                            <button class="btn btn-continue">
                                <i class="fa-solid fa-play"></i>
                                <span>Quản lý xung đột</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="recent-activity">
                <h2 class="section-title">Hoạt động gần đây</h2>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon icon-green">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="activity-details">
                            <p>Hoàn thành bài học: <strong>React Components</strong></p>
                            <span class="course-name">Lập trình Web Full-Stack</span>
                            <span class="timestamp">2 giờ trước</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon icon-blue">
                            <i class="fa-solid fa-play"></i>
                        </div>
                        <div class="activity-details">
                            <p>Bắt đầu khóa học mới: <strong>Data Science & AI</strong></p>
                            <span class="course-name">Data Science & AI</span>
                            <span class="timestamp">1 ngày trước</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon icon-purple">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div class="activity-details">
                            <p>Nhận chứng chỉ: <strong>JavaScript Advanced</strong></p>
                            <span class="course-name">JavaScript nâng cao</span>
                            <span class="timestamp">3 ngày trước</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

</body>
</html>