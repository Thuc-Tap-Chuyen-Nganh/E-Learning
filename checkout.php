<?php
session_start();
require_once 'config/config.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: " . BASE_URL . "login.php");
    exit();
}

// 2. Lấy ID khóa học
if (!isset($_GET['course_id'])) {
    header("Location: " . BASE_URL . "courses.php");
    exit();
}

$course_id = intval($_GET['course_id']);
$user_id = $_SESSION['user_id'];

// 3. Lấy thông tin khóa học
$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) die("Khóa học không tồn tại");

// 4. Kiểm tra nếu đã mua rồi -> Đá về trang học
$check = $conn->query("SELECT * FROM enrollments WHERE user_id = $user_id AND course_id = $course_id");
if ($check->num_rows > 0) {
    header("Location: " . BASE_URL . "student/learning.php?course_id=$course_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - <?= htmlspecialchars($course['title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/index.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .checkout-container {
            max-width: 900px; margin: 50px auto;
            display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;
        }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .course-summary img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; }
        .price-row { display: flex; justify-content: space-between; margin: 15px 0; font-size: 16px; color: #555; }
        .total-row { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-weight: 700; font-size: 20px; color: #333; }
        
        .qr-box { text-align: center; background: #f0fdf4; border: 2px dashed #22c55e; padding: 20px; border-radius: 12px; }
        .qr-box img { max-width: 200px; mix-blend-mode: multiply; }
        .bank-info { margin-top: 15px; text-align: left; font-size: 14px; }
        .bank-info p { margin-bottom: 5px; }
        .copy-btn { cursor: pointer; color: #2563eb; font-size: 12px; margin-left: 5px; }

        .btn-confirm { width: 100%; padding: 15px; background: #22c55e; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 16px; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-confirm:hover { background: #16a34a; }

        /* Responsive */
        @media (max-width: 768px) { .checkout-container { grid-template-columns: 1fr; padding: 0 20px; } }
    </style>
</head>
<body>

    <?php require 'includes/header.php'; ?>

    <div class="checkout-container">
        
        <div class="card">
            <h2 style="margin-bottom: 20px;">Xác nhận đơn hàng</h2>
            <div class="course-summary">
                <img src="<?= get_course_image($course['thumbnail'], $course['category']) ?>" alt="">
                <h3><?= htmlspecialchars($course['title']) ?></h3>
                <p style="color: #666; font-size: 14px; margin-top: 5px;">Giảng viên: EduTech Team</p>
            </div>

            <div class="price-row">
                <span>Giá gốc:</span>
                <span style="text-decoration: line-through;"><?= number_format($course['price'] * 1.2) ?>đ</span>
            </div>
            <div class="price-row">
                <span>Giảm giá:</span>
                <span style="color: #22c55e;">-20%</span>
            </div>
            <div class="total-row">
                <span>Thành tiền:</span>
                <span style="color: #d32f2f;"><?= number_format($course['price']) ?>đ</span>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 20px;">Thanh toán</h2>
            
            <?php 
                // === CẤU HÌNH TÀI KHOẢN NHẬN TIỀN ===
                $my_bank_id = "MB";          // Mã ngân hàng (MB, VCB, TCB, ACB...)
                $my_account_no = "0797355264";  // Số tài khoản 
                $my_account_name = "TRAN THIEN TRIEU"; // Tên chủ tài khoản
                
                // Mã đơn hàng tự động
                $order_code = "ET" . time() . $user_id; 
                
                // Link tạo QR tự động 
                // Cú pháp VietQR: https://img.vietqr.io/image/[BankID]-[AccountNo]-compact.png
                $qr_url = "https://img.vietqr.io/image/{$my_bank_id}-{$my_account_no}-compact.png?amount={$course['price']}&addInfo={$order_code}&accountName={$my_account_name}";
            ?>

            <div class="qr-box">
                <p style="font-weight: 600; margin-bottom: 15px;">Quét mã QR để thanh toán</p>
                
                <img src="<?= $qr_url ?>" alt="QR Code" style="width: 100%; max-width: 250px;">
                
                <div class="bank-info" style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <p><strong>Ngân hàng:</strong> <?= $my_bank_id ?></p>
                    <p><strong>Số TK:</strong> <?= $my_account_no ?></p>
                    <p><strong>Chủ TK:</strong> <?= $my_account_name ?></p>
                    <p style="color: #d32f2f; margin-top: 10px;"><strong>Nội dung CK:</strong> <?= $order_code ?></p>
                </div>
            </div>

            <form action="<?= BASE_URL ?>logic/student/process_payment.php" method="POST" onsubmit="return confirm('Bạn chắc chắn đã chuyển khoản chưa?');">
                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                <input type="hidden" name="amount" value="<?= $course['price'] ?>">
                <input type="hidden" name="transaction_code" value="<?= $order_code ?>">
                
                <button type="submit" class="btn-confirm">
                    <i class="fa-solid fa-check"></i> Tôi đã thanh toán
                </button>
            </form>
            
            <p style="font-size: 12px; color: #666; text-align: center; margin-top: 15px;">
                Hệ thống sẽ duyệt đơn hàng của bạn trong vòng 24h.
            </p>
        </div>

    </div>

    <?php require 'includes/footer.php'; ?>

</body>
</html>