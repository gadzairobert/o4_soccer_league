<?php
// admin/actions/clubs.php

if (!defined('IN_DASHBOARD')) {
    exit('Direct access not allowed');
}

require_once __DIR__ . '/../includes/functions.php';

// Define upload directory (absolute path)
$clubsDir = __DIR__ . '/../../uploads/clubs/';
if (!is_dir($clubsDir)) {
    mkdir($clubsDir, 0755, true);
}

$action = $_POST['action'] ?? '';

if ($action === 'add_club' || $action === 'edit_club') {
    $id          = $action === 'edit_club' ? (int)($_POST['id'] ?? 0) : 0;
    $name        = trim($_POST['name'] ?? '');
    $stadium     = trim($_POST['stadium'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $logo        = $_POST['existing_logo'] ?? null; // Keep old logo if no new upload

    // === HANDLE LOGO UPLOAD ===
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['logo'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, GIF, WebP allowed.';
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB
            $_SESSION['error'] = 'Logo too large. Max 5MB allowed.';
        } else {
            // Generate unique filename
            $filename = 'club_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $destination = $clubsDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $logo = $filename; // Save only filename in DB

                // Optional: Delete old logo on edit
                if ($action === 'edit_club' && !empty($_POST['existing_logo'])) {
                    $oldFile = $clubsDir . $_POST['existing_logo'];
                    if (file_exists($oldFile) && is_file($oldFile)) {
                        unlink($oldFile);
                    }
                }
            } else {
                $_SESSION['error'] = 'Failed to upload logo. Check folder permissions.';
            }
        }
    }

    // === SAVE TO DATABASE ===
    if (empty($name)) {
        $_SESSION['error'] = 'Club name is required.';
    } elseif (!isset($_SESSION['error'])) {
        try {
            if ($action === 'add_club') {
                $stmt = $pdo->prepare("INSERT INTO clubs (name, stadium, description, logo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $stadium, $description, $logo]);
                $_SESSION['success'] = 'Club added successfully!';
            } else {
                $stmt = $pdo->prepare("UPDATE clubs SET name = ?, stadium = ?, description = ?, logo = ? WHERE id = ?");
                $stmt->execute([$name, $stadium, $description, $logo, $id]);
                $_SESSION['success'] = 'Club updated successfully!';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }

} elseif ($action === 'delete_club') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            // Get logo to delete file
            $stmt = $pdo->prepare("SELECT logo FROM clubs WHERE id = ?");
            $stmt->execute([$id]);
            $club = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($club && $club['logo']) {
                $filePath = $clubsDir . $club['logo'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM clubs WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Club deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting club: ' . $e->getMessage();
        }
    }
}

// Always redirect back
header("Location: ?page=clubs" . ($edit_id ?? '' ? "&edit_id=$edit_id" : ''));
exit;