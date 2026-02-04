<?php
session_start();
include "../config/db.php";

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

// 2. Xử lý xóa người dùng (Không cho phép Admin tự xóa chính mình)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $current_admin_id = $_SESSION['user']['id'];

    if ($id == $current_admin_id) {
        $error = "Bạn không thể tự xóa tài khoản của chính mình!";
    } else {
        $conn->query("DELETE FROM users WHERE id = $id");
        header("Location: quanlynguoidung.php");
        exit;
    }
}

// 3. Lấy danh sách người dùng
$result = $conn->query("SELECT id, username, role FROM users ORDER BY role ASC, id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .role-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .role-admin { background: #ffeaa7; color: #d35400; }
        .role-user { background: #dfe6e9; color: #636e72; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Food</h2>
    <a href="trangchuadmin.php">🏠 Dashboard</a>
    <a href="quanly_monan.php">🍴 Quản lý món ăn</a>
    <a href="quanly_danhmuc.php">📂 Quản lý danh mục</a>
    <a href="quanly_donhang.php">🛒 Quản lý đơn hàng</a>
    <a href="quanly_nguoidung.php" class="active">👥 Quản lý người dùng</a>
    <a href="lichsu_thunhap.php">💰 Lịch sử thu nhập</a>
    <a href="thongke_doanhthu.php">📊 Thống kê doanh thu</a>
    <a href="../dangxuat.php" style="margin-top: 50px; color: #ff7675;">🚪 Đăng xuất</a>
</div>

<div class="main-content">
    <div class="header">
        <span>Quản lý Tài khoản</span>
        <strong>Admin: <?= $_SESSION['user']['username'] ?></strong>
    </div>

    <h1>👥 Danh sách người dùng</h1>
    <br>

    <?php if(isset($error)): ?>
        <div style="background: #fab1a0; color: #c0392b; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Vai trò</th>
                    <th style="text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><strong><?= $row['username'] ?></strong></td>
                    <td>
                        <span class="role-badge <?= $row['role'] == 'admin' ? 'role-admin' : 'role-user' ?>">
                            <?= $row['role'] ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <?php if($row['id'] != $_SESSION['user']['id']): ?>
                            <a href="quanlynguoidung.php?delete_id=<?= $row['id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Thao tác này không thể hoàn tác!')">
                               Xóa tài khoản
                            </a>
                        <?php else: ?>
                            <span style="color: #999; font-size: 13px; font-style: italic;">Đang trực</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>