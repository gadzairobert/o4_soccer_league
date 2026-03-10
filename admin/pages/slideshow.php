<?php
// admin/pages/slideshow.php

define('IN_DASHBOARD', true);

// Simple, proven path — same logic as players.php
$slideshowDir = '../uploads/admin/slideshow/';
if (!is_dir($slideshowDir)) {
    mkdir($slideshowDir, 0755, true);
}

// Edit mode
$edit_id   = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM slideshow_images WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

// Success/error messages
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
    <h2>Slideshow Images</h2>
    <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#slideshowModal">
        Add New Image
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Caption</th>
                <th>Alt Text</th>
                <th>Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="slideshowTableBody">
            <?php
            $imgs = $pdo->query("SELECT * FROM slideshow_images ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

            if (empty($imgs)): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        No images yet. Click "Add New Image" to start.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($imgs as $i => $img): ?>
                    <tr class="slideshow-row" data-id="<?= $img['id'] ?>">
                        <td class="text-center fw-bold"><?= $i + 1 ?></td>
                        <td>
                            <?php if ($img['filename']): ?>
                                <img src="../uploads/admin/slideshow/<?= htmlspecialchars($img['filename']) ?>"
                                     class="img-thumbnail"
                                     style="width:140px;height:80px;object-fit:cover;"
                                     alt="<?= htmlspecialchars($img['alt_text']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary rounded" style="width:140px;height:80px;"></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($img['caption'] ?: '—') ?></td>
                        <td><?= htmlspecialchars($img['alt_text'] ?: '—') ?></td>
                        <td class="text-center"><span class="badge bg-light text-dark"><?= $img['sort_order'] ?></span></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $img['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $img['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <a href="?page=slideshow&edit_id=<?= $img['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>

                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle_active">
                                <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                <button class="btn btn-sm btn-<?= $img['is_active'] ? 'danger' : 'success' ?> me-1">
                                    <?= $img['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>

                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this image?');">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; // ← THIS WAS MISSING BEFORE! ?>
        </tbody>
    </table>
</div>

<?php if (!empty($imgs)): ?>
<form method="POST" class="mt-4">
    <input type="hidden" name="action" value="save_order">
    <div id="orderInputs"></div>
    <button type="submit" class="btn btn-primary">Save Order</button>
</form>
<?php endif; ?>

<!-- Modal -->
<div class="modal fade" id="slideshowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_image' : 'add_image' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?= $edit_id ? 'Edit Image' : 'Add New Image' ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept="image/*" <?= $edit_id ? '' : 'required' ?>>
                                <input type="hidden" name="existing_filename" value="<?= $edit_data['filename'] ?? '' ?>">
                            </div>

                            <?php if (!empty($edit_data['filename'])): ?>
                                <div class="text-center mt-3">
                                    <img src="../uploads/admin/slideshow/<?= htmlspecialchars($edit_data['filename']) ?>"
                                         class="img-fluid rounded border" style="max-height:220px;">
                                    <small class="text-muted d-block mt-2">Current image</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Caption</label>
                                <input type="text" name="caption" class="form-control" value="<?= htmlspecialchars($edit_data['caption'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label>Alt Text</label>
                                <input type="text" name="alt_text" class="form-control" value="<?= htmlspecialchars($edit_data['alt_text'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" min="0" value="<?= $edit_data['sort_order'] ?? 0 ?>">
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" id="isActive"
                                    <?= (!isset($edit_data['is_active']) || $edit_data['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Active (shown in slideshow)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><?= $edit_id ? 'Update' : 'Add Image' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal('#slideshowModal').show());
</script>
<?php endif; ?>

<script>
// Simple drag-to-reorder
document.querySelectorAll('.slideshow-row').forEach(row => {
    row.draggable = true;
    row.addEventListener('dragstart', () => row.classList.add('dragging'));
    row.addEventListener('dragend', () => row.classList.remove('dragging'));
    row.addEventListener('dragover', e => e.preventDefault());
    row.addEventListener('drop', e => {
        e.preventDefault();
        const dragged = document.querySelector('.dragging');
        if (!dragged || dragged === row) return;
        const after = [...row.parentNode.children].indexOf(row) > [...row.parentNode.children].indexOf(dragged);
        after ? row.after(dragged) : row.before(dragged);

        document.querySelectorAll('.slideshow-row').forEach((r, i) => {
            r.querySelector('td:first-child').textContent = i + 1;
            r.querySelector('.badge').textContent = i;
        });

        const container = document.getElementById('orderInputs');
        container.innerHTML = '';
        document.querySelectorAll('.slideshow-row').forEach((r, i) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order[' + i + ']';
            input.value = r.dataset.id;
            container.appendChild(input);
        });
    });
});
</script>