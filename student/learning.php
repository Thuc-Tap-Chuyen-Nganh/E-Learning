<?php
// student/learning.php
session_start();
require '../src/core/db_connect.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. LẤY COURSE_ID
if (!isset($_GET['course_id'])) {
    header("Location: my_courses.php");
    exit();
}
$course_id = intval($_GET['course_id']);

// 3. KIỂM TRA QUYỀN TRUY CẬP (Đã đăng ký chưa?)
$check_enroll = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE user_id = ? AND course_id = ?");
$check_enroll->bind_param("ii", $user_id, $course_id);
$check_enroll->execute();
if ($check_enroll->get_result()->num_rows === 0) {
    die("Bạn chưa đăng ký khóa học này. <a href='../course_detail.php?id=$course_id'>Đăng ký ngay</a>");
}

// 4. LẤY DANH SÁCH BÀI HỌC (Curriculum)
// Lấy thông tin khóa học
$course_res = $conn->query("SELECT title FROM courses WHERE course_id = $course_id");
$course_info = $course_res->fetch_assoc();

// Lấy Chương và Bài học
$chapters_query = $conn->query("SELECT * FROM chapters WHERE course_id = $course_id ORDER BY sort_order ASC");
$curriculum = [];
$first_lesson_id = null; // Để mặc định chọn bài đầu tiên nếu không có ID trên URL

while ($chap = $chapters_query->fetch_assoc()) {
    $chap_id = $chap['chapter_id'];
    $lessons_query = $conn->query("SELECT * FROM lessons WHERE chapter_id = $chap_id ORDER BY sort_order ASC");
    
    $lessons = [];
    while ($less = $lessons_query->fetch_assoc()) {
        $lessons[] = $less;
        if ($first_lesson_id === null) $first_lesson_id = $less['lesson_id'];
    }
    $chap['lessons'] = $lessons;
    $curriculum[] = $chap;
}

// 5. XÁC ĐỊNH BÀI HỌC HIỆN TẠI
$current_lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : $first_lesson_id;
$current_lesson = null;

// Tìm thông tin bài học hiện tại trong mảng curriculum (tránh query lại DB)
foreach ($curriculum as $chap) {
    foreach ($chap['lessons'] as $less) {
        if ($less['lesson_id'] == $current_lesson_id) {
            $current_lesson = $less;
            break 2;
        }
    }
}

// NẾU LÀ QUIZ -> LẤY CÂU HỎI
$quiz_questions = null; // Sửa [] thành null để dễ kiểm tra
$quiz_count = 0;        // Biến đếm số câu hỏi

if ($current_lesson && $current_lesson['lesson_type'] == 'quiz') {
    $stmt_q = $conn->prepare("SELECT question_id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE lesson_id = ? ORDER BY question_id ASC");
    $stmt_q->bind_param("i", $current_lesson_id);
    $stmt_q->execute();
    $quiz_questions = $stmt_q->get_result();
    
    // Lấy số lượng câu hỏi
    if ($quiz_questions) {
        $quiz_count = $quiz_questions->num_rows;
    }
}

// Hàm helper chuyển link YouTube sang Embed
function getYoutubeEmbedUrl($url) {
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($longUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtube_id = $matches[1];
    }
    
    if (isset($youtube_id)) {
        return 'https://www.youtube.com/embed/' . $youtube_id;
    }
    return $url; // Trả về nguyên gốc nếu không phải youtube (hoặc link mp4)
}

// 6. LẤY DANH SÁCH BÀI ĐÃ HỌC (Để hiển thị tích xanh)
$completed_lessons = [];
$prog_query = $conn->query("SELECT lesson_id FROM lesson_progress WHERE user_id = $user_id AND course_id = $course_id");
while ($row = $prog_query->fetch_assoc()) {
    $completed_lessons[] = $row['lesson_id'];
}

// Lấy % tiến độ hiện tại
$enroll_query = $conn->query("SELECT progress FROM enrollments WHERE user_id = $user_id AND course_id = $course_id");
$current_progress = $enroll_query->fetch_assoc()['progress'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Học tập: <?php echo htmlspecialchars($course_info['title']); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../public/css/learning.css?v=<?php echo time(); ?>">
</head>
<body>

    <header class="learning-header">
        <div style="display: flex; align-items: center;">
            <a href="../course_detail.php?id=<?php echo $course_id; ?>" class="back-link">
                <i class="fa-solid fa-chevron-left"></i> Quay lại
            </a>
            <span class="course-title"><?php echo htmlspecialchars($course_info['title']); ?></span>
        </div>
        <div class="user-progress">
            <div class="progress-track">
                <div id="progressBar" class="progress-fill" style="width: <?php echo $current_progress; ?>%;"></div>
            </div>
            <span class="progress-text">
                <span id="progressText"><?php echo $current_progress; ?></span>%
            </span>
        </div>
    </header>

    <div class="learning-container">
        
        <div class="learning-content">
            <?php if ($current_lesson): ?>
                
                <?php if ($current_lesson['lesson_type'] == 'video'): ?>
                    <div class="video-wrapper">
                        <?php $embedUrl = getYoutubeEmbedUrl($current_lesson['video_url']); ?>
                        <iframe src="<?php echo $embedUrl; ?>" title="Video player" allowfullscreen></iframe>
                    </div>
                    
                    <div class="lesson-info-bar">
                        <h2><?php echo htmlspecialchars($current_lesson['title']); ?></h2>
                        <p class="lesson-desc">Thời lượng: <?php echo $current_lesson['duration']; ?> phút</p>
                    </div>

                    <div class="action-bar" style="padding: 20px; text-align: right; background: #fff; border-top: 1px solid #eee;">
                        <?php if (in_array($current_lesson['lesson_id'], $completed_lessons)): ?>
                            <button class="btn btn-success" disabled style="background: #22c55e; color: white; border: none; padding: 10px 20px; border-radius: 5px; opacity: 0.8; cursor: default;">
                                <i class="fa-solid fa-check"></i> Đã hoàn thành
                            </button>
                        <?php else: ?>
                            <button id="btnMarkComplete" onclick="markCompleted()" class="btn btn-primary" style="background: #2e89ff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                                Hoàn thành bài học <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                <?php elseif ($current_lesson['lesson_type'] == 'text'): ?>
                    <div class="text-content-wrapper">
                        <h1><?php echo htmlspecialchars($current_lesson['title']); ?></h1>
                        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                        <div class="ck-content">
                            <?php echo $current_lesson['content']; // HTML từ CKEditor ?>
                        </div>
                    </div>

                    <div class="action-bar" style="padding: 20px; text-align: right; background: #fff; border-top: 1px solid #eee;">
                        <?php if (in_array($current_lesson['lesson_id'], $completed_lessons)): ?>
                            <button class="btn btn-success" disabled style="background: #22c55e; color: white; border: none; padding: 10px 20px; border-radius: 5px; opacity: 0.8; cursor: default;">
                                <i class="fa-solid fa-check"></i> Đã hoàn thành
                            </button>
                        <?php else: ?>
                            <button id="btnMarkComplete" onclick="markCompleted()" class="btn btn-primary" style="background: #2e89ff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                                Hoàn thành bài học <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                <?php elseif ($current_lesson['lesson_type'] == 'quiz'): ?>
                    <div class="text-content-wrapper quiz-wrapper" style="max-width: 800px; margin: 0 auto; padding: 40px;">
        
                    <div id="quiz-start-screen" style="text-align: center; padding: 40px;">
                        <i class="fa-solid fa-clipboard-question" style="font-size: 60px; color: #8e2de2; margin-bottom: 20px;"></i>
                        <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($current_lesson['title']); ?></h1>
                        <p style="color: #666; margin-bottom: 30px;">
                            Bài kiểm tra gồm <strong><?php echo $quiz_count; ?></strong> câu hỏi.<br>
                            Bạn cần đạt tối thiểu <strong>70%</strong> để vượt qua bài học này.
                        </p>
                        
                        <?php if (in_array($current_lesson['lesson_id'], $completed_lessons)): ?>
                            <div style="margin-bottom: 20px; color: #16a34a; font-weight: bold;">
                                <i class="fa-solid fa-check-circle"></i> Bạn đã hoàn thành bài tập này.
                            </div>
                            <button class="btn btn-primary" onclick="startQuiz()">Làm lại bài thi</button>
                        <?php else: ?>
                            <button class="btn btn-primary" onclick="startQuiz()">Bắt đầu làm bài</button>
                        <?php endif; ?>
                    </div>

                    <div id="quiz-container" style="display: none;">
                        <form id="quizForm">
                            <input type="hidden" name="lesson_id" value="<?php echo $current_lesson_id; ?>">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                            <?php 
                            if ($quiz_questions && $quiz_count > 0): 
                                $q_index = 1;
                                // Reset con trỏ dữ liệu về đầu (phòng trường hợp đã dùng ở đâu đó)
                                $quiz_questions->data_seek(0);
                                while ($q = $quiz_questions->fetch_assoc()):
                            ?>
                                <div class="quiz-item" id="q_item_<?php echo $q['question_id']; ?>" style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                                    <h4 style="margin-bottom: 15px;">
                                        <span style="color: #8e2de2;">Câu <?php echo $q_index++; ?>:</span> 
                                        <?php echo htmlspecialchars($q['question_text']); ?>
                                    </h4>
                                    
                                    <div class="quiz-options" style="display: grid; gap: 10px;">
                                        <label class="quiz-option" style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                                            <input type="radio" name="answers[<?php echo $q['question_id']; ?>]" value="A">
                                            <span>A. <?php echo htmlspecialchars($q['option_a']); ?></span>
                                        </label>
                                        
                                        <label class="quiz-option" style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                                            <input type="radio" name="answers[<?php echo $q['question_id']; ?>]" value="B">
                                            <span>B. <?php echo htmlspecialchars($q['option_b']); ?></span>
                                        </label>

                                        <label class="quiz-option" style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                                            <input type="radio" name="answers[<?php echo $q['question_id']; ?>]" value="C">
                                            <span>C. <?php echo htmlspecialchars($q['option_c']); ?></span>
                                        </label>

                                        <label class="quiz-option" style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                                            <input type="radio" name="answers[<?php echo $q['question_id']; ?>]" value="D">
                                            <span>D. <?php echo htmlspecialchars($q['option_d']); ?></span>
                                        </label>
                                    </div>
                                    
                                    <div class="feedback-msg" style="margin-top: 10px; font-weight: 600;"></div>
                                </div>
                            <?php endwhile; endif; ?>

                            <div style="text-align: center; margin-top: 30px;">
                                <button type="button" id="btnSubmitQuiz" onclick="submitQuiz()" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">
                                    Nộp bài
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="quiz-result" style="display: none; text-align: center; padding: 30px; background: #f8f9fa; border-radius: 12px; margin-bottom: 30px;">
                        <h2 id="res-title">Kết quả</h2>
                        <div style="font-size: 40px; font-weight: 800; color: #8e2de2; margin: 10px 0;">
                            <span id="res-score">0</span>%
                        </div>
                        <p id="res-msg">Bạn đã làm rất tốt!</p>
                        <div style="margin-top: 20px;">
                            <button class="btn btn-secondary" onclick="reviewQuiz()">Xem lại bài làm</button>
                            </div>
                    </div>

                </div>

            <?php endif; ?>

            <?php else: ?>
                <div style="padding: 50px; text-align: center; color: white;">
                    <h2>Khóa học này chưa có nội dung.</h2>
                </div>
            <?php endif; ?>
        </div>

        <aside class="learning-sidebar">
            <div class="sidebar-header">
                Nội dung khóa học
            </div>
            
            <div class="curriculum-list">
                <?php foreach ($curriculum as $idx => $chap): ?>
                    <div class="chapter-item">
                        <div class="chapter-header" onclick="toggleChapter(this)">
                            <span><?php echo $idx + 1; ?>. <?php echo htmlspecialchars($chap['title']); ?></span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <div class="lesson-list">
                            <?php foreach ($chap['lessons'] as $less): ?>
                                <?php 
                                    $isActive = ($less['lesson_id'] == $current_lesson_id) ? 'active' : '';
                                    // Kiểm tra đã học chưa
                                    $isDone = in_array($less['lesson_id'], $completed_lessons);
                                    // Icon
                                    $icon = 'fa-circle-play'; // Default video
                                    if ($less['lesson_type'] == 'text') $icon = 'fa-file-lines';
                                    if ($less['lesson_type'] == 'quiz') $icon = 'fa-circle-question';
                                ?>
                                <a href="?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $less['lesson_id']; ?>" 
                                   class="lesson-item <?php echo $isActive; ?> 
                                    <?php echo $less['lesson_type'] == 'quiz' ? 'quiz-type' : ''; ?>">
                                    
                                    <?php if ($isDone): ?>
                                        <i class="fa-solid fa-circle-check lesson-icon" style="color: #22c55e;"></i>
                                    <?php else: ?>
                                        <i class="fa-regular <?php echo $icon; ?> lesson-icon"></i>
                                    <?php endif; ?>
                                    <span class="lesson-title"><?php echo htmlspecialchars($less['title']); ?></span>
                                    <span class="lesson-duration"><?php echo $less['duration']; ?>p</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

    </div>

    <script>
        // Script nhỏ để ẩn hiện chương (Accordion)
        function toggleChapter(header) {
            const list = header.nextElementSibling;
            const icon = header.querySelector('.fa-chevron-down');
            
            if (list.style.display === "none") {
                list.style.display = "block";
                icon.style.transform = "rotate(0deg)";
            } else {
                list.style.display = "none";
                icon.style.transform = "rotate(-90deg)";
            }
        }

        // === CÁC HÀM XỬ LÝ QUIZ ===

        function startQuiz() {
            document.getElementById('quiz-start-screen').style.display = 'none';
            document.getElementById('quiz-container').style.display = 'block';
            document.getElementById('quiz-result').style.display = 'none';
            
            // Reset form nếu làm lại
            document.getElementById('quizForm').reset();
            // Reset giao diện các câu hỏi (bỏ màu xanh/đỏ cũ)
            document.querySelectorAll('.quiz-option').forEach(el => {
                el.style.backgroundColor = '';
                el.style.borderColor = '#ddd';
            });
            document.querySelectorAll('.feedback-msg').forEach(el => el.innerHTML = '');
        }

        function submitQuiz() {
            if(!confirm('Bạn có chắc chắn muốn nộp bài không?')) return;

            const btn = document.getElementById('btnSubmitQuiz');
            btn.disabled = true;
            btn.innerText = 'Đang chấm điểm...';

            const formData = new FormData(document.getElementById('quizForm'));

            fetch('../src/student_handlers/submit_quiz.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showResult(data);
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Nộp bài';
            });
        }

        function showResult(data) {
            // 1. Ẩn form, hiện bảng điểm
            document.getElementById('quiz-container').style.display = 'none';
            const resBox = document.getElementById('quiz-result');
            resBox.style.display = 'block';

            // 2. Điền thông tin điểm
            document.getElementById('res-score').innerText = data.score_percent;
            
            const msg = document.getElementById('res-msg');
            const title = document.getElementById('res-title');

            if (data.is_passed) {
                title.innerText = 'Chúc mừng! Bạn đã vượt qua.';
                title.style.color = '#16a34a';
                msg.innerHTML = `Bạn trả lời đúng <strong>${data.correct_count}/${data.total_questions}</strong> câu. <br> Bài học đã được đánh dấu hoàn thành.`;
                
                // Reload sidebar để hiện tích xanh (nếu muốn)
                // setTimeout(() => location.reload(), 3000); 
            } else {
                title.innerText = 'Chưa đạt yêu cầu';
                title.style.color = '#dc2626';
                msg.innerHTML = `Bạn trả lời đúng <strong>${data.correct_count}/${data.total_questions}</strong> câu. <br> Hãy ôn lại kiến thức và thử lại nhé!`;
            }

            // 3. Xử lý hiển thị chữa bài (Review Mode)
            // Chúng ta sẽ tô màu các đáp án trong form, nhưng form đang ẩn
            // Khi user bấm "Xem lại bài làm", ta sẽ hiện form lên ở chế độ chỉ xem (readonly)
            
            const correction = data.correction;
            for (const [qid, info] of Object.entries(correction)) {
                const container = document.getElementById('q_item_' + qid);
                const feedback = container.querySelector('.feedback-msg');
                
                // Reset style cũ
                container.querySelectorAll('.quiz-option').forEach(opt => {
                    opt.style.opacity = '0.5'; // Làm mờ các đáp án không chọn
                });

                // Tô màu đáp án ĐÚNG (Màu xanh)
                const correctInput = container.querySelector(`input[value="${info.correct_option}"]`);
                if (correctInput) {
                    correctInput.parentElement.style.backgroundColor = '#dcfce7';
                    correctInput.parentElement.style.borderColor = '#16a34a';
                    correctInput.parentElement.style.opacity = '1';
                    correctInput.parentElement.style.color = '#166534';
                }

                // Tô màu đáp án SAI (Màu đỏ) - Nếu user chọn sai
                if (!info.is_correct && info.user_choice) {
                    const wrongInput = container.querySelector(`input[value="${info.user_choice}"]`);
                    if (wrongInput) {
                        wrongInput.parentElement.style.backgroundColor = '#fee2e2';
                        wrongInput.parentElement.style.borderColor = '#dc2626';
                        wrongInput.parentElement.style.opacity = '1';
                        wrongInput.parentElement.style.color = '#991b1b';
                    }
                    feedback.innerHTML = `<span style="color: #dc2626;">Sai rồi! Đáp án đúng là ${info.correct_option}.</span>`;
                } else if (info.is_correct) {
                    feedback.innerHTML = `<span style="color: #16a34a;">Chính xác!</span>`;
                } else {
                    feedback.innerHTML = `<span style="color: #666;">Bạn chưa chọn đáp án.</span>`;
                }
            }
        }

        function reviewQuiz() {
            document.getElementById('quiz-result').style.display = 'none';
            document.getElementById('quiz-container').style.display = 'block';
            
            // Ẩn nút nộp bài
            document.getElementById('btnSubmitQuiz').style.display = 'none';
            
            // Cuộn lên đầu
            document.querySelector('.learning-content').scrollTop = 0;
        }

        // HÀM XỬ LÝ HOÀN THÀNH BÀI HỌC
        function markCompleted() {
            const btn = document.getElementById('btnMarkComplete');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('lesson_id', <?php echo $current_lesson_id; ?>);
            formData.append('course_id', <?php echo $course_id; ?>);

            fetch('../src/student_handlers/mark_completed.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // 1. Cập nhật nút bấm
                    btn.style.background = '#22c55e';
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã hoàn thành';
                    
                    // 2. Cập nhật thanh tiến độ trên Header
                    document.getElementById('progressBar').style.width = data.new_progress + '%';
                    document.getElementById('progressText').innerText = data.new_progress;

                    // 3. (Optional) Tự động chuyển bài sau 2 giây
                    // setTimeout(() => { window.location.reload(); }, 1500); 
                    // Hoặc chỉ reload để cập nhật tích xanh ở sidebar
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Thử lại';
                }
            })
            .catch(err => console.error(err));
        }
    </script>

</body>
</html>