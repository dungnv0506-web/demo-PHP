<?php
session_start();
require 'config/db.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit;
}

$user = $_SESSION['user'];
$cart = $_SESSION['cart'] ?? [];

// Tính tổng số lượng để hiển thị trên Header
$cartCount = 0;
foreach ($cart as $item) { $cartCount += $item['qty']; }

if (empty($cart)) {
    header("Location: giohang.php");
    exit;
}

$success = false;
$order_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'COD';

    if ($name === '' || $phone === '' || $address === '') {
        die("Vui lòng nhập đầy đủ thông tin");
    }

    $user_id = $user['id'];
    $total = 0;
    foreach ($cart as $item) { $total += $item['price'] * $item['qty']; }

    $status = ($payment_method === 'MOMO') ? 'Chờ thanh toán' : 'Chờ xử lý';

    // 1. Lưu đơn hàng
    $sql_order = "INSERT INTO orders (user_id, total, status, payment_method) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("iiss", $user_id, $total, $status, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id; 

    // 2. Lưu chi tiết
    $sql_detail = "INSERT INTO order_details (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_detail = $conn->prepare($sql_detail);

    foreach ($cart as $id_key => $item) {
        $food_id = intval($item['id'] ?? $id_key); 
        $qty     = intval($item['qty']);
        $price   = intval($item['price']);
        $stmt_detail->bind_param("iiii", $order_id, $food_id, $qty, $price);
        $stmt_detail->execute();
    }

    unset($_SESSION['cart']);

    if ($payment_method === 'MOMO') {
        header("Location: momo_qr.php?order_id=" . $order_id);
        exit;
    }
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng</title>
    <link rel="stylesheet" href="trangchu.css">
    <style>
        .order-form { max-width: 500px; margin: 20px auto; padding: 25px; border-radius: 12px; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .order-form label { display: block; margin-top: 12px; font-weight: bold; color: #555; }
        .order-form input, .order-form textarea { width: 100%; padding: 12px; margin-top: 6px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .payment-box { margin: 15px 0; padding: 15px; background: #fefefe; border: 1px dashed #ff4d4f; border-radius: 8px; }
        .checkout-btn { background: #ff4d4f; color: white; border: none; padding: 15px; cursor: pointer; border-radius: 8px; width: 100%; font-size: 16px; font-weight: bold; transition: 0.3s; }
        .checkout-btn:hover { background: #e63e40; transform: translateY(-2px); }
    </style>
</head>
<body>

<header class="topbar">
    <div class="logo">🍔 FoodStore</div>
    <div class="user-box">
        Chào <b><?= htmlspecialchars($user['username']) ?></b>
        <a href="giohang.php">🛒 Giỏ hàng (<?= $cartCount ?>)</a>
        <a href="dangxuat.php">Đăng xuất</a>
    </div>
</header>

<main class="main">
<?php if ($success): ?>
    <div style="text-align: center; margin-top: 80px;">
        <h1 style="font-size: 60px;">🎉</h1>
        <h2>Đặt hàng thành công!</h2>
        <p>Đơn hàng của bạn là: <b>#<?= $order_id ?></b></p>
        <br>
        <a href="donhang.php" class="checkout-btn" style="text-decoration:none; display:inline-block; width:auto; padding: 12px 30px;">📦 Theo dõi đơn hàng</a>
    </div>
<?php else: ?>
    <form method="post" class="order-form">
        <h2 style="text-align:center; margin-top:0;">Thông tin giao hàng</h2>
        <label>Người nhận</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['username']) ?>" required>
        <label>Số điện thoại</label>
        <input type="text" name="phone" required placeholder="090x xxx xxx">
        <label>Địa chỉ nhận hàng</label>
        <textarea name="address" required rows="3" placeholder="Số nhà, tên đường..."></textarea>
        <label>Thanh toán</label>
        <div class="payment-box">
            <input type="radio" name="payment_method" value="COD" checked> 💵 Tiền mặt (COD) <br><br>
            <input type="radio" name="payment_method" value="MOMO"> 📱 Ví MoMo
        </div>
        <button type="submit" class="checkout-btn">XÁC NHẬN ĐẶT HÀNG</button>
    </form>
<?php endif; ?>
</main>
</body>
</html>