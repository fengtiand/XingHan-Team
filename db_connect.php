<?php
ini_set('default_charset', 'UTF-8');
$conn = new mysqli('localhost', 'wwwww', 'wwwww', 'wwwww');
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>