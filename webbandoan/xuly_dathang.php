<?php
session_start();
if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header("Location: trangchu.php");
    exit;
}

// 👉 Ở mức cơ bản: chỉ giả lập đặt hàng
// Sau này bạn có thể lưu vào MySQL

unset($_SESSION['cart']); // Xóa giỏ hàng sau khi đặt

header("Location: dathang_thanhcong.php");
exit;
