<?php
// admin/pages/constitutions.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    require __DIR__ . '/../actions/constitutions.php';
}

$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';

$edit_id   = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM club_constitutions WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Load all constitutions newest first
$constitutions = $pdo->query("
    SELECT * FROM club_constitutions 
    ORDER BY effective_date DESC, created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

/**
 * Format bytes into a human-readable string.
 */
function formatBytes(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1)    . ' KB';
    return $bytes . ' B';
}
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

<!-- ===== Page Header ===== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Club Constitution</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#constitutionModal">
        <i class="bi bi-plus-lg me-1"></i> Upload New Constitution
    </button>
</div>

<!-- ===== Constitutions Table ===== -->
<div class="card">
    <div class="card-body">
        <?php if (empty($constitutions)): ?>
            <p class="text-muted text-center py-5">
                <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                No constitutions uploaded yet. Upload your first club constitution!
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Version</th>
                            <th>Effective Date</th>
                            <th>File Size</th>
                            <th>Uploaded By</th>
                            <th>Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($constitutions as $c): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($c['title']) ?></strong>
                                    <?php if ($c['description']): ?>
                                        <div class="text-muted small"><?= htmlspecialchars(mb_strimwidth($c['description'], 0, 80, '…')) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($c['version']) ?></span></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($c['effective_date']))) ?></td>
                                <td><?= formatBytes((int)$c['pdf_size']) ?></td>
                                <td><?= htmlspecialchars($c['uploaded_by'] ?: '—') ?></td>
                                <td>
                                    <?php if ($c['is_active']): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                                    <?php else: ?>
                                        <em class="text-muted">Archived</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    // pdf_path is relative from site root e.g. uploads/constitutions/file.pdf
                                    // Admin panel lives in /admin/ subfolder, so we must build an absolute URL
                                    // to avoid the browser prepending /admin/ to the path.
                                    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                                    $host     = $_SERVER['HTTP_HOST'];
                                    // SCRIPT_NAME = /04sl/admin/index.php → go up one level to get site base
                                    $adminDir = dirname($_SERVER['SCRIPT_NAME']);          // e.g. /04sl/admin
                                    $siteBase = rtrim(dirname($adminDir), '/\\');          // e.g. /04sl
                                    $absUrl   = $scheme . '://' . $host . $siteBase . '/' . ltrim($c['pdf_path'], '/');
                                    $safeUrl  = htmlspecialchars($absUrl);
                                    ?>
                                    <!-- View / Download PDF -->
                                    <a href="<?= $safeUrl ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View PDF">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= $safeUrl ?>"
                                       download
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Download PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <!-- Edit -->
                                    <a href="?page=constitutions&edit_id=<?= $c['id'] ?>"
                                       class="btn btn-sm btn-info"
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <!-- Delete -->
                                    <a href="?page=constitutions&action=delete_constitution&id=<?= $c['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       title="Delete"
                                       onclick="return confirm('Delete this constitution and its PDF file? This cannot be undone.');">
                                        <i class="bi bi-trash"></i>
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

<!-- ===== Add / Edit Modal ===== -->
<div class="modal fade" id="constitutionModal" tabindex="-1" aria-labelledby="constitutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- enctype MUST be multipart/form-data for file uploads -->
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_constitution' : 'add_constitution' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header">
                    <h5 class="modal-title" id="constitutionModalLabel">
                        <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>
                        <?= $edit_id ? 'Edit' : 'Upload New' ?> Club Constitution
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Title -->
                        <div class="col-md-8">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   required
                                   value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>"
                                   placeholder="e.g. Club Constitution & Rules">
                            <div class="form-text">Full display title of this constitution document.</div>
                        </div>

                        <!-- Version -->
                        <div class="col-md-4">
                            <label class="form-label">Version <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="version"
                                   class="form-control"
                                   required
                                   value="<?= htmlspecialchars($edit_data['version'] ?? '') ?>"
                                   placeholder="e.g. v3.0 or 2024">
                            <div class="form-text">Must be unique across all versions.</div>
                        </div>

                        <!-- Effective Date -->
                        <div class="col-md-6">
                            <label class="form-label">Effective Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="effective_date"
                                   class="form-control"
                                   required
                                   value="<?= htmlspecialchars($edit_data['effective_date'] ?? '') ?>">
                        </div>

                        <!-- Uploaded By -->
                        <div class="col-md-6">
                            <label class="form-label">Uploaded / Approved By</label>
                            <input type="text"
                                   name="uploaded_by"
                                   class="form-control"
                                   value="<?= htmlspecialchars($edit_data['uploaded_by'] ?? '') ?>"
                                   placeholder="e.g. Club Secretary, AGM 2024">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Description / Notes</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Brief description of this version or key changes…"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                        </div>

                        <!-- PDF Upload -->
                        <div class="col-12">
                            <label class="form-label">
                                PDF File <?= $edit_id ? '' : '<span class="text-danger">*</span>' ?>
                            </label>
                            <input type="file"
                                   name="pdf_file"
                                   id="pdf_file"
                                   class="form-control"
                                   accept=".pdf,application/pdf"
                                   <?= $edit_id ? '' : 'required' ?>>
                            <div class="form-text">
                                <?php if ($edit_id && !empty($edit_data['pdf_filename'])): ?>
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    Current file: <strong><?= htmlspecialchars($edit_data['pdf_filename']) ?></strong>
                                    (<?= formatBytes((int)$edit_data['pdf_size']) ?>)
                                    — leave blank to keep existing file.
                                    <br>
                                <?php endif; ?>
                                Only <strong>.pdf</strong> files are accepted. Maximum size: <strong>5 MB</strong>.
                            </div>

                            <!-- Client-side size warning -->
                            <div id="pdf_size_warning" class="alert alert-warning mt-2 d-none">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Selected file exceeds 5 MB. Please choose a smaller PDF.
                            </div>
                        </div>

                        <!-- Active Flag -->
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       <?= ($edit_data['is_active'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Mark as the Active / Current Constitution</strong>
                                </label>
                                <div class="form-text">Only one constitution can be active at a time. All others will be archived.</div>
                            </div>
                        </div>

                    </div><!-- /.row -->
                </div><!-- /.modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-cloud-upload me-1"></i>
                        <?= $edit_id ? 'Update Constitution' : 'Upload Constitution' ?>
                    </button>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- ===== Scripts ===== -->
<script>
(function () {
    const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    const fileInput  = document.getElementById('pdf_file');
    const warning    = document.getElementById('pdf_size_warning');
    const submitBtn  = document.getElementById('submitBtn');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.size > MAX_SIZE) {
                warning.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                warning.classList.add('d-none');
                submitBtn.disabled = false;
            }
        });
    }
})();
</script>

<?php if ($edit_id > 0): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('constitutionModal')).show();
    });
</script>
<?php endif; ?>