<?php
session_start();
require_once '../config/config.php'; 

// === BẢO VỆ ===
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// === 1. LẤY COURSE_ID TỪ URL ===
if (!isset($_GET['course_id'])) {
    header("Location: admin_courses.php?error=no_course_id");
    exit();
}
$course_id = $_GET['course_id'];

// === 2. LẤY THÔNG TIN KHÓA HỌC ===
$stmt_course = $conn->prepare("SELECT title FROM courses WHERE course_id = ?");
$stmt_course->bind_param("i", $course_id);
$stmt_course->execute();
$course_result = $stmt_course->get_result();
$course = $course_result->fetch_assoc();
if (!$course) {
    header("Location: admin_courses.php?error=course_not_found");
    exit();
}
$course_title = $course['title'];
$stmt_course->close();

// === 3. LẤY DANH SÁCH CÁC CHƯƠNG ===
$stmt_chapters = $conn->prepare("SELECT * FROM chapters WHERE course_id = ? ORDER BY sort_order ASC");
$stmt_chapters->bind_param("i", $course_id);
$stmt_chapters->execute();
$chapters_result = $stmt_chapters->get_result();
$total_chapters = $chapters_result->num_rows;

// === 4. TÍNH TOÁN THỐNG KÊ TOÀN KHÓA HỌC (DỮ LIỆU THẬT) ===
// Giả định bạn đã tạo bảng 'lessons' có cột 'chapter_id' và 'duration' (phút)

// Tính tổng số bài học và tổng thời lượng của khóa học này
// Chúng ta JOIN bảng lessons với chapters
$sql_stats = "SELECT 
                COUNT(l.lesson_id) as total_lessons, 
                SUM(l.duration) as total_duration 
              FROM lessons l 
              JOIN chapters c ON l.chapter_id = c.chapter_id 
              WHERE c.course_id = ?";

$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("i", $course_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

$total_lessons = $stats['total_lessons'] ?? 0;
$total_minutes = $stats['total_duration'] ?? 0;

// Hàm helper đổi phút sang giờ:phút (VD: 90 -> 1h 30m)
function format_duration($minutes) {
    if ($minutes < 60) {
        return $minutes . "m";
    }
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return "{$hours}h {$mins}m";
}

$total_duration_formatted = format_duration($total_minutes);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Chương - <?php echo htmlspecialchars($course_title); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/admin_styles.css?v=<?= filemtime('css/admin_styles.css') ?>">
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <a href="admin_courses.php" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách khóa học
                </a>
                <h1><?php echo htmlspecialchars($course_title); ?></h1>
                <p>Quản lý chương và nội dung khóa học</p>
            </div>
        </header>

        <main class="main-content">
            
            <?php if (isset($_GET['status']) && $_GET['status'] == 'added'): ?>
                <div id="successAlert" class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; transition: opacity 0.5s ease;">
                    Thêm chương mới thành công!
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
                <div id="successAlert" class="alert alert-success" style="...">
                    Cập nhật chương thành công!
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    Có lỗi xảy ra, vui lòng thử lại.
                </div>
            <?php endif; ?>

            <section class="stat-cards-grid">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng số chương</span>
                        <span class="card-value"><?php echo $total_chapters; ?></span>
                    </div>
                    <div class="card-icon icon-book-blue">
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng số bài học</span>
                        <span class="card-value"><?php echo $total_lessons; ?></span> </div>
                    <div class="card-icon icon-book-green">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng thời lượng</span>
                        <span class="card-value"><?php echo $total_duration_formatted; ?></span> </div>
                    <div class="card-icon icon-book-purple">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
            </section>

            <section class="list-container">
                <div class="list-header"> 
                    <h2>Danh sách chương</h2>
                    <div class="list-controls"> 
                        <button class="btn btn-primary" id="addChapterBtn">
                            <i class="fa-solid fa-plus"></i>
                            <span>Thêm chương</span>
                        </button>
                    </div>
                </div>

                <div class="chapter-list">
                    <?php if ($chapters_result->num_rows > 0): ?>
                        <?php 
                        while($chapter = $chapters_result->fetch_assoc()): 
                            // --- TÍNH TOÁN CHO TỪNG CHƯƠNG ---
                            // Đếm số bài học và thời lượng của RIÊNG chương này
                            $chap_id = $chapter['chapter_id'];
                            $sql_chap_stats = "SELECT COUNT(*) as count, SUM(duration) as duration FROM lessons WHERE chapter_id = $chap_id";
                            $res_chap_stats = $conn->query($sql_chap_stats);
                            $chap_stats = $res_chap_stats->fetch_assoc();
                            
                            $chap_lessons = $chap_stats['count'] ?? 0;
                            $chap_duration = format_duration($chap_stats['duration'] ?? 0);
                        ?>
                            <div class="chapter-item">
                                <div class="chapter-info">
                                    <span class="chapter-order"><?php echo htmlspecialchars($chapter['sort_order']); ?></span>
                                    <div class="chapter-details">
                                        <h3><?php echo htmlspecialchars($chapter['title']); ?></h3>
                                        
                                        <?php if (!empty($chapter['description'])): ?>
                                            <p><?php echo htmlspecialchars($chapter['description']); ?></p> 
                                        <?php endif; ?>

                                        <div class="chapter-meta">
                                            <span><?php echo $chap_lessons; ?> bài học</span>
                                            <span>•</span>
                                            <span><?php echo $chap_duration; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="chapter-actions">
                                    <button class="btn-icon btn-edit edit-chapter-btn" 
                                            data-id="<?php echo $chapter['chapter_id']; ?>"
                                            data-title="<?php echo htmlspecialchars($chapter['title']); ?>"
                                            data-desc="<?php echo htmlspecialchars($chapter['description']); ?>"
                                            data-order="<?php echo $chapter['sort_order']; ?>">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button class="btn-icon btn-delete delete-chapter-btn" 
                                            data-id="<?php echo $chapter['chapter_id']; ?>"
                                            data-title="<?php echo htmlspecialchars($chapter['title']); ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <a href="admin_chapter_details.php?chapter_id=<?php echo $chapter['chapter_id']; ?>" class="btn btn-detail-link">
                                        <span>Xem bài học</span>
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; padding: 20px; color: #666;">Chưa có chương nào. Hãy thêm chương mới!</p>
                    <?php endif; ?>
                </div>

            </section>
        </main>

        <div id="addChapterModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Thêm chương mới</h3>
                </div>
                
                <form action="<?= BASE_URL ?>logic/admin/chapter_add.php" method="POST">
                    
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="chapter_title">Tên chương</label>
                            <input type="text" id="chapter_title" name="title" placeholder="Nhập tên chương" required>
                        </div>

                        <div class="form-group">
                            <label for="chapter_desc">Mô tả</label>
                            <textarea id="chapter_desc" name="description" rows="3" placeholder="Mô tả ngắn về chương này..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="chapter_order">Thứ tự</label>
                            <input type="number" id="chapter_order" name="sort_order" value="<?php echo $total_chapters + 1; ?>" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="closeModalBtn">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm chương</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteChapterModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
                <h3 style="color: #dc3545;">Xác nhận xóa</h3>
            </div>
            <div class="modal-body">
                <p id="deleteModalText">Bạn có chắc chắn muốn xóa chương này không?</p>
                <p style="font-size: 0.9em; color: #666;">Lưu ý: Tất cả bài học trong chương này cũng sẽ bị xóa.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelDeleteBtn">Hủy</button>
                <button class="btn btn-danger" id="confirmDeleteBtn" style="background-color: #dc3545; color: white; border: none;">Xóa</button>
            </div>
        </div>
    </div>

    <div id="editChapterModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cập nhật chương</h3>
            </div>
            
            <form action="<?= BASE_URL ?>logic/admin/chapter_edit.php" method="POST">
                <input type="hidden" name="chapter_id" id="edit_chapter_id">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_chapter_title">Tên chương</label>
                        <input type="text" id="edit_chapter_title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_chapter_desc">Mô tả</label>
                        <textarea id="edit_chapter_desc" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_chapter_order">Thứ tự</label>
                        <input type="number" id="edit_chapter_order" name="sort_order" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeEditModalBtn">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/admin_chapters.js?v=<?php echo filemtime('js/admin_chapters.js'); ?>"></script>

</body>
</html>