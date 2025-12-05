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

    // Ảnh Placeholder
    $cat = strtolower($category);
    if (strpos($cat, 'web') !== false) return 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=500&q=80';
    if (strpos($cat, 'data') !== false) return 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&q=80';
    if (strpos($cat, 'design') !== false) return 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=500&q=80';
    if (strpos($cat, 'mobile') !== false) return 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=500&q=80';
    if (strpos($cat, 'security') !== false) return 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=500&q=80';
    
    return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&q=80';
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