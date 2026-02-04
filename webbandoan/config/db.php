<?php
$conn = new mysqli("127.0.0.1", "root", "", "webbandoan", 3309);

if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
