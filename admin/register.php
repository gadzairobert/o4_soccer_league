<?php
require '../config.php';
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'user';  // Default user; admin can promote later

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (empty($username) || empty($email)) {
        $error = "Username and email are required.";
    } else {
        // Check if user exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        if ($checkStmt->fetch()) {
            $error = "Username or email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashedPassword, $email, $role])) {
                $success = "User registered successfully! You can now log in.";
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Register - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h2>Register New User</h2>
                <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                <form method="POST">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                    <select name="role" class="form-control mb-2">
                        <option value="user">User</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Register</button>
                    <a href="login.php" class="btn btn-secondary">Back to Login</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>