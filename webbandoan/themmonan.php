<?php
session_start();

$id = $_POST['id'];

if (!isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = [
        'id'    => $id, // QUAN TRỌNG: Phải có dòng này
        'name'  => $_POST['name'],
        'price' => $_POST['price'],
        'image' => $_POST['image'],
        'qty'   => 1
    ];
} else {
    $_SESSION['cart'][$id]['qty']++;
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;