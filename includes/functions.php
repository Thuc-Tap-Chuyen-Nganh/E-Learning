<?php
/**
 * Hàm lấy đường dẫn ảnh bìa khóa học
 */
function get_course_image($thumbnail, $category) {
    if (!empty($thumbnail)) {
        // Kiểm tra file vật lý
        $file_path = BASE_PATH . $thumbnail;
        if (file_exists($file_path)) {
            return BASE_URL . $thumbnail;
        }
    }

    // Ảnh mặc định
    return BASE_URL . 'assets/images/default_course_img.PNG';
}

/**
 * Hàm định dạng tiền tệ
 */
function format_currency($amount) {
    if ($amount == 0) return '<span style="color: #16a34a;">Miễn phí</span>';
    return number_format($amount, 0, ',', '.') . 'đ';
}

/**
 * Hàm định dạng thời gian
 */
function format_time($minutes) {
    if ($minutes < 60) return $minutes . "p";
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return "{$h}h {$m}m";
}
?>