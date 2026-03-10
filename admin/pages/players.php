<?php
// admin/pages/players.php
define('IN_DASHBOARD', true);

$playersDir = '../uploads/players/';
if (!is_dir($playersDir)) mkdir($playersDir, 0755, true);

$edit_id = (int)($_GET['edit_id'] ?? 0);
$search_name = trim($_GET['search_name'] ?? '');

$edit_data = [];
$clubs = $pdo->query("SELECT id, name FROM clubs ORDER BY name")
               ->fetchAll(PDO::FETCH_KEY_PAIR);

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Players Directory</h2>
    <div class="d-flex gap-2">
        <!-- Search Form -->
        <form method="GET" class="d-flex" id="searchForm">
            <input type="hidden" name="page" value="players">
            <input type="text" name="search_name" class="form-control me-2" 
                   placeholder="Search by name..." value="<?= htmlspecialchars($search_name) ?>" 
                   style="width: 250px;">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <?php if ($search_name): ?>
                <a href="?page=players" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Add Player Button -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#playerModal">
            Add Player
        </button>
    </div>
</div>

<?php
// Build query based on search
$sql = "
    SELECT p.*, c.name as club_name 
    FROM players p 
    LEFT JOIN clubs c ON p.club_id = c.id 
";
$params = [];

if ($search_name) {
    $sql .= " WHERE LOWER(p.name) LIKE LOWER(?)";
    $params[] = '%' . $search_name . '%';
}

$sql .= " ORDER BY p.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$has_results = $stmt->rowCount() > 0;
?>

<div class="table-responsive">
    <?php if ($search_name && !$has_results): ?>
        <div class="alert alert-info text-center py-4">
            No players found matching "<strong><?= htmlspecialchars($search_name) ?></strong>".
            You can now safely add this player.
        </div>
    <?php elseif ($search_name && $has_results): ?>
        <div class="alert alert-warning mb-3">
            Found <?= $stmt->rowCount() ?> player(s) matching "<strong><?= htmlspecialchars($search_name) ?></strong>".
            Check if this player already exists before adding.
        </div>
    <?php endif; ?>

    <table class="table table-hover align-middle table-bordered">
        <thead class="table-dark">
            <tr>
                <th rowspan="2" class="text-center align-middle">#</th>
                <th rowspan="2" class="text-center align-middle">Photo</th>
                <th rowspan="2" class="text-center align-middle">Player</th>
                <th rowspan="2" class="text-center align-middle">Club</th>
                <th rowspan="2" class="text-center align-middle">Status</th> <!-- NEW -->
                <th rowspan="2" class="text-center align-middle">Pos</th>
                <th rowspan="2" class="text-center align-middle">No.</th>
                <th rowspan="2" class="text-center align-middle">DOB</th>
                <th rowspan="2" class="text-center align-middle">ID No.</th>
                
                <!-- LEAGUE STATS -->
                <th colspan="5" class="text-center bg-primary text-white">League Stats</th>
                
                <!-- TOURNAMENT STATS -->
                <th colspan="5" class="text-center bg-success text-white">Tournament Stats</th>
                
                <th rowspan="2" class="text-center align-middle">Actions</th>
            </tr>
            <tr class="table-secondary">
                <th class="text-center"><small>Goals</small></th>
                <th class="text-center"><small>Assists</small></th>
                <th class="text-center"><small>YC</small></th>
                <th class="text-center"><small>RC</small></th>
                <th class="text-center"><small>CS</small></th>
                
                <th class="text-center"><small>Goals</small></th>
                <th class="text-center"><small>Assists</small></th>
                <th class="text-center"><small>YC</small></th>
                <th class="text-center"><small>RC</small></th>
                <th class="text-center"><small>CS</small></th>
            </tr>
        </thead>
        <tbody>
        <?php while ($p = $stmt->fetch(PDO::FETCH_ASSOC)): 
            $player_id = $p['id'];

            // League Stats
            $league_goals = (int)$pdo->query("SELECT COUNT(*) FROM goals g WHERE g.player_id = $player_id")->fetchColumn();
            $league_assists = (int)$pdo->query("SELECT COUNT(*) FROM assists a JOIN goals g ON a.goal_id = g.id WHERE a.player_id = $player_id")->fetchColumn();
            $league_yc = (int)$pdo->query("SELECT COUNT(*) FROM cards WHERE player_id = $player_id AND card_type = 'yellow'")->fetchColumn();
            $league_rc = (int)$pdo->query("SELECT COUNT(*) FROM cards WHERE player_id = $player_id AND card_type = 'red'")->fetchColumn();
            $league_cs = (int)$pdo->query("SELECT COUNT(*) FROM clean_sheets WHERE player_id = $player_id")->fetchColumn();

            // Tournament Stats
            $tourn_goals = (int)$pdo->query("SELECT COUNT(*) FROM tournament_goals tg WHERE tg.player_id = $player_id")->fetchColumn();
            $tourn_assists = (int)$pdo->query("SELECT COUNT(*) FROM tournament_assists ta JOIN tournament_goals tg ON ta.goal_id = tg.id WHERE ta.player_id = $player_id")->fetchColumn();
            $tourn_yc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards tc WHERE tc.player_id = $player_id AND tc.card_type = 'yellow'")->fetchColumn();
            $tourn_rc = (int)$pdo->query("SELECT COUNT(*) FROM tournament_cards tc WHERE tc.player_id = $player_id AND tc.card_type = 'red'")->fetchColumn();
            $tourn_cs = (int)$pdo->query("SELECT COUNT(*) FROM tournament_clean_sheets tcs WHERE tcs.player_id = $player_id")->fetchColumn();

            // Status badge styling
            $status = $p['status'] ?? 'Active';
            $badgeClass = match($status) {
                'Active'     => 'bg-success',
                'Inactive'   => 'bg-secondary',
                'Suspended'  => 'bg-warning text-dark',
                'Transferred'=> 'bg-info',
                'Banned'     => 'bg-danger',
                default      => 'bg-light text-dark'
            };
        ?>
            <tr class="align-middle">
                <td class="text-center small"><?= $p['id'] ?></td>
                <td class="text-center">
                    <?php if ($p['photo']): ?>
                        <img src="../uploads/players/<?= htmlspecialchars($p['photo']) ?>" 
                             width="45" class="rounded-circle shadow-sm" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-inline-block" style="width:45px;height:45px;"></div>
                    <?php endif; ?>
                </td>
                <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['club_name'] ?? '—') ?></td>
                <td class="text-center">
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                </td>
                <td class="text-center">
                    <span class="badge bg-info"><?= $p['position'] ?: '—' ?></span>
                </td>
                <td class="text-center fw-bold fs-5"><?= $p['jersey_number'] ?: '—' ?></td>
                <td class="text-center small"><?= $p['date_of_birth'] ?: '—' ?></td>
                <td class="text-center small"><?= htmlspecialchars($p['id_number'] ?: '—') ?></td>

                <!-- League Stats -->
                <td class="text-center text-success fw-bold"><?= $league_goals ?></td>
                <td class="text-center text-primary"><?= $league_assists ?></td>
                <td class="text-center"><span class="badge bg-warning text-dark"><?= $league_yc ?></span></td>
                <td class="text-center"><span class="badge bg-danger"><?= $league_rc ?></span></td>
                <td class="text-center text-success"><?= $league_cs ?></td>

                <!-- Tournament Stats -->
                <td class="text-center text-success fw-bold"><?= $tourn_goals ?></td>
                <td class="text-center text-primary"><?= $tourn_assists ?></td>
                <td class="text-center"><span class="badge bg-warning text-dark"><?= $tourn_yc ?></span></td>
                <td class="text-center"><span class="badge bg-danger"><?= $tourn_rc ?></span></td>
                <td class="text-center text-success"><?= $tourn_cs ?></td>

                <td class="text-center">
                    <a href="?page=players&edit_id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" style="display:inline;" 
                          onsubmit="return confirm('Delete <?= addslashes(htmlspecialchars($p['name'])) ?>?');">
                        <input type="hidden" name="action" value="delete_player">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Del</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php if (!$has_results && !$search_name): ?>
            <tr>
                <td colspan="19" class="text-center py-4 text-muted">No players registered yet.</td> <!-- +1 for Status -->
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="playerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_player' : 'add_player' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?= $edit_id ? 'Edit' : 'Add' ?> Player</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 text-center">
                            <label>Photo</label><br>
                            <?php if (!empty($edit_data['photo'])): ?>
                                <img src="../uploads/players/<?= htmlspecialchars($edit_data['photo']) ?>" 
                                     class="img-fluid rounded-circle mb-3 shadow" style="width:150px;height:150px;object-fit:cover;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle mx-auto mb-3" style="width:150px;height:150px;"></div>
                            <?php endif; ?>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_photo" value="<?= $edit_data['photo'] ?? '' ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Club *</label>
                                    <select name="club_id" class="form-select" required>
                                        <option value="">Select Club</option>
                                        <?php foreach ($clubs as $cid => $cname): ?>
                                            <option value="<?= $cid ?>" <?= ($edit_data['club_id'] ?? 0) == $cid ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cname) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Active"     <?= ($edit_data['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Inactive"   <?= ($edit_data['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="Suspended"  <?= ($edit_data['status'] ?? '') === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                        <option value="Transferred"<?= ($edit_data['status'] ?? '') === 'Transferred' ? 'selected' : '' ?>>Transferred</option>
                                        <option value="Banned"     <?= ($edit_data['status'] ?? '') === 'Banned' ? 'selected' : '' ?>>Banned</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" 
                                           value="<?= htmlspecialchars($edit_data['date_of_birth'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ID Number</label>
                                    <input type="text" name="id_number" class="form-control" 
                                           value="<?= htmlspecialchars($edit_data['id_number'] ?? '') ?>" placeholder="e.g. Passport or License ID">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Position</label>
                                    <select name="position" class="form-select">
                                        <option value="">—</option>
                                        <option value="GK"   <?= ($edit_data['position'] ?? '')=='GK' ? 'selected':'' ?>>GK</option>
                                        <option value="DEF"  <?= ($edit_data['position'] ?? '')=='DEF' ? 'selected':'' ?>>DEF</option>
                                        <option value="MID"  <?= ($edit_data['position'] ?? '')=='MID' ? 'selected':'' ?>>MID</option>
                                        <option value="FWD"  <?= ($edit_data['position'] ?? '')=='FWD' ? 'selected':'' ?>>FWD</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jersey #</label>
                                    <input type="number" name="jersey_number" class="form-control" min="1" max="99" 
                                           value="<?= $edit_data['jersey_number'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <?= $edit_id ? 'Update Player' : 'Add Player' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal('#playerModal').show();
    });
</script>
<?php endif; ?>