<?php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM clean_sheets WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = 'Clean sheet removed successfully!';
} else {
    $_SESSION['error'] = 'Invalid clean sheet ID';
}

header('Location: ../dashboard.php?page=stats');
exit;