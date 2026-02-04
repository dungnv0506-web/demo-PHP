<?php
session_start();
require 'config/db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

// 2. Lấy order_id từ form MoMo gửi sang
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if ($order_id > 0) {
    // 3. Cập nhật trạng thái trong Database thành 'Đã thanh toán'
    $stmt = $conn->prepare("UPDATE orders SET status = 'Đã thanh toán' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
} else {
    header("Location: trangchu.php");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán thành công</title>
    <link rel="stylesheet" href="trangchu.css">
    <style>
        .success-container {
            text-align: center;
            padding: 50px 20px;
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .checkmark {
            font-size: 80px;
            color: #52c41a;
            margin-bottom: 20px;
        }
        .order-id-label {
            background: #f5f5f5;
            padding: 10px 20px;
            border-radius: 30px;
            display: inline-block;
            margin: 15px 0;
            font-weight: bold;
            color: #333;
        }
        .btn-group {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-primary { background: #ff4d4f; color: white; }
        .btn-secondary { background: #e8e8e8; color: #555; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    </style>
</head>
<body>

<header class="topbar">
    <div class="logo">🍔 FoodStore</div>
</header>

<main class="main">
    <div class="success-container">
        <div class="checkmark">✔</div>
        <h1 style="color: #333;">Thanh toán thành công!</h1>
        <p style="font-size: 18px; color: #666;">Cảm ơn bạn, MoMo đã xác nhận thanh toán cho đơn hàng của bạn.</p>
        
        <div class="order-id-label">Mã đơn hàng: #<?= $order_id ?></div>

        <div class="btn-group">
            <a href="trangchu.php" class="btn btn-secondary">🏠 Về trang chủ</a>
            <a href="donhang.php" class="btn btn-primary">📦 Xem đơn hàng</a>
        </div>
    </div>
</main>

</body>
</html>