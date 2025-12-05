<?php
session_start();
require_once '../config/config.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. LẤY THÔNG TIN USER
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();
$username = $user_info['username'];
$email = $user_info['email'];
$user_initial = strtoupper(substr($username, 0, 2));

// 3. LẤY DANH SÁCH CHỨNG CHỈ
// Logic: Chỉ lấy khóa học có progress = 100
$sql = "SELECT 
            c.course_id, c.title, c.category, 
            e.enrolled_at, e.progress
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.user_id = ? AND e.progress = 100
        ORDER BY e.enrolled_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$certificates = [];
while ($row = $result->fetch_assoc()) {
    $certificates[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chứng chỉ của tôi - EduTech</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/student_dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php require '../includes/header.php'; ?>

    <div class="student-layout container">
        
        <?php require '../includes/student_sidebar.php'; ?>

        <main class="student-content">
            
            <div class="section-header">
                <h2>Chứng chỉ của tôi</h2>
            </div>

            <div class="cert-grid">
                
                <?php if (count($certificates) > 0): ?>
                    <?php foreach ($certificates as $cert): ?>
                        <div class="cert-card">
                            <div class="cert-thumb">
                                <div class="cert-logo"><i class="fa-solid fa-graduation-cap"></i></div>
                                <div class="cert-badge"><i class="fa-solid fa-check"></i> Verified</div>
                            </div>
                            
                            <div class="cert-body">
                                <div class="cert-date">Cấp ngày: <?php echo date('d/m/Y'); ?></div>
                                
                                <h3 class="cert-title"><?php echo htmlspecialchars($cert['title']); ?></h3>
                                <p class="cert-cat">Lĩnh vực: <?php echo htmlspecialchars($cert['category']); ?></p>
                                
                                <div class="cert-footer">
                                    <span class="cert-id">ID: CERT-<?php echo $cert['course_id'] . date('Y'); ?></span>
                                    
                                    <a href="certificate.php?course_id=<?php echo $cert['course_id']; ?>" target="_blank" class="btn-cert-action" title="Xem & Tải xuống">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    
                    <div class="empty-state-cert">
                        <div class="empty-icon-box">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <h3>Chưa có chứng chỉ nào</h3>
                        <p>Hãy hoàn thành 100% khóa học để nhận chứng chỉ danh giá từ EduTech.</p>
                        <a href="my_courses.php" class="btn btn-primary btn-sm">Tiếp tục học ngay</a>
                    </div>

                <?php endif; ?>

            </div>

        </main>
    </div>

</body>
</html>