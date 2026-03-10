<?php
define('IN_DASHBOARD', true);
$aboutUsDir = '../uploads/admin/about_us/';
if (!is_dir($aboutUsDir)) mkdir($aboutUsDir, 0755, true);

// === CLEAN URL AFTER SUCCESSFUL SAVE ===
if (isset($_SESSION['success']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cleanUrl = '?page=about_us';
    header("Location: " . $cleanUrl);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['success'])) {
    require __DIR__ . '/../actions/about_us.php';
}

$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];
if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM about_us WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

$stmt = $pdo->query("SELECT * FROM about_us ORDER BY sort_order ASC, id ASC");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!-- YOUR ORIGINAL HTML + ONLY ADDED CATEGORY DROPDOWN BELOW -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold text-dark">About Us - Sections</h2>
    <button class="btn btn-success shadow-sm px-4 btn-lg" data-bs-toggle="modal" data-bs-target="#aboutModal">
        Add New Section
    </button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($members as $member): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 text-center hover-shadow transition-all">
                <?php if ($member['image']): ?>
                    <img src="<?= htmlspecialchars($aboutUsDir . $member['image']) ?>"
                         class="card-img-top"
                         style="height:280px; object-fit:cover;"
                         alt="<?= htmlspecialchars($member['name']) ?>">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:280px;">
                        <i class="bi bi-image fs-1 text-muted"></i>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($member['name']) ?></h5>
                    <p class="text-primary fw-bold"><?= htmlspecialchars($member['title']) ?></p>
                    <p class="text-muted small">
                        <?= nl2br(htmlspecialchars(substr($member['description'], 0, 120))) ?>
                        <?= strlen($member['description']) > 120 ? '...' : '' ?>
                    </p>
                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="<?= $member['id'] ?>">
                        Edit
                    </button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this section permanently?');">
                        <input type="hidden" name="action" value="delete_member">
                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal - ONLY ADDED CATEGORY DROPDOWN -->
<div class="modal fade" id="aboutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_member' : 'add_member' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><?= $edit_id ? 'Edit' : 'Add' ?> About Us Section</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-5 text-center">
                            <?php if (!empty($edit_data['image'])): ?>
                                <img src="<?= $aboutUsDir . htmlspecialchars($edit_data['image']) ?>"
                                     class="img-fluid rounded shadow mb-3"
                                     style="max-height:260px; object-fit:cover;">
                                <p class="text-success small fw-bold">Current Image</p>
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                     style="height:260px; border: 3px dashed #dee2e6;">
                                    <i class="bi bi-image fs-1 text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_image" value="<?= $edit_data['image'] ?? '' ?>">
                            <small class="text-muted d-block mt-2">Recommended: 800x600px • JPG/PNG</small>
                        </div>
                        <div class="col-md-7">
                            <!-- NEW: Category Dropdown -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select form-select-lg" required>
                                    <option value="about_us"     <?= ($edit_data['category']??'') === 'about_us' ? 'selected' : '' ?>>About Us</option>
                                    <option value="league_rules" <?= ($edit_data['category']??'') === 'league_rules' ? 'selected' : '' ?>>League Rules</option>
                                    <option value="sponsorships" <?= ($edit_data['category']??'') === 'sponsorships' ? 'selected' : '' ?>>Sponsorships</option>
                                    <option value="training" <?= ($edit_data['category']??'') === 'training' ? 'selected' : '' ?>>Training</option>
                                    <option value="others"       <?= ($edit_data['category']??'') === 'others' ? 'selected' : '' ?>>Others</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tab Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg"
                                       value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" required>
                                <small class="text-muted">This appears as the tab button</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Section Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                       value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                       value="<?= $edit_data['sort_order'] ?? 0 ?>">
                                <small class="text-muted">Lower number = appears first</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="8" required
                                          placeholder="Enter one point per line for beautiful bullet display on frontend..."><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <?= $edit_id ? 'Update Section' : 'Add Section' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- YOUR ORIGINAL JAVASCRIPT - UNCHANGED -->
<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const url = new URL(window.location);
        url.searchParams.set('page', 'about_us');
        url.searchParams.set('edit_id', id);
        window.history.pushState({}, '', url);
        window.location.reload();
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('edit_id')) {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('aboutModal'));
            modal.show();
        }, 150);
    }
});
</script>

<style>
.hover-shadow { transition: all 0.4s ease; }
.hover-shadow:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important; }
</style>