<?php
session_start();
require_once '../config/config.php';

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy danh sách đơn hàng (Sắp xếp mới nhất lên đầu)
$sql = "SELECT p.*, u.username, u.email, c.title as course_title 
        FROM payments p
        JOIN users u ON p.user_id = u.user_id
        JOIN courses c ON p.course_id = c.course_id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Quản lý thanh toán</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Quản lý thanh toán</h1>
            </div>
        </header>

        <main class="main-content">
            <section class="table-container">
                <div class="table-header">
                    <h2>Lịch sử giao dịch</h2>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Học viên</th>
                            <th>Khóa học</th>
                            <th>Số tiền</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['transaction_code']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($row['username']) ?><br>
                                        <small style="color: #666;"><?= htmlspecialchars($row['email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['course_title']) ?></td>
                                    <td style="font-weight: bold; color: #d32f2f;"><?= number_format($row['amount']) ?>đ</td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'pending'): ?>
                                            <span class="status status-draft">Chờ duyệt</span>
                                        <?php elseif ($row['status'] == 'completed'): ?>
                                            <span class="status status-active">Thành công</span>
                                        <?php else: ?>
                                            <span class="status status-inactive">Đã hủy</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'pending'): ?>
                                            <a href="../logic/admin/payment_approve.php?id=<?= $row['payment_id'] ?>&action=approve" 
                                               class="btn-icon btn-edit" title="Duyệt đơn" 
                                               onclick="return confirm('Xác nhận đã nhận tiền và kích hoạt khóa học?');"
                                               style="color: #16a34a; background: #dcfce7;">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                            
                                            <a href="../logic/admin/payment_approve.php?id=<?= $row['payment_id'] ?>&action=reject" 
                                               class="btn-icon btn-delete" title="Hủy đơn"
                                               onclick="return confirm('Hủy đơn hàng này?');">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #ccc;"><i class="fa-solid fa-lock"></i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center;">Chưa có giao dịch nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>