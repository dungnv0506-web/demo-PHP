<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

require 'config/db.php';

$user = $_SESSION['user'];

// cart count (an toàn)
$cart = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach ($cart as $item) {
    $cartCount += $item['qty'];
}

$user_id = $user['id'];

$stmt = $conn->prepare(
    "SELECT id, total, status, created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đơn hàng của tôi</title>
<link rel="stylesheet" href="trangchu.css">
</head>
<body>

<!-- HEADER -->
<header class="topbar">
    <div class="logo">🍔 FoodStore</div>
    <div class="user-box">
        Xin chào <b><?= htmlspecialchars($user['username']) ?></b>
        <a href="giohang.php">🛒 Giỏ hàng (<?= $cartCount ?>)</a>
        <a href="dangxuat.php">Đăng xuất</a>
    </div>
</header>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="trangchu.php">Trang chủ</a>
    <a href="thucdon.php">Thực đơn</a>
    <a href="giohang.php">Giỏ hàng (<?= $cartCount ?>)</a>
    <a class="active">📦 Đơn hàng của tôi</a>
</nav>

<section class="hero small-hero">
    <h1>📦 Đơn hàng của tôi</h1>
</section>

<main class="main">

<?php if ($result->num_rows == 0): ?>
    <div class="empty-cart">
        <h2>Bạn chưa có đơn hàng nào 😢</h2>
    </div>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): 
    // tạo class trạng thái
    $statusClass = 'status-' . strtolower(
        str_replace([' ', 'ã', 'à'], ['-', 'a', 'a'], $row['status'])
    );
?>
<div class="order-box">
    <div>
        <p><b>Mã đơn:</b> #<?= $row['id'] ?></p>
        <p><b>Ngày đặt:</b> <?= $row['created_at'] ?></p>
        <p><b>Tổng tiền:</b> <?= number_format($row['total']) ?>đ</p>
        <p>
            <b>Trạng thái:</b>
            <span class="status <?= $statusClass ?>">
                <?= $row['status'] ?>
            </span>
        </p>
    </div>

    <a href="chitietdon.php?id=<?= $row['id'] ?>" class="detail-btn">
        Xem chi tiết
    </a>
</div>
<?php endwhile; ?>

</main>
</body>
</html>
