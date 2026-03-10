<?php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$goal_id   = (int)($_POST['goal_id'] ?? 0);
$player_id = (int)($_POST['player_id'] ?? 0);
$match_id  = (int)($_POST['match_id'] ?? 0);

if ($goal_id > 0 && $player_id > 0 && $match_id > 0) {
    // Prevent self-assist
    $stmt = $pdo->prepare("SELECT player_id FROM goals WHERE id = ?");
    $stmt->execute([$goal_id]);
    if ($stmt->fetchColumn() != $player_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO assists (goal_id, player_id) VALUES (?, ?)");
        $stmt->execute([$goal_id, $player_id]);
        $_SESSION['success'] = 'Assist added successfully!';
    } else {
        $_SESSION['error'] = 'Player cannot assist own goal';
    }
} else {
    $_SESSION['error'] = 'Invalid assist data';
}

// SAME AS YOUR WORKING add_card.php
header('Location: ../dashboard.php?page=stats');
exit;