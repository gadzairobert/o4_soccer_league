<?php
// admin/actions/competitions.php
if (!defined('IN_APP')) exit('Direct access not allowed');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ——————————————————————— ADD COMPETITION SEASON ———————————————————————
if ($action === 'add_competition') {
    $name            = trim($_POST['name']);
    $competition_name = trim($_POST['competition_name']);
    $season          = (int)$_POST['season'];
    $type            = $_POST['type']; // league, cup, international
    $country         = trim($_POST['country'] ?? '');
    $logo            = trim($_POST['logo'] ?? '');
    $is_current      = isset($_POST['is_current']) ? 1 : 0;

    if ($name && $competition_name && $season && in_array($type, ['league', 'cup', 'international'])) {
        try {
            // Optional: unset previous current season for same competition
            if ($is_current) {
                $pdo->prepare("UPDATE competition_seasons SET is_current = 0 WHERE competition_name = ?")->execute([$competition_name]);
            }

            $stmt = $pdo->prepare("
                INSERT INTO competition_seasons 
                (name, competition_name, season, type, country, logo, is_current) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $competition_name, $season, $type, $country, $logo, $is_current]);

            $success = 'Competition season added successfully!';
        } catch (PDOException $e) {
            $error = 'Error: This competition + season may already exist.';
        }
    } else {
        $error = 'Please fill all required fields.';
    }

// ——————————————————————— EDIT COMPETITION SEASON ———————————————————————
} elseif ($action === 'edit_competition') {
    $id              = (int)$_POST['id'];
    $name            = trim($_POST['name']);
    $competition_name = trim($_POST['competition_name']);
    $season          = (int)$_POST['season'];
    $type            = $_POST['type'];
    $country         = trim($_POST['country'] ?? '');
    $logo            = trim($_POST['logo'] ?? '');
    $is_current      = isset($_POST['is_current']) ? 1 : 0;

    if ($id && $name && $competition_name && $season && in_array($type, ['league', 'cup', 'international'])) {
        try {
            if ($is_current) {
                $pdo->prepare("UPDATE competition_seasons SET is_current = 0 WHERE competition_name = ? AND id != ?")
                    ->execute([$competition_name, $id]);
            }

            $stmt = $pdo->prepare("
                UPDATE competition_seasons 
                SET name = ?, competition_name = ?, season = ?, type = ?, country = ?, logo = ?, is_current = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $competition_name, $season, $type, $country, $logo, $is_current, $id]);

            $success = 'Competition season updated!';
        } catch (PDOException $e) {
            $error = 'Update failed. Check for duplicates.';
        }
    } else {
        $error = 'Invalid data submitted.';
    }

// ——————————————————————— DELETE COMPETITION SEASON ———————————————————————
} elseif ($action === 'delete_competition' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Optional: Prevent delete if fixtures exist
        $check = $pdo->prepare("
            SELECT COUNT(*) FROM fixtures WHERE competition_season_id = ? 
            UNION ALL 
            SELECT COUNT(*) FROM tournament_fixtures WHERE competition_season_id = ?
        ");
        $check->execute([$id, $id]);
        $used = (int)$check->fetchColumn() + (int)$check->fetchColumn();

        if ($used > 0) {
            $error = 'Cannot delete: This competition has fixtures attached.';
        } else {
            $pdo->prepare("DELETE FROM competition_seasons WHERE id = ?")->execute([$id]);
            $success = 'Competition season deleted.';
        }
    } catch (Exception $e) {
        $error = 'Delete failed.';
    }
}

// Redirect back
$redirect = $_SERVER['HTTP_REFERER'] ?? '?page=competitions';
if (isset($success)) {
    header("Location: $redirect&success=" . urlencode($success));
} elseif (isset($error)) {
    header("Location: $redirect&error=" . urlencode($error));
} else {
    header("Location: $redirect");
}
exit;