<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// --- ĐOẠN THÊM MỚI: XỬ LÝ XÓA ---
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);

    // Kiểm tra xem có món ăn nào thuộc danh mục này không
    $check_food = $conn->query("SELECT id FROM foods WHERE category_id = $del_id");
    
    if ($check_food->num_rows > 0) {
        $msg = "error_exist"; // Gửi mã lỗi nếu còn món ăn
    } else {
        $sql_delete = "DELETE FROM categories WHERE id = $del_id";
        if ($conn->query($sql_delete)) {
            header("Location: quanly_danhmuc.php?msg=deleted");
            exit;
        }
    }
}
// --------------------------------

// 2. Lấy dữ liệu từ database
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Food</h2>
    <a href="trangchuadmin.php">🏠 Dashboard</a>
    <a href="quanly_monan.php">🍴 Quản lý món ăn</a>
    <a href="quanly_danhmuc.php" class="active">📂 Quản lý danh mục</a>
    <a href="quanly_donhang.php">🛒 Quản lý đơn hàng</a>
    <a href="quanly_nguoidung.php">👥 Quản lý người dùng</a>
    <a href="lichsu_thunhap.php">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header-box">
        <h1>Quản lý danh mục</h1>
        <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong></span>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <p style="color: green; font-weight: bold; margin-bottom: 10px;">✅ Đã xóa danh mục thành công!</p>
    <?php endif; ?>
    
    <?php if(isset($msg) && $msg == 'error_exist'): ?>
        <p style="color: red; font-weight: bold; margin-bottom: 10px;">❌ Không thể xóa! Danh mục này đang có món ăn.</p>
    <?php endif; ?>

    <div class="table-container">
        <a href="them_danhmuc.php" class="btn btn-add">+ Thêm danh mục mới</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0): 
                    while($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td>
                            <a href="sua_danhmuc.php?id=<?= $row['id'] ?>" class="btn btn-edit">Sửa</a>
                            <a href="quanly_danhmuc.php?delete_id=<?= $row['id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px;">
                            Chưa có danh mục nào được lưu trong hệ thống.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>