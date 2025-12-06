<?php
session_start();
require_once '../config/config.php'; 

// === BẢO VỆ SESSION ===
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// === 1. LẤY CHAPTER_ID TỪ URL ===
if (!isset($_GET['chapter_id'])) {
    header("Location: admin_courses.php");
    exit();
}
$chapter_id = $_GET['chapter_id'];

// === 2. LẤY THÔNG TIN CHƯƠNG & KHÓA HỌC (ĐỂ LÀM BREADCRUMB/BACK LINK) ===
$stmt = $conn->prepare("
    SELECT c.title as chapter_title, co.course_id, co.title as course_title 
    FROM chapters c 
    JOIN courses co ON c.course_id = co.course_id 
    WHERE c.chapter_id = ?
");
$stmt->bind_param("i", $chapter_id);
$stmt->execute();
$chapter_info = $stmt->get_result()->fetch_assoc();

if (!$chapter_info) {
    die("Không tìm thấy chương này.");
}

$course_id = $chapter_info['course_id'];
$course_title = $chapter_info['course_title'];
$chapter_title = $chapter_info['chapter_title'];

// === 3. LẤY DANH SÁCH BÀI HỌC ===
// Cấu trúc DB: lesson_id, chapter_id, title, lesson_type, content_url, content_text, duration
$stmt_lessons = $conn->prepare("SELECT * FROM lessons WHERE chapter_id = ? ORDER BY lesson_id ASC");
$stmt_lessons->bind_param("i", $chapter_id);
$stmt_lessons->execute();
$result_lessons = $stmt_lessons->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapter_title); ?> - Quản lý bài học</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

    <link rel="stylesheet" href="css/admin_styles.css?v=<?= filemtime('css/admin_styles.css') ?>">
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <a href="admin_course_details.php?course_id=<?php echo $course_id; ?>" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại: <?php echo htmlspecialchars($course_title); ?>
                </a>
                <h1><?php echo htmlspecialchars($chapter_title); ?></h1>
                <p>Quản lý các bài học trong chương này</p>
            </div>
            <a href="../src/handlers/admin_logout.php" class="logout-btn">
                 <i class="fa-solid fa-arrow-right-from-bracket"></i>
                 <span>Đăng xuất</span>
            </a>
        </header>

        <main class="main-content">
            
            <section class="list-container">
                <div class="list-header">
                    <div>
                        <h2>Danh sách bài học</h2>
                        <p style="color: #666; font-size: 14px; margin-top: 5px;">Quản lý nội dung chi tiết trong chương này</p>
                    </div>
                    <div class="list-controls">
                        <button class="btn btn-primary" id="addLessonBtn">
                            <i class="fa-solid fa-plus"></i>
                            <span>Thêm bài học</span>
                        </button>
                    </div>
                </div>

                <div class="lesson-list">
                    <?php if ($result_lessons->num_rows > 0): ?>
                        <?php while($lesson = $result_lessons->fetch_assoc()): ?>
                            
                            <div class="lesson-item">
                                
                                <div class="lesson-icon-wrapper">
                                    <?php if($lesson['lesson_type'] == 'video'): ?>
                                        <div class="icon-circle icon-video"><i class="fa-solid fa-play"></i></div>
                                    <?php elseif($lesson['lesson_type'] == 'text'): ?>
                                        <div class="icon-circle icon-doc"><i class="fa-solid fa-file-lines"></i></div>
                                    <?php else: ?>
                                        <div class="icon-circle icon-quiz"><i class="fa-solid fa-circle-question"></i></div>
                                    <?php endif; ?>
                                </div>

                                <div class="lesson-info">
                                    <div class="lesson-header-row">
                                        <h3 class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                                        
                                        <?php if($lesson['lesson_type'] == 'video'): ?>
                                            <span class="meta-badge badge-video">Video</span>
                                        <?php elseif($lesson['lesson_type'] == 'text'): ?>
                                            <span class="meta-badge badge-doc">Bài đọc</span>
                                        <?php else: ?>
                                            <span class="meta-badge badge-quiz">Quiz</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="lesson-meta-row">
                                        <span class="meta-item">
                                            <i class="fa-regular fa-clock"></i> 
                                            <?php echo ($lesson['duration'] > 0) ? $lesson['duration'] . ' phút' : '---'; ?>
                                        </span>
                                        
                                        <span class="meta-dot">•</span>

                                        <span class="meta-item" style="color: var(--status-green-text);">
                                            Đã xuất bản
                                        </span>
                                    </div>
                                </div>

                                <div class="lesson-actions">
                                    <button class="btn-icon btn-edit edit-lesson-btn"
                                        data-id="<?php echo $lesson['lesson_id']; ?>"
                                        data-title="<?php echo htmlspecialchars($lesson['title']); ?>"
                                        data-type="<?php echo $lesson['lesson_type']; ?>"
                                        data-video="<?php echo htmlspecialchars($lesson['video_url'] ?? ''); ?>"
                                        data-duration="<?php echo $lesson['duration']; ?>"
                                        data-order="<?php echo $lesson['sort_order']; ?>"
                                    >
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <div id="lesson-content-data-<?php echo $lesson['lesson_id']; ?>" style="display:none;">
                                        <?php echo $lesson['content']; ?>
                                    </div>

                                    <button class="btn-icon btn-delete delete-lesson-btn" 
                                            data-id="<?php echo $lesson['lesson_id']; ?>" 
                                            data-title="<?php echo htmlspecialchars($lesson['title']); ?>"
                                            title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    
                                    <?php if($lesson['lesson_type'] == 'quiz'): ?>
                                        <a href="admin_quiz_manage.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="btn-action-pill">
                                            Câu hỏi <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>
                            <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <p class="empty-text">Chưa có bài học nào trong chương này.</p>
                            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('addLessonBtn').click()">
                                Tạo bài học ngay
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

            </section>
        </main>
    </div>

    <div id="addLessonModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 1000px;"> <div class="modal-header">
                <h3>Thêm bài học mới</h3>
            </div>
            
            <form action="<?= BASE_URL ?>logic/admin/lesson_add.php" method="POST">
                
                <input type="hidden" name="chapter_id" value="<?php echo $chapter_id; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="lesson_title">Tên bài học</label>
                        <input type="text" id="lesson_title" name="title" placeholder="Ví dụ: Giới thiệu React..." required>
                    </div>

                    <div class="form-group">
                        <label>Thứ tự</label>
                        <input type="number" name="sort_order" value="<?php echo $result_lessons->num_rows + 1; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="lesson_type">Loại bài học</label>
                        <div class="select-wrapper">
                            <select id="lesson_type" name="lesson_type">
                                <option value="video">Video</option>
                                <option value="text">Bài đọc (Text)</option>
                                <option value="quiz">Trắc nghiệm (Quiz)</option>
                            </select>
                        </div>
                    </div>

                    <div id="group_video_url">
                        <div class="form-group">
                            <label for="duration">Thời lượng (Phút)</label>
                            <input type="number" id="duration" name="duration" value="0" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="content_url">Video URL (YouTube/Vimeo/Server)</label>
                            <input type="text" id="content_url" name="content_url" placeholder="https://...">
                            <small style="color: #666; font-size: 12px;">Dán đường dẫn video vào đây.</small>
                        </div>
                    </div>

                    <div class="form-group" id="group_content_text" style="display: none;">
                        <label for="content_text">Nội dung bài học</label>

                        <div style="margin-bottom: 10px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('import_word_add').click()">
                                <i class="fa-solid fa-file-word" style="color: #2b579a;"></i> Nhập từ Word (.docx)
                            </button>
                            <input type="file" id="import_word_add" accept=".docx" style="display: none;" onchange="importWordToEditor(this, 'add')">
                            <span id="import_status_add" style="font-size: 12px; margin-left: 10px; color: #666;"></span>
                        </div>
                        
                        <textarea id="content_text" name="content_text"></textarea>
                    </div>

                    <div class="form-group" id="group_quiz_info" style="display: none;">
                        <div style="background: #f0f4ff; padding: 15px; border-radius: 8px; border: 1px dashed #007bff; color: #0056b3;">
                            <i class="fa-solid fa-info-circle"></i> 
                            Bạn đang tạo bài kiểm tra. Sau khi nhấn <strong>"Thêm bài học"</strong>, bạn sẽ có thể thêm các câu hỏi ở màn hình danh sách.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModalBtn">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm bài học</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editLessonModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 1000px;">
            <div class="modal-header">
                <h3>Cập nhật bài học</h3>
            </div>
            
            <form action="<?= BASE_URL ?>logic/admin/lesson_edit.php" method="POST">
                <input type="hidden" name="lesson_id" id="edit_lesson_id">
                <input type="hidden" name="chapter_id" value="<?php echo $chapter_id; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên bài học</label>
                        <input type="text" id="edit_lesson_title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label>Thứ tự</label>
                        <input type="number" id="edit_sort_order" name="sort_order" required>
                    </div>

                    <div class="form-group">
                        <label>Loại bài học</label>
                        <select id="edit_lesson_type" name="lesson_type" class="input-field" onchange="toggleEditFields()">
                            <option value="video">Video</option>
                            <option value="text">Bài đọc (Text)</option>
                            <option value="quiz">Trắc nghiệm (Quiz)</option>
                        </select>
                    </div>

                    <div id="edit_group_video">
                        <div class="form-group">
                            <label>Thời lượng (Phút)</label>
                            <input type="number" id="edit_duration" name="duration" min="0">
                        </div>
                        <div class="form-group">
                            <label>Video URL</label>
                            <input type="text" id="edit_content_url" name="content_url">
                        </div>
                    </div>

                    <div class="form-group" id="edit_group_text" style="display: none;">
                        <label>Nội dung bài học</label>

                        <div style="margin-bottom: 10px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('import_word_edit').click()">
                                <i class="fa-solid fa-file-word" style="color: #2b579a;"></i> Nhập từ Word (.docx)
                            </button>
                            <input type="file" id="import_word_edit" accept=".docx" style="display: none;" onchange="importWordToEditor(this, 'edit')">
                            <span id="import_status_edit" style="font-size: 12px; margin-left: 10px; color: #666;"></span>
                        </div>

                        <textarea id="edit_content_text" name="content_text"></textarea>
                    </div>

                    <div class="form-group" id="edit_group_quiz" style="display: none;">
                        <div style="background: #f0f4ff; padding: 15px; border-radius: 8px; border: 1px dashed #007bff; color: #0056b3;">
                            <i class="fa-solid fa-info-circle"></i> 
                            Để sửa câu hỏi, vui lòng nhấn nút <strong>"Câu hỏi"</strong> ở ngoài danh sách.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeEditLessonBtn">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteLessonModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none;">
                <h3 style="color: #dc3545;">Xác nhận xóa</h3>
            </div>
            <div class="modal-body">
                <p id="deleteLessonText">Bạn có chắc chắn muốn xóa bài học này không?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelDeleteLessonBtn">Hủy</button>
                <button class="btn btn-danger" id="confirmDeleteLessonBtn" style="background-color: #dc3545; color: white;">Xóa</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
    <script src="js/admin_lessons.js?v=<?php echo filemtime('js/admin_lessons.js'); ?>"></script>

</body>
</html>