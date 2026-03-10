<?php
// admin/pages/logos.php
define('IN_DASHBOARD', true);

$uploadDir = '../uploads/admin/logos/';
$webPath   = '../uploads/admin/logos/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../actions/logos.php';
}

// Available logo purposes
$purposes = [
    'login_logo'        => 'Login Page Logo',
    'admin_header'      => 'Admin Panel Header',
    'frontend_header'   => 'Website Header',
    'footer_logo'       => 'Website Footer',
    'email_logo'        => 'Email Templates',
    'favicon'           => 'Favicon (32x32 PNG)',
    'sponsor_banner'    => 'Sponsor Banner',
    'trophy'            => 'Trophy / Award Icon',
    'league_shield'     => 'League Shield'
];
?>

<div class="container-fluid">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-image-fill text-primary me-3 fs-3"></i>
                <h4 class="mb-0 fw-bold">Logos Management</h4>
            </div>
            <button class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#logoModal">
                Add Logo
            </button>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <?php foreach ($purposes as $key => $label): ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM logos WHERE purpose = ? AND is_active = 1 LIMIT 1");
                    $stmt->execute([$key]);
                    $logo = $stmt->fetch();
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><?= $label ?></h6>
                                <?php if ($logo): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Not Set</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body text-center py-4">
                                <?php if ($logo): ?>
                                    <img src="<?= $webPath . htmlspecialchars($logo['filename']) ?>" 
                                         class="img-fluid rounded shadow-sm mb-3" 
                                         style="max-height: 120px; object-fit: contain;">
                                    <p class="small text-success fw-bold mb-1">
                                        <?= htmlspecialchars($logo['title']) ?>
                                    </p>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="remove_logo">
                                        <input type="hidden" name="purpose" value="<?= $key ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Remove this logo from <?= $label ?>?')">
                                            Remove
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                    </div>
                                    <p class="text-muted small mt-3">No logo assigned</p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white border-0 text-center">
                                <small class="text-muted">Click "Add Logo" to assign</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- All uploaded logos -->
            <hr class="my-5">
            <h5 class="mb-4">All Uploaded Logos (<?= $pdo->query("SELECT COUNT(*) FROM logos")->fetchColumn() ?>)</h5>
            <?php 
            $stmt = $pdo->query("SELECT * FROM logos ORDER BY uploaded_at DESC");
            if ($stmt->rowCount() == 0): ?>
                <p class="text-center text-muted py-4">No logos uploaded yet.</p>
            <?php else: ?>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
                    <?php while ($logo = $stmt->fetch()): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="<?= $webPath . htmlspecialchars($logo['filename']) ?>" 
                                     class="card-img-top" style="height: 100px; object-fit: contain; padding: 10px;">
                                <div class="card-body text-center py-2">
                                    <small class="d-block fw-bold"><?= htmlspecialchars($logo['title']) ?></small>
                                    <?php if ($logo['purpose']): ?>
                                        <span class="badge bg-primary small"><?= $purposes[$logo['purpose']] ?? $logo['purpose'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ADD LOGO MODAL -->
<div class="modal fade" id="logoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_logo">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Upload New Logo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Logo Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Main League Logo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Use For</label>
                            <select name="purpose" class="form-select">
                                <option value="">General Use (No assignment)</option>
                                <?php foreach ($purposes as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Only one active logo per purpose</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Image File</label>
                            <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.gif" required>
                            <small class="text-muted">PNG recommended for transparency • GIF allowed (including animated) • Max 2MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5">Upload & Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>