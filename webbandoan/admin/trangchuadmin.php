<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Thống kê số lượng cơ bản
$count_foods = $conn->query("SELECT COUNT(*) as total FROM foods")->fetch_assoc()['total'];
$count_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$count_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];

// 3. THỐNG KÊ DOANH THU (Chỉ tính đơn đã hoàn thành)
// Câu lệnh SQL này cộng tổng cột 'total' với các trạng thái phổ biến của đơn hàng thành công
$revenue_res = $conn->query("SELECT SUM(total) as total_revenue FROM orders 
                             WHERE status = 'Hoàn thành' 
                             OR status = 'Đã giao' 
                             OR status = 'Completed'");
$total_revenue = $revenue_res->fetch_assoc()['total_revenue'] ?? 0;

// 4. Thống kê đơn hàng đang chờ (Để đối soát)
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders 
                                WHERE status = 'Chờ xử lý' 
                                OR status = 'Pending' 
                                OR status IS NULL")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Admin</title>
    <style>
        /* GIỮ NGUYÊN CSS GỐC CỦA BẠN */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; }
        .sidebar { width: 260px; height: 100vh; background: #2c3e50; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .sidebar a { display: block; color: #bdc3c7; padding: 12px; text-decoration: none; transition: 0.3s; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; color: white; }
        .sidebar a.active { background: #e67e22; color: white; }
        .main-content { margin-left: 260px; padding: 30px; width: 100%; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #e67e22; }
        .card h3 { color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
        .card p { font-size: 28px; font-weight: bold; color: #2c3e50; margin-top: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Food</h2>
    <a href="trangchuadmin.php" class="active">🏠 Dashboard</a>
    <a href="quanly_monan.php">🍴 Quản lý món ăn</a>
    <a href="quanly_danhmuc.php">📂 Quản lý danh mục</a>
    <a href="quanly_donhang.php">🛒 Quản lý đơn hàng</a>
    <a href="quanly_nguoidung.php">👥 Quản lý người dùng</a>
    <a href="lichsu_thunhap.php">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header">
        <span>Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong></span>
        <span>Hệ thống quản lý bán đồ ăn</span>
    </div>

    <h1>Tổng quan hệ thống</h1>
    <br>
    
    <div class="cards">
        <div class="card">
            <h3>Tổng món ăn</h3>
            <p><?php echo number_format($count_foods); ?></p>
        </div>
        <div class="card" style="border-left-color: #27ae60;">
            <h3>Tổng đơn hàng</h3>
            <p><?php echo number_format($count_orders); ?></p>
        </div>
        <div class="card" style="border-left-color: #2980b9;">
            <h3>Khách hàng</h3>
            <p><?php echo number_format($count_users); ?></p>
        </div>

        <div class="card" style="border-left-color: #f1c40f;">
            <h3>Doanh thu (Đã giao)</h3>
            <p><?php echo number_format($total_revenue, 0, ',', '.'); ?>đ</p>
        </div>
        <div class="card" style="border-left-color: #e67e22;">
            <h3>Đơn chờ xử lý</h3>
            <p><?php echo number_format($pending_orders); ?></p>
        </div>
        <div class="card" style="border-left-color: #95a5a6;">
            <h3>Tỷ lệ hoàn tất</h3>
            <p><?php echo ($count_orders > 0) ? round((($count_orders - $pending_orders) / $count_orders) * 100, 1) : 0; ?>%</p>
        </div>
    </div>
</div>

</body>
</html>