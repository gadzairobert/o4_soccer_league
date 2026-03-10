<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo->prepare("DELETE FROM tournament_clean_sheets WHERE id = ?")->execute([$id]);
    $_SESSION['success'] = 'Clean sheet removed.';
}
header("Location: ../dashboard.php?page=tournament_stats");
exit;