<?php
// admin/actions/tournament_images.php

if (!defined('IN_DASHBOARD')) exit('Direct access not allowed');

require_once __DIR__ . '/../includes/functions.php';

$uploadDir = __DIR__ . '/../../uploads/tournaments/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$action = $_POST['action'] ?? '';

if ($action === 'add_image' || $action === 'edit_image') {
    $id = $action === 'edit_image' ? (int)($_POST['id'] ?? 0) : 0;
    $competition_season_id = (int)($_POST['competition_season_id'] ?? 0);
    $caption = trim($_POST['caption'] ?? '');
    $image = $_POST['existing_image'] ?? null;

    if ($competition_season_id <= 0) {
        $_SESSION['error'] = 'Please select a tournament.';
    }

    // Handle image upload
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, GIF, WebP allowed.';
        } elseif ($file['size'] > 10 * 1024 * 1024) {
            $_SESSION['error'] = 'Image too large. Max 10MB.';
        } else {
            $filename = 'tournament_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $image = $filename;

                // Delete old image if editing
                if ($action === 'edit_image' && !empty($_POST['existing_image'])) {
                    $old = $uploadDir . $_POST['existing_image'];
                    if (file_exists($old) && is_file($old)) unlink($old);
                }
            } else {
                $_SESSION['error'] = 'Failed to upload image.';
            }
        }
    }

    if (!isset($_SESSION['error'])) {
        try {
            if ($action === 'add_image') {
                $stmt = $pdo->prepare("INSERT INTO tournament_images (competition_season_id, image, caption, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$competition_season_id, $image, $caption]);
                $_SESSION['success'] = 'Image added successfully!';
            } else {
                $stmt = $pdo->prepare("UPDATE tournament_images SET competition_season_id = ?, image = ?, caption = ? WHERE id = ?");
                $stmt->execute([$competition_season_id, $image, $caption, $id]);
                $_SESSION['success'] = 'Image updated successfully!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }

} elseif ($action === 'delete_image') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT image FROM tournament_images WHERE id = ?");
            $stmt->execute([$id]);
            $img = $stmt->fetchColumn();

            if ($img) {
                $file = $uploadDir . $img;
                if (file_exists($file)) unlink($file);
            }

            $stmt = $pdo->prepare("DELETE FROM tournament_images WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Image deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting image.';
        }
    }
}

header("Location: ?page=tournament_images");
exit;