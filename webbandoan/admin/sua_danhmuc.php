<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Lấy thông tin danh mục cần sửa
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM categories WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        header("Location: quanly_danhmuc.php");
        exit;
    }
} else {
    header("Location: quanly_danhmuc.php");
    exit;
}

// 3. Xử lý cập nhật khi nhấn nút Lưu
if (isset($_POST['update_category'])) {
    $name = trim($_POST['name']);
    $id = intval($_POST['id']);

    if (!empty($name)) {
        // Kiểm tra xem tên mới có bị trùng với danh mục khác không
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $check->bind_param("si", $name, $id);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $error = "Tên danh mục này đã tồn tại!";
        } else {
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            if ($stmt->execute()) {
                header("Location: quanly_danhmuc.php?msg=updated");
                exit;
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    } else {
        $error = "Vui lòng không để trống tên danh mục.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa danh mục</title>
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
        <h1>Chỉnh sửa danh mục</h1>
        <a href="quanly_danhmuc.php" style="text-decoration: none; color: #3498db;">⬅ Quay lại</a>
    </div>

    <div class="table-container" style="max-width: 500px; margin-top: 20px;">
        <?php if(isset($error)): ?>
            <p style="color: #e74c3c; margin-bottom: 15px; font-weight: bold;">❌ <?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Tên danh mục hiện tại</label>
                <input type="text" name="name" 
                       value="<?= htmlspecialchars($category['name']) ?>" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;" required>
            </div>

            <button type="submit" name="update_category" class="btn btn-edit" 
                    style="width: 100%; padding: 12px; font-size: 16px; cursor: pointer; background-color: #f1c40f; color: white; border: none; border-radius: 5px;">
                💾 Cập nhật thay đổi
            </button>
        </form>
    </div>
</div>

</body>
</html>