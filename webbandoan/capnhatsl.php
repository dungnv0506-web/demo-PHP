<?php
session_start();

$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if (!isset($_SESSION['cart'][$id])) {
    header("Location: giohang.php");
    exit;
}

if ($type === 'plus') {
    $_SESSION['cart'][$id]['qty']++;
}

if ($type === 'minus') {
    $_SESSION['cart'][$id]['qty']--;

    if ($_SESSION['cart'][$id]['qty'] <= 0) {
        unset($_SESSION['cart'][$id]);
    }
}

header("Location: giohang.php");
exit;
