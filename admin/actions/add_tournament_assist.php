<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$goal_id   = (int)($_POST['goal_id'] ?? 0);
$player_id = (int)($_POST['player_id'] ?? 0);
$match_id  = (int)($_POST['match_id'] ?? 0);

if ($goal_id && $player_id && $match_id) {
    $stmt = $pdo->prepare("SELECT player_id FROM tournament_goals WHERE id = ?");
    $stmt->execute([$goal_id]);
    if ($stmt->fetchColumn() != $player_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO tournament_assists (goal_id, player_id) VALUES (?, ?)");
        $stmt->execute([$goal_id, $player_id]);
        $_SESSION['success'] = 'Assist added!';
    } else {
        $_SESSION['error'] = 'Player cannot assist own goal';
    }
}
header('Location: ../dashboard.php?page=tournament_stats');
exit;