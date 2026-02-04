<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Food Store</title>
<link rel="stylesheet" href="trangchu.css">
</head>
<body>

<!-- HEADER -->
<header class="topbar">
    <div class="logo">🍔 FoodStore</div>
    <div class="user-box">
        Xin chào, <b><?= htmlspecialchars($user['username']) ?></b>
        <a href="dangxuat.php">Đăng xuất</a>
    </div>
</header>

<!-- MENU -->
<nav class="navbar">
    <a class="active">Trang chủ</a>
    <a href="thucdon.php">Thực đơn</a>
    <a href="giohang.php">Giỏ hàng (<?= count($_SESSION['cart']) ?>)</a>
    <a href="donhang.php">📦 Đơn hàng của tôi</a>

    <?php if ($user['role'] === 'admin'): ?>
        <a href="admin/dashboard.php">Admin</a>
    <?php endif; ?>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-text">
        <h1>Ăn ngon mỗi ngày 🍕</h1>
        <p>Đặt món nhanh – Giao hàng tận nơi</p>
        <button>Khám phá thực đơn</button>
    </div>
</section>

<!-- MAIN -->
<main class="main">
    <h2>🔥 Món bán chạy</h2>

<div class="food-grid">

<!-- MÓN 1 -->
<div class="food-card">
    <img src="images/pizzahaisan.jpg">
    <h3>Pizza Hải Sản</h3>
    <p class="price">80.000đ</p>
    <form method="post" action="themmonan.php">
        <input type="hidden" name="id" value="1">
        <input type="hidden" name="name" value="Pizza Hải Sản">
        <input type="hidden" name="price" value="80000">
        <input type="hidden" name="image" value="pizzahaisan.jpg">
        <button type="submit">Thêm vào giỏ</button>
    </form>
</div>

<!-- MÓN 2 -->
<div class="food-card">
    <img src="images/burger.jpg">
    <h3>Burger Bò</h3>
    <p class="price">50.000đ</p>
    <form method="post" action="themmonan.php">
        <input type="hidden" name="id" value="2">
        <input type="hidden" name="name" value="Burger Bò">
        <input type="hidden" name="price" value="50000">
        <input type="hidden" name="image" value="burger.jpg">
        <button type="submit">Thêm vào giỏ</button>
    </form>
</div>

<!-- MÓN 3 -->
<div class="food-card">
    <img src="images/khoaitaychien.jpg">
    <h3>Khoai Tây Chiên</h3>
    <p class="price">30.000đ</p>
    <form method="post" action="themmonan.php">
        <input type="hidden" name="id" value="3">
        <input type="hidden" name="name" value="Khoai Tây Chiên">
        <input type="hidden" name="price" value="30000">
        <input type="hidden" name="image" value="fries.jpg">
        <button type="submit">Thêm vào giỏ</button>
    </form>
</div>

<!-- MÓN 4 -->
<div class="food-card">
    <img src="images/comga.jpg">
    <h3>Cơm Gà Xối Mỡ</h3>
    <p class="price">45.000đ</p>
    <form method="post" action="themmonan.php">
        <input type="hidden" name="id" value="4">
        <input type="hidden" name="name" value="Cơm Gà Xối Mỡ">
        <input type="hidden" name="price" value="45000">
        <input type="hidden" name="image" value="comga.jpg">
        <button type="submit">Thêm vào giỏ</button>
    </form>
</div>

</div>


</main>

</body>
</html>
