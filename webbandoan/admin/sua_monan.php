<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Lấy thông tin món ăn cũ
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM foods WHERE id = $id";
    $result = $conn->query($sql);
    $food = $result->fetch_assoc();

    if (!$food) {
        die("Món ăn không tồn tại.");
    }
} else {
    header("Location: quanlymonan.php");
    exit;
}

// 3. Lấy danh sách danh mục để hiển thị trong <select>
$categories = $conn->query("SELECT * FROM categories");

// 4. Xử lý khi nhấn nút Cập nhật
if (isset($_POST['update_food'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    
    // Xử lý ảnh (nếu người dùng chọn ảnh mới)
    if ($_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../img/" . $image);
    } else {
        // Nếu không chọn ảnh mới, giữ lại tên ảnh cũ
        $image = $food['image'];
    }

    $update_sql = "UPDATE foods SET name=?, price=?, image=?, category_id=?, description=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sisssi", $name, $price, $image, $category_id, $description, $id);

    if ($stmt->execute()) {
        header("Location: quanly_monan.php?msg=success");
        exit;
    } else {
        $error = "Có lỗi xảy ra khi cập nhật.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa món ăn</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .form-container { background: white; padding: 25px; border-radius: 10px; max-width: 700px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #2c3e50; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 15px; }
        .current-img { margin: 10px 0; display: block; border: 1px solid #eee; padding: 5px; border-radius: 5px; }
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
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header">
        <span>Chỉnh sửa thông tin</span>
        <a href="quanly_monan.php" style="color: #3498db; text-decoration: none;">⬅ Quay lại</a>
    </div>

    <h1>Chỉnh sửa: <?= $food['name'] ?></h1>
    <br>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên món ăn</label>
                <input type="text" name="name" value="<?= htmlspecialchars($food['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Giá bán (VNĐ)</label>
                <input type="number" name="price" value="<?= $food['price'] ?>" required>
            </div>

            <div class="form-group">
                <label>Danh mục</label>
                <select name="category_id">
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $food['category_id']) ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Hình ảnh hiện tại</label>
                <img src="../img/<?= $food['image'] ?>" width="120" class="current-img">
                <label>Thay đổi ảnh (để trống nếu giữ nguyên)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <label>Mô tả món ăn</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($food['description']) ?></textarea>
            </div>

            <button type="submit" name="update_food" class="btn btn-add" style="width: 100%; padding: 12px;">Cập nhật món ăn</button>
        </form>
    </div>
</div>

</body>
</html>