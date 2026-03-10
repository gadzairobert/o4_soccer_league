<?php
// admin/actions/delete_card.php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$id       = (int)($_GET['id'] ?? 0);
$match_id = (int)($_GET['match_id'] ?? 0);

if ($id <= 0 || $match_id <= 0) {
    $_SESSION['error'] = 'Invalid card ID.';
} else {
    try {
        $stmt = $pdo->prepare("DELETE FROM cards WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Card removed.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to delete card.';
    }
}

header("Location: ../dashboard.php?page=stats");
exit;