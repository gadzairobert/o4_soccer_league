<?php
// admin/pages/nav_bar.php
define('IN_DASHBOARD', true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../actions/nav_bar.php';
}

// Get current league name
$league_name = "WARD 24 COMMUNITY LEAGUE";
$stmt = $pdo->query("SELECT value FROM settings WHERE key_name = 'league_name' LIMIT 1");
if ($row = $stmt->fetchColumn()) {
    $league_name = $row ?: $league_name;
}

// Get all nav items
$stmt = $pdo->prepare("SELECT ni.*, p.name as parent_name FROM nav_items ni LEFT JOIN nav_items p ON ni.parent_id = p.id ORDER BY ni.parent_id ASC, ni.sort_order ASC");
$stmt->execute();
$all_items = $stmt->fetchAll();

$main_items = array_filter($all_items, fn($i) => $i['parent_id'] == 0);
$dropdown_structure = [];
foreach ($all_items as $item) {
    if ($item['parent_id'] > 0) $dropdown_structure[$item['parent_id']][] = $item;
}
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- League Name Editor -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>League / Site Name</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="save_league_name">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Name</label>
                            <input type="text" name="league_name" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars($league_name) ?>" required>
                            <div class="form-text">This appears in the dark navbar next to the logo</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check2-circle me-2"></i>Update League Name
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-menu-app-fill text-primary me-3"></i>Navigation Menu</h4>
                    <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#navModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                    </button>
                </div>

                <div class="card-body">
                    <div class="alert alert-info small">
                        Short URLs allowed (<code>news.php</code>), leave URL empty for dropdown parents
                    </div>

                    <?php if (empty($main_items)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-menu-app fs-1 mb-3 opacity-50"></i>
                            <p>No menu items added yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Menu Item</th>
                                        <th>URL</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($main_items as $i => $item):
                                        $has_dropdown = isset($dropdown_structure[$item['id']]) && count($dropdown_structure[$item['id']]) > 0;
                                    ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                                            <?php if ($has_dropdown): ?>
                                                <span class="badge bg-warning text-dark ms-2">Dropdown (<?= count($dropdown_structure[$item['id']]) ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small><?= $item['link'] ? htmlspecialchars($item['link']) : '<em class="text-danger">Dropdown Parent</em>' ?></small></td>
                                        <td><span class="badge bg-<?= $has_dropdown ? 'warning' : 'info' ?>"><?= $has_dropdown ? 'Dropdown' : 'Link' ?></span></td>
                                        <td><span class="badge bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" style="gap: 6px;">
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#navModal"
                                                        onclick='editItem(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="toggle">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-<?= $item['is_active'] ? 'danger' : 'success' ?>">
                                                        <i class="bi bi-eye<?= $item['is_active'] ? '' : '-slash' ?>"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this item and all sub-items permanently?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php if ($has_dropdown): foreach ($dropdown_structure[$item['id']] as $sub): ?>
                                    <tr class="table-light">
                                        <td></td>
                                        <td style="padding-left: 50px;">
                                            <i class="bi bi-arrow-return-right text-muted me-2"></i>
                                            <?= htmlspecialchars($sub['name']) ?>
                                        </td>
                                        <td><small><?= htmlspecialchars($sub['link']) ?></small></td>
                                        <td><em>Sub-item</em></td>
                                        <td><span class="badge bg-<?= $sub['is_active'] ? 'success' : 'secondary' ?> small"><?= $sub['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                        <td class="text-center">...</td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="navModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add Menu Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Menu Name *</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">URL</label>
                            <input type="text" name="link" id="link" class="form-control" placeholder="e.g. news.php">
                            <small class="text-muted">Leave empty if this is a dropdown parent</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Parent (for dropdown items)</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="0">— Main Menu —</option>
                                <?php foreach ($main_items as $m): 
                                    if (empty($m['link']) || isset($dropdown_structure[$m['id']])): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (Dropdown)</option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" value="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Icon Class (optional)</label>
                            <input type="text" name="icon_class" id="icon_class" class="form-control" placeholder="bi bi-trophy">
                            <small><a href="https://icons.getbootstrap.com" target="_blank">Browse icons</a></small>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_blank" id="target_blank" value="1">
                                <label class="form-check-label">Open link in new tab</label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label">Active (visible on site)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Menu Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editItem(item) {
    document.getElementById('modalTitle').textContent = 'Edit Menu Item';
    document.getElementById('edit_id').value = item.id;
    document.getElementById('name').value = item.name;
    document.getElementById('link').value = item.link || '';
    document.getElementById('parent_id').value = item.parent_id || 0;
    document.getElementById('sort_order').value = item.sort_order || 0;
    document.getElementById('icon_class').value = item.icon_class || '';
    document.getElementById('target_blank').checked = !!item.target_blank;
    document.getElementById('is_active').checked = !!item.is_active;
}
</script>