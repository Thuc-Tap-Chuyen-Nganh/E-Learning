<?php
$in_student_folder = (basename(dirname($_SERVER['PHP_SELF'])) == 'student');
$path = $in_student_folder ? '../' : '';

require_once $path . 'config/config.php'; 
?>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <a href="#" class="logo-text">
                    <i class="fa-solid fa-book-open"></i> EduTech
                </a>
                <p>Nền tảng học trực tuyến hàng đầu Việt Nam, giúp bạn tiếp cận kiến thức công nghệ dễ dàng.</p>
            </div>
            <div class="footer-links">
                <h4>Liên kết nhanh</h4>
                <ul>
                    <li><a href="#">Về chúng tôi</a></li>
                    <li><a href="<?= $path ?>courses.php">Tất cả khóa học</a></li>
                    <li><a href="#">Trở thành giảng viên</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Danh mục phổ biến</h4>
                <ul>
                    <li><a href="#">Lập trình Web</a></li>
                    <li><a href="#">Data Science</a></li>
                    <li><a href="#">Cybersecurity</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Liên hệ</h4>
                <ul>
                    <li><i class="fa-solid fa-location-dot"></i> TP. Hồ Chí Minh, Việt Nam</li>
                    <li><i class="fa-solid fa-phone"></i> +84 90 123 4567</li>
                    <li><i class="fa-solid fa-envelope"></i> support@edutech.vn</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 EduTech. All rights reserved.</p>
        </div>
    </div>
</footer>

<link rel="stylesheet" href="<?php echo $path; ?>assets/css/ai_assistant.css?v=<?php echo time(); ?>">

<?php include 'ai_widget.php'; ?>
<script>
    const BASE_URL = "<?= BASE_URL ?>"; // Biến toàn cục cho JS dùng
</script>
<script src="<?= $path; ?>assets/js/ai_assistant.js?v=<?php echo time(); ?>"></script>

<script src="<?= $path ?>assets/js/main.js?v=<?= filemtime(($in_student_folder ? '../' : '') . 'assets/js/main.js') ?>"></script>