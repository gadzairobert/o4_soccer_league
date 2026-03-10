<?php
require '../../config.php';
if (!isset($_SESSION['admin_id'])) die('Unauthorized');

$match_id   = (int)($_GET['match_id'] ?? 0);
$home_club  = (int)($_GET['home_club'] ?? 0);
$away_club  = (int)($_GET['away_club'] ?? 0);

if (!$match_id || !$home_club || !$away_club) die('Invalid request');

$stmt = $pdo->prepare("
    SELECT tm.*, tf.tournament_date, 
           ch.name AS home_name, ca.name AS away_name,
           ch.logo AS home_logo, ca.logo AS away_logo
    FROM tournament_matches tm
    JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
    JOIN clubs ch ON tf.home_club_id = ch.id
    JOIN clubs ca ON tf.away_club_id = ca.id
    WHERE tm.id = ?
");
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$match) die('Match not found');

// Count recorded goals
$home_goals_recorded = (int)$pdo->query("SELECT COUNT(*) FROM tournament_goals tg JOIN players p ON tg.player_id = p.id WHERE tg.match_id = $match_id AND p.club_id = $home_club")->fetchColumn();
$away_goals_recorded = (int)$pdo->query("SELECT COUNT(*) FROM tournament_goals tg JOIN players p ON tg.player_id = p.id WHERE tg.match_id = $match_id AND p.club_id = $away_club")->fetchColumn();
?>

<div class="container-fluid">
    <div class="text-center mb-4">
        <h4 class="mb-0">
            <?php if ($match['home_logo']): ?>
                <img src="../uploads/clubs/<?= htmlspecialchars($match['home_logo']) ?>" width="40" class="me-2 align-text-bottom">
            <?php endif; ?>
            <strong><?= htmlspecialchars($match['home_name']) ?></strong>
            <span class="mx-3 fs-3 fw-bold"><?= $match['home_score'] ?> - <?= $match['away_score'] ?></span>
            <strong><?= htmlspecialchars($match['away_name']) ?></strong>
            <?php if ($match['away_logo']): ?>
                <img src="../uploads/clubs/<?= htmlspecialchars($match['away_logo']) ?>" width="40" class="ms-2 align-text-bottom">
            <?php endif; ?>
        </h4>
        <small class="text-muted">Tournament Match • <?= date('d M Y', strtotime($match['tournament_date'])) ?></small>
    </div>

    <div class="row">
        <!-- HOME TEAM - LEFT -->
        <div class="col-md-6">
            <div class="bg-light p-3 rounded mb-3 text-center border border-success">
                <strong>HOME: <?= htmlspecialchars($match['home_name']) ?></strong>
                <span class="badge bg-<?= $home_goals_recorded >= $match['home_score'] ? 'success' : 'warning' ?> ms-2">
                    Goals: <?= $home_goals_recorded ?>/<?= $match['home_score'] ?>
                </span>
            </div>

            <!-- Add Goal (Home) -->
            <?php if ($home_goals_recorded < $match['home_score']): ?>
            <form method="POST" action="actions/add_tournament_goal.php" class="mb-3 bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="match_id" value="<?= $match_id ?>">
                <h6 class="text-success mb-2">Add Goal (Home)</h6>
                <div class="row g-2">
                    <div class="col"><select name="player_id" class="form-select form-select-sm" required><option value="">Scorer</option><?php
                        $stmt = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? ORDER BY name");
                        $stmt->execute([$home_club]);
                        while ($p = $stmt->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                    ?></select></div>
                    <div class="col-3"><input type="number" name="minute" class="form-control form-control-sm" min="1" max="120" placeholder="Min" required></div>
                    <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_penalty"><label class="form-check-label small">Pen</label></div></div>
                    <div class="col-auto"><button type="submit" class="btn btn-success btn-sm">Add</button></div>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-success small mb-3">All home goals recorded</div>
            <?php endif; ?>

            <!-- Add Assist (Home) -->
            <form method="POST" action="actions/add_tournament_assist.php" class="mb-3 bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="match_id" value="<?= $match_id ?>">
                <h6 class="text-primary mb-2">Add Assist (Home)</h6>
                <div class="row g-2">
                    <div class="col"><select name="goal_id" class="form-select form-select-sm" required><option value="">Goal</option><?php
                        $stmt = $pdo->prepare("SELECT tg.id, p.name, tg.minute FROM tournament_goals tg JOIN players p ON tg.player_id = p.id WHERE tg.match_id = ? AND p.club_id = ? ORDER BY tg.minute");
                        $stmt->execute([$match_id, $home_club]);
                        while ($g = $stmt->fetch()) echo "<option value='{$g['id']}'>{$g['name']} {$g['minute']}'</option>";
                    ?></select></div>
                    <div class="col"><select name="player_id" class="form-select form-select-sm" required><option value="">Assister</option><?php
                        $stmt = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? ORDER BY name");
                        $stmt->execute([$home_club]);
                        while ($p = $stmt->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                    ?></select></div>
                    <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Add</button></div>
                </div>
            </form>

            <!-- Add Card (Home) -->
            <form method="POST" action="actions/add_tournament_card.php" class="mb-3 bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="match_id" value="<?= $match_id ?>">
                <h6 class="text-warning mb-2">Add Card (Home)</h6>
                <div class="row g-2">
                    <div class="col"><select name="player_id" class="form-select form-select-sm" required><option value="">Player</option><?php
                        $stmt->execute([$home_club]);
                        while ($p = $stmt->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                    ?></select></div>
                    <div class="col-4"><select name="card_type" class="form-select form-select-sm"><option value="yellow">Yellow</option><option value="red">Red</option></select></div>
                    <div class="col-3"><input type="number" name="minute" class="form-control form-control-sm" min="1" max="120" placeholder="Min" required></div>
                    <div class="col-auto"><button type="submit" class="btn btn-warning btn-sm">Add</button></div>
                </div>
            </form>

            <!-- Clean Sheet (Home) -->
            <?php if ($match['away_score'] == 0 && $pdo->query("SELECT 1 FROM tournament_clean_sheets WHERE match_id = $match_id AND player_id IN (SELECT id FROM players WHERE club_id = $home_club)")->rowCount() == 0): ?>
                <form method="POST" action="actions/add_tournament_cleansheet.php" class="text-center mb-3">
                    <input type="hidden" name="match_id" value="<?= $match_id ?>">
                    <input type="hidden" name="club_id" value="<?= $home_club ?>">
                    <button type="submit" class="btn btn-info btn-sm w-100">Award Clean Sheet</button>
                </form>
            <?php endif; ?>

            <!-- HOME EVENTS TIMELINE -->
            <div class="mt-4">
                <h6 class="text-success">Home Events</h6>
                <div class="border rounded p-3 bg-light" style="max-height:380px; overflow-y:auto; font-size:0.9em;">
                    <?php
                    $events = $pdo->prepare("
                        SELECT 'goal' type, g.id, g.minute, p.name player, g.is_penalty, 'goal' event
                        FROM tournament_goals g JOIN players p ON g.player_id = p.id WHERE g.match_id = ? AND p.club_id = ?
                        UNION ALL
                        SELECT 'card', c.id, c.minute, p.name, 0, c.card_type
                        FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = ? AND p.club_id = ?
                        UNION ALL
                        SELECT 'cleansheet', cs.id, 999, pl.name, 0, 'cleansheet'
                        FROM tournament_clean_sheets cs JOIN players pl ON cs.player_id = pl.id WHERE cs.match_id = ? AND pl.club_id = ?
                        ORDER BY minute DESC
                    ");
                    $events->execute([$match_id, $home_club, $match_id, $home_club, $match_id, $home_club]);
                    if ($events->rowCount() == 0) {
                        echo "<small class='text-muted'>No events yet</small>";
                    } else {
                        while ($e = $events->fetch()) {
                            if ($e['type'] === 'goal') {
                                $pen = $e['is_penalty'] ? ' (P)' : '';
                                echo "<div class='d-flex justify-content-between align-items-center py-1 border-bottom'>
                                        <span class='text-success'><strong>{$e['minute']}'</strong> Goal — {$e['player']}$pen</span>
                                        <a href='actions/delete_tournament_goal.php?id={$e['id']}&match_id={$match_id}' class='text-danger small' onclick='return confirm(\"Delete?\")'>Delete</a>
                                      </div>";
                            } elseif ($e['type'] === 'card') {
                                $color = $e['event'] === 'yellow' ? 'warning' : 'danger';
                                echo "<div class='d-flex justify-content-between align-items-center py-1 border-bottom'>
                                        <span class='text-$color'><strong>{$e['minute']}'</strong> " . ucfirst($e['event']) . " Card — {$e['player']}</span>
                                        <a href='actions/delete_tournament_card.php?id={$e['id']}&match_id={$match_id}' class='text-danger small' onclick='return confirm(\"Delete?\")'>Delete</a>
                                      </div>";
                            } elseif ($e['type'] === 'cleansheet') {
                                echo "<div class='d-flex justify-content-between align-items-center py-1 text-info border-bottom'>
                                        <span>Clean Sheet — {$e['player']}</span>
                                        <a href='actions/delete_tournament_cleansheet.php?id={$e['id']}' class='text-danger small' onclick='return confirm(\"Remove?\")'>Delete</a>
                                      </div>";
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- AWAY TEAM - RIGHT -->
        <div class="col-md-6">
            <div class="bg-light p-3 rounded mb-3 text-center border border-danger">
                <strong>AWAY: <?= htmlspecialchars($match['away_name']) ?></strong>
                <span class="badge bg-<?= $away_goals_recorded >= $match['away_score'] ? 'success' : 'warning' ?> ms-2">
                    Goals: <?= $away_goals_recorded ?>/<?= $match['away_score'] ?>
                </span>
            </div>

            <!-- Add Goal (Away) -->
            <?php if ($away_goals_recorded < $match['away_score']): ?>
            <form method="POST" action="actions/add_tournament_goal.php" class="mb-3 bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="match_id" value="<?= $match_id ?>">
                <h6 class="text-danger mb-2">Add Goal (Away)</h6>
                <div class="row g-2">
                    <div class="col"><select name="player_id" class="form-select form-select-sm" required><option value="">Scorer</option><?php
                        $stmt = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? ORDER BY name");
                        $stmt->execute([$away_club]);
                        while ($p = $stmt->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                    ?></select></div>
                    <div class="col-3"><input type="number" name="minute" class="form-control form-control-sm" min="1" max="120" placeholder="Min" required></div>
                    <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_penalty"><label class="form-check-label small">Pen</label></div></div>
                    <div class="col-auto"><button type="submit" class="btn btn-danger btn-sm text-white">Add</button></div>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-success small mb-3">All away goals recorded</div>
            <?php endif; ?>

            <!-- Add Assist (Away) -->
            <form method="POST" action="actions/add_tournament_assist.php" class="mb-3 bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="match_id" value="<?= $match_id ?>">
                <h6 class="text-primary mb-2">Add Assist (Away)</h6>
                <div class="row g-2">
                    <div class="col"><select name="goal_id" class="form-select form-select-sm" required><option value="">Goal</option><?php
                        $stmt = $pdo->prepare("SELECT tg.id, p.name, tg.minute FROM tournament_goals tg JOIN players p ON tg.player_id = p.id WHERE tg.match_id = ? AND p.club_id = ? ORDER BY tg.minute");
                        $stmt->execute([$match_id, $away_club]);
                        while ($g = $stmt->fetch()) echo "<option value='{$g['id']}'>{$g['name']} {$g['minute']}'</option>";
                    ?></select></div>
                    <div class="col"><select name="player_id" class="form-select form-select-sm" required><option value="">Assister</option><?php
                        $stmt = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? ORDER BY name");
                        $stmt->execute([$away_club]);
                        while ($p = $stmt->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                    ?></select></div>
                    <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Add</button></div>
                </div>
            </form>

            <!-- Add Card (Away) -->
            <form method="POST" action="actions/add_tournament_card.php" class="mb-3 bg-white p-3 rounded shadow-sm border">
                <input type="hidden" name="match_id" value="<?= $match_id ?>">
                <h6 class="text-warning mb-2">Add Card (Away)</h6>
                <div class="row g-2">
                    <div class="col"><select name="player_id" class="form-select form-select-sm" required><option value="">Player</option><?php
                        $stmt->execute([$away_club]);
                        while ($p = $stmt->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                    ?></select></div>
                    <div class="col-4"><select name="card_type" class="form-select form-select-sm"><option value="yellow">Yellow</option><option value="red">Red</option></select></div>
                    <div class="col-3"><input type="number" name="minute" class="form-control form-control-sm" min="1" max="120" placeholder="Min" required></div>
                    <div class="col-auto"><button type="submit" class="btn btn-warning btn-sm text-white">Add</button></div>
                </div>
            </form>

            <!-- Clean Sheet (Away) -->
            <?php if ($match['home_score'] == 0 && $pdo->query("SELECT 1 FROM tournament_clean_sheets WHERE match_id = $match_id AND player_id IN (SELECT id FROM players WHERE club_id = $away_club)")->rowCount() == 0): ?>
                <form method="POST" action="actions/add_tournament_cleansheet.php" class="text-center mb-3">
                    <input type="hidden" name="match_id" value="<?= $match_id ?>">
                    <input type="hidden" name="club_id" value="<?= $away_club ?>">
                    <button type="submit" class="btn btn-info btn-sm w-100">Award Clean Sheet</button>
                </form>
            <?php endif; ?>

            <!-- AWAY EVENTS TIMELINE -->
            <div class="mt-4">
                <h6 class="text-danger">Away Events</h6>
                <div class="border rounded p-3 bg-light" style="max-height:380px; overflow-y:auto; font-size:0.9em;">
                    <?php
                    $events->execute([$match_id, $away_club, $match_id, $away_club, $match_id, $away_club]);
                    if ($events->rowCount() == 0) {
                        echo "<small class='text-muted'>No events yet</small>";
                    } else {
                        while ($e = $events->fetch()) {
                            if ($e['type'] === 'goal') {
                                $pen = $e['is_penalty'] ? ' (P)' : '';
                                echo "<div class='d-flex justify-content-between align-items-center py-1 border-bottom'>
                                        <span class='text-danger'><strong>{$e['minute']}'</strong> Goal — {$e['player']}$pen</span>
                                        <a href='actions/delete_tournament_goal.php?id={$e['id']}&match_id={$match_id}' class='text-danger small' onclick='return confirm(\"Delete?\")'>Delete</a>
                                      </div>";
                            } elseif ($e['type'] === 'card') {
                                $color = $e['event'] === 'yellow' ? 'warning' : 'danger';
                                echo "<div class='d-flex justify-content-between align-items-center py-1 border-bottom'>
                                        <span class='text-$color'><strong>{$e['minute']}'</strong> " . ucfirst($e['event']) . " Card — {$e['player']}</span>
                                        <a href='actions/delete_tournament_card.php?id={$e['id']}&match_id={$match_id}' class='text-danger small' onclick='return confirm(\"Delete?\")'>Delete</a>
                                      </div>";
                            } elseif ($e['type'] === 'cleansheet') {
                                echo "<div class='d-flex justify-content-between align-items-center py-1 text-info border-bottom'>
                                        <span>Clean Sheet — {$e['player']}</span>
                                        <a href='actions/delete_tournament_cleansheet.php?id={$e['id']}' class='text-danger small' onclick='return confirm(\"Remove?\")'>Delete</a>
                                      </div>";
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>