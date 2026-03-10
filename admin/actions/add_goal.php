<?php
// admin/actions/add_goal.php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request';
    header('Location: ../dashboard.php?page=stats');
    exit;
}

$match_id   = (int)($_POST['match_id'] ?? 0);
$player_id  = (int)($_POST['player_id'] ?? 0);
$minute     = (int)($_POST['minute'] ?? 0);
$is_penalty = !empty($_POST['is_penalty']) ? 1 : 0;

if ($match_id <= 0 || $player_id <= 0 || $minute < 1 || $minute > 120) {
    $_SESSION['error'] = 'Invalid goal data.';
} else {
    try {
        $stmt = $pdo->prepare("INSERT INTO goals (match_id, player_id, minute, is_penalty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$match_id, $player_id, $minute, $is_penalty]);
        $_SESSION['success'] = 'Goal added successfully!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to add goal.';
    }
}

header('Location: ../dashboard.php?page=stats');
exit;