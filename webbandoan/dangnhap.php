<?php
session_start();
include "config/db.php";

$message = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin') {
            header("Location: admin/trangchuadmin.php");
        } else {
            header("Location: trangchu.php");
        }
        exit;
    } else {
        $message = "Sai tài khoản hoặc mật khẩu";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập</title>
<link rel="stylesheet" href="dangnhap-dangky.css">
</head>
<body>

<div class="container">
    <form method="post" class="card">
        <h2>Đăng nhập</h2>

        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Tên đăng nhập" required>

        <div class="password-box">
            <input type="password" name="password" id="password" placeholder="Mật khẩu" required>
            <span onclick="togglePassword()">👁</span>
        </div>

        <button name="login">Đăng nhập</button>

        <p class="link">
            Chưa có tài khoản? <a href="dangky.php">Đăng ký</a>
        </p>
    </form>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
