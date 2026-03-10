<?php
if (!defined('IN_DASHBOARD')) exit('Direct access not allowed');

$action = $_POST['action'] ?? '';

if ($action === 'add_user' || $action === 'edit_user') {
    $id = $action === 'edit_user' ? (int)$_POST['id'] : 0;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($action === 'add_user' && empty($password)) {
        $error = 'Password required.';
    }

    if (!isset($error)) {
        try {
            if ($action === 'add_user') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?,?,?)");
                $stmt->execute([$username, $hash, $role]);
            } else {
                $sql = "UPDATE users SET username=?, role=?";
                $params = [$username, $role];
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql .= ", password=?";
                    $params[] = $hash;
                }
                $sql .= " WHERE id=?";
                $params[] = $id;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            $success = 'User saved.';
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

} elseif ($action === 'delete_user') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id && $id != $_SESSION['admin_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $success = 'User deleted.';
    }
}