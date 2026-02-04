<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Xử lý khi nhấn nút Thêm
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        // Kiểm tra xem tên danh mục đã tồn tại chưa
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Tên danh mục này đã tồn tại!";
        } else {
            // Thêm mới
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) {
                header("Location: quanly_danhmuc.php?msg=added");
                exit;
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    } else {
        $error = "Vui lòng nhập tên danh mục.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm danh mục mới</title>
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
    <a href="lichsu_thunhap.php">Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header">
        <span>Thêm Danh Mục</span>
        <a href="quanly_danhmuc.php" style="text-decoration: none; color: #3498db;">⬅ Quay lại danh sách</a>
    </div>

    <h1>📂 Tạo danh mục món ăn mới</h1>
    <br>

    <div class="table-container" style="max-width: 500px;">
        <?php if(isset($error)): ?>
            <p style="color: #e74c3c; margin-bottom: 15px; font-weight: bold;">❌ <?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Tên danh mục</label>
                <input type="text" name="name" placeholder="Ví dụ: Đồ ăn nhanh, Nước giải khát..." 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;" required>
            </div>

            <button type="submit" name="add_category" class="btn btn-add" 
                    style="width: 100%; padding: 12px; font-size: 16px; cursor: pointer;">
                + Thêm ngay
            </button>
        </form>
    </div>
</div>

</body>
</html>