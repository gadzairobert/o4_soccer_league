<?php
require '../config.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$id = (int)$_GET['id'] ?? 0;
if ($id > 0) {
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
}
header('Location: dashboard.php?page=news');
exit;
?>