<?php
session_start();
require_once '../config/config.php'; 

// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// 1. TÍNH TOÁN THỐNG KÊ
// Tổng học viên (chỉ lấy role student)
$res_total = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
$total_students = $res_total->fetch_assoc()['total'];

// Đang hoạt động (status = active)
$res_active = $conn->query("SELECT COUNT(*) as active FROM users WHERE role = 'student' AND status = 'active'");
$active_students = $res_active->fetch_assoc()['active'];

// Mới tháng này
$current_month = date('Y-m');
$res_new = $conn->query("SELECT COUNT(*) as new FROM users WHERE role = 'student' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'");
$new_students = $res_new->fetch_assoc()['new'];

// 2. LẤY DANH SÁCH HỌC VIÊN
// Lấy thêm thông tin số khóa học đã đăng ký (subquery)
$sql = "SELECT 
            u.user_id, u.username, u.email, u.created_at, u.status,
            (SELECT COUNT(*) FROM enrollments e WHERE e.user_id = u.user_id) as enrolled_count
        FROM users u 
        WHERE u.role = 'student' 
        ORDER BY u.user_id DESC";

$result = $conn->query($sql);

// Helper status
function get_status_badge($status) {
    if ($status == 'active') return '<span class="status status-active">Hoạt động</span>';
    if ($status == 'pending') return '<span class="status status-draft">Chờ duyệt</span>'; // Hoặc màu vàng
    if ($status == 'banned' || $status == 'inactive') return '<span class="status status-inactive">Đã khóa</span>';
    return '<span class="status">' . $status . '</span>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý học viên</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_styles.css?v=<?= filemtime('css/admin_styles.css') ?>">
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Quản lý học viên</h1>
                <p>Quản lý và theo dõi thông tin học viên</p>
            </div>
        </header>

        <main class="main-content">
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <section class="stat-cards-grid">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng học viên</span>
                        <span class="card-value"><?php echo $total_students; ?></span>
                    </div>
                    <div class="card-icon icon-blue"><i class="fa-solid fa-users"></i></div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Đang hoạt động</span>
                        <span class="card-value"><?php echo $active_students; ?></span>
                    </div>
                    <div class="card-icon icon-green"><i class="fa-solid fa-user-check"></i></div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Mới tháng này</span>
                        <span class="card-value"><?php echo $new_students; ?></span>
                    </div>
                    <div class="card-icon icon-purple"><i class="fa-solid fa-user-clock"></i></div>
                </div>
            </section>

            <section class="table-container">
                <div class="table-header">
                    <h2>Danh sách học viên</h2>
                    </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Học viên</th>
                            <th>Email</th>
                            <th>Khóa học ĐK</th>
                            <th>Ngày tham gia</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $initial = strtoupper(substr($row['username'], 0, 1));
                                // Random màu cho avatar
                                $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                                $bg_color = $colors[array_rand($colors)];
                            ?>
                                <tr>
                                    <td>
                                        <div class="avatar-cell">
                                            <div class="avatar" style="background: <?php echo $bg_color; ?>; color: white; display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 50%; font-weight: bold; margin-right: 10px;">
                                                <?php echo $initial; ?>
                                            </div> 
                                            <span><?php echo htmlspecialchars($row['username']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td style="text-align: center;"><?php echo $row['enrolled_count']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo get_status_badge($row['status']); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'active'): ?>
                                            <a href="<?= BASE_URL ?>/logic/admin/toggle_user_status.php?id=<?php echo $row['user_id']; ?>&action=ban" 
                                               class="btn-icon btn-delete" title="Khóa tài khoản" onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?');">
                                                <i class="fa-solid fa-lock"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/logic/admin/toggle_user_status.php?id=<?php echo $row['user_id']; ?>&action=active" 
                                               class="btn-icon btn-edit" title="Mở khóa" onclick="return confirm('Mở khóa tài khoản này?');">
                                                <i class="fa-solid fa-lock-open"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center;">Chưa có học viên nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

</body>
</html>