<?php
include "config/db.php";

$message = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $check = $conn->prepare("SELECT id FROM users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Tên đăng nhập đã tồn tại";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users(username,password) VALUES (?,?)");
        $stmt->bind_param("ss", $username, $hash);
        $stmt->execute();
        $message = "Đăng ký thành công!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký</title>
<link rel="stylesheet" href="dangnhap-dangky.css">
</head>
<body>

<div class="container">
    <form method="post" class="card">
        <h2>Đăng ký tài khoản</h2>

        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <button name="register">Đăng ký</button>

        <p class="link">
            Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a>
        </p>
    </form>
</div>

</body>
</html>
