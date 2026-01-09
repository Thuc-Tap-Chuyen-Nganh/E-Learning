<?php
// config/config.php

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 1. Cấu hình Database
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'edutech_db');

// 2. Cấu hình Đường dẫn gốc (BASE URL)
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/E-Learning/');

// 3. Đường dẫn vật lý (Dùng để include file PHP)
define('BASE_PATH', dirname(__DIR__) . '/');

// 4. Khởi động Session luôn ở đây (để không phải gọi session_start() ở mọi nơi)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Kết nối Database (Tích hợp luôn vào đây)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
require_once __DIR__ . '/../includes/functions.php';

// 6. API Key Google Gemini
define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? '');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>