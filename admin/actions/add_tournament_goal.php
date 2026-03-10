<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$match_id   = (int)($_POST['match_id'] ?? 0);
$player_id  = (int)($_POST['player_id'] ?? 0);
$minute     = (int)($_POST['minute'] ?? 0);
$is_penalty = !empty($_POST['is_penalty']) ? 1 : 0;

if ($match_id && $player_id && $minute >= 1 && $minute <= 120) {
    $stmt = $pdo->prepare("INSERT INTO tournament_goals (match_id, player_id, minute, is_penalty) VALUES (?, ?, ?, ?)");
    $stmt->execute([$match_id, $player_id, $minute, $is_penalty]);
    $_SESSION['success'] = 'Tournament goal added!';
} else {
    $_SESSION['error'] = 'Invalid goal data';
}
header('Location: ../dashboard.php?page=tournament_stats');
exit;