<?php
// admin/pages/stats.php
$stats_matches = getRecentMatchesWithStats($pdo, 100);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        League Match Stats
    </h2>
    <small class="text-muted">Recent league matches • Full event breakdown</small>
</div>

<?php if (empty($stats_matches)): ?>
    <div class="alert alert-info text-center py-5">
        No league match stats recorded yet.
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
                    $match_id = $m['id'];
                    $home_club_id = $m['home_club_id'];
                    $away_club_id = $m['away_club_id'];

                    // === GOALS - HOME ===
                    $stmt = $pdo->prepare("
                        SELECT p.name, g.minute, g.is_penalty 
                        FROM goals g 
                        JOIN players p ON g.player_id = p.id 
                        WHERE g.match_id = ? AND p.club_id = ? 
                        ORDER BY g.minute
                    ");
                    $stmt->execute([$match_id, $home_club_id]);
                    $home_goals = '';
                    while ($g = $stmt->fetch()) {
                        $pen = $g['is_penalty'] ? ' (P)' : '';
                        $home_goals .= $g['name'] . " {$g['minute']}'" . $pen . "<br>";
                    }
                    $home_goals = $home_goals ?: '<em class="text-muted">—</em>';

                    // === GOALS - AWAY ===
                    $stmt->execute([$match_id, $away_club_id]);
                    $away_goals = '';
                    while ($g = $stmt->fetch()) {
                        $pen = $g['is_penalty'] ? ' (P)' : '';
                        $away_goals .= $g['name'] . " {$g['minute']}'" . $pen . "<br>";
                    }
                    $away_goals = $away_goals ?: '<em class="text-muted">—</em>';

                    // === ASSISTS - HOME ===
                    $stmt = $pdo->prepare("
                        SELECT p.name 
                        FROM assists a 
                        JOIN goals g ON a.goal_id = g.id 
                        JOIN players p ON a.player_id = p.id 
                        WHERE g.match_id = ? AND p.club_id = ?
                    ");
                    $stmt->execute([$match_id, $home_club_id]);
                    $home_assists = '';
                    while ($a = $stmt->fetch()) {
                        $home_assists .= $a['name'] . "<br>";
                    }
                    $home_assists = $home_assists ?: '<em class="text-muted">—</em>';

                    // === ASSISTS - AWAY ===
                    $stmt->execute([$match_id, $away_club_id]);
                    $away_assists = '';
                    while ($a = $stmt->fetch()) {
                        $away_assists .= $a['name'] . "<br>";
                    }
                    $away_assists = $away_assists ?: '<em class="text-muted">—</em>';

                    // === CARDS ===
                    $home_yc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $home_club_id AND c.card_type = 'yellow'")->fetchColumn();
                    $away_yc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $away_club_id AND c.card_type = 'yellow'")->fetchColumn();
                    $home_rc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $home_club_id AND c.card_type = 'red'")->fetchColumn();
                    $away_rc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $away_club_id AND c.card_type = 'red'")->fetchColumn();

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
                            <?= date('d M', strtotime($m['fixture_date'])) ?><br>
                            <strong><?= date('Y', strtotime($m['fixture_date'])) ?></strong>
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
                            <?= $home_goals ?>
                        </td>
                        <!-- Away Goals -->
                        <td class="text-start small text-danger">
                            <?= $away_goals ?>
                        </td>

                        <!-- Home Assists -->
                        <td class="text-start small text-primary">
                            <?= $home_assists ?>
                        </td>
                        <!-- Away Assists -->
                        <td class="text-start small text-primary">
                            <?= $away_assists ?>
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
                            <button class="btn btn-warning btn-sm me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editResultModal"
                                    onclick="loadEditResult(<?= $m['id'] ?>, '<?= addslashes(htmlspecialchars($m['home_name'])) ?>', '<?= addslashes(htmlspecialchars($m['away_name'])) ?>', <?= $m['home_score'] ?>, <?= $m['away_score'] ?>)">
                                Edit Result
                            </button>
                            <button class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editStatsModal"
                                    onclick="loadMatchStats(<?= $m['id'] ?>, <?= $home_club_id ?>, <?= $away_club_id ?>)">
                                Edit Events
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- ====================== EDIT RESULT MODAL ====================== -->
<div class="modal fade" id="editResultModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="actions/update_result.php">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Edit Match Result</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <p class="fw-bold fs-5 mb-4" id="resultMatchName">— vs —</p>
                    <div class="row g-4 align-items-center justify-content-center">
                        <div class="col-4">
                            <input type="number" name="home_score" class="form-control form-control-lg text-center" min="0" required>
                        </div>
                        <div class="col-auto"><h2>–</h2></div>
                        <div class="col-4">
                            <input type="number" name="away_score" class="form-control form-control-lg text-center" min="0" required>
                        </div>
                    </div>
                    <input type="hidden" name="match_id" id="resultMatchId">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-5">Save Result</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====================== EDIT STATS MODAL ====================== -->
<div class="modal fade" id="editStatsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Match Events</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statsModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Loading match events...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadEditResult(id, h, a, hs, as) {
    document.getElementById('resultMatchName').textContent = h + ' vs ' + a;
    document.getElementById('resultMatchId').value = id;
    document.querySelector('#editResultModal [name="home_score"]').value = hs;
    document.querySelector('#editResultModal [name="away_score"]').value = as;
}

function loadMatchStats(id, hc, ac) {
    const b = document.getElementById('statsModalBody');
    b.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading events...</p></div>`;
    fetch(`actions/load_match_events.php?match_id=${id}&home_club=${hc}&away_club=${ac}`)
        .then(r => r.text())
        .then(html => b.innerHTML = html)
        .catch(() => b.innerHTML = '<div class="alert alert-danger">Failed to load events.</div>');
}
</script>

<?php
// Auto-open modals
if (isset($_GET['edit_result']) || isset($_GET['edit_stats'])) {
    $mid = (int)($_GET['edit_result'] ?? $_GET['edit_stats'] ?? 0);
    if ($mid > 0) {
        $stmt = $pdo->prepare("
            SELECT m.*, f.home_club_id, f.away_club_id, ch.name home_name, ca.name away_name 
            FROM matches m 
            JOIN fixtures f ON m.fixture_id = f.id 
            JOIN clubs ch ON f.home_club_id = ch.id 
            JOIN clubs ca ON f.away_club_id = ca.id 
            WHERE m.id = ?
        ");
        $stmt->execute([$mid]);
        $match = $stmt->fetch();
        if ($match) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    " . (isset($_GET['edit_result']) ? "loadEditResult({$match['id']}, '".addslashes($match['home_name'])."', '".addslashes($match['away_name'])."', {$match['home_score']}, {$match['away_score']}); new bootstrap.Modal('#editResultModal').show();" : "") . "
                    " . (isset($_GET['edit_stats']) ? "loadMatchStats({$match['id']}, {$match['home_club_id']}, {$match['away_club_id']}); new bootstrap.Modal('#editStatsModal').show();" : "") . "
                });
            </script>";
        }
    }
}
?>