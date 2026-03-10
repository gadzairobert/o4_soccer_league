<?php
// admin/pages/clubs.php
define('IN_DASHBOARD', true);
$clubsDir = '../uploads/clubs/';
if (!is_dir($clubsDir)) mkdir($clubsDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__.'/../actions/clubs.php';
}

$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];
if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

$clubs_list = $pdo->query("SELECT id, name, logo, description, stadium FROM clubs ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold text-dark">
        Clubs Directory
    </h2>
    <button type="button" class="btn btn-success shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#clubModal">
        Add New Club
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

<?php if (empty($clubs_list)): ?>
    <div class="text-center py-5">
        <div class="bg-light rounded-4 d-inline-block p-5">
            <h4 class="text-muted">No clubs added yet</h4>
            <p class="text-muted">Click the button above to add your first club!</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($clubs_list as $club): ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all club-card">
                    <div class="card-body text-center p-4">
                        <div class="club-logo mb-4">
                            <?php if ($club['logo']): ?>
                                <img src="../uploads/clubs/<?= htmlspecialchars($club['logo']) ?>" 
                                     class="img-fluid rounded-circle shadow" 
                                     style="width:120px;height:120px;object-fit:contain;background:#f8f9fa;"
                                     alt="<?= htmlspecialchars($club['name']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto shadow" 
                                     style="width:120px;height:120px;">
                                    <h3 class="text-white mb-0"><?= strtoupper(substr($club['name'], 0, 2)) ?></h3>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h5 class="card-title mb-2 fw-bold text-dark">
                            <?= htmlspecialchars($club['name']) ?>
                        </h5>

                        <?php if ($club['stadium']): ?>
                            <p class="text-primary small mb-2">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                <?= htmlspecialchars($club['stadium']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($club['description']): ?>
                            <p class="text-muted small mb-3" style="line-height:1.5;">
                                <?= nl2br(htmlspecialchars(substr($club['description'], 0, 120))) ?>
                                <?= strlen($club['description']) > 120 ? '...' : '' ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted small mb-3"><em>No description</em></p>
                        <?php endif; ?>

                        <div class="d-flex gap-2 justify-content-center mt-auto">
                            <a href="?page=clubs&edit_id=<?= $club['id'] ?>" 
                               class="btn btn-warning btn-sm px-3">
                                Edit
                            </a>
                            <form method="POST" class="d-inline" 
                                  onsubmit="return confirm('Delete <?= addslashes(htmlspecialchars($club['name'])) ?>?\nThis cannot be undone.');">
                                <input type="hidden" name="action" value="delete_club">
                                <input type="hidden" name="id" value="<?= $club['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm px-3">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL — BEAUTIFUL & MODERN -->
<div class="modal fade" id="clubModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" enctype="multipart/form-data" id="clubForm">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_club' : 'add_club' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <?= $edit_id ? 'Edit Club' : 'Add New Club' ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Club Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg" required 
                                       value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>" placeholder="e.g. Manchester United">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Stadium / Home Ground</label>
                                <input type="text" name="stadium" class="form-control" 
                                       value="<?= htmlspecialchars($edit_data['stadium'] ?? '') ?>" placeholder="e.g. Old Trafford">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="5" 
                                          placeholder="Club history, achievements, or any info..."><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                            </div>
                            <small class="text-muted">This will appear on the club card.</small>
                        </div>

                        <div class="col-lg-4">
                            <div class="text-center">
                                <label class="form-label fw-bold d-block mb-3">Club Logo</label>
                                
                                <?php if (!empty($edit_data['logo'])): ?>
                                    <div class="mb-3">
                                        <img src="../uploads/clubs/<?= htmlspecialchars($edit_data['logo']) ?>" 
                                             class="img-fluid rounded shadow-lg" 
                                             style="max-height:200px;object-fit:contain;background:#f8f9fa;">
                                        <p class="text-success small mt-2">Current logo</p>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" 
                                         style="height:200px;border:2px dashed #ccc;">
                                        <p class="text-muted">No logo uploaded</p>
                                    </div>
                                <?php endif; ?>

                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($edit_data['logo'] ?? '') ?>">
                                <small class="text-muted d-block mt-2">Recommended: Square image (PNG/JPG)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <?= $edit_id ? 'Update Club' : 'Add Club' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AUTO OPEN MODAL + CLEANUP -->
<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal('#clubModal').show();
    });
</script>
<?php endif; ?>

<!-- Optional: Add hover effect & smooth transitions -->
<style>
    .club-card {
        transition: all 0.3s ease;
        border-radius: 1rem !important;
    }
    .club-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
    .hover-shadow {
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .modal-content {
        border-radius: 1rem;
    }
    @media (max-width: 768px) {
        .club-card {
            margin-bottom: 1rem;
        }
    }
</style>

<?php
// Auto-close modal after success (optional enhancement)
if ($success || $error) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.querySelector('#clubModal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        });
    </script>";
}
?>