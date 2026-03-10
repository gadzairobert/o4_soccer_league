<?php
require '../config.php';
if (!isset($_SESSION['admin_id'])) header('Location: login.php');

// Fetch unplayed fixtures
$fixtures = $pdo->query("SELECT f.*, ch.name as home_name, ca.name as away_name FROM fixtures f 
                         JOIN clubs ch ON f.home_club_id = ch.id 
                         JOIN clubs ca ON f.away_club_id = ca.id 
                         WHERE f.status = 'Scheduled'")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fixture_id = $_POST['fixture_id'];
    $home_score = $_POST['home_score'];
    $away_score = $_POST['away_score'];
    $match_date = date('Y-m-d H:i:s');  // Or from form

    // Insert match
    $stmt = $pdo->prepare("INSERT INTO matches (fixture_id, home_score, away_score, match_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fixture_id, $home_score, $away_score, $match_date]);

    $match_id = $pdo->lastInsertId();

    // Update fixture status
    $pdo->prepare("UPDATE fixtures SET status = 'Played' WHERE id = ?")->execute([$fixture_id]);

    // Handle goals (loop over form arrays, e.g., home_goals_player_ids[])
    if (!empty($_POST['home_goals_players'])) {
        foreach ($_POST['home_goals_players'] as $player_id) {
            $minute = $_POST['home_goals_minutes'][$player_id] ?? 90;  // Default
            $stmt_goal = $pdo->prepare("INSERT INTO goals (match_id, player_id, minute) VALUES (?, ?, ?)");
            $stmt_goal->execute([$match_id, $player_id, $minute]);
            // Trigger will update player goals
        }
    }
    // Similar for away goals, assists (link to goal_id after insert), cards

    header('Location: dashboard.php?success=result_entered');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Enter Results</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <form method="POST" class="card p-4">
        <h2>Enter Match Result</h2>
        <select name="fixture_id" class="form-control mb-2" required>
            <option value="">Select Fixture</option>
            <?php foreach ($fixtures as $f): ?>
                <option value="<?= $f['id'] ?>"><?= $f['home_name'] ?> vs <?= $f['away_name'] ?> (<?= $f['fixture_date'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="home_score" class="form-control mb-2" placeholder="Home Score" required>
        <input type="number" name="away_score" class="form-control mb-2" placeholder="Away Score" required>
        
        <!-- Dynamic JS for adding goal scorers (use JS to add inputs for players per team) -->
        <div id="goals-section">
            <h4>Home Goals Scorers</h4>
            <!-- JS will populate player selects per team -->
        </div>
        <!-- Similar for away, assists, cards -->
        
        <button type="submit" class="btn btn-primary">Save Result</button>
    </form>
    <script>
    // Simple JS to fetch players via AJAX or pre-load, but for brevity, assume static selects
    </script>
</body>
</html>