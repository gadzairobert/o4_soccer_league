<?php
define('IN_DASHBOARD', true);

// Process save if coming from actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../actions/smtp_settings.php';
}

// Load current settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'smtp_%'");
$current = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$current = array_map('htmlspecialchars', $current);
?>

<div class="container-fluid">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-envelope-paper-heart-fill text-danger me-3 fs-3"></i>
                <h4 class="mb-0 fw-bold">SMTP Email Settings</h4>
            </div>
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#smtpModal">
                <i class="bi bi-pencil-square me-2"></i> Edit SMTP Settings
            </button>
        </div>

        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="bg-light rounded-4 p-4 border <?= !empty($current['smtp_username']) ? 'border-success' : 'border-warning' ?>">
                        <div class="row small text-muted fw-medium">
                            <div class="col-md-4"><strong>Host:</strong> <?= $current['smtp_host'] ?? '<em>not set</em>' ?></div>
                            <div class="col-md-4"><strong>Port:</strong> <?= $current['smtp_port'] ?? '465' ?> (SSL)</div>
                            <div class="col-md-4"><strong>Username:</strong> <?= $current['smtp_username'] ?? '<em>not set</em>' ?></div>
                        </div>
                        <hr class="my-3">
                        <div class="row small text-muted fw-medium">
                            <div class="col-md-6"><strong>From Email:</strong> <?= $current['smtp_from_email'] ?? '<em>not set</em>' ?></div>
                            <div class="col-md-6"><strong>From Name:</strong> <?= $current['smtp_from_name'] ?? 'Ward 24 League' ?></div>
                        </div>
                        <div class="mt-3 text-end">
                            <span class="badge bg-<?= !empty($current['smtp_username']) ? 'success' : 'warning' ?> fs-6 px-4 py-2">
                                <?= !empty($current['smtp_username']) ? 'Configured & Ready' : 'Not Configured' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="smtpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="">
                <input type="hidden" name="save_smtp" value="1">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">SMTP Settings – ward24league.online</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5">
                    <div class="alert alert-success mb-4">
                        <strong>Your hosting requires:</strong><br>
                        Host: <code>mail.ward24league.online</code> • Port: <code>465</code> • Encryption: <strong>SSL</strong>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control form-control-lg" 
                                   value="<?= $current['smtp_host'] ?? 'mail.ward24league.online' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SMTP Port</label>
                            <input type="text" name="smtp_port" class="form-control form-control-lg" 
                                   value="<?= $current['smtp_port'] ?? '465' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Username (Email)</label>
                            <input type="email" name="smtp_username" class="form-control form-control-lg" 
                                   value="<?= $current['smtp_username'] ?? 'info@ward24league.online' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="smtp_password" class="form-control form-control-lg" 
                                   placeholder="Leave blank to keep current" autocomplete="new-password">
                            <small class="text-muted">Only fill if you want to change it</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">From Email</label>
                            <input type="email" name="smtp_from_email" class="form-control form-control-lg" 
                                   value="<?= $current['smtp_from_email'] ?? 'info@ward24league.online' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">From Name</label>
                            <input type="text" name="smtp_from_name" class="form-control form-control-lg" 
                                   value="<?= $current['smtp_from_name'] ?? 'Ward 24 League' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Encryption</label>
                            <select name="smtp_encryption" class="form-select form-select-lg">
                                <option value="ssl" <?= ($current['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465 – Required)</option>
                                <option value="tls" <?= ($current['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                                <option value="">None</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="bi bi-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>