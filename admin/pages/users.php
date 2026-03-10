<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__.'/../actions/users.php';
}

$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];
if ($edit_id) {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Users</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userModal">Add User</button>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Actions</th></tr></thead>
        <tbody>
        <?php
        $stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY id");
        while ($u = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><span class="badge bg-<?= $u['role']==='admin'?'danger':'primary' ?>"><?= ucfirst($u['role']) ?></span></td>
                <td>
                    <a href="?page=users&edit_id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <?php if ($u['id'] != $_SESSION['admin_id']): ?>
                        <button class="btn btn-sm btn-danger delete-btn"
                                data-delete-url="?page=users"
                                data-id="<?= $u['id'] ?>">Delete</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_user' : 'add_user' ?>">
                <?php if ($edit_id): ?><input type="hidden" name="id" value="<?= $edit_id ?>"><?php endif; ?>
                <div class="modal-header"><h5 class="modal-title"><?= $edit_id?'Edit':'Add' ?> User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username *</label>
                        <input type="text" name="username" class="form-control" value="<?= $edit_data['username']??'' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Password <?= $edit_id?'(leave blank to keep)':'' ?> *</label>
                        <input type="password" name="password" class="form-control" <?= $edit_id?'':'required' ?>>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="user" <?= ($edit_data['role']??'user')==='user'?'selected':'' ?>>User</option>
                            <option value="admin" <?= ($edit_data['role']??'')==='admin'?'selected':'' ?>>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><?= $edit_id?'Update':'Add' ?> User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal('#userModal').show());
</script>
<?php endif; ?>