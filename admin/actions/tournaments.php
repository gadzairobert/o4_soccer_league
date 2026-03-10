<?php
// admin/actions/tournaments.php
if (!defined('IN_APP')) exit('Direct access not allowed');

$action = $_POST['action'] ?? '';

// ——————————————————————— ADD / EDIT TOURNAMENT FIXTURE ———————————————————————
if ($action === 'add_tournament_fixture' || $action === 'edit_tournament_fixture') {
    $id = $action === 'edit_tournament_fixture' ? (int)$_POST['id'] : null;
    $home_club_id = (int)$_POST['home_club_id'];
    $away_club_id = (int)$_POST['away_club_id'];
    $tournament_date = $_POST['tournament_date'];
    $venue = trim($_POST['venue'] ?? '');
    $competition_season_id = (int)$_POST['competition_season_id']; // NEW

    if ($home_club_id && $away_club_id && $home_club_id != $away_club_id && $tournament_date && $competition_season_id) {
        try {
            if ($action === 'add_tournament_fixture') {
                $stmt = $pdo->prepare("
                    INSERT INTO tournament_fixtures 
                    (home_club_id, away_club_id, tournament_date, venue, status, competition_season_id) 
                    VALUES (?, ?, ?, ?, 'Scheduled', ?)
                ");
                $stmt->execute([$home_club_id, $away_club_id, $tournament_date, $venue, $competition_season_id]);
                $success = 'Tournament fixture added successfully!';
            } else {
                $stmt = $pdo->prepare("
                    UPDATE tournament_fixtures 
                    SET home_club_id=?, away_club_id=?, tournament_date=?, venue=?, competition_season_id=? 
                    WHERE id=?
                ");
                $stmt->execute([$home_club_id, $away_club_id, $tournament_date, $venue, $competition_season_id, $id]);
                $success = 'Tournament fixture updated successfully!';
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'All fields are required, including Competition & Season.';
    }

// ——————————————————————— RECORD / EDIT RESULT ———————————————————————
} elseif ($action === 'record_match_result' || $action === 'edit_match_result') {
    $fixture_id = (int)$_POST['fixture_id'];
    $home_score = (int)$_POST['home_score'];
    $away_score = (int)$_POST['away_score'];

    if ($home_score < 0 || $away_score < 0 || !$fixture_id) {
        $error = 'Invalid scores.';
    } else {
        try {
            if ($action === 'record_match_result') {
                $check = $pdo->prepare("SELECT id FROM tournament_matches WHERE fixture_id = ?");
                $check->execute([$fixture_id]);
                if ($check->fetch()) {
                    $error = 'Result already recorded.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO tournament_matches (fixture_id, home_score, away_score, match_date) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$fixture_id, $home_score, $away_score]);
                    $pdo->prepare("UPDATE tournament_fixtures SET status = 'Completed' WHERE id = ?")->execute([$fixture_id]);
                    $success = 'Tournament result recorded!';
                }
            } else {
                $match_id = (int)$_POST['match_id'];
                $stmt = $pdo->prepare("UPDATE tournament_matches SET home_score = ?, away_score = ? WHERE id = ?");
                $stmt->execute([$home_score, $away_score, $match_id]);
                $success = 'Result updated!';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }

// ——————————————————————— DELETE FIXTURE ———————————————————————
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete_tournament_fixture' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM tournament_fixtures WHERE id = ?")->execute([$id]);
        $success = 'Tournament fixture deleted.';
    } catch (PDOException $e) {
        $error = 'Cannot delete (may have results).';
    }
}

// Redirect
$redirect = '?page=tournaments';
header("Location: $redirect" . (isset($success) ? '&success=' . urlencode($success) : '') . (isset($error) ? '&error=' . urlencode($error) : ''));
exit;