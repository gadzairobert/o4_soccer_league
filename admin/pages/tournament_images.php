<?php
// admin/pages/tournament_images.php
define('IN_DASHBOARD', true);

$uploadDir = '../uploads/tournaments/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__.'/../actions/tournament_images.php';
}

// Get current cup season
$currentCup = $pdo->query("SELECT id, name, season FROM competition_seasons WHERE is_current = 1 AND type = 'cup' LIMIT 1")->fetch();
$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM tournament_images WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

// Fetch all images
$images = $pdo->query("
    SELECT ti.*, cs.name AS tournament_name, cs.season 
    FROM tournament_images ti
    LEFT JOIN competition_seasons cs ON ti.competition_season_id = cs.id
    ORDER BY ti.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get all cup seasons for dropdown
$seasons = $pdo->query("SELECT id, name, season FROM competition_seasons WHERE type = 'cup' ORDER BY season DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4 fw-bold text-dark">Tournament Images</h2>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Upload and manage images for tournaments (appears on tournaments.php)</p>
    </div>
    <button type="button" class="btn btn-success shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#imageModal">
        <i class="bi bi-plus-lg"></i> Add Image
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">#</th>
                <th width="180">Image</th>
                <th>Tournament</th>
                <th>Caption</th>
                <th width="140">Uploaded</th>
                <th width="160" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($images)): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted">No images uploaded yet.</td></tr>
            <?php else: foreach ($images as $i => $img): ?>
                <tr>
                    <td class="text-center fw-bold"><?= $i + 1 ?></td>
                    <td>
                        <img src="../uploads/tournaments/<?= htmlspecialchars($img['image']) ?>"
                             class="img-thumbnail rounded shadow-sm"
                             style="width:160px;height:100px;object-fit:cover;" alt="">
                    </td>
                    <td class="fw-semibold">
                        <?= htmlspecialchars($img['tournament_name'] ?? '—') ?>
                        <small class="text-muted d-block"><?= $img['season'] ?? '' ?></small>
                    </td>
                    <td><?= $img['caption'] ? htmlspecialchars($img['caption']) : '<em class="text-muted">— No caption —</em>' ?></td>
                    <td class="text-muted small">
                        <?= date('M j, Y', strtotime($img['created_at'])) ?><br>
                        <span class="text-primary"><?= date('g:i A', strtotime($img['created_at'])) ?></span>
                    </td>
                    <td class="text-center">
                        <a href="?page=tournament_images&edit_id=<?= $img['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this image permanently?');">
                            <input type="hidden" name="action" value="delete_image">
                            <input type="hidden" name="id" value="<?= $img['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_image' : 'add_image' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><?= $edit_id ? 'Edit Image' : 'Add New Tournament Image' ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label fw-bold">Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*" <?= $edit_id ? '' : 'required' ?>>
                            <input type="hidden" name="existing_image" value="<?= $edit_data['image'] ?? '' ?>">

                            <?php if (!empty($edit_data['image'])): ?>
                                <div class="mt-3 text-center">
                                    <img src="../uploads/tournaments/<?= htmlspecialchars($edit_data['image']) ?>"
                                         class="img-fluid rounded shadow" style="max-height:280px;">
                                    <small class="text-success d-block mt-2">Current image</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tournament <span class="text-danger">*</span></label>
                                <select name="competition_season_id" class="form-select" required>
                                    <option value="">— Select Tournament —</option>
                                    <?php foreach ($seasons as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= ($edit_data['competition_season_id'] ?? $currentCup['id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['name']) ?> <?= $s['season'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Caption (Optional)</label>
                                <textarea name="caption" class="form-control" rows="4" placeholder="e.g. Final Match Celebration"><?= htmlspecialchars($edit_data['caption'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <?= $edit_id ? 'Update Image' : 'Upload Image' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto-open modal on edit -->
<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal('#imageModal').show();
    });
</script>
<?php endif; ?>