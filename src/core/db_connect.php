<?php
$host = "localhost";
$username = "root";
$passwd = "";
$dbname = "edutech_db";
$charset = "utf8mb4";

$conn = new mysqli($host, $username, $passwd, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>