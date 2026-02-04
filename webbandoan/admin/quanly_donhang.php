<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Lấy danh sách đơn hàng từ CSDL
$result = $conn->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        /* CSS bổ sung cho nhãn trạng thái đơn hàng */
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .bg-pending { background: #ffeaa7; color: #d35400; }
        .bg-shipping { background: #81ecec; color: #008b8b; }
        .bg-completed { background: #55efc4; color: #00b894; }
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
        <span>Quản lý Giao dịch</span>
        <strong>Admin: <?= $_SESSION['user']['username'] ?></strong>
    </div>

    <h1>📦 Danh sách đơn hàng</h1>
    <br>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    // Xác định màu sắc badge dựa trên trạng thái
                    $status_class = 'bg-pending';
                    if($row['status'] == 'Đang giao') $status_class = 'bg-shipping';
                    if($row['status'] == 'Hoàn thành') $status_class = 'bg-completed';
                ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><strong><?= $row['username'] ?></strong></td>
                    <td><?= number_format($row['total'], 0, ',', '.') ?>đ</td>
                    <td><small><?= $row['payment_method'] ?></small></td>
                    <td><span class="badge <?= $status_class ?>"><?= $row['status'] ?></span></td>
                    <td>
                        <a href="quanly_chitiet_donhang.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="font-size: 12px;">Xem</a>
                        <a href="capnhat_trangthai.php?id=<?= $row['id'] ?>&status=Đang giao" class="btn" style="background: #00cec9; font-size: 12px;">Giao</a>
                        <a href="capnhat_trangthai.php?id=<?= $row['id'] ?>&status=Hoàn thành" class="btn" style="background: #2ecc71; font-size: 12px;">Xong</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>