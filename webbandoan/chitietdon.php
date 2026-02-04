<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

require 'config/db.php';

$user = $_SESSION['user'];
$order_id = intval($_GET['id'] ?? 0);

// Lấy thông tin đơn hàng (đúng user)
$stmt = $conn->prepare(
    "SELECT * FROM orders WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $order_id, $user['id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("❌ Đơn hàng không tồn tại");
}

// Lấy chi tiết món
$stmt = $conn->prepare(
    "SELECT f.name, f.image, od.quantity, od.price
     FROM order_details od
     JOIN foods f ON od.food_id = f.id
     WHERE od.order_id = ?"
);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết đơn hàng</title>
<link rel="stylesheet" href="trangchu.css">
</head>
<body>

<!-- HEADER -->
<header class="topbar">
    <div class="logo">🍔 FoodStore</div>
    <div class="user-box">
        Xin chào <b><?= htmlspecialchars($user['username']) ?></b>
        <a href="donhang.php">📦 Đơn hàng</a>
        <a href="dangxuat.php">Đăng xuất</a>
    </div>
</header>

<!-- HERO -->
<section class="hero small-hero">
    <h1>📄 Chi tiết đơn hàng #<?= $order['id'] ?></h1>
</section>

<main class="main">

<!-- THÔNG TIN ĐƠN -->
<div class="order-box">
    <p><b>Ngày đặt:</b> <?= $order['created_at'] ?></p>
    <p><b>Trạng thái:</b>
        <span class="status"><?= $order['status'] ?></span>
    </p>
    <p><b>Tổng tiền:</b>
        <span style="color:#ff4d4f;font-weight:bold">
            <?= number_format($order['total']) ?>đ
        </span>
    </p>
</div>

<!-- DANH SÁCH MÓN -->
<div class="cart-list">

<?php while ($item = $items->fetch_assoc()): ?>
<div class="cart-item">
    <img src="images/<?= $item['image'] ?>">

    <div class="cart-info">
        <h3><?= $item['name'] ?></h3>
        <p>Số lượng: <b><?= $item['quantity'] ?></b></p>
        <p class="price">
            <?= number_format($item['price'] * $item['quantity']) ?>đ
        </p>
    </div>
</div>
<?php endwhile; ?>

</div>

<div class="cart-actions">
    <a href="donhang.php" class="btn-back">⬅ Quay lại</a>
</div>

</main>
</body>
</html>
