<?php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized');
}

$match_id    = (int)($_POST['match_id'] ?? 0);
$home_score  = (int)($_POST['home_score'] ?? 0);
$away_score  = (int)($_POST['away_score'] ?? 0);

if ($match_id <= 0 || $home_score < 0 || $away_score < 0) {
    $_SESSION['error'] = 'Invalid match result data.';
    header('Location: ../dashboard.php?page=tournament_stats');
    exit;
}

try {
    // CORRECT: 3 placeholders → 3 parameters in execute()
    $stmt = $pdo->prepare("
        UPDATE tournament_matches 
        SET home_score = ?, away_score = ? 
        WHERE id = ?
    ");
    $stmt->execute([$home_score, $away_score, $match_id]);

    $_SESSION['success'] = 'Tournament match result updated successfully!';
} catch (Exception $e) {
    $_SESSION['error'] = 'Failed to update result.';
}

header('Location: ../dashboard.php?page=tournament_stats');
exit;