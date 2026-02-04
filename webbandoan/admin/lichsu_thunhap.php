<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Thống kê theo các mốc thời gian (Chỉ tính đơn Hoàn thành/Đã giao)
$today = date('Y-m-d');
$this_month = date('Y-m');

// Doanh thu hôm nay
$rev_today = $conn->query("SELECT SUM(total) as total FROM orders WHERE DATE(created_at) = '$today' AND (status = 'Hoàn thành' OR status = 'Đã giao')")->fetch_assoc()['total'] ?? 0;

// Doanh thu tháng này
$rev_month = $conn->query("SELECT SUM(total) as total FROM orders WHERE created_at LIKE '$this_month%' AND (status = 'Hoàn thành' OR status = 'Đã giao')")->fetch_assoc()['total'] ?? 0;

// Tổng doanh thu mọi thời đại
$rev_all = $conn->query("SELECT SUM(total) as total FROM orders WHERE status = 'Hoàn thành' OR status = 'Đã giao'")->fetch_assoc()['total'] ?? 0;

// 3. Lấy danh sách 10 đơn hàng thành công gần nhất
$sql_recent = "SELECT o.*, u.username 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               WHERE o.status = 'Hoàn thành' OR o.status = 'Đã giao' 
               ORDER BY o.created_at DESC LIMIT 10";
$recent_revenue = $conn->query($sql_recent);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê doanh thu - Admin</title>
    <style>
        /* Sử dụng lại bộ Style của bạn */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; }
        .sidebar { width: 260px; height: 100vh; background: #2c3e50; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .sidebar a { display: block; color: #bdc3c7; padding: 12px; text-decoration: none; transition: 0.3s; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; color: white; }
        .sidebar a.active { background: #e67e22; color: white; }
        .main-content { margin-left: 260px; padding: 30px; width: 100%; }
        
        /* Dashboard Cards */
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #2ecc71; }
        .card h3 { color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
        .card p { font-size: 24px; font-weight: bold; color: #2c3e50; margin-top: 10px; }

        /* Table Style */
        .data-table { width: 100%; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .data-table th { background: #f8f9fa; color: #34495e; font-weight: 600; }
        .data-table tr:hover { background: #f1f2f6; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Food</h2>
    <a href="trangchuadmin.php">🏠 Dashboard</a>
    <a href="quanly_monan.php">🍴 Quản lý món ăn</a>
    <a href="quanly_danhmuc.php">📂 Quản lý danh mục</a>
    <a href="quanly_donhang.php">🛒 Quản lý đơn hàng</a>
    <a href="quanly_nguoidung.php">👥 Quản lý người dùng</a>
    <a href="lichsu_thunhap.php" class="active">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <h1>Chi tiết doanh thu</h1>
    <br>

    <div class="cards">
        <div class="card">
            <h3>Hôm nay</h3>
            <p><?php echo number_format($rev_today, 0, ',', '.'); ?>đ</p>
        </div>
        <div class="card" style="border-left-color: #3498db;">
            <h3>Tháng này</h3>
            <p><?php echo number_format($rev_month, 0, ',', '.'); ?>đ</p>
        </div>
        <div class="card" style="border-left-color: #f1c40f;">
            <h3>Tổng doanh thu</h3>
            <p><?php echo number_format($rev_all, 0, ',', '.'); ?>đ</p>
        </div>
    </div>

    <h2>Lịch sử dòng tiền (10 đơn gần nhất)</h2>
    <br>
    <table class="data-table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Ngày thanh toán</th>
                <th>Phương thức</th>
                <th>Số tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $recent_revenue->fetch_assoc()): ?>
            <tr>
                <td><strong>#<?php echo $row['id']; ?></strong></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                <td><?php echo $row['payment_method']; ?></td>
                <td style="color: #27ae60; font-weight: bold;">+<?php echo number_format($row['total'], 0, ',', '.'); ?>đ</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>