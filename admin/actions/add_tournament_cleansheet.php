<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$match_id = (int)($_POST['match_id'] ?? 0);
$club_id  = (int)($_POST['club_id'] ?? 0);

if ($match_id && $club_id) {
    $stmt = $pdo->prepare("SELECT id FROM players WHERE club_id = ? AND position = 'GK' LIMIT 1");
    $stmt->execute([$club_id]);
    $gk_id = $stmt->fetchColumn();

    if ($gk_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO tournament_clean_sheets (match_id, player_id) VALUES (?, ?)");
        $stmt->execute([$match_id, $gk_id]);
        $_SESSION['success'] = 'Clean sheet awarded!';
    } else {
        $_SESSION['error'] = 'No goalkeeper found for this team';
    }
}
header('Location: ../dashboard.php?page=tournament_stats');
exit;