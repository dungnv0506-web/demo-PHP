<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// Lấy danh sách danh mục để chọn
$categories = $conn->query("SELECT * FROM categories");

if (isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    
    // Xử lý upload ảnh
    $image = $_FILES['image']['name'];
    $target = "../img/" . basename($image);

    $sql = "INSERT INTO foods (name, price, image, category_id, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $name, $price, $image, $category_id, $description);

    if ($stmt->execute()) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        header("Location: quanly_monan.php");
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm món ăn</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .form-container { background: white; padding: 25px; border-radius: 10px; max-width: 600px; }
    </style>
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

    
</div>

<div class="main-content">
    <div class="header"><h1>Thêm món ăn mới</h1></div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên món ăn</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Giá bán (VNĐ)</label>
                <input type="number" name="price" required>
            </div>
            <div class="form-group">
                <label>Danh mục</label>
                <select name="category_id">
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Hình ảnh</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            <button type="submit" name="add_food" class="btn btn-add" style="width: 100%;">Lưu món ăn</button>
        </form>
    </div>
</div>

</body>
</html>