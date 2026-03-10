<?php
// admin/actions/logos.php
if (!defined('IN_DASHBOARD')) die('Access denied');

require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? '';

if ($action === 'add_logo') {
    $title   = trim($_POST['title'] ?? '');
    $purpose = $_POST['purpose'] ?? null;
    $file    = $_FILES['logo'] ?? null;

    if (empty($title) || !$file || $file['error'] !== 0) {
        $_SESSION['error'] = "Title and image are required.";
    } else {
        $uploaded = uploadFile($file, '../uploads/admin/logos/');
        if ($uploaded) {
            try {
                // Deactivate previous logo for this purpose
                if ($purpose) {
                    $pdo->prepare("UPDATE logos SET is_active = 0 WHERE purpose = ?")->execute([$purpose]);
                }

                $stmt = $pdo->prepare("INSERT INTO logos (title, filename, purpose, is_active) VALUES (?, ?, ?, 1)");
                $stmt->execute([$title, $uploaded, $purpose ?: null]);

                $_SESSION['success'] = "Logo uploaded" . ($purpose ? " and assigned to " . ($purposes[$purpose] ?? $purpose) : "") . "!";
            } catch (Exception $e) {
                deleteFile('../uploads/admin/logos/' . $uploaded);
                $_SESSION['error'] = "Upload failed.";
            }
        } else {
            $_SESSION['error'] = "Invalid file. Use PNG, JPG or GIF under 2MB.";
        }
    }

} elseif ($action === 'remove_logo') {
    $purpose = $_POST['purpose'] ?? '';
    $pdo->prepare("UPDATE logos SET is_active = 0 WHERE purpose = ?")->execute([$purpose]);
    $_SESSION['success'] = "Logo removed from this position.";
}