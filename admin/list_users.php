<?php
require '../config.php';
if (!isset($_SESSION['admin_id']) || $currentUser['role'] !== 'admin') {  // Reuse from dashboard or fetch again
    header('Location: login.php');
    exit;
}

$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];
if ($search) {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%";
}
if ($roleFilter) {
    $sql .= " AND role = ?";
    $params[] = $roleFilter;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><title>List Users</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <h2>Manage Users</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    
    <!-- Search Form -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control d-inline w-auto" placeholder="Search username/email" value="<?= htmlspecialchars($search) ?>">
        <select name="role" class="form-control d-inline w-auto ms-2">
            <option value="">All Roles</option>
            <option value="admin" <?= $roleFilter == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="moderator" <?= $roleFilter == 'moderator' ? 'selected' : '' ?>>Moderator</option>
            <option value="user" <?= $roleFilter == 'user' ? 'selected' : '' ?>>User</option>
        </select>
        <button type="submit" class="btn btn-primary ms-2">Filter</button>
    </form>

    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td><?= $u['created_at'] ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This cannot be undone.')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>