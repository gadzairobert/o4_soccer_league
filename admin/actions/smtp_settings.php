<?php
// admin/actions/smtp_settings.php
if (!defined('IN_DASHBOARD')) die('Access denied!');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_smtp'])) {

    $settings = [
        'smtp_host'         => trim($_POST['smtp_host'] ?? ''),
        'smtp_port'         => trim($_POST['smtp_port'] ?? '465'),
        'smtp_username'     => trim($_POST['smtp_username'] ?? ''),
        'smtp_password'     => $_POST['smtp_password'] ?? '', // do NOT trim password
        'smtp_from_email'   => trim($_POST['smtp_from_email'] ?? ''),
        'smtp_from_name'    => trim($_POST['smtp_from_name'] ?? 'Ward 24 League'),
        'smtp_encryption'   => $_POST['smtp_encryption'] ?? 'ssl'
    ];

    try {
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("
                INSERT INTO site_settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$key, $value, $value]);
        }

        $_SESSION['success'] = "SMTP settings saved successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to save settings. Please try again.";
    }

    header("Location: ?page=smtp_settings");
    exit;
}