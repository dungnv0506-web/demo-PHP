<?php
session_start();
require 'config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

// Lấy thông tin người dùng
$user = $_SESSION['user'];

// Tính số lượng món trong giỏ hàng để hiển thị trên Header
$cart = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach ($cart as $item) {
    $cartCount += $item['qty'] ?? 0;
}

if (!isset($_GET['order_id'])) {
    header("Location: trangchu.php");
    exit;
}

$order_id = (int)$_GET['order_id'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán MoMo</title>
<link rel="stylesheet" href="trangchu.css">
</head>
<body>

<header class="topbar">
    <div class="logo">🍔 FoodStore</div>
    <div class="user-box">
        Xin chào <b><?= htmlspecialchars($user['username']) ?></b>
        <a href="giohang.php">🛒 Giỏ hàng (<?= $cartCount ?>)</a>
        <a href="dangxuat.php">Đăng xuất</a>
    </div>
</header>

<section class="hero small-hero">
    <h1>📱 Thanh toán MoMo (Demo)</h1>
</section>

<main class="main">

<div class="order-box" style="text-align:center; max-width: 500px; margin: 20px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <h2 style="color: #ae2070;">Quét mã QR để thanh toán</h2>

    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=MOMO_ORDER_<?= $order_id ?>" 
         alt="QR MoMo" style="margin: 20px 0; border: 10px solid #f5f5f5;">

    <p style="font-size: 18px;"><b>Mã đơn hàng:</b> <span style="color: #ff4d4f;">#<?= $order_id ?></span></p>
    <p style="color: #666; font-style: italic;">(Đây là trang mô phỏng thanh toán MoMo)</p>

    <form method="post" action="momo_success.php">
        <input type="hidden" name="order_id" value="<?= $order_id ?>">
        <button type="submit" class="checkout-btn" style="background: #ae2070; margin-top: 20px;">
            ✅ Đã thanh toán thành công
        </button>
    </form>
    
    <a href="donhang.php" style="display: block; margin-top: 15px; color: #666; text-decoration: none;">Thanh toán sau</a>
</div>

</main>

</body>
</html>