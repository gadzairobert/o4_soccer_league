<?php
// admin/actions/delete_goal.php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$id       = (int)($_GET['id'] ?? 0);
$match_id = (int)($_GET['match_id'] ?? 0);

if ($id <= 0 || $match_id <= 0) {
    $_SESSION['error'] = 'Invalid goal ID.';
} else {
    try {
        $stmt = $pdo->prepare("DELETE FROM goals WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Goal deleted.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to delete goal.';
    }
}

header("Location: ../dashboard.php?page=stats");
exit;