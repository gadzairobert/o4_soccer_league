<?php
// admin/pages/tournament_stats.php
require_once __DIR__ . '/../includes/tournament_stats.php';

$stats_matches = getRecentTournamentMatchesWithStats($pdo, 30);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        Tournament Match Stats
    </h2>
    <small class="text-muted">Last 30 matches • Full breakdown</small>
</div>

<?php if (empty($stats_matches)): ?>
    <div class="alert alert-info text-center py-5">
        No tournament match stats recorded yet.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-dark">
                <tr>
                    <th rowspan="2" class="text-center align-middle">Date</th>
                    <th rowspan="2" class="text-center align-middle">Match</th>
                    <th rowspan="2" class="text-center align-middle">Result</th>
                    <th colspan="2" class="text-center bg-success text-white">Goals Scored</th>
                    <th colspan="2" class="text-center bg-primary text-white">Assists</th>
                    <th colspan="2" class="text-center bg-warning text-dark">Yellow Cards</th>
                    <th colspan="2" class="text-center bg-danger text-white">Red Cards</th>
                    <th rowspan="2" class="text-center align-middle">Clean Sheet</th>
                    <th rowspan="2" class="text-center align-middle">Actions</th>
                </tr>
                <tr class="table-secondary">
                    <th class="text-center"><small>Home</small></th>
                    <th class="text-center"><small>Away</small></th>
                    <th class="text-center"><small>Home</small></th>
                    <th class="text-center"><small>Away</small></th>
                    <th class="text-center"><small>Home</small></th>
                    <th class="text-center"><small>Away</small></th>
                    <th class="text-center"><small>Home</small></th>
                    <th class="text-center"><small>Away</small></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats_matches as $m): 
                    $match_id = $m['match_id'];
                    $home_club_id = $m['home_club_id'];
                    $away_club_id = $m['away_club_id'];

                    // === GOALS - HOME ===
                    $home_goals = $pdo->prepare("
                        SELECT p.name, tg.minute, tg.is_penalty 
                        FROM tournament_goals tg 
                        JOIN players p ON tg.player_id = p.id 
                        WHERE tg.match_id = ? AND p.club_id = ? 
                        ORDER BY tg.minute
                    ");
                    $home_goals->execute([$match_id, $home_club_id]);
                    $home_goal_list = '';
                    while ($g = $home_goals->fetch()) {
                        $pen = $g['is_penalty'] ? ' (P)' : '';
                        $home_goal_list .= $g['name'] . " {$g['minute']}'" . $pen . "<br>";
                    }
                    $home_goal_list = $home_goal_list ?: '<em class="text-muted">—</em>';

                    // === GOALS - AWAY ===
                    $away_goals = $pdo->prepare("
                        SELECT p.name, tg.minute, tg.is_penalty 
                        FROM tournament_goals tg 
                        JOIN players p ON tg.player_id = p.id 
                        WHERE tg.match_id = ? AND p.club_id = ? 
                        ORDER BY tg.minute
                    ");
                    $away_goals->execute([$match_id, $away_club_id]);
                    $away_goal_list = '';
                    while ($g = $away_goals->fetch()) {
                        $pen = $g['is_penalty'] ? ' (P)' : '';
                        $away_goal_list .= $g['name'] . " {$g['minute']}'" . $pen . "<br>";
                    }
                    $away_goal_list = $away_goal_list ?: '<em class="text-muted">—</em>';

                    // === ASSISTS - HOME ===
                    $home_assists = $pdo->prepare("
                        SELECT p.name 
                        FROM tournament_assists ta 
                        JOIN tournament_goals tg ON ta.goal_id = tg.id 
                        JOIN players p ON ta.player_id = p.id 
                        WHERE tg.match_id = ? AND p.club_id = ?
                    ");
                    $home_assists->execute([$match_id, $home_club_id]);
                    $home_assist_list = '';
                    while ($a = $home_assists->fetch()) {
                        $home_assist_list .= $a['name'] . "<br>";
                    }
                    $home_assist_list = $home_assist_list ?: '<em class="text-muted">—</em>';

                    // === ASSISTS - AWAY ===
                    $away_assists = $pdo->prepare("
                        SELECT p.name 
                        FROM tournament_assists ta 
                        JOIN tournament_goals tg ON ta.goal_id = tg.id 
                        JOIN players p ON ta.player_id = p.id 
                        WHERE tg.match_id = ? AND p.club_id = ?
                    ");
                    $away_assists->execute([$match_id, $away_club_id]);
                    $away_assist_list = '';
                    while ($a = $away_assists->fetch()) {
                        $away_assist_list .= $a['name'] . "<br>";
                    }
                    $away_assist_list = $away_assist_list ?: '<em class="text-muted">—</em>';

                    // === CARDS ===
                    $home_yc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $home_club_id AND c.card_type = 'yellow'")->fetchColumn();
                    $away_yc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $away_club_id AND c.card_type = 'yellow'")->fetchColumn();
                    $home_rc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $home_club_id AND c.card_type = 'red'")->fetchColumn();
                    $away_rc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $away_club_id AND c.card_type = 'red'")->fetchColumn();

                    // === CLEAN SHEET ===
                    $clean_sheet = '';
                    if ($m['away_score'] == 0) {
                        $clean_sheet = '<span class="text-success fw-bold">' . htmlspecialchars($m['home_name']) . '</span>';
                    } elseif ($m['home_score'] == 0) {
                        $clean_sheet = '<span class="text-success fw-bold">' . htmlspecialchars($m['away_name']) . '</span>';
                    } else {
                        $clean_sheet = '—';
                    }
                ?>
                    <tr class="align-middle">
                        <td class="text-center small text-muted">
                            <?= date('d M', strtotime($m['tournament_date'])) ?><br>
                            <strong><?= date('Y', strtotime($m['tournament_date'])) ?></strong>
                        </td>
                        <td class="text-center">
                            <div>
                                <strong><?= htmlspecialchars($m['home_name']) ?></strong><br>
                                <small class="text-muted">vs</small><br>
                                <strong><?= htmlspecialchars($m['away_name']) ?></strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <h3 class="mb-0 text-primary"><?= $m['home_score'] ?> - <?= $m['away_score'] ?></h3>
                        </td>

                        <!-- Home Goals -->
                        <td class="text-start small text-success">
                            <?= $home_goal_list ?>
                        </td>
                        <!-- Away Goals -->
                        <td class="text-start small text-danger">
                            <?= $away_goal_list ?>
                        </td>

                        <!-- Home Assists -->
                        <td class="text-start small text-primary">
                            <?= $home_assist_list ?>
                        </td>
                        <!-- Away Assists -->
                        <td class="text-start small text-primary">
                            <?= $away_assist_list ?>
                        </td>

                        <!-- Yellow Cards -->
                        <td class="text-center">
                            <span class="badge bg-warning text-dark"><?= $home_yc ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark"><?= $away_yc ?></span>
                        </td>

                        <!-- Red Cards -->
                        <td class="text-center">
                            <span class="badge bg-danger"><?= $home_rc ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger"><?= $away_rc ?></span>
                        </td>

                        <!-- Clean Sheet -->
                        <td class="text-center">
                            <?= $clean_sheet ?>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" title="Edit Result"
                                    data-bs-toggle="modal" data-bs-target="#editTournamentResultModal"
                                    onclick="loadEditTournamentResult(<?= $m['match_id'] ?>, '<?= addslashes(htmlspecialchars($m['home_name'])) ?>', '<?= addslashes(htmlspecialchars($m['away_name'])) ?>', <?= $m['home_score'] ?>, <?= $m['away_score'] ?>)">
                                Edit Result
                            </button>
                            <button class="btn btn-primary btn-sm" title="Edit Events"
                                    data-bs-toggle="modal" data-bs-target="#editTournamentStatsModal"
                                    onclick="loadTournamentMatchStats(<?= $m['match_id'] ?>, <?= $home_club_id ?>, <?= $away_club_id ?>)">
                                Edit Events
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Modals & Scripts (same as before) -->
<div class="modal fade" id="editTournamentResultModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="actions/update_tournament_result.php">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Edit Result</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <p class="fw-bold fs-5 mb-4" id="tResultMatchName">— vs —</p>
                    <div class="row g-4 align-items-center justify-content-center">
                        <div class="col-4">
                            <input type="number" name="home_score" class="form-control form-control-lg text-center" min="0" required>
                        </div>
                        <div class="col-auto"><h2>–</h2></div>
                        <div class="col-4">
                            <input type="number" name="away_score" class="form-control form-control-lg text-center" min="0" required>
                        </div>
                    </div>
                    <input type="hidden" name="match_id" id="tResultMatchId">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-5">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTournamentStatsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Match Events</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="tStatsModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Loading events...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadEditTournamentResult(id, h, a, hs, as) {
    document.getElementById('tResultMatchName').textContent = h + ' vs ' + a;
    document.getElementById('tResultMatchId').value = id;
    document.querySelector('#editTournamentResultModal [name="home_score"]').value = hs;
    document.querySelector('#editTournamentResultModal [name="away_score"]').value = as;
}
function loadTournamentMatchStats(id, hc, ac) {
    const b = document.getElementById('tStatsModalBody');
    b.innerHTML = `<div class="text-center py-5"><div class="spinner-border"></div><p>Loading...</p></div>`;
    fetch(`actions/load_tournament_match_events.php?match_id=${id}&home_club=${hc}&away_club=${ac}`)
        .then(r => r.text()).then(h => b.innerHTML = h);
}
</script>

<?php
if (isset($_GET['edit_t_result']) || isset($_GET['edit_t_stats'])) {
    $mid = (int)($_GET['edit_t_result'] ?? $_GET['edit_t_stats'] ?? 0);
    if ($mid > 0) {
        $stmt = $pdo->prepare("SELECT tm.*, ch.name home_name, ca.name away_name, tf.home_club_id, tf.away_club_id 
                               FROM tournament_matches tm 
                               JOIN tournament_fixtures tf ON tm.fixture_id = tf.id 
                               JOIN clubs ch ON tf.home_club_id = ch.id 
                               JOIN clubs ca ON tf.away_club_id = ca.id 
                               WHERE tm.id = ?");
        $stmt->execute([$mid]);
        $m = $stmt->fetch();
        if ($m) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    " . (isset($_GET['edit_t_result']) ? "loadEditTournamentResult({$m['id']}, '".addslashes($m['home_name'])."', '".addslashes($m['away_name'])."', {$m['home_score']}, {$m['away_score']}); new bootstrap.Modal('#editTournamentResultModal').show();" : "") . "
                    " . (isset($_GET['edit_t_stats']) ? "loadTournamentMatchStats({$m['id']}, {$m['home_club_id']}, {$m['away_club_id']}); new bootstrap.Modal('#editTournamentStatsModal').show();" : "") . "
                });
            </script>";
        }
    }
}
?>