<?php
// student/certificate.php
session_start();
require '../src/core/db_connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) exit('Vui lòng đăng nhập.');

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// 2. Lấy thông tin & Kiểm tra tiến độ
$sql = "SELECT 
            c.title as course_title, 
            u.username as student_name, 
            e.enrolled_at,
            e.progress
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        JOIN users u ON e.user_id = u.user_id
        WHERE e.user_id = ? AND e.course_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) exit('Không tìm thấy thông tin khóa học.');
if ($data['progress'] < 100) exit('Bạn chưa hoàn thành khóa học này nên chưa thể nhận chứng chỉ.');

// Ngày hoàn thành (Lấy ngày hiện tại làm ngày cấp)
$date = date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chứng chỉ - <?php echo htmlspecialchars($data['course_title']); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Merriweather:wght@300;400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            margin: 0; padding: 0;
            background: #eee;
            font-family: 'Open Sans', sans-serif;
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh;
        }

        .cert-container {
            width: 1000px; height: 700px; /* Tỉ lệ gần giống A4 ngang */
            background: #fff;
            padding: 50px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            border: 10px solid #2c3e50;
            background-image: radial-gradient(circle at center, #fff 0%, #f9f9f9 100%);
        }

        /* Border họa tiết bên trong */
        .cert-border {
            width: 100%; height: 100%;
            border: 2px solid #c0392b;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }

        /* Logo góc */
        .cert-logo {
            position: absolute; top: 40px; right: 40px;
            font-size: 24px; font-weight: bold; color: #8e2de2;
            display: flex; align-items: center; gap: 10px;
        }

        .cert-header {
            font-family: 'Merriweather', serif;
            font-size: 48px;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cert-sub {
            font-size: 18px; color: #7f8c8d; margin-top: 10px;
            text-transform: uppercase; letter-spacing: 1px;
        }

        .cert-present {
            font-size: 16px; margin-top: 40px; color: #34495e; font-style: italic;
        }

        .student-name {
            font-family: 'Great Vibes', cursive; /* Font chữ ký đẹp */
            font-size: 80px;
            color: #c0392b; /* Màu đỏ đô sang trọng */
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .cert-body {
            font-size: 18px; color: #2c3e50; line-height: 1.6; max-width: 80%; margin: 0 auto;
        }
        .course-name { font-weight: 700; font-size: 24px; color: #2980b9; display: block; margin: 10px 0; }

        .cert-footer {
            margin-top: 60px;
            display: flex; justify-content: space-between; align-items: flex-end;
            padding: 0 50px;
        }

        .signature { text-align: center; }
        .sign-line {
            width: 200px; border-bottom: 2px solid #333; margin-bottom: 10px;
        }
        .sign-name { font-weight: bold; font-size: 16px; }
        .sign-title { font-size: 12px; color: #7f8c8d; text-transform: uppercase; }

        .cert-badge {
            font-size: 80px; color: #f1c40f;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Nút In (Sẽ ẩn khi in thật) */
        .print-btn {
            position: fixed; bottom: 30px; right: 30px;
            background: #2980b9; color: white;
            padding: 15px 30px; border-radius: 50px;
            text-decoration: none; font-weight: bold;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transition: 0.3s;
            cursor: pointer; border: none; font-size: 16px;
        }
        .print-btn:hover { transform: translateY(-3px); }

        /* CSS KHI IN */
        @media print {
            body { background: none; margin: 0; }
            .cert-container { box-shadow: none; border: 5px solid #2c3e50; width: 100%; height: 100vh; page-break-after: always; }
            .print-btn { display: none; }
            @page { size: landscape; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="cert-container">
        <div class="cert-border">
            
            <div class="cert-logo">
                <i class="fa-solid fa-book-open"></i> EduTech
            </div>

            <div class="cert-header">Giấy Chứng Nhận</div>
            <div class="cert-sub">Hoàn thành khóa học</div>

            <div class="cert-present">Chứng nhận này được trao tặng cho</div>

            <div class="student-name"><?php echo htmlspecialchars($data['student_name']); ?></div>

            <div class="cert-body">
                Đã hoàn thành xuất sắc tất cả các bài học và bài kiểm tra của khóa học:
                <span class="course-name">"<?php echo htmlspecialchars($data['course_title']); ?>"</span>
                Tại nền tảng học trực tuyến EduTech.
            </div>

            <div class="cert-footer">
                <div class="signature">
                    <div style="font-family: 'Great Vibes', cursive; font-size: 30px; margin-bottom: 5px;">Edutech Team</div>
                    <div class="sign-line"></div>
                    <div class="sign-name">EduTech Team</div>
                    <div class="sign-title">Đơn vị đào tạo</div>
                </div>

                <div class="cert-badge">
                    <i class="fa-solid fa-certificate"></i>
                </div>

                <div class="signature">
                    <div style="font-family: 'Courier New', monospace; margin-bottom: 10px;"><?php echo $date; ?></div>
                    <div class="sign-line"></div>
                    <div class="sign-name">Ngày cấp</div>
                    <div class="sign-title">Xác nhận</div>
                </div>
            </div>

        </div>
    </div>

    <button onclick="window.print()" class="print-btn">
        <i class="fa-solid fa-print"></i> In chứng chỉ
    </button>

</body>
</html>