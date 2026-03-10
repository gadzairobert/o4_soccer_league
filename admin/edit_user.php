<?php
require '../config.php';
if (!isset($_SESSION['admin_id']) || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) header('Location: list_users.php');

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) header('Location: list_users.php');

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];  // Optional

    if (empty($username) || empty($email)) {
        $error = "Username and email required.";
    } else {
        $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $params = [$username, $email, $role, $id];
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
            $params = [$username, $email, $role, $hashed, $id];
        }
        $updateStmt = $pdo->prepare($sql);
        if ($updateStmt->execute($params)) {
            $success = "User updated successfully.";
            $stmt->execute([$id]);  // Refresh
            $user = $stmt->fetch();
        } else {
            $error = "Update failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Edit User</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <h2>Edit User: <?= htmlspecialchars($user['username']) ?></h2>
    <a href="list_users.php" class="btn btn-secondary mb-3">Back to List</a>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <form method="POST" class="card p-4">
        <input type="text" name="username" class="form-control mb-2" value="<?= htmlspecialchars($user['username']) ?>" required>
        <input type="email" name="email" class="form-control mb-2" value="<?= htmlspecialchars($user['email']) ?>" required>
        <input type="password" name="password" class="form-control mb-2" placeholder="New Password (leave blank to keep current)">
        <select name="role" class="form-control mb-2" required>
            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
            <option value="moderator" <?= $user['role'] == 'moderator' ? 'selected' : '' ?>>Moderator</option>
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</body>
</html>