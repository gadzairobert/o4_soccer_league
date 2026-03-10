<?php
// admin/actions/fixtures.php
if (!defined('IN_APP')) exit('Direct access not allowed');

$action = $_POST['action'] ?? '';

// ————————————————————————————————————————
// ADD / EDIT FIXTURE
// ————————————————————————————————————————
if ($action === 'add_fixture') {
    $home_club_id = (int)$_POST['home_club_id'];
    $away_club_id = (int)$_POST['away_club_id'];
    $fixture_date = $_POST['fixture_date'];
    $venue        = trim($_POST['venue']);
    $comp_season_id = (int)$_POST['competition_season_id'];
    $matchday     = (int)$_POST['matchday'];

    if ($home_club_id && $away_club_id && $home_club_id != $away_club_id && $fixture_date && $comp_season_id && $matchday > 0) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO fixtures 
                (matchday, home_club_id, away_club_id, fixture_date, venue, status, competition_season_id) 
                VALUES (?, ?, ?, ?, ?, 'Scheduled', ?)
            ");
            $stmt->execute([$matchday, $home_club_id, $away_club_id, $fixture_date, $venue, $comp_season_id]);
            $success = 'Fixture added successfully!';
        } catch (PDOException $e) {
            $error = 'Fixture already exists or invalid data: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill all required fields including Matchday and competition/season.';
    }

} elseif ($action === 'edit_fixture') {
    $id             = (int)$_POST['id'];
    $home_club_id   = (int)$_POST['home_club_id'];
    $away_club_id   = (int)$_POST['away_club_id'];
    $fixture_date   = $_POST['fixture_date'];
    $venue          = trim($_POST['venue']);
    $comp_season_id = (int)$_POST['competition_season_id'];
    $matchday       = (int)$_POST['matchday'];

    if ($id && $home_club_id && $away_club_id && $home_club_id != $away_club_id && $fixture_date && $comp_season_id && $matchday > 0) {
        try {
            $stmt = $pdo->prepare("
                UPDATE fixtures 
                SET matchday=?, home_club_id=?, away_club_id=?, fixture_date=?, venue=?, competition_season_id=? 
                WHERE id=?
            ");
            $stmt->execute([$matchday, $home_club_id, $away_club_id, $fixture_date, $venue, $comp_season_id, $id]);
            $success = 'Fixture updated successfully!';
        } catch (PDOException $e) {
            $error = 'Update failed: ' . $e->getMessage();
        }
    } else {
        $error = 'Invalid data. All fields including Matchday are required.';
    }

// ————————————————————————————————————————
// RECORD MATCH RESULT – USES fixture_date FROM fixtures
// ————————————————————————————————————————
} elseif ($action === 'record_match_result' || $action === 'edit_match_result') {
    $fixture_id  = (int)$_POST['fixture_id'];
    $home_score  = (int)$_POST['home_score'];
    $away_score  = (int)$_POST['away_score'];
    $type        = $_POST['type'] ?? 'fixture';

    $match_table   = ($type === 'tournament') ? 'tournament_matches' : 'matches';
    $fixture_table = ($type === 'tournament') ? 'tournament_fixtures' : 'fixtures';

    if ($home_score < 0 || $away_score < 0 || !$fixture_id) {
        $error = 'Invalid scores or fixture.';
        header("Location: ?page=fixtures&error=" . urlencode($error));
        exit;
    }

    try {
        if ($action === 'record_match_result') {
            $check = $pdo->prepare("SELECT id FROM $match_table WHERE fixture_id = ?");
            $check->execute([$fixture_id]);
            if ($check->fetch()) {
                $error = 'Result already recorded.';
                header("Location: ?page=fixtures&error=" . urlencode($error));
                exit;
            }

            // Get fixture_date from fixtures table
            $fStmt = $pdo->prepare("SELECT fixture_date FROM $fixture_table WHERE id = ?");
            $fStmt->execute([$fixture_id]);
            $fixture = $fStmt->fetch(PDO::FETCH_ASSOC);

            if (!$fixture || empty($fixture['fixture_date'])) {
                $error = 'Fixture date not found.';
                header("Location: ?page=fixtures&error=" . urlencode($error));
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO $match_table 
                (fixture_id, home_score, away_score, match_date) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$fixture_id, $home_score, $away_score, $fixture['fixture_date']]);

            $pdo->prepare("UPDATE $fixture_table SET status = 'Completed' WHERE id = ?")
                ->execute([$fixture_id]);

            $success = 'Match result recorded successfully!';
        } else {
            $match_id = (int)$_POST['match_id'];
            if (!$match_id) {
                $error = 'Match ID missing.';
                header("Location: ?page=fixtures&error=" . urlencode($error));
                exit;
            }
            $stmt = $pdo->prepare("UPDATE $match_table SET home_score = ?, away_score = ? WHERE id = ?");
            $stmt->execute([$home_score, $away_score, $match_id]);
            $success = 'Match result updated successfully!';
        }
    } catch (Exception $e) {
        $error = 'Database error: ' . $e->getMessage();
    }

    $redirect = ($type === 'tournament') ? '?page=tournaments' : '?page=fixtures';
    header("Location: $redirect" . (isset($success) ? '&success=' . urlencode($success) : '&error=' . urlencode($error ?? '')));
    exit;

// ————————————————————————————————————————
// DELETE FIXTURE – FIXED & SAFE
// ————————————————————————————————————————
} elseif ($action === 'delete_fixture' && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        // Prevent deletion if result exists
        $check = $pdo->prepare("SELECT 1 FROM matches WHERE fixture_id = ? LIMIT 1");
        $check->execute([$id]);
        if ($check->fetch()) {
            $error = 'Cannot delete: Match result already recorded.';
        } else {
            $pdo->prepare("DELETE FROM fixtures WHERE id = ?")->execute([$id]);
            $success = 'Fixture deleted successfully.';
        }
    } catch (PDOException $e) {
        $error = 'Delete failed: ' . $e->getMessage();
    }
}

// ————————————————————————————————————————
// FINAL REDIRECT
// ————————————————————————————————————————
$redirect = $_SERVER['HTTP_REFERER'] ?? '?page=fixtures';
if (isset($success)) {
    header("Location: $redirect&success=" . urlencode($success));
} elseif (isset($error)) {
    header("Location: $redirect&error=" . urlencode($error));
} else {
    header("Location: $redirect");
}
exit;