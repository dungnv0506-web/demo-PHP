<?php
session_start();
include "config/db.php"; // Phải có file kết nối này

if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$user = $_SESSION['user'];

// Giỏ hàng
$cart = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach($cart as $item) {
    $cartCount += $item['qty'];
}

/* 1. LẤY DANH SÁCH DANH MỤC TỪ CSDL */
$cat_res = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = [];
while($row = $cat_res->fetch_assoc()){
    $categories[$row['id']] = $row['name'];
}

/* 2. XỬ LÝ LỌC & TÌM KIẾM */
$categoryFilter = $_GET['category'] ?? '';
$keyword = $_GET['keyword'] ?? '';

// Xây dựng câu lệnh SQL lấy món từ bảng foods
$sql = "SELECT * FROM foods WHERE 1=1";
if (!empty($categoryFilter)) {
    $sql .= " AND category_id = " . intval($categoryFilter);
}
if (!empty($keyword)) {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($keyword) . "%'";
}
$sql .= " ORDER BY id DESC";

$foodResult = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thực đơn - FoodStore</title>
    <link rel="stylesheet" href="trangchu.css">
    <style>
        .category-nav { text-align: center; margin: 20px 0; }
        .category-nav a { 
            padding: 8px 15px; border: 1px solid #ddd; 
            text-decoration: none; border-radius: 20px; color: #333; margin: 0 5px;
            display: inline-block;
        }
        .category-nav a.active { background: #ff4757; color: white; border-color: #ff4757; }
        /* Đảm bảo ảnh hiển thị đều nhau */
        .food-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 10px 10px 0 0; }
    </style>
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
    <a class="active">Thực đơn</a>
    <a href="giohang.php">Giỏ hàng</a>
    <a href="donhang.php">📦 Đơn hàng của tôi</a>
</nav>

<section class="hero small-hero">
    <h1>🍽️ Thực đơn</h1>
    <p>Chọn món bạn yêu thích</p>
</section>

<div class="category-nav">
    <a href="thucdon.php" class="<?= $categoryFilter == '' ? 'active' : '' ?>">Tất cả</a>
    <?php foreach ($categories as $id => $name): ?>
        <a href="?category=<?= $id ?>" class="<?= $categoryFilter == $id ? 'active' : '' ?>">
            🍴 <?= htmlspecialchars($name) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php include 'search.php'; ?>

<main class="main">
    <div class="food-grid">
    <?php 
    if ($foodResult->num_rows > 0):
        while ($f = $foodResult->fetch_assoc()): 
    ?>
        <div class="food-card"><img src="img/<?= htmlspecialchars($f['image']) ?>" 
                 alt="<?= htmlspecialchars($f['name']) ?>" 
                 onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
            
            <h3><?= htmlspecialchars($f['name']) ?></h3>
            <p class="price"><?= number_format($f['price'], 0, ',', '.') ?>đ</p>

            <form method="post" action="themmonan.php">
                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($f['name']) ?>">
                <input type="hidden" name="price" value="<?= $f['price'] ?>">
                <input type="hidden" name="image" value="<?= htmlspecialchars($f['image']) ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit">Thêm vào giỏ</button>
            </form>
        </div>
    <?php 
        endwhile; 
    else:
        echo "<p style='text-align:center; grid-column: 1/-1;'>Không tìm thấy món ăn nào phù hợp.</p>";
    endif;
    ?>
    </div>
</main>

</body>
</html>