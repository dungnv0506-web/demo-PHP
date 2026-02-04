<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Lấy ID đơn hàng và thông tin đơn hàng
if (!isset($_GET['id'])) {
    header("Location: quanly_donhang.php");
    exit;
}

$id = intval($_GET['id']); // Chống SQL Injection cơ bản

// Lấy thông tin tổng quan của đơn hàng
$order_query = $conn->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = $id
");
$order = $order_query->fetch_assoc();

// Nếu không tìm thấy đơn hàng
if (!$order) {
    die("Đơn hàng không tồn tại.");
}

// Lấy danh sách các món trong đơn hàng đó
$items = $conn->query("
    SELECT d.*, f.name, f.image 
    FROM order_details d 
    JOIN foods f ON d.food_id = f.id
    WHERE d.order_id = $id
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $id ?></title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .order-info { margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; border-left: 5px solid #3498db; }
        .order-info p { margin-bottom: 10px; font-size: 16px; }
        .total-row { font-size: 20px; font-weight: bold; color: #e67e22; text-align: right; margin-top: 20px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Food</h2>
    <a href="trangchuadmin.php">🏠 Dashboard</a>
    <a href="quanly_monan.php">🍴 Quản lý món ăn</a>
    <a href="quanly_danhmuc.php">📂 Quản lý danh mục</a>
    <a href="quanly_donhang.php" class="active">🛒 Quản lý đơn hàng</a>
    <a href="quanly_nguoidung.php">👥 Quản lý người dùng</a>
    <a href="lichsu_thunhap.php">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header">
        <span>Chi tiết hóa đơn</span>
        <a href="quanly_donhang.php" style="text-decoration: none; color: #3498db;">⬅ Quay lại danh sách</a>
    </div>

    <h1>🧾 Đơn hàng #<?= $id ?></h1>
    <br>

    <div class="order-info">
        <p>👤 <strong>Khách hàng:</strong> <?= $order['username'] ?></p>
        <p>🕒 <strong>Ngày đặt:</strong> <?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></p>
        <p>💳 <strong>Thanh toán:</strong> <?= $order['payment_method'] ?></p>
        <p>🚩 <strong>Trạng thái:</strong> <span class="badge"><?= $order['status'] ?></span></p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Món ăn</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($i = $items->fetch_assoc()): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="../img/<?= $i['image'] ?>" width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                            <strong><?= $i['name'] ?></strong>
                        </div>
                    </td>
                    <td><?= number_format($i['price'], 0, ',', '.') ?>đ</td>
                    <td>x<?= $i['quantity'] ?></td>
                    <td><strong><?= number_format($i['price'] * $i['quantity'], 0, ',', '.') ?>đ</strong></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-row">
            TỔNG CỘNG: <?= number_format($order['total'], 0, ',', '.') ?>đ
        </div>
    </div>
</div>

</body>
</html>