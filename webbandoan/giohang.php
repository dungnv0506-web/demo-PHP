<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$user = $_SESSION['user'];
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$cartCount = 0;

// Tính tổng số lượng hiển thị (Đồng bộ với thucdon.php)
foreach ($cart as $item) {
    $cartCount += (isset($item['qty']) ? (int)$item['qty'] : 0);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giỏ hàng</title>
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

<nav class="navbar">
    <a href="trangchu.php">Trang chủ</a>
    <a href="thucdon.php">Thực đơn</a>
    <a class="active">Giỏ hàng (<?= $cartCount ?>)</a>
    <a href="donhang.php">📦 Đơn hàng của tôi</a>
</nav>

<section class="hero small-hero">
    <h1>🛒 Giỏ hàng</h1>
</section>

<main class="main">

<?php if (empty($cart)): ?>
    <div class="empty-cart">
        <h2>Giỏ hàng trống 😢</h2>
        <a href="thucdon.php" style="display:block; margin-top:20px; color:#ff4757;">Quay lại thực đơn</a>
    </div>
<?php else: ?>

<?php foreach ($cart as $id => $item):
    $subtotal = $item['price'] * $item['qty'];
    $total += $subtotal;
?>
<div class="cart-item">
    <img src="img/<?= htmlspecialchars($item['image']) ?>" onerror="this.src='https://via.placeholder.com/150?text=No+Image'">

    <div class="cart-info">
        <h3><?= htmlspecialchars($item['name']) ?></h3>

        <div class="qty-box">
            <a href="capnhatsl.php?id=<?= $id ?>&type=minus" class="qty-btn">−</a>
            <span class="qty-number"><?= $item['qty'] ?></span>
            <a href="capnhatsl.php?id=<?= $id ?>&type=plus" class="qty-btn">+</a>
        </div>

        <p class="price"><?= number_format($subtotal, 0, ',', '.') ?>đ</p>
        <a href="xoamon.php?id=<?= $id ?>" class="remove" onclick="return confirm('Xóa món này?')">❌ Xóa</a>
    </div>
</div>
<?php endforeach; ?>

<h2 style="text-align:right; margin-top: 20px;">
    Tổng tiền: <span style="color:#ff4d4f"><?= number_format($total, 0, ',', '.') ?>đ</span>
</h2>

<a href="dat_hang.php" class="checkout-btn">✅ Đặt hàng</a>

<?php endif; ?>

</main>
</body>
</html>