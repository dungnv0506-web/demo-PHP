<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Xử lý xóa món ăn (Đã sửa để tránh lỗi Foreign Key Constraint)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Bước A: Xóa các chi tiết đơn hàng liên quan đến món này để gỡ ràng buộc
    $conn->query("DELETE FROM order_details WHERE food_id = $id");
    
    // Bước B: Xóa món ăn chính
    $conn->query("DELETE FROM foods WHERE id = $id");
    
    header("Location: quanly_monan.php?msg=deleted");
    exit;
}

// 3. Lấy danh sách món ăn (Giữ nguyên logic của bạn)
$sql = "SELECT foods.*, categories.name AS category_name 
        FROM foods 
        LEFT JOIN categories ON foods.category_id = categories.id 
        ORDER BY foods.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý món ăn</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Food</h2>
    <a href="trangchuadmin.php">🏠 Dashboard</a>
    <a href="quanly_monan.php" class="active">🍴 Quản lý món ăn</a>
    <a href="quanly_danhmuc.php">📂 Quản lý danh mục</a>
    <a href="quanly_donhang.php">🛒 Quản lý đơn hàng</a>
    <a href="quanly_nguoidung.php">👥 Quản lý người dùng</a>
    <a href="lichsu_thunhap.php">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header">
        <span>Quản lý Thực đơn</span>
        <strong>Admin: <?= htmlspecialchars($_SESSION['user']['username']) ?></strong>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="them_monan.php" class="btn btn-add">+ Thêm món ăn mới</a>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <span style="color: #2ecc71; font-weight: bold;">✅ Đã xóa món ăn thành công!</span>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên món</th>
                    <th>Giá bán</th>
                    <th>Danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <img src="../img/<?= htmlspecialchars($row['image']) ?>" 
                                 alt="<?= htmlspecialchars($row['name']) ?>" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #eee;"
                                 onerror="this.src='../img/default.png'">
                        </td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td style="color: #e67e22; font-weight: bold;"><?= number_format($row['price'], 0, ',', '.') ?>đ</td>
                        <td><?= htmlspecialchars($row['category_name'] ?? 'Chưa phân loại') ?></td>
                        <td>
                            <a href="sua_monan.php?id=<?= $row['id'] ?>" class="btn btn-edit">Sửa</a>
                            <a href="quanly_monan.php?delete_id=<?= $row['id'] ?>" 
                               class="btn btn-delete" 
                               style="background: #ff7675; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px;"
                               onclick="return confirm('Cảnh báo: Xóa món ăn này sẽ xóa toàn bộ lịch sử món này trong các đơn hàng cũ. Bạn vẫn muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">Chưa có món ăn nào trong thực đơn.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>