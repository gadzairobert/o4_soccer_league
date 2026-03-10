<?php
require '../../config.php';

// Prevent double constant/session errors when included from actions
if (!defined('IN_APP')) {
    define('IN_APP', true);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Show flash messages inside the modal
if (!empty($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show">
            ' . htmlspecialchars($_SESSION['success']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show">
            ' . htmlspecialchars($_SESSION['error']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['error']);
}
if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized');
}

$match_id     = (int)($_GET['match_id'] ?? 0);
$home_club_id = (int)($_GET['home_club'] ?? 0);
$away_club_id = (int)($_GET['away_club'] ?? 0);

if ($match_id <= 0 || $home_club_id <= 0 || $away_club_id <= 0) {
    echo '<div class="alert alert-danger">Invalid match data.</div>';
    exit;
}

// Fetch match + club details
$stmt = $pdo->prepare("
    SELECT m.*, ch.name AS home_name, ca.name AS away_name,
           ch.logo AS home_logo, ca.logo AS away_logo
    FROM matches m
    JOIN fixtures f ON m.fixture_id = f.id
    JOIN clubs ch ON f.home_club_id = ch.id
    JOIN clubs ca ON f.away_club_id = ca.id
    WHERE m.id = ?
");
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    die('<div class="alert alert-danger">Match not found.</div>');
}

// Players – FIXED SQL INJECTION
$stmt = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? ORDER BY name");
$stmt->execute([$home_club_id]);
$home_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt->execute([$away_club_id]);
$away_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Goals
$goals_q = $pdo->prepare("SELECT g.*, p.name, p.club_id FROM goals g JOIN players p ON g.player_id = p.id WHERE g.match_id = ? ORDER BY g.minute");
$goals_q->execute([$match_id]);
$goals = $goals_q->fetchAll();

$home_goals_count = count(array_filter($goals, fn($g) => $g['club_id'] == $home_club_id));
$away_goals_count = count(array_filter($goals, fn($g) => $g['club_id'] == $away_club_id));

// Assists (goal_id => assister name)
$assist_stmt = $pdo->prepare("
    SELECT g.id AS goal_id, p.name AS assister_name
    FROM assists a
    JOIN goals g ON a.goal_id = g.id
    JOIN players p ON a.player_id = p.id
    WHERE g.match_id = ?
");
$assist_stmt->execute([$match_id]);
$assists = $assist_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Cards
$cards_q = $pdo->prepare("SELECT c.*, p.name, p.club_id FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = ? ORDER BY c.minute");
$cards_q->execute([$match_id]);
$cards = $cards_q->fetchAll();

// Clean Sheets
$clean_sheets_q = $pdo->prepare("SELECT cs.*, p.name, p.club_id FROM clean_sheets cs JOIN players p ON cs.player_id = p.id WHERE cs.match_id = ?");
$clean_sheets_q->execute([$match_id]);
$clean_sheets = $clean_sheets_q->fetchAll();

$home_cs = !empty(array_filter($clean_sheets, fn($cs) => $cs['club_id'] == $home_club_id));
$away_cs = !empty(array_filter($clean_sheets, fn($cs) => $cs['club_id'] == $away_club_id));
?>

<!-- Alert Area -->
<div id="modalAlert"></div>

<div class="text-center mb-4">
    <h4 class="fw-bold">
        <?php if ($match['home_logo']): ?>
            <img src="../uploads/clubs/<?=htmlspecialchars($match['home_logo'])?>" width="45" class="me-2 rounded">
        <?php endif; ?>
        <span class="text-success"><?=htmlspecialchars($match['home_name'])?></span>
        <span class="mx-4 fs-2"><?=$match['home_score']?> – <?=$match['away_score']?></span>
        <span class="text-danger"><?=htmlspecialchars($match['away_name'])?></span>
        <?php if ($match['away_logo']): ?>
            <img src="../uploads/clubs/<?=htmlspecialchars($match['away_logo'])?>" width="45" class="ms-2 rounded">
        <?php endif; ?>
    </h4>
</div>

<div class="row">
    <!-- ==================== HOME TEAM ==================== -->
    <div class="col-lg-6">
        <h5 class="text-center text-success mb-3 border-bottom pb-2">
            HOME: <?=htmlspecialchars($match['home_name'])?>
        </h5>

        <!-- Add Goal (Home) -->
        <?php if ($home_goals_count < $match['home_score']): ?>
        <div class="card mb-3 border-success">
            <div class="card-header bg-success text-white small">Add Goal (Home)</div>
            <div class="card-body py-2">
                <form method="POST" action="actions/add_goal.php" class="ajax-form row g-2">
                    <input type="hidden" name="match_id" value="<?=$match_id?>">
                    <div class="col-6">
                        <select name="player_id" class="form-select form-select-sm" required>
                            <option value="">Scorer</option>
                            <?php foreach($home_players as $p): ?>
                                <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <input type="number" name="minute" class="form-control form-control-sm" placeholder="Min" min="1" max="120" required>
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-success btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-success small mb-3">Home goals complete</div>
        <?php endif; ?>

        <!-- Add Card (Home) -->
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark small">Add Card (Home)</div>
            <div class="card-body py-2">
                <form method="POST" action="actions/add_card.php" class="ajax-form row g-2">
                    <input type="hidden" name="match_id" value="<?=$match_id?>">
                    <div class="col-5">
                        <select name="player_id" class="form-select form-select-sm" required>
                            <option value="">Player</option>
                            <?php foreach($home_players as $p): ?>
                                <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="card_type" class="form-select form-select-sm" required>
                            <option value="yellow">Yellow Card</option>
                            <option value="red">Red Card</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="number" name="minute" class="form-control form-control-sm" placeholder="Min" min="1" max="120" required>
                    </div>
                    <div class="col-1">
                        <button type="submit" class="btn btn-warning btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Clean Sheet (Home) -->
        <?php if ($match['away_score'] == 0 && !$home_cs): ?>
            <div class="card mb-3 border-info">
                <div class="card-header bg-info text-white small">Add Clean Sheet (Home Team)</div>
                <div class="card-body py-2">
                    <form method="POST" action="actions/add_cleansheet.php" class="ajax-form">
                        <input type="hidden" name="match_id" value="<?=$match_id?>">
                        <input type="hidden" name="club_id" value="<?=$home_club_id?>">
                        <div class="row g-2 align-items-center">
                            <div class="col-8">
                                <?php
                                $keepers = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? AND position = 'GK' ORDER BY name");
                                $keepers->execute([$home_club_id]);
                                $keeper_list = $keepers->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if (count($keeper_list) > 1): ?>
                                    <select name="player_id" class="form-select form-select-sm" required>
                                        <option value="">Select Goalkeeper</option>
                                        <?php foreach($keeper_list as $k): ?>
                                            <option value="<?=$k['id']?>"><?=htmlspecialchars($k['name'])?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif (count($keeper_list) == 1): ?>
                                    <input type="hidden" name="player_id" value="<?=$keeper_list[0]['id']?>">
                                    <div class="small text-muted fw-500">Keeper: <?=htmlspecialchars($keeper_list[0]['name'])?></div>
                                <?php else: ?>
                                    <div class="text-danger small">No goalkeeper found in squad!</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-info btn-sm w-100">+ Add Clean Sheet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php elseif ($match['away_score'] > 0 && $home_cs): ?>
            <div class="alert alert-warning small mb-3">Clean sheet removed — opponent scored</div>
        <?php elseif ($home_cs): ?>
            <div class="alert alert-info small mb-3">Clean sheet awarded</div>
        <?php endif; ?>

        <!-- Home Goals List + NEW ASSIST FORM -->
        <?php $home_goals_list = array_filter($goals, fn($g) => $g['club_id'] == $home_club_id); ?>
        <?php if ($home_goals_list): ?>
            <h6 class="text-success">Goals & Assists</h6>
            <table class="table table-sm table-bordered">
                <thead class="table-light"><tr><th>Scorer</th><th>Min</th><th>Assist</th><th></th></tr></thead>
                <tbody>
                    <?php foreach($home_goals_list as $g): ?>
                    <tr>
                        <td><strong><?=htmlspecialchars($g['name'])?></strong></td>
                        <td><?=$g['minute']?>' <?= $g['is_penalty'] ? '(P)' : '' ?></td>
                        <td>
                            <?php if (!empty($assists[$g['id']])): ?>
                                <?=htmlspecialchars($assists[$g['id']])?>
                            <?php else: ?>
                                <form method="POST" action="actions/add_assist.php" class="d-inline">
                                    <input type="hidden" name="goal_id" value="<?=$g['id']?>">
                                    <input type="hidden" name="match_id" value="<?=$match_id?>">
                                    <select name="player_id" class="form-select form-select-sm d-inline-block" style="width:140px;" required>
                                        <option value="">+ Assist</option>
                                        <?php foreach($home_players as $p): ?>
                                            <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-1">Add</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="actions/delete_goal.php?id=<?=$g['id']?>&match_id=<?=$match_id?>" class="text-danger ajax-delete">×</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Home Cards -->
        <?php $home_cards = array_filter($cards, fn($c) => $c['club_id'] == $home_club_id); ?>
        <?php if ($home_cards): ?>
            <h6 class="text-warning">Cards</h6>
            <ul class="list-group mb-3">
                <?php foreach($home_cards as $c): ?>
                <li class="list-group-item d-flex justify-content-between small py-1">
                    <span>
                        <strong><?=htmlspecialchars($c['name'])?></strong>
                        <span class="badge bg-<?= $c['card_type'] === 'red' ? 'danger' : 'warning' ?> ms-2">
                            <?= $c['card_type'] === 'red' ? 'Red Card' : 'Yellow Card' ?>
                        </span>
                        <?=$c['minute']?>'
                    </span>
                    <a href="actions/delete_card.php?id=<?=$c['id']?>&match_id=<?=$match_id?>" class="text-danger ajax-delete">×</a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Home Clean Sheet Display -->
        <?php if ($home_cs): ?>
            <h6 class="text-info">Clean Sheet Awarded</h6>
            <ul class="list-group mb-3">
                <?php foreach($clean_sheets as $cs): if ($cs['club_id'] != $home_club_id) continue; ?>
                <li class="list-group-item d-flex justify-content-between small py-1">
                    <span><strong><?=htmlspecialchars($cs['name'])?></strong> (Clean Sheet)</span>
                    <a href="actions/delete_cleansheet.php?id=<?=$cs['id']?>&match_id=<?=$match_id?>" class="text-danger ajax-delete">×</a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- ==================== AWAY TEAM ==================== -->
    <div class="col-lg-6">
        <h5 class="text-center text-danger mb-3 border-bottom pb-2">
            AWAY: <?=htmlspecialchars($match['away_name'])?>
        </h5>

        <!-- Add Goal (Away) -->
        <?php if ($away_goals_count < $match['away_score']): ?>
        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white small">Add Goal (Away)</div>
            <div class="card-body py-2">
                <form method="POST" action="actions/add_goal.php" class="ajax-form row g-2">
                    <input type="hidden" name="match_id" value="<?=$match_id?>">
                    <div class="col-6">
                        <select name="player_id" class="form-select form-select-sm" required>
                            <option value="">Scorer</option>
                            <?php foreach($away_players as $p): ?>
                                <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <input type="number" name="minute" class="form-control form-control-sm" placeholder="Min" min="1" max="120" required>
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-danger btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-danger small mb-3">Away goals complete</div>
        <?php endif; ?>

        <!-- Add Card (Away) -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white small">Add Card (Away)</div>
            <div class="card-body py-2">
                <form method="POST" action="actions/add_card.php" class="ajax-form row g-2">
                    <input type="hidden" name="match_id" value="<?=$match_id?>">
                    <div class="col-5">
                        <select name="player_id" class="form-select form-select-sm" required>
                            <option value="">Player</option>
                            <?php foreach($away_players as $p): ?>
                                <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="card_type" class="form-select form-select-sm" required>
                            <option value="yellow">Yellow Card</option>
                            <option value="red">Red Card</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="number" name="minute" class="form-control form-control-sm" placeholder="Min" min="1" max="120" required>
                    </div>
                    <div class="col-1">
                        <button type="submit" class="btn btn-dark btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Clean Sheet (Away) -->
        <?php if ($match['home_score'] == 0 && !$away_cs): ?>
            <div class="card mb-3 border-info">
                <div class="card-header bg-info text-white small">Add Clean Sheet (Away Team)</div>
                <div class="card-body py-2">
                    <form method="POST" action="actions/add_cleansheet.php" class="ajax-form">
                        <input type="hidden" name="match_id" value="<?=$match_id?>">
                        <input type="hidden" name="club_id" value="<?=$away_club_id?>">
                        <div class="row g-2 align-items-center">
                            <div class="col-8">
                                <?php
                                $keepers = $pdo->prepare("SELECT id, name FROM players WHERE club_id = ? AND position = 'GK' ORDER BY name");
                                $keepers->execute([$away_club_id]);
                                $keeper_list = $keepers->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if (count($keeper_list) > 1): ?>
                                    <select name="player_id" class="form-select form-select-sm" required>
                                        <option value="">Select Goalkeeper</option>
                                        <?php foreach($keeper_list as $k): ?>
                                            <option value="<?=$k['id']?>"><?=htmlspecialchars($k['name'])?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif (count($keeper_list) == 1): ?>
                                    <input type="hidden" name="player_id" value="<?=$keeper_list[0]['id']?>">
                                    <div class="small text-muted fw-500">Keeper: <?=htmlspecialchars($keeper_list[0]['name'])?></div>
                                <?php else: ?>
                                    <div class="text-danger small">No goalkeeper found in squad!</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-info btn-sm w-100">+ Add Clean Sheet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php elseif ($match['home_score'] > 0 && $away_cs): ?>
            <div class="alert alert-warning small mb-3">Clean sheet removed — opponent scored</div>
        <?php elseif ($away_cs): ?>
            <div class="alert alert-info small mb-3">Clean sheet awarded</div>
        <?php endif; ?>

        <!-- Away Goals List + ASSIST FORM -->
        <?php $away_goals_list = array_filter($goals, fn($g) => $g['club_id'] == $away_club_id); ?>
        <?php if ($away_goals_list): ?>
            <h6 class="text-danger">Goals & Assists</h6>
            <table class="table table-sm table-bordered">
                <thead class="table-light"><tr><th>Scorer</th><th>Min</th><th>Assist</th><th></th></tr></thead>
                <tbody>
                    <?php foreach($away_goals_list as $g): ?>
                    <tr>
                        <td><strong><?=htmlspecialchars($g['name'])?></strong></td>
                        <td><?=$g['minute']?>' <?= $g['is_penalty'] ? '(P)' : '' ?></td>
                        <td>
                            <?php if (!empty($assists[$g['id']])): ?>
                                <?=htmlspecialchars($assists[$g['id']])?>
                            <?php else: ?>
                                <form method="POST" action="actions/add_assist.php" class="d-inline">
                                    <input type="hidden" name="goal_id" value="<?=$g['id']?>">
                                    <input type="hidden" name="match_id" value="<?=$match_id?>">
                                    <select name="player_id" class="form-select form-select-sm d-inline-block" style="width:140px;" required>
                                        <option value="">+ Assist</option>
                                        <?php foreach($away_players as $p): ?>
                                            <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-1">Add</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="actions/delete_goal.php?id=<?=$g['id']?>&match_id=<?=$match_id?>" class="text-danger ajax-delete">×</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Away Cards -->
        <?php $away_cards = array_filter($cards, fn($c) => $c['club_id'] == $away_club_id); ?>
        <?php if ($away_cards): ?>
            <h6 class="text-dark">Cards</h6>
            <ul class="list-group mb-3">
                <?php foreach($away_cards as $c): ?>
                <li class="list-group-item d-flex justify-content-between small py-1">
                    <span>
                        <strong><?=htmlspecialchars($c['name'])?></strong>
                        <span class="badge bg-<?= $c['card_type'] === 'red' ? 'danger' : 'warning' ?> ms-2">
                            <?= $c['card_type'] === 'red' ? 'Red Card' : 'Yellow Card' ?>
                        </span>
                        <?=$c['minute']?>'
                    </span>
                    <a href="actions/delete_card.php?id=<?=$c['id']?>&match_id=<?=$match_id?>" class="text-danger ajax-delete">×</a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Away Clean Sheet Display -->
        <?php if ($away_cs): ?>
            <h6 class="text-info">Clean Sheet Awarded</h6>
            <ul class="list-group mb-3">
                <?php foreach($clean_sheets as $cs): if ($cs['club_id'] != $away_club_id) continue; ?>
                <li class="list-group-item d-flex justify-content-between small py-1">
                    <span><strong><?=htmlspecialchars($cs['name'])?></strong> (Clean Sheet)</span>
                    <a href="actions/delete_cleansheet.php?id=<?=$cs['id']?>&match_id=<?=$match_id?>" class="text-danger ajax-delete">×</a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="text-center mt-4">
    <button class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Close</button>
</div>

<script>
function showAlert(message, type = 'success') {
    const alert = `<div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    document.getElementById('modalAlert').innerHTML = alert;
    setTimeout(() => document.querySelector('#modalAlert .alert')?.remove(), 5000);
}

function loadMatchStats() {
    fetch(`actions/load_match_events.php?match_id=<?=$match_id?>&home_club=<?=$home_club_id?>&away_club=<?=$away_club_id?>&t=${Date.now()}`)
        .then(r => r.text())
        .then(html => {
            document.querySelector('#matchEventsModal .modal-body').innerHTML = html;
            attachEvents();
        });
}

function attachEvents() {
    // All forms (goals, cards, assists, cleansheets)
    document.querySelectorAll('.ajax-form').forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message || 'Success!');
                    loadMatchStats();
                } else {
                    showAlert(data.error || 'Error occurred', 'danger');
                }
            })
            .catch(() => showAlert('Network error', 'danger'));
        };
    });

    // Delete links
    document.querySelectorAll('.ajax-delete').forEach(link => {
        link.onclick = function(e) {
            if (!confirm('Delete this item?')) return false;
            e.preventDefault();
            fetch(this.href)
                .then(r => r.json())
                .then(data => {
                    showAlert(data.message || 'Deleted!', data.success ? 'success' : 'danger');
                    if (data.success) loadMatchStats();
                });
        };
    });
}

document.addEventListener('DOMContentLoaded', attachEvents);
</script>