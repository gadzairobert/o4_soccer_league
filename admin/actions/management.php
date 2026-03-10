<?php
// admin/actions/management.php
if (!defined('IN_DASHBOARD')) {
    die('Direct access not allowed');
}

$action = $_POST['action'] ?? '';

if ($action === 'add_management' || $action === 'edit_management') {

    $id            = $action === 'edit_management' ? (int)($_POST['id'] ?? 0) : 0;
    $full_name     = trim($_POST['full_name'] ?? '');
    $club_id       = (int)($_POST['club_id'] ?? 0);
    $role          = $_POST['role'] ?? '';
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    $photo         = $_POST['existing_photo'] ?? null;

    // Photo upload handling
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed) && $file['size'] < 5_000_000) {
            $filename = 'management_' . uniqid() . '.' . $ext;
            $path = '../uploads/management/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $photo = $filename;

                // Delete old photo on edit
                if ($action === 'edit_management' && !empty($_POST['existing_photo']) && $_POST['existing_photo'] !== $filename) {
                    @unlink('../uploads/management/' . $_POST['existing_photo']);
                }
            }
        }
    }

    if (!$full_name || !$club_id || !$role) {
        $_SESSION['error'] = 'Full name, club and role are required.';
        header('Location: ?page=management');
        exit;
    }

    try {
        // Duplicate check: same full name (case-insensitive)
        $dupSql = "SELECT id FROM management WHERE LOWER(full_name) = LOWER(?)";
        $dupParams = [$full_name];

        if ($action === 'edit_management' && $id > 0) {
            $dupSql .= " AND id != ?";
            $dupParams[] = $id;
        }

        $stmt = $pdo->prepare($dupSql);
        $stmt->execute($dupParams);

        if ($stmt->fetch()) {
            $_SESSION['error'] = 'A staff member with the name "' . htmlspecialchars($full_name) . '" already exists.';
            header('Location: ?page=management');
            exit;
        }

        // Insert or Update
        if ($action === 'add_management') {
            $stmt = $pdo->prepare("INSERT INTO management 
                (full_name, club_id, role, date_of_birth, is_active, photo, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$full_name, $club_id, $role, $date_of_birth, $is_active, $photo]);
            $_SESSION['success'] = 'Staff member added successfully.';
        } else {
            $stmt = $pdo->prepare("UPDATE management 
                SET full_name = ?, club_id = ?, role = ?, date_of_birth = ?, 
                    is_active = ?, photo = ? 
                WHERE id = ?");
            $stmt->execute([$full_name, $club_id, $role, $date_of_birth, $is_active, $photo, $id]);
            $_SESSION['success'] = 'Staff member updated successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

} elseif ($action === 'delete_management') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT photo FROM management WHERE id = ?");
            $stmt->execute([$id]);
            $member = $stmt->fetch();

            $stmt = $pdo->prepare("DELETE FROM management WHERE id = ?");
            $stmt->execute([$id]);

            if ($member && $member['photo']) {
                @unlink('../uploads/management/' . $member['photo']);
            }

            $_SESSION['success'] = 'Staff member deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete staff member.';
        }
    } else {
        $_SESSION['error'] = 'Invalid ID.';
    }
}

header('Location: ?page=management');
exit;