<?php
session_start();
require_once '../config.php';

// Block logged-in members
if (isset($_SESSION['member_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $full_name  = trim($_POST['full_name']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if username or email exists
        $stmt = $pdo->prepare("SELECT id FROM members WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or email is already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO members (username, email, full_name, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $full_name, $hash]);
            $success = "Registration successful! You can now log in.";
        }
    }
}

// Same logo logic
$stmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'login_logo' AND is_active = 1 LIMIT 1");
$stmt->execute();
$loginLogo = $stmt->fetchColumn();
$logoSrc = $loginLogo ? '../uploads/admin/logos/' . $loginLogo : '../uploads/logo.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register – 04 Soccer League Member Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
            font-family: 'Segoe UI', sans-serif; 
        }
        .login-card { max-width: 460px; width: 100%; border-radius: 1.6rem; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.5); background: white; }
        .login-header { background: #ffffff; padding: 2.2rem 2rem 1.8rem; text-align: center; border-bottom: 6px solid #198754; }
        .login-header img { height: 140px; width: auto; object-fit: contain; filter: drop-shadow(0 8px 20px rgba(0,0,0,0.3)); margin-bottom: 0.8rem; transition: transform 0.4s ease; }
        .login-header img:hover { transform: scale(1.08); }
        .login-header h3 { color: #1e293b; font-weight: 800; margin: 0; font-size: 1.75rem; }
        .login-header small { color: #64748b; font-size: 0.98rem; font-weight: 500; }
        .login-body { padding: 2.2rem 2.5rem 2.5rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
        .form-control { height: 54px; border-radius: 1rem; padding: 0 1.3rem; font-size: 1.02rem; border: 2px solid #e2e8f0; }
        .form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.18); }
        .btn-register { height: 54px; border-radius: 1rem; font-weight: 700; font-size: 1.12rem; background: linear-gradient(135deg, #198754, #157347); border: none; }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(25, 135, 84, 0.4); }
        .alert { border-radius: 1rem; padding: 0.9rem 1.2rem; font-size: 0.95rem; }
        .footer-text { color: #475569; font-size: 0.88rem; font-weight: 500; margin-top: 1.5rem; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="<?= htmlspecialchars($logoSrc) ?>" alt="04 Soccer League Logo">
        <h3>Member Registration</h3>
        <small>Join the League Portal</small>
    </div>

    <div class="login-body">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Full Name</label>
                <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Username</label>
                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-dark">Confirm Password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100 btn-register text-white">
                Create My Account
            </button>
        </form>

        <div class="text-center footer-text">
            Already have an account?<br>
            <a href="login.php" style="color:#0d6efd; font-weight:600;">Login Here</a>
            <hr class="my-4">
            © <?= date('Y') ?> <strong>04 Soccer League</strong> • All Rights Reserved
        </div>
    </div>
</div>

<script>
if (history.pushState) {
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>