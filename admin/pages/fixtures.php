<?php
// admin/pages/fixtures.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__.'/../actions/fixtures.php';
}

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

$edit_id   = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];

// Load clubs
$clubs = $pdo->query("SELECT id, name, stadium FROM clubs ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Load only LEAGUE competition seasons
$comp_seasons = $pdo->query("
    SELECT id, name, competition_name, season, type 
    FROM competition_seasons 
    WHERE type = 'league'
    ORDER BY season DESC, competition_name ASC
")->fetchAll(PDO::FETCH_ASSOC);

if ($edit_id) {
    $stmt = $pdo->prepare("
        SELECT f.*, h.name AS home_name, a.name AS away_name,
               cs.id AS comp_season_id, cs.name AS comp_season_name
        FROM fixtures f
        JOIN clubs h ON f.home_club_id = h.id
        JOIN clubs a ON f.away_club_id = a.id
        LEFT JOIN competition_seasons cs ON f.competition_season_id = cs.id
        WHERE f.id = ?
    ");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Upcoming fixtures
$upcoming = $pdo->query("
    SELECT f.*, 
           ch.name AS home_name, ca.name AS away_name,
           ch.id AS home_club_id, ca.id AS away_club_id,
           cs.name AS comp_name
    FROM fixtures f
    JOIN clubs ch ON f.home_club_id = ch.id
    JOIN clubs ca ON f.away_club_id = ca.id
    LEFT JOIN competition_seasons cs ON f.competition_season_id = cs.id
    WHERE f.status = 'Scheduled'
      AND (cs.type = 'league' OR cs.type IS NULL OR f.competition_season_id IS NULL)
    ORDER BY f.fixture_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Recent results
$results = $pdo->query("
    SELECT m.*, f.fixture_date, f.matchday, f.home_club_id, f.away_club_id,
           ch.name AS home_name, ca.name AS away_name
    FROM matches m
    JOIN fixtures f ON m.fixture_id = f.id
    JOIN clubs ch ON f.home_club_id = ch.id
    JOIN clubs ca ON f.away_club_id = ca.id
    LEFT JOIN competition_seasons cs ON f.competition_season_id = cs.id
    WHERE (cs.type = 'league' OR cs.type IS NULL OR f.competition_season_id IS NULL)
    ORDER BY m.match_date DESC LIMIT 20
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
    <h2>League Fixtures & Results</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFixtureModal">
        Add New League Fixture
    </button>
</div>

<ul class="nav nav-tabs" id="fixtureTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
            Upcoming League Fixtures
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button">
            Recent League Results
        </button>
    </li>
</ul>

<div class="tab-content mt-3">

    <!-- Upcoming Fixtures -->
    <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
        <?php if (empty($upcoming)): ?>
            <p class="text-muted">No upcoming league fixtures.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Matchday</th>
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
                                <td class="text-center fw-bold text-primary">
                                    <?= $f['matchday'] ? 'MD ' . htmlspecialchars($f['matchday']) : '—' ?>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($f['fixture_date'])) ?><br>
                                    <small class="text-muted"><?= date('H:i', strtotime($f['fixture_date'])) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary fw-semibold">
                                        <?= htmlspecialchars($f['comp_name'] ?? 'League') ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($f['home_name']) ?></strong> vs <strong><?= htmlspecialchars($f['away_name']) ?></strong></td>
                                <td><?= htmlspecialchars($f['venue'] ?: 'TBD') ?></td>
                                <td>
                                    <a href="?page=fixtures&edit_id=<?= $f['id'] ?>" class="btn btn-sm btn-info">Edit</a>

                                    <!-- DELETE BUTTON WITH CONFIRM MODAL -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $f['id'] ?>">
                                        Delete
                                    </button>

                                    <button class="btn btn-sm btn-success record-result-btn" data-fixture-id="<?= $f['id'] ?>">
                                        Record Result
                                    </button>
                                </td>
                            </tr>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal<?= $f['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="?page=fixtures">
                                            <input type="hidden" name="action" value="delete_fixture">
                                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Confirm Delete Fixture</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this fixture?</p>
                                                <strong><?= htmlspecialchars($f['home_name']) ?> vs <?= htmlspecialchars($f['away_name']) ?></strong><br>
                                                <small class="text-muted">
                                                    Matchday <?= $f['matchday'] ?> • <?= date('d M Y H:i', strtotime($f['fixture_date'])) ?>
                                                </small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Results -->
<div class="tab-pane fade" id="results" role="tabpanel">
    <?php if (empty($results)): ?>
        <p class="text-muted text-center py-4">No league results recorded yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2" class="text-center align-middle">Matchday</th>
                        <th rowspan="2" class="text-center align-middle">Date</th>
                        <th rowspan="2" class="text-center align-middle">Match</th>
                        <th rowspan="2" class="text-center align-middle">Result</th>
                        <th colspan="2" class="text-center bg-success text-white">Goals Scored</th>
                        <th colspan="2" class="text-center bg-primary text-white">Assists</th>
                        <th colspan="2" class="text-center bg-warning text-dark">Yellow Cards</th>
                        <th colspan="2" class="text-center bg-danger text-white">Red Cards</th>
                        <th rowspan="2" class="text-center align-middle">Clean Sheet</th>
                    </tr>
                    <tr class="table-secondary">
                        <th><small>Home</small></th>
                        <th><small>Away</small></th>
                        <th><small>Home</small></th>
                        <th><small>Away</small></th>
                        <th><small>Home</small></th>
                        <th><small>Away</small></th>
                        <th><small>Home</small></th>
                        <th><small>Away</small></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $m): 
                        $match_id     = $m['id'];
                        $home_club_id = $m['home_club_id'];
                        $away_club_id = $m['away_club_id'];

                        // Goals Home
                        $stmt = $pdo->prepare("SELECT p.name, g.minute, g.is_penalty FROM goals g JOIN players p ON g.player_id = p.id WHERE g.match_id = ? AND p.club_id = ? ORDER BY g.minute");
                        $stmt->execute([$match_id, $home_club_id]);
                        $home_goals = ''; 
                        while ($g = $stmt->fetch()) {
                            $pen = $g['is_penalty'] ? ' (P)' : '';
                            $home_goals .= htmlspecialchars($g['name']) . " {$g['minute']}'" . $pen . "<br>";
                        }
                        $home_goals = $home_goals ?: '<em class="text-muted">—</em>';

                        // Goals Away
                        $stmt->execute([$match_id, $away_club_id]);
                        $away_goals = ''; 
                        while ($g = $stmt->fetch()) {
                            $pen = $g['is_penalty'] ? ' (P)' : '';
                            $away_goals .= htmlspecialchars($g['name']) . " {$g['minute']}'" . $pen . "<br>";
                        }
                        $away_goals = $away_goals ?: '<em class="text-muted">—</em>';

                        // Assists
                        $stmt = $pdo->prepare("SELECT p.name FROM assists a JOIN goals g ON a.goal_id = g.id JOIN players p ON a.player_id = p.id WHERE g.match_id = ? AND p.club_id = ?");
                        $stmt->execute([$match_id, $home_club_id]);
                        $home_assists = ''; 
                        while ($a = $stmt->fetch()) $home_assists .= htmlspecialchars($a['name']) . "<br>";
                        $home_assists = $home_assists ?: '<em class="text-muted">—</em>';

                        $stmt->execute([$match_id, $away_club_id]);
                        $away_assists = ''; 
                        while ($a = $stmt->fetch()) $away_assists .= htmlspecialchars($a['name']) . "<br>";
                        $away_assists = $away_assists ?: '<em class="text-muted">—</em>';

                        // Cards
                        $home_yc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $home_club_id AND c.card_type = 'yellow'")->fetchColumn();
                        $away_yc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $away_club_id AND c.card_type = 'yellow'")->fetchColumn();
                        $home_rc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $home_club_id AND c.card_type = 'red'")->fetchColumn();
                        $away_rc = (int)$pdo->query("SELECT COUNT(*) FROM cards c JOIN players p ON c.player_id = p.id WHERE c.match_id = $match_id AND p.club_id = $away_club_id AND c.card_type = 'red'")->fetchColumn();

                        // NEW: Clean Sheet - Get goalkeeper(s) from clean_sheets table
                        $clean_sheet_players = [];
                        $stmt = $pdo->prepare("
                            SELECT p.name, p.club_id 
                            FROM clean_sheets cs 
                            JOIN players p ON cs.player_id = p.id 
                            WHERE cs.match_id = ? 
                            ORDER BY p.name
                        ");
                        $stmt->execute([$match_id]);
                        while ($cs = $stmt->fetch()) {
                            $clean_sheet_players[] = [
                                'name' => htmlspecialchars($cs['name']),
                                'club_id' => $cs['club_id']
                            ];
                        }

                        if (!empty($clean_sheet_players)) {
                            $names = array_map(fn($p) => $p['name'], $clean_sheet_players);
                            $clean_sheet = '<span class="text-success fw-bold">' . implode('<br>', $names) . '</span>';
                        } else {
                            $clean_sheet = '<em class="text-muted">—</em>';
                        }
                    ?>
                        <tr class="align-middle">
                            <td class="text-center fw-bold text-primary">
                                <?= $m['matchday'] ? 'MD ' . htmlspecialchars($m['matchday']) : '—' ?>
                            </td>
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
                            <td class="text-start small text-success"><?= $home_goals ?></td>
                            <td class="text-start small text-danger"><?= $away_goals ?></td>
                            <td class="text-start small text-primary"><?= $home_assists ?></td>
                            <td class="text-start small text-primary"><?= $away_assists ?></td>
                            <td class="text-center"><span class="badge bg-warning text-dark"><?= $home_yc ?></span></td>
                            <td class="text-center"><span class="badge bg-warning text-dark"><?= $away_yc ?></span></td>
                            <td class="text-center"><span class="badge bg-danger"><?= $home_rc ?></span></td>
                            <td class="text-center"><span class="badge bg-danger"><?= $away_rc ?></span></td>
                            <td class="text-center"><strong><?= $clean_sheet ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Fixture Modal -->
<div class="modal fade" id="addFixtureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_fixture' : 'add_fixture' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?= $edit_id ? 'Edit' : 'Add New' ?> League Fixture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Matchday <span class="text-danger">*</span></label>
                            <input type="number" name="matchday" class="form-control" min="1" max="38" required
                                   value="<?= htmlspecialchars($edit_data['matchday'] ?? '') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Competition & Season <span class="text-danger">*</span></label>
                            <select name="competition_season_id" class="form-select" required>
                                <option value="">-- Select League Season --</option>
                                <?php foreach ($comp_seasons as $cs): ?>
                                    <option value="<?= $cs['id'] ?>" <?= ($edit_data['comp_season_id'] ?? '') == $cs['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cs['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fixture_date" class="form-control" required
                                   value="<?= htmlspecialchars($edit_data['fixture_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Home Team <span class="text-danger">*</span></label>
                            <select name="home_club_id" id="addFixtureHome" class="form-select" required>
                                <option value="">Select Home Team</option>
                                <?php foreach ($clubs as $c): ?>
                                    <option value="<?= $c['id'] ?>" 
                                            data-stadium="<?= htmlspecialchars($c['stadium'] ?? '') ?>"
                                            <?= ($edit_data['home_club_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Away Team <span class="text-danger">*</span></label>
                            <select name="away_club_id" id="addFixtureAway" class="form-select" required>
                                <option value="">Select Away Team</option>
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
                            <input type="text" name="venue" id="addFixtureVenue" class="form-control"
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
                <input type="hidden" name="type" value="fixture">
                <div class="modal-header">
                    <h5 class="modal-title">Record Result</h5>
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
        new bootstrap.Modal(document.getElementById('addFixtureModal')).show();
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const homeSelect = document.getElementById('addFixtureHome');
    const awaySelect = document.getElementById('addFixtureAway');

    function updateAwayOptions() {
        const homeId = homeSelect.value;
        Array.from(awaySelect.options).forEach(opt => {
            if (opt.value && opt.dataset.clubId == homeId) {
                opt.disabled = true;
                if (opt.selected) awaySelect.value = '';
            } else {
                opt.disabled = false;
            }
        });
    }

    if (homeSelect && awaySelect) {
        homeSelect.addEventListener('change', updateAwayOptions);
        updateAwayOptions();

        homeSelect.addEventListener('change', function () {
            const stadium = this.selectedOptions[0].dataset.stadium || '';
            document.getElementById('addFixtureVenue').value = stadium;
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