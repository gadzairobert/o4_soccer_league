<?php
// admin/pages/competitions.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    require __DIR__.'/../actions/competitions.php';
}

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM competition_seasons WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Load all competition seasons
$seasons = $pdo->query("
    SELECT * FROM competition_seasons 
    ORDER BY season DESC, competition_name ASC
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
    <h2>Competitions & Seasons</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCompetitionModal">
        Add New Competition
    </button>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($seasons)): ?>
            <p class="text-muted text-center py-5">No competitions added yet. Add your first league or cup season!</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Full Name</th>
                            <th>Competition</th>
                            <th>Season</th>
                            <th>Type</th>
                            <th>Country</th>
                            <th>Current?</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seasons as $s): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                                <td><?= htmlspecialchars($s['competition_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= $s['season'] ?></span></td>
                                <td>
                                    <span class="badge bg-<?= $s['type'] == 'cup' ? 'danger' : ($s['type'] == 'international' ? 'info' : 'primary') ?>">
                                        <?= ucfirst($s['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($s['country'] ?: '—') ?></td>
                                <td>
                                    <?php if ($s['is_current']): ?>
                                        <span class="badge bg-success">Current</span>
                                    <?php else: ?>
                                        <em class="text-muted">No</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=competitions&edit_id=<?= $s['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="?page=competitions&action=delete_competition&id=<?= $s['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Delete this competition season? This cannot be undone.');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addCompetitionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_competition' : 'add_competition' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header">
                    <h5 class="modal-title"><?= $edit_id ? 'Edit' : 'Add New' ?> Competition & Season</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Full Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>"
                                   placeholder="e.g. Premier League 2024/2025">
                            <div class="form-text">How it will appear in fixtures and standings</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Season Year <span class="text-danger">*</span></label>
                            <input type="number" name="season" class="form-control" required min="1900" max="2100"
                                   value="<?= $edit_data['season'] ?? date('Y') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Competition Name <span class="text-danger">*</span></label>
                            <input type="text" name="competition_name" class="form-control" required 
                                   value="<?= htmlspecialchars($edit_data['competition_name'] ?? '') ?>"
                                   placeholder="e.g. Premier League">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="league" <?= ($edit_data['type'] ?? '') == 'league' ? 'selected' : '' ?>>League</option>
                                <option value="cup" <?= ($edit_data['type'] ?? '') == 'cup' ? 'selected' : '' ?>>Cup / Knockout</option>
                                <option value="international" <?= ($edit_data['type'] ?? '') == 'international' ? 'selected' : '' ?>>International</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control"
                                   value="<?= htmlspecialchars($edit_data['country'] ?? '') ?>"
                                   placeholder="e.g. England, Europe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Logo URL (optional)</label>
                            <input type="url" name="logo" class="form-control"
                                   value="<?= htmlspecialchars($edit_data['logo'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_current" id="is_current"
                                       <?= ($edit_data['is_current'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_current">
                                    Mark as Current Active Season
                                </label>
                                <div class="form-text">Only one season per competition can be current.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?= $edit_id ? 'Update Competition' : 'Add Competition' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_id > 0): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('addCompetitionModal')).show();
    });
</script>
<?php endif; ?>