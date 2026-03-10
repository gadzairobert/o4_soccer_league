<?php
// admin/pages/social_media.php
define('IN_DASHBOARD', true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../actions/social_media.php';
}

// Fetch all links
$stmt = $pdo->query("SELECT * FROM social_links ORDER BY sort_order ASC, id ASC");
$social_links = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-share-fill text-primary me-3 fs-3"></i>
                <h4 class="mb-0 fw-bold">Social Media Links</h4>
            </div>
            <button class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#socialModal">
                Add New Link
            </button>
        </div>

        <div class="card-body">
            <?php if (empty($social_links)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-share fs-1 text-muted mb-3 opacity-50"></i>
                    <p class="text-muted">No social media links added yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Platform</th>
                                <th>Icon</th>
                                <th>URL</th>
                                <th>Display In</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($social_links as $i => $link): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($link['platform_name']) ?></strong></td>
                                <td><i class="<?= htmlspecialchars($link['icon_class']) ?> fs-4"></i></td>
                                <td><small class="text-muted"><?= htmlspecialchars(substr($link['url'], 0, 50)) ?><?= strlen($link['url']) > 50 ? '...' : '' ?></small></td>
                                <td>
                                    <?php
                                    $locations = [];
                                    if ($link['display_in_header']) $locations[] = '<span class="badge bg-primary">Header</span>';
                                    if ($link['display_in_footer']) $locations[] = '<span class="badge bg-info">Footer</span>';
                                    if ($link['display_in_contact']) $locations[] = '<span class="badge bg-success">Contact</span>';
                                    echo $locations ? implode(' ', $locations) : '<span class="text-muted">—</span>';
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $link['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $link['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class "btn-group" role="group">
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#socialModal"
                                                onclick="editSocial(<?= htmlspecialchars(json_encode($link)) ?>)">
                                            Edit
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $link['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this link?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="socialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="edit_id" value="">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add Social Media Link</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Platform Name</label>
                            <input type="text" name="platform_name" id="platform_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Icon Class <a href="https://icons.getbootstrap.com/" target="_blank" class="text-primary small">Browse Icons</a></label>
                            <input type="text" name="icon_class" id="icon_class" class="form-control" placeholder="e.g. bi bi-facebook" required>
                            <small class="text-muted">Example: bi bi-instagram, bi bi-twitter-x, bi bi-tiktok</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Full URL</label>
                            <input type="url" name="url" id="url" class="form-control" placeholder="https://facebook.com/yourpage" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Display In</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="display_in_header" id="display_header" value="1">
                                        <label class="form-check-label">Header Navbar</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="display_in_footer" id="display_footer" value="1">
                                        <label class="form-check-label">Footer</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="display_in_contact" id="display_contact" value="1">
                                        <label class="form-check-label">Contact Page</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label fw-bold">Active (Visible on site)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5">Save Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit functionality
function editSocial(link) {
    document.getElementById('modalTitle').textContent = 'Edit Social Media Link';
    document.getElementById('edit_id').value = link.id;
    document.getElementById('platform_name').value = link.platform_name;
    document.getElementById('icon_class').value = link.icon_class;
    document.getElementById('url').value = link.url;
    document.getElementById('display_header').checked = !!link.display_in_header;
    document.getElementById('display_footer').checked = !!link.display_in_footer;
    document.getElementById('display_contact').checked = !!link.display_in_contact;
    document.getElementById('is_active').checked = !!link.is_active;
}
</script>