<?php
// admin/actions/update_result.php
require '../../config.php';

if (!isset($_SESSION['admin_id'])) die('Unauthorized');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die('Invalid request');

$match_id    = (int)($_POST['match_id'] ?? 0);
$home_score  = (int)($_POST['home_score'] ?? 0);
$away_score  = (int)($_POST['away_score'] ?? 0);

if ($match_id <= 0 || $home_score < 0 || $away_score < 0) {
    $_SESSION['error'] = 'Invalid match data.';
    header('Location: ../dashboard.php?page=stats');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE matches SET home_score = ?, away_score = ?, match_date = NOW() WHERE id = ?");
    $stmt->execute([$home_score, $away_score, $match_id]);

    // Get club IDs
    $stmt = $pdo->prepare("
        SELECT f.home_club_id, f.away_club_id 
        FROM fixtures f 
        JOIN matches m ON f.id = m.fixture_id 
        WHERE m.id = ?
    ");
    $stmt->execute([$match_id]);
    $clubs = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($clubs) {
        // Safely include league table updater (if exists)
        $file = '../includes/update_league_table.php';
        if (file_exists($file)) {
            require $file;
            if (function_exists('updateLeagueTableForMatch')) {
                updateLeagueTableForMatch($pdo, $match_id, $clubs['home_club_id'], $clubs['away_club_id'], $home_score, $away_score);
            }
        }
        // If file doesn't exist → silently skip (no crash)
    }

    $_SESSION['success'] = 'Match result updated successfully!';
} catch (Exception $e) {
    $_SESSION['error'] = 'Failed to update result.';
}

header('Location: ../dashboard.php?page=stats');
exit;