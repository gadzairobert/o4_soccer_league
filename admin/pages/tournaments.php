<?php
// admin/pages/tournaments.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    require __DIR__.'/../actions/tournaments.php';
}

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

$edit_id   = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];

// Load clubs
$clubs = $pdo->query("SELECT id, name, stadium FROM clubs ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Load competition seasons
$comp_seasons = $pdo->query("
    SELECT id, name, competition_name, type 
    FROM competition_seasons 
    ORDER BY season DESC, competition_name ASC
")->fetchAll(PDO::FETCH_ASSOC);

if ($edit_id) {
    $stmt = $pdo->prepare("
        SELECT tf.*, h.name AS home_name, a.name AS away_name,
               cs.id AS comp_season_id, cs.name AS comp_name, cs.type AS comp_type
        FROM tournament_fixtures tf
        JOIN clubs h ON tf.home_club_id = h.id
        JOIN clubs a ON tf.away_club_id = a.id
        LEFT JOIN competition_seasons cs ON tf.competition_season_id = cs.id
        WHERE tf.id = ?
    ");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Upcoming fixtures
$upcoming = $pdo->query("
    SELECT tf.*, 
           ch.name AS home_name, ca.name AS away_name,
           cs.name AS comp_name, cs.type AS comp_type
    FROM tournament_fixtures tf
    JOIN clubs ch ON tf.home_club_id = ch.id
    JOIN clubs ca ON tf.away_club_id = ca.id
    LEFT JOIN competition_seasons cs ON tf.competition_season_id = cs.id
    WHERE tf.status = 'Scheduled'
    ORDER BY tf.tournament_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Recent results
$results = $pdo->query("
    SELECT tm.*, tf.tournament_date, tf.home_club_id, tf.away_club_id,
           ch.name AS home_name, ca.name AS away_name,
           cs.name AS comp_name, cs.type AS comp_type
    FROM tournament_matches tm
    JOIN tournament_fixtures tf ON tm.fixture_id = tf.id
    JOIN clubs ch ON tf.home_club_id = ch.id
    JOIN clubs ca ON tf.away_club_id = ca.id
    LEFT JOIN competition_seasons cs ON tf.competition_season_id = cs.id
    ORDER BY tm.match_date DESC LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars(urldecode($success)) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars(urldecode($error)) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Tournament Fixtures & Results</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTournamentModal">
        Add Tournament Fixture
    </button>
</div>

<ul class="nav nav-tabs" id="tournamentTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upcoming">Upcoming</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#results">Recent Results</button>
    </li>
</ul>

<div class="tab-content mt-3">

    <!-- Upcoming Fixtures -->
    <div class="tab-pane fade show active" id="upcoming">
        <?php if (empty($upcoming)): ?>
            <p class="text-muted">No upcoming tournament fixtures.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Date & Time</th>
                            <th>Competition</th>
                            <th>Fixture</th>
                            <th>Venue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming as $f): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($f['tournament_date'])) ?><br><small class="text-muted"><?= date('H:i', strtotime($f['tournament_date'])) ?></small></td>
                                <td>
                                    <?php if ($f['comp_name']): ?>
                                        <span class="badge bg-<?= $f['comp_type'] == 'cup' ? 'danger' : 'info' ?> fw-semibold">
                                            <?= htmlspecialchars($f['comp_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <em class="text-muted small">Not set</em>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($f['home_name']) ?></strong> vs <strong><?= htmlspecialchars($f['away_name']) ?></strong></td>
                                <td><?= htmlspecialchars($f['venue'] ?: 'TBD') ?></td>
                                <td>
                                    <a href="?page=tournaments&edit_id=<?= $f['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="?page=tournaments&action=delete_tournament_fixture&id=<?= $f['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Delete this fixture?');">Del</a>
                                    <button class="btn btn-sm btn-success record-result-btn"
                                            data-fixture-id="<?= $f['id'] ?>">Record Result</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Results -->
    <div class="tab-pane fade" id="results">
        <?php if (empty($results)): ?>
            <p class="text-muted text-center py-4">No tournament results yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Competition</th>
                            <th>Match</th>
                            <th>Score</th>
                            <th>Home Goals</th>
                            <th>Away Goals</th>
                            <th>Cards & CS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $m):
                            $match_id = $m['id'];
                            $home_club_id = $m['home_club_id'];
                            $away_club_id = $m['away_club_id'];

                            $stmt = $pdo->prepare("SELECT p.name, p.club_id, g.minute, g.is_penalty FROM tournament_goals g JOIN players p ON g.player_id = p.id WHERE g.match_id = ? ORDER BY g.minute");
                            $stmt->execute([$match_id]);
                            $home_goals = $away_goals = [];
                            while ($g = $stmt->fetch()) {
                                $txt = htmlspecialchars($g['name']) . " {$g['minute']}'" . ($g['is_penalty'] ? ' (P)' : '');
                                if ($g['club_id'] == $home_club_id) $home_goals[] = $txt;
                                else $away_goals[] = $txt;
                            }

                            $yc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards WHERE match_id = $match_id AND card_type = 'yellow'")->fetchColumn();
                            $rc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards WHERE match_id = $match_id AND card_type = 'red'")->fetchColumn();
                            $cs = $m['away_score'] == 0 ? $m['home_name'] : ($m['home_score'] == 0 ? $m['away_name'] : '—');
                        ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($m['tournament_date'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $m['comp_type'] == 'cup' ? 'danger' : 'info' ?>">
                                        <?= htmlspecialchars($m['comp_name'] ?? '—') ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($m['home_name']) ?></strong> vs <strong><?= htmlspecialchars($m['away_name']) ?></strong></td>
                                <td class="text-center"><h5 class="mb-0"><?= $m['home_score'] ?> - <?= $m['away_score'] ?></h5></td>
                                <td class="small text-success"><?= $home_goals ? implode('<br>', $home_goals) : '—' ?></td>
                                <td class="small text-danger"><?= $away_goals ? implode('<br>', $away_goals) : '—' ?></td>
                                <td class="small">
                                    YC: <span class="badge bg-warning text-dark"><?= $yc ?></span><br>
                                    RC: <span class="badge bg-danger"><?= $rc ?></span><br>
                                    CS: <strong><?= htmlspecialchars($cs) ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Tournament Modal -->
<div class="modal fade" id="addTournamentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_tournament_fixture' : 'add_tournament_fixture' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?= $edit_id ? 'Edit' : 'Add New' ?> Tournament Fixture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Competition & Season <span class="text-danger">*</span></label>
                            <select name="competition_season_id" class="form-select" required>
                                <option value="">-- Select Tournament --</option>
                                <?php foreach ($comp_seasons as $cs): ?>
                                    <option value="<?= $cs['id'] ?>" 
                                        <?= ($edit_data['competition_season_id'] ?? '') == $cs['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cs['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tournament_date" class="form-control" required
                                   value="<?= htmlspecialchars($edit_data['tournament_date'] ?? (isset($_GET['edit_id']) ? '' : date('Y-m-d\T15:00'))) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Home Team <span class="text-danger">*</span></label>
                            <select name="home_club_id" id="addTournamentHome" class="form-select" required>
                                <option value="">Select Home</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>" data-stadium="<?= htmlspecialchars($c['stadium'] ?? '') ?>"
                                            <?= ($edit_data['home_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Away Team <span class="text-danger">*</span></label>
                            <select name="away_club_id" id="addTournamentAway" class="form-select" required>
                                <option value="">Select Away</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                            data-club-id="<?= $c['id'] ?>"
                                            <?= ($edit_data['away_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Venue</label>
                            <input type="text" name="venue" id="addTournamentVenue" class="form-control"
                                   value="<?= htmlspecialchars($edit_data['venue'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><?= $edit_id ? 'Update' : 'Add' ?> Fixture</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Record Result Modal -->
<div class="modal fade" id="recordResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="record_match_result">
                <input type="hidden" name="fixture_id" id="resultFixtureId">
                <input type="hidden" name="type" value="tournament">
                <div class="modal-header">
                    <h5 class="modal-title">Record Tournament Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label>Home Score</label>
                            <input type="number" name="home_score" class="form-control" min="0" value="0" required>
                        </div>
                        <div class="col-6">
                            <label>Away Score</label>
                            <input type="number" name="away_score" class="form-control" min="0" value="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Result</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_id > 0): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('addTournamentModal')).show();
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const homeSelect = document.getElementById('addTournamentHome');
    const awaySelect = document.getElementById('addTournamentAway');

    function updateAwayOptions() {
        const homeId = homeSelect.value;
        Array.from(awaySelect.options).forEach(option => {
            if (option.value && option.dataset.clubId == homeId) {
                option.disabled = true;
                if (option.selected) awaySelect.value = '';
            } else {
                option.disabled = false;
            }
        });
    }

    if (homeSelect && awaySelect) {
        homeSelect.addEventListener('change', updateAwayOptions);
        updateAwayOptions();

        homeSelect.addEventListener('change', function () {
            const stadium = this.selectedOptions[0].dataset.stadium || '';
            document.getElementById('addTournamentVenue').value = stadium;
        });
    }

    document.querySelectorAll('.record-result-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('resultFixtureId').value = this.dataset.fixtureId;
            new bootstrap.Modal(document.getElementById('recordResultModal')).show();
        });
    });
});
</script>