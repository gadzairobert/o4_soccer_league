<?php
// admin/pages/management.php
define('IN_DASHBOARD', true);

// Create upload directory if not exists
$managementDir = '../uploads/management/';
if (!is_dir($managementDir)) mkdir($managementDir, 0755, true);

$edit_id = (int)($_GET['edit_id'] ?? 0);
$search_name = trim($_GET['search_name'] ?? '');

$edit_data = [];
$clubs = $pdo->query("SELECT id, name FROM clubs ORDER BY name")
               ->fetchAll(PDO::FETCH_KEY_PAIR);

$roles = [
    'Referee', 'Linesman', 'Head Coach', 'Assistant Coach', 'Secretary',
    'Treasurer', 'Committee Member', 'Medical Aid', 'Councillor', 'Chairman','Vice-Chairman'
];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM management WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

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
    <h2 class="mb-0">Management & Staff Directory</h2>
    <div class="d-flex gap-2">
        <!-- Search Form -->
        <form method="GET" class="d-flex" id="searchForm">
            <input type="hidden" name="page" value="management">
            <input type="text" name="search_name" class="form-control me-2" 
                   placeholder="Search by name..." value="<?= htmlspecialchars($search_name) ?>" 
                   style="width: 250px;">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <?php if ($search_name): ?>
                <a href="?page=management" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Add Button -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#managementModal">
            Add Staff Member
        </button>
    </div>
</div>

<?php
// Build query
$sql = "
    SELECT m.*, c.name as club_name 
    FROM management m 
    LEFT JOIN clubs c ON m.club_id = c.id 
";
$params = [];

if ($search_name) {
    $sql .= " WHERE LOWER(m.full_name) LIKE LOWER(?)";
    $params[] = '%' . $search_name . '%';
}

$sql .= " ORDER BY m.full_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$has_results = $stmt->rowCount() > 0;
?>

<div class="table-responsive">
    <?php if ($search_name && !$has_results): ?>
        <div class="alert alert-info text-center py-4">
            No staff members found matching "<strong><?= htmlspecialchars($search_name) ?></strong>".
            You can now safely add this person.
        </div>
    <?php elseif ($search_name && $has_results): ?>
        <div class="alert alert-warning mb-3">
            Found <?= $stmt->rowCount() ?> staff member(s) matching "<strong><?= htmlspecialchars($search_name) ?></strong>".
        </div>
    <?php endif; ?>

    <table class="table table-hover align-middle table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Photo</th>
                <th>Full Name</th>
                <th>Club</th>
                <th>Role</th>
                <th>Date of Birth</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($m = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr class="align-middle">
                <td class="text-center small"><?= $m['id'] ?></td>
                <td class="text-center">
                    <?php if ($m['photo']): ?>
                        <img src="../uploads/management/<?= htmlspecialchars($m['photo']) ?>" 
                             width="45" class="rounded-circle shadow-sm" alt="<?= htmlspecialchars($m['full_name']) ?>">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-inline-block" style="width:45px;height:45px;"></div>
                    <?php endif; ?>
                </td>
                <td class="fw-bold"><?= htmlspecialchars($m['full_name']) ?></td>
                <td><?= htmlspecialchars($m['club_name'] ?? '—') ?></td>
                <td>
                    <span class="badge bg-primary"><?= htmlspecialchars($m['role']) ?></span>
                </td>
                <td class="text-center small"><?= $m['date_of_birth'] ?: '—' ?></td>
                <td class="text-center">
                    <span class="badge <?= $m['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="text-center">
                    <a href="?page=management&edit_id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" style="display:inline;" 
                          onsubmit="return confirm('Delete <?= addslashes(htmlspecialchars($m['full_name'])) ?>?');">
                        <input type="hidden" name="action" value="delete_management">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Del</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php if (!$has_results && !$search_name): ?>
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">No staff members registered yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="managementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_management' : 'add_management' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?= $edit_id ? 'Edit' : 'Add' ?> Staff Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 text-center">
                            <label>Photo</label><br>
                            <?php if (!empty($edit_data['photo'])): ?>
                                <img src="../uploads/management/<?= htmlspecialchars($edit_data['photo']) ?>" 
                                     class="img-fluid rounded-circle mb-3 shadow" style="width:150px;height:150px;object-fit:cover;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle mx-auto mb-3" style="width:150px;height:150px;"></div>
                            <?php endif; ?>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <input type="hidden" name="existing_photo" value="<?= $edit_data['photo'] ?? '' ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($edit_data['full_name'] ?? '') ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Club *</label>
                                    <select name="club_id" class="form-select" required>
                                        <option value="">Select Club</option>
                                        <?php foreach ($clubs as $cid => $cname): ?>
                                            <option value="<?= $cid ?>" <?= ($edit_data['club_id'] ?? 0) == $cid ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cname) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role *</label>
                                    <select name="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r ?>" <?= ($edit_data['role'] ?? '') === $r ? 'selected' : '' ?>>
                                                <?= $r ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" 
                                           value="<?= htmlspecialchars($edit_data['date_of_birth'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label><br>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                               <?= ($edit_data['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <?= $edit_id ? 'Update' : 'Add' ?> Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal('#managementModal').show();
    });
</script>
<?php endif; ?>