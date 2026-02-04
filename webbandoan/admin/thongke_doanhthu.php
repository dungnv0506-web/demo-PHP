<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Thống kê theo mốc thời gian
$today = date('Y-m-d');
$this_year = date('Y');

// Lấy dữ liệu doanh thu 12 tháng trong năm hiện tại
$monthly_revenue = [];
for ($m = 1; $m <= 12; $m++) {
    $date_str = $this_year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
    $res = $conn->query("SELECT SUM(total) as total FROM orders 
                         WHERE created_at LIKE '$date_str%' 
                         AND (status = 'Hoàn thành' OR status = 'Đã giao')");
    $row = $res->fetch_assoc();
    $monthly_revenue[] = $row['total'] ?? 0;
}

// Chuyển mảng PHP sang JSON để Javascript có thể đọc được
$chart_data = json_encode($monthly_revenue);

// Doanh thu tổng năm nay
$rev_year = array_sum($monthly_revenue);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sơ đồ doanh thu - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* GIỮ NGUYÊN CSS CỦA BẠN */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background: #f4f7f6; }
        .sidebar { width: 260px; height: 100vh; background: #2c3e50; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        .sidebar a { display: block; color: #bdc3c7; padding: 12px; text-decoration: none; transition: 0.3s; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; color: white; }
        .sidebar a.active { background: #e67e22; color: white; }
        .main-content { margin-left: 260px; padding: 30px; width: 100%; }
        
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .chart-container { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px; }
        .revenue-total { font-size: 20px; font-weight: bold; color: #2ecc71; }
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
    <a href="lichsu_thunhap.php">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php" class="active">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header-box">
        <h1>Sơ đồ doanh thu năm <?php echo $this_year; ?></h1>
        <div class="revenue-total">Tổng năm: <?php echo number_format($rev_year, 0, ',', '.'); ?>đ</div>
    </div>

    <div class="chart-container">
        <canvas id="revenueChart" style="max-height: 400px;"></canvas>
    </div>
</div>

<script>
    // Lấy dữ liệu từ PHP
    const dataRevenue = <?php echo $chart_data; ?>;

    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar', // Có thể đổi thành 'line' nếu muốn sơ đồ đường
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: dataRevenue,
                backgroundColor: 'rgba(230, 126, 34, 0.6)', // Màu cam giống màu active sidebar của bạn
                borderColor: 'rgba(230, 126, 34, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>