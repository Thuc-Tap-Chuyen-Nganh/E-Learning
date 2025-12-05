<?php
session_start();
require_once '../config/config.php'; 

// Bảo vệ
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy ID bài học
if (!isset($_GET['lesson_id'])) {
    header("Location: admin_courses.php");
    exit();
}
$lesson_id = intval($_GET['lesson_id']);

// Lấy thông tin bài học để hiển thị tiêu đề
$stmt_lesson = $conn->prepare("SELECT title, chapter_id FROM lessons WHERE lesson_id = ?");
$stmt_lesson->bind_param("i", $lesson_id);
$stmt_lesson->execute();
$lesson = $stmt_lesson->get_result()->fetch_assoc();

if (!$lesson) die("Bài học không tồn tại.");

// Lấy danh sách câu hỏi
$stmt_questions = $conn->prepare("SELECT * FROM questions WHERE lesson_id = ? ORDER BY question_id ASC");
$stmt_questions->bind_param("i", $lesson_id);
$stmt_questions->execute();
$questions = $stmt_questions->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý câu hỏi - <?php echo htmlspecialchars($lesson['title']); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/admin_styles.css?v=<?= filemtime('css/admin_styles.css') ?>">
    
    <style>
        /* Quiz Specific Styles */
        .quiz-layout {
            display: grid;
            grid-template-columns: 2fr 1fr; /* Cột trái rộng gấp đôi cột phải */
            gap: 30px;
        }

        /* Question Card */
        .question-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
            position: relative;
        }
        .question-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-color: var(--primary-blue);
        }

        .q-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .q-number {
            font-weight: 700;
            color: var(--primary-blue);
            margin-right: 8px;
        }
        .q-text {
            font-weight: 600;
            color: var(--dark-text);
            font-size: 16px;
            flex-grow: 1;
        }
        .q-actions {
            display: flex;
            gap: 8px;
            margin-left: 15px;
        }

        /* Options Grid */
        .q-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .q-opt {
            padding: 10px 15px;
            background: var(--background-grey);
            border-radius: 8px;
            border: 1px solid transparent;
            font-size: 14px;
            color: var(--light-text);
            display: flex;
            align-items: center;
        }
        .q-opt span {
            font-weight: 700;
            margin-right: 8px;
            color: var(--grey-text);
        }
        /* Highlight Correct Option */
        .q-opt.correct {
            background: #ecfdf5; /* Xanh lá nhạt */
            border-color: #a7f3d0;
            color: #065f46;
        }
        .q-opt.correct span {
            color: #059669;
        }
        .q-opt.correct::after {
            content: '\f00c'; /* FontAwesome Check */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-left: auto;
            color: #059669;
        }

        /* Right Sidebar (Add Form) */
        .sticky-sidebar {
            position: sticky;
            top: 20px;
        }
        .add-box {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .add-box h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--primary-blue);
            display: flex; align-items: center; gap: 8px;
        }

        /* Import Box */
        .import-box {
            background: #eef2ff;
            border: 1px dashed #6366f1;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .import-box h4 {
            color: #4338ca;
            font-size: 15px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .import-box p {
            font-size: 13px; color: #666; margin-bottom: 15px;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .quiz-layout { grid-template-columns: 1fr; }
            .sticky-sidebar { position: static; }
        }
    </style>
</head>
<body>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <a href="admin_chapter_details.php?chapter_id=<?php echo $lesson['chapter_id']; ?>" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại bài học
                </a>
                <h1>Quản lý câu hỏi</h1>
                <p>Bài học: <?php echo htmlspecialchars($lesson['title']); ?></p>
            </div>
        </header>

        <main class="main-content">
            
            <div class="quiz-layout">
                
                <div class="questions-list">
                    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 20px; font-weight: 700;">Danh sách câu hỏi (<?php echo $questions->num_rows; ?>)</h2>
                    </div>
                    
                    <?php if ($questions->num_rows > 0): ?>
                        <?php $i = 1; while ($q = $questions->fetch_assoc()): ?>
                            <div class="question-card">
                                <div class="q-header">
                                    <div style="display: flex; align-items: flex-start;">
                                        <span class="q-number">Câu <?php echo $i++; ?>:</span>
                                        <span class="q-text"><?php echo htmlspecialchars($q['question_text']); ?></span>
                                    </div>
                                    
                                    <div class="q-actions">
                                        <button type="button" class="btn-icon btn-edit" title="Sửa"
                                                data-id="<?php echo $q['question_id']; ?>"
                                                data-text="<?php echo htmlspecialchars($q['question_text']); ?>"
                                                data-opta="<?php echo htmlspecialchars($q['option_a']); ?>"
                                                data-optb="<?php echo htmlspecialchars($q['option_b']); ?>"
                                                data-optc="<?php echo htmlspecialchars($q['option_c']); ?>"
                                                data-optd="<?php echo htmlspecialchars($q['option_d']); ?>"
                                                data-correct="<?php echo $q['correct_option']; ?>">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>

                                        <form action="<?= BASE_URL ?>logic/admin/quiz_delete.php" method="POST" onsubmit="return confirm('Xóa câu hỏi này?');" style="display:inline;">
                                            <input type="hidden" name="question_id" value="<?php echo $q['question_id']; ?>">
                                            <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                                            <button type="submit" class="btn-icon btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>

                                <div class="q-options">
                                    <div class="q-opt <?php echo $q['correct_option'] == 'A' ? 'correct' : ''; ?>">
                                        <span>A.</span> <?php echo htmlspecialchars($q['option_a']); ?>
                                    </div>
                                    <div class="q-opt <?php echo $q['correct_option'] == 'B' ? 'correct' : ''; ?>">
                                        <span>B.</span> <?php echo htmlspecialchars($q['option_b']); ?>
                                    </div>
                                    <div class="q-opt <?php echo $q['correct_option'] == 'C' ? 'correct' : ''; ?>">
                                        <span>C.</span> <?php echo htmlspecialchars($q['option_c']); ?>
                                    </div>
                                    <div class="q-opt <?php echo $q['correct_option'] == 'D' ? 'correct' : ''; ?>">
                                        <span>D.</span> <?php echo htmlspecialchars($q['option_d']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-clipboard-question" style="font-size: 40px; color: #ccc; margin-bottom: 10px;"></i>
                            <p class="empty-text">Chưa có câu hỏi nào trong bài kiểm tra này.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="sidebar-wrapper">
                    <div class="sticky-sidebar">
                        
                        <div class="import-box">
                            <h4><i class="fa-solid fa-file-csv"></i> Nhập nhanh từ Excel/CSV</h4>
                            <p>Tải lên file .csv (UTF-8) để nhập nhiều câu hỏi cùng lúc.</p>
                            
                            <form action="<?= BASE_URL ?>/logic/admin/quiz_import.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                                <input type="file" name="quiz_file" accept=".csv" required style="width: 100%; margin-bottom: 10px; font-size: 13px;">
                                <button type="submit" class="btn btn-secondary" style="width: 100%; background-color: #4f46e5; color: white; border: none;">
                                    Upload & Nhập
                                </button>
                            </form>
                        </div>

                        <div class="add-box">
                            <h3><i class="fa-solid fa-plus-circle"></i> Thêm câu hỏi mới</h3>
                            
                            <form action="<?= BASE_URL ?>logic/admin/quiz_add.php" method="POST">
                                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                                
                                <div class="form-group">
                                    <label>Nội dung câu hỏi</label>
                                    <textarea name="question_text" rows="3" required placeholder="Nhập câu hỏi..."></textarea>
                                </div>

                                <div class="form-group-grid">
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <input type="text" name="option_a" placeholder="Đáp án A" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <input type="text" name="option_b" placeholder="Đáp án B" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <input type="text" name="option_c" placeholder="Đáp án C" required>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <input type="text" name="option_d" placeholder="Đáp án D" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Đáp án đúng</label>
                                    <div class="select-wrapper">
                                        <select name="correct_option">
                                            <option value="A">Đáp án A</option>
                                            <option value="B">Đáp án B</option>
                                            <option value="C">Đáp án C</option>
                                            <option value="D">Đáp án D</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;">Lưu câu hỏi</button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="editQuestionModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Cập nhật câu hỏi</h3>
            </div>
            
            <form action="<?= BASE_URL ?>logic/admin/quiz_edit.php" method="POST">
                <input type="hidden" name="question_id" id="edit_q_id">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nội dung câu hỏi</label>
                        <textarea name="question_text" id="edit_q_text" rows="3" required></textarea>
                    </div>

                    <div class="form-row-2-col">
                        <div class="form-group">
                            <label>Đáp án A</label>
                            <input type="text" name="option_a" id="edit_opt_a" required>
                        </div>
                        <div class="form-group">
                            <label>Đáp án B</label>
                            <input type="text" name="option_b" id="edit_opt_b" required>
                        </div>
                    </div>
                    <div class="form-row-2-col">
                        <div class="form-group">
                            <label>Đáp án C</label>
                            <input type="text" name="option_c" id="edit_opt_c" required>
                        </div>
                        <div class="form-group">
                            <label>Đáp án D</label>
                            <input type="text" name="option_d" id="edit_opt_d" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Đáp án đúng</label>
                        <div class="select-wrapper">
                            <select name="correct_option" id="edit_correct">
                                <option value="A">Đáp án A</option>
                                <option value="B">Đáp án B</option>
                                <option value="C">Đáp án C</option>
                                <option value="D">Đáp án D</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeEditModalBtn">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editQuestionModal');
            const closeBtn = document.getElementById('closeEditModalBtn');
            const editBtns = document.querySelectorAll('.btn-edit'); 

            // Mở Modal và điền dữ liệu cũ
            editBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Lấy dữ liệu từ data attributes
                    const id = this.getAttribute('data-id');
                    const text = this.getAttribute('data-text');
                    const optA = this.getAttribute('data-opta');
                    const optB = this.getAttribute('data-optb');
                    const optC = this.getAttribute('data-optc');
                    const optD = this.getAttribute('data-optd');
                    const correct = this.getAttribute('data-correct');

                    // Điền vào form
                    document.getElementById('edit_q_id').value = id;
                    document.getElementById('edit_q_text').value = text;
                    document.getElementById('edit_opt_a').value = optA;
                    document.getElementById('edit_opt_b').value = optB;
                    document.getElementById('edit_opt_c').value = optC;
                    document.getElementById('edit_opt_d').value = optD;
                    document.getElementById('edit_correct').value = correct;

                    // Hiển thị modal
                    editModal.classList.add('show');
                });
            });

            // Đóng Modal
            if(closeBtn) {
                closeBtn.addEventListener('click', () => {
                    editModal.classList.remove('show');
                });
            }
            window.addEventListener('click', (e) => {
                if (e.target == editModal) editModal.classList.remove('show');
            });
        });
    </script>

</body>
</html>