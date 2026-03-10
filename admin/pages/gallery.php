<?php
// admin/pages/gallery.php
define('IN_DASHBOARD', true);

$galleryDir = '../uploads/gallery/';
if (!is_dir($galleryDir)) mkdir($galleryDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__.'/../actions/gallery.php';
}

$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];
if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

$images = $pdo->query("
    SELECT id, title, description, image, uploaded_at 
    FROM gallery 
    ORDER BY uploaded_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold text-dark">Photo Gallery</h2>
    <button type="button" class="btn btn-success shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#galleryModal">
        Add New Image
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">#</th>
                <th width="160">Image</th>
                <th>Title</th>
                <th>Description</th>
                <th width="130">Uploaded</th>
                <th width="180" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="galleryTableBody">
            <?php if (empty($images)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="text-center py-4">
                            <h5>No images in gallery yet</h5>
                            <p>Click "Add New Image" to upload your first photo!</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($images as $i => $img): ?>
                    <tr class="gallery-row" data-id="<?= $img['id'] ?>">
                        <td class="text-center fw-bold"><?= $i + 1 ?></td>
                        <td>
                            <img src="../uploads/gallery/<?= htmlspecialchars($img['image']) ?>"
                                 class="img-thumbnail rounded shadow-sm"
                                 style="width:140px;height:90px;object-fit:cover;"
                                 alt="<?= htmlspecialchars($img['title']) ?>">
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($img['title']) ?></td>
                        <td>
                            <?php if ($img['description']): ?>
                                <span class="text-muted small">
                                    <?= nl2br(htmlspecialchars(substr($img['description'], 0, 100))) ?>
                                    <?= strlen($img['description']) > 100 ? '...' : '' ?>
                                </span>
                            <?php else: ?>
                                <em class="text-muted">— No description —</em>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= date('M j, Y', strtotime($img['uploaded_at'])) ?><br>
                            <span class="text-primary"><?= date('g:i A', strtotime($img['uploaded_at'])) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="?page=gallery&edit_id=<?= $img['id'] ?>"
                               class="btn btn-sm btn-warning me-1">
                                Edit
                            </a>

                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this image permanently?');">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL - Same Style as Slideshow -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_image' : 'add_image' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><?= $edit_id ? 'Edit Image' : 'Add New Image' ?></h5>
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
                                    <img src="../uploads/gallery/<?= htmlspecialchars($edit_data['image']) ?>"
                                         class="img-fluid rounded shadow" style="max-height:260px;">
                                    <small class="text-success d-block mt-2">Current image</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg" required
                                       value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>"
                                       placeholder="e.g. Champions League Final 2024">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Description (Optional)</label>
                                <textarea name="description" class="form-control" rows="5"
                                          placeholder="Event, players, moment..."><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
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

<!-- Auto open modal on edit -->
<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal('#galleryModal').show();
    });
</script>
<?php endif; ?>

<!-- Close modal after success/error -->
<?php if ($success || $error): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('galleryModal'));
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    });
</script>
<?php endif; ?>