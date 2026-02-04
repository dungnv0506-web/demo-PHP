<?php
require 'check_admin.php';
session_start();
require '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../dangnhap.php");
    exit;
}

$id = $_GET['id'];
$status = $_GET['status'];

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: quanly_donhang.php");
