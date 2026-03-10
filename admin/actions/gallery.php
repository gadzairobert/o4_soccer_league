<?php
// admin/actions/gallery.php

if (!defined('IN_DASHBOARD')) {
    exit('Direct access not allowed');
}

require_once __DIR__ . '/../includes/functions.php';

// Define upload directory
$galleryDir = __DIR__ . '/../../uploads/gallery/';
if (!is_dir($galleryDir)) {
    mkdir($galleryDir, 0755, true);
}

$action = $_POST['action'] ?? '';

if ($action === 'add_image' || $action === 'edit_image') {
    $id          = $action === 'edit_image' ? (int)($_POST['id'] ?? 0) : 0;
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image       = $_POST['existing_image'] ?? null; // Keep old image if no new upload

    // === HANDLE IMAGE UPLOAD ===
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, GIF, WebP allowed.';
        } elseif ($file['size'] > 10 * 1024 * 1024) { // 10MB max
            $_SESSION['error'] = 'Image too large. Max 10MB allowed.';
        } else {
            $filename = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $destination = $galleryDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image = $filename;

                // Delete old image on edit
                if ($action === 'edit_image' && !empty($_POST['existing_image'])) {
                    $oldFile = $galleryDir . $_POST['existing_image'];
                    if (file_exists($oldFile) && is_file($oldFile)) {
                        unlink($oldFile);
                    }
                }
            } else {
                $_SESSION['error'] = 'Failed to upload image. Check folder permissions.';
            }
        }
    }

    // === SAVE TO DATABASE ===
    if (empty($title)) {
        $_SESSION['error'] = 'Image title is required.';
    } elseif (!isset($_SESSION['error'])) {  // ← Fixed line
        try {
            if ($action === 'add_image') {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, description, image) VALUES (?, ?, ?)");
                $stmt->execute([$title, $description, $image]);
                $_SESSION['success'] = 'Image added to gallery successfully!';
            } else {
                $stmt = $pdo->prepare("UPDATE gallery SET title = ?, description = ?, image = ? WHERE id = ?");
                $stmt->execute([$title, $description, $image, $id]);
                $_SESSION['success'] = 'Image updated successfully!';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }

} elseif ($action === 'delete_image') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            // Get image filename to delete file
            $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($img && $img['image']) {
                $filePath = $galleryDir . $img['image'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Image deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting image: ' . $e->getMessage();
        }
    }
}

// Redirect back
header("Location: ?page=gallery" . ($edit_id ?? '' ? "&edit_id=$edit_id" : ''));
exit;