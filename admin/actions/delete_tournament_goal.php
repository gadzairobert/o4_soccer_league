<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$id = (int)($_GET['id'] ?? 0);
$match_id = (int)($_GET['match_id'] ?? 0);

if ($id > 0) {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM tournament_assists WHERE goal_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM tournament_goals WHERE id = ?")->execute([$id]);
    $pdo->commit();
    $_SESSION['success'] = 'Goal and assist deleted.';
} else {
    $_SESSION['error'] = 'Invalid goal';
}
header("Location: ../dashboard.php?page=tournament_stats");
exit;