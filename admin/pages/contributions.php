<?php
// admin/pages/contributions.php
define('IN_DASHBOARD', true);

// ────────────────────────────────────────────────
// AJAX ENDPOINT FOR LOADING PEOPLE (PLAYERS / MANAGEMENT)
// Must be FIRST – before any output
// ────────────────────────────────────────────────
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_people') {
    header('Content-Type: application/json');
    ob_clean();
    $club_id = (int)($_GET['club_id'] ?? 0);
    $type = $_GET['type'] ?? '';
    $response = [
        'success' => false,
        'people' => [],
        'debug' => ['club_id' => $club_id, 'type' => $type, 'message' => '']
    ];
    if ($club_id > 0 && in_array($type, ['player', 'management'])) {
        try {
            if ($type === 'player') {
                $stmt = $pdo->prepare("
                    SELECT id, name FROM players
                    WHERE club_id = ? AND status = 'active'
                    ORDER BY name ASC
                ");
            } else {
                $stmt = $pdo->prepare("
                    SELECT id, full_name AS name, role FROM management
                    WHERE club_id = ? AND is_active = 1
                    ORDER BY full_name ASC
                ");
            }
            $stmt->execute([$club_id]);
            $people = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['success'] = true;
            $response['people'] = $people;
            $response['debug']['count'] = count($people);
        } catch (Exception $e) {
            $response['debug']['message'] = $e->getMessage();
        }
    }
    echo json_encode($response);
    exit;
}

$tab = $_GET['tab'] ?? 'contributions';
$edit_id = (int)($_GET['edit_id'] ?? 0);
$search = trim($_GET['search'] ?? '');

// Common data - only members with password_hash
$members = $pdo->query("
    SELECT id, full_name, username 
    FROM members 
    WHERE password_hash IS NOT NULL AND password_hash != ''
    ORDER BY full_name
")->fetchAll(PDO::FETCH_ASSOC);

$types = $pdo->query("SELECT id, name, is_monetary FROM contribution_types ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$purposes = $pdo->query("SELECT name FROM contribution_purposes WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_COLUMN);
$clubs = $pdo->query("SELECT id, name FROM clubs ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$expense_types = ['Training Expense', 'Transport', 'Medical Bills', 'Food', 'Affiliation', 'Equipment Purchase', 'Other'];

// Balance
$total_contributions = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM contributions WHERE amount IS NOT NULL")->fetchColumn();
$total_expenses = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM contribution_expenses")->fetchColumn();
$current_balance = $total_contributions - $total_expenses;

// Load edit data
if ($edit_id) {
    if ($tab === 'contributions') {
        $stmt = $pdo->prepare("SELECT *, DATE(recorded_at) AS contrib_date FROM contributions WHERE id = ? AND (contributor_type = 'member' OR contributor_type IS NULL)");
        $stmt->execute([$edit_id]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } elseif ($tab === 'player_contributions') {
        $stmt = $pdo->prepare("
            SELECT c.*, DATE(c.recorded_at) AS contrib_date,
                   CASE WHEN c.contributor_type = 'player' THEN p.name ELSE mng.full_name END AS person_name,
                   CASE WHEN c.contributor_type = 'player' THEN 'Player' ELSE 'Management' END AS contrib_role,
                   cl.name AS club_name, cl.id AS club_id
            FROM contributions c
            LEFT JOIN players p ON c.contributor_type = 'player' AND c.contributor_id = p.id
            LEFT JOIN management mng ON c.contributor_type = 'management' AND c.contributor_id = mng.id
            LEFT JOIN clubs cl ON (p.club_id = cl.id OR mng.club_id = cl.id)
            WHERE c.id = ? AND c.contributor_type IN ('player','management')
        ");
        $stmt->execute([$edit_id]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } elseif ($tab === 'expenses') {
        $stmt = $pdo->prepare("SELECT *, DATE(recorded_at) AS expense_date FROM contribution_expenses WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}

// Load data with search
if ($tab === 'contributions') {
    $sql = "
        SELECT c.*, m.full_name, m.username, t.name AS type_name, t.is_monetary
        FROM contributions c
        JOIN members m ON c.member_id = m.id
        JOIN contribution_types t ON c.type_id = t.id
        WHERE (c.contributor_type = 'member' OR c.contributor_type IS NULL)
    ";
    $params = [];
    if ($search) {
        $sql .= " AND (LOWER(m.full_name) LIKE ? OR LOWER(m.username) LIKE ?)";
        $params = ["%$search%", "%$search%"];
    }
    $sql .= " ORDER BY c.recorded_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $contributions = [];
}

if ($tab === 'player_contributions') {
    $sql = "
        SELECT c.id, c.amount, c.purpose, c.description, c.recorded_at,
               CASE WHEN c.contributor_type = 'player' THEN p.name ELSE mng.full_name END AS person_name,
               CASE WHEN c.contributor_type = 'player' THEN 'Player' ELSE 'Management' END AS contrib_role,
               cl.name AS club_name
        FROM contributions c
        LEFT JOIN players p ON c.contributor_type = 'player' AND c.contributor_id = p.id
        LEFT JOIN management mng ON c.contributor_type = 'management' AND c.contributor_id = mng.id
        LEFT JOIN clubs cl ON (p.club_id = cl.id OR mng.club_id = cl.id)
        WHERE c.contributor_type IN ('player','management')
    ";
    $params = [];
    if ($search) {
        $sql .= " AND LOWER(CASE WHEN c.contributor_type = 'player' THEN p.name ELSE mng.full_name END) LIKE ?";
        $params[] = "%$search%";
    }
    $sql .= " ORDER BY c.recorded_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $player_contribs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $player_contribs = $pdo->query("
        SELECT c.id, c.amount, c.purpose, c.description, c.recorded_at,
               CASE WHEN c.contributor_type = 'player' THEN p.name ELSE mng.full_name END AS person_name,
               CASE WHEN c.contributor_type = 'player' THEN 'Player' ELSE 'Management' END AS contrib_role,
               cl.name AS club_name
        FROM contributions c
        LEFT JOIN players p ON c.contributor_type = 'player' AND c.contributor_id = p.id
        LEFT JOIN management mng ON c.contributor_type = 'management' AND c.contributor_id = mng.id
        LEFT JOIN clubs cl ON (p.club_id = cl.id OR mng.club_id = cl.id)
        WHERE c.contributor_type IN ('player','management')
        ORDER BY c.recorded_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

if ($tab === 'expenses') {
    $sql = "
        SELECT e.*, u.username AS recorded_by_name
        FROM contribution_expenses e
        LEFT JOIN users u ON e.recorded_by = u.id
    ";
    $params = [];
    if ($search) {
        $sql .= " WHERE LOWER(e.type) LIKE ? OR LOWER(e.description) LIKE ? OR LOWER(e.purpose) LIKE ?";
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    $sql .= " ORDER BY e.recorded_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $expenses = $pdo->query("
        SELECT e.*, u.username AS recorded_by_name
        FROM contribution_expenses e
        LEFT JOIN users u ON e.recorded_by = u.id
        ORDER BY e.recorded_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Tabs + Balance -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <ul class="nav nav-tabs bg-dark mb-0 flex-grow-1" id="financeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white <?= $tab === 'contributions' ? 'active bg-primary' : '' ?>"
                    onclick="window.location='?page=contributions&tab=contributions'">
                Member Contributions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white <?= $tab === 'player_contributions' ? 'active bg-primary' : '' ?>"
                    onclick="window.location='?page=contributions&tab=player_contributions'">
                Player & Management
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-white <?= $tab === 'expenses' ? 'active bg-primary' : '' ?>"
                    onclick="window.location='?page=contributions&tab=expenses'">
                Expenses
            </button>
        </li>
    </ul>
    <div class="text-end">
        <div class="card border-primary shadow-sm" style="min-width: 180px;">
            <div class="card-body text-center py-2 px-3">
                <h6 class="card-title mb-0 text-primary small">League Balance</h6>
                <h5 class="mb-0 fw-normal text-<?= $current_balance >= 0 ? 'success' : 'danger' ?>">
                    $<?= number_format($current_balance, 2) ?>
                </h5>
            </div>
        </div>
    </div>
</div>

<!-- Tab content -->
<?php if ($tab === 'contributions'): ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Member Contributions</h4>
    <div class="d-flex align-items-center gap-2">
        <form method="GET" class="d-flex">
            <input type="hidden" name="page" value="contributions">
            <input type="hidden" name="tab" value="contributions">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search member..."
                   value="<?= htmlspecialchars($search) ?>" style="width: 180px;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
        </form>
        <?php if ($search): ?>
            <a href="?page=contributions&tab=contributions" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#contributionModal">
            Add Contribution
        </button>
    </div>
</div>

<div class="table-responsive">
    <?php if (empty($contributions)): ?>
        <div class="alert alert-info text-center py-4">No contributions found.</div>
    <?php else: ?>
        <table class="table table-hover align-middle table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Type</th>
                    <th>Purpose</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contributions as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= date('d M Y', strtotime($c['recorded_at'])) ?></td>
                    <td><?= htmlspecialchars($c['full_name']) ?></td>
                    <td><?= htmlspecialchars($c['type_name']) ?></td>
                    <td><?= htmlspecialchars($c['purpose'] ?? '—') ?></td>
                    <td>
                        <?php if ($c['is_monetary']): ?>
                            $<?= number_format($c['amount'], 2) ?>
                        <?php else: ?>
                            <?= $c['quantity'] ?> item(s)
                            <?php if ($c['description']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($c['description']) ?></small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?page=contributions&tab=contributions&edit_id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                data-id="<?= $c['id'] ?>" data-type="contribution" data-tab="contributions">
                            Del
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'player_contributions'): ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Player & Management Cash Contributions</h4>
    <div class="d-flex align-items-center gap-2">
        <form method="GET" class="d-flex">
            <input type="hidden" name="page" value="contributions">
            <input type="hidden" name="tab" value="player_contributions">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search person..."
                   value="<?= htmlspecialchars($search) ?>" style="width: 180px;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
        </form>
        <?php if ($search): ?>
            <a href="?page=contributions&tab=player_contributions" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#playerMgmtModal">
            Add Contribution
        </button>
    </div>
</div>

<div class="table-responsive">
    <?php if (empty($player_contribs)): ?>
        <div class="alert alert-info text-center py-4">No contributions found.</div>
    <?php else: ?>
        <table class="table table-hover align-middle table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Club</th>
                    <th>Role</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Purpose</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($player_contribs as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= date('d M Y', strtotime($c['recorded_at'])) ?></td>
                    <td><?= htmlspecialchars($c['club_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['contrib_role']) ?></td>
                    <td><?= htmlspecialchars($c['person_name'] ?? '—') ?></td>
                    <td>$<?= number_format($c['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($c['purpose'] ?: '—') ?></td>
                    <td>
                        <a href="?page=contributions&tab=player_contributions&edit_id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                data-id="<?= $c['id'] ?>" data-type="contribution" data-tab="player_contributions">
                            Del
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php else: // expenses ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">League Expenses</h4>
    <div class="d-flex align-items-center gap-2">
        <form method="GET" class="d-flex">
            <input type="hidden" name="page" value="contributions">
            <input type="hidden" name="tab" value="expenses">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search type/desc..."
                   value="<?= htmlspecialchars($search) ?>" style="width: 180px;">
            <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
        </form>
        <?php if ($search): ?>
            <a href="?page=contributions&tab=expenses" class="btn btn-outline-secondary btn-sm">Clear</a>
        <?php endif; ?>
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#expenseModal">
            Add Expense
        </button>
    </div>
</div>

<div class="table-responsive">
    <?php if (empty($expenses)): ?>
        <div class="alert alert-info text-center py-4">No expenses found.</div>
    <?php else: ?>
        <table class="table table-hover align-middle table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Details</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expenses as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= date('d M Y', strtotime($e['recorded_at'])) ?></td>
                    <td><?= htmlspecialchars($e['type']) ?></td>
                    <td><?= htmlspecialchars($e['description'] ?: '—') ?>
                        <?php if ($e['purpose']): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($e['purpose']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>-$<?= number_format($e['amount'], 2) ?></td>
                    <td>
                        <a href="?page=contributions&tab=expenses&edit_id=<?= $e['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                data-id="<?= $e['id'] ?>" data-type="expense" data-tab="expenses">
                            Del
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Are you sure you want to delete this record?</p>
                <p class="text-danger fw-bold mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Player / Management Contribution Modal -->
<div class="modal fade" id="playerMgmtModal" tabindex="-1" aria-labelledby="playerMgmtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="playerMgmtForm">
                <input type="hidden" name="action" value="<?= $edit_id && $tab === 'player_contributions' ? 'edit_player_mgmt_contrib' : 'add_player_mgmt_contrib' ?>">
                <?php if ($edit_id && $tab === 'player_contributions'): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="playerMgmtModalLabel"><?= $edit_id ? 'Edit' : 'Add' ?> Player / Management Contribution</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="custom_date" class="form-control" required
                                   value="<?= htmlspecialchars($edit_data['contrib_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contributor Type *</label>
                            <select name="contributor_type" id="contributorType" class="form-select" required onchange="loadClubs()">
                                <option value="">Select Type</option>
                                <option value="player" <?= ($edit_data['contributor_type'] ?? '') === 'player' ? 'selected' : '' ?>>Player</option>
                                <option value="management" <?= ($edit_data['contributor_type'] ?? '') === 'management' ? 'selected' : '' ?>>Management</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Club *</label>
                            <select name="club_id" id="clubSelect" class="form-select" required disabled onchange="loadPeople()">
                                <option value="">Select club...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Person *</label>
                            <select name="contributor_id" id="personSelect" class="form-select" required disabled>
                                <option value="">Select person...</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount ($) *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required
                                   value="<?= htmlspecialchars($edit_data['amount'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <select name="purpose" class="form-select">
                            <option value="">— None —</option>
                            <?php foreach ($purposes as $p): ?>
                                <option value="<?= htmlspecialchars($p) ?>" <?= ($edit_data['purpose'] ?? '') === $p ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Note (optional)</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Optional note..."><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Save Contribution</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Member Contribution Modal -->
<div class="modal fade" id="contributionModal" tabindex="-1" aria-labelledby="contributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_id && $tab === 'contributions' ? 'edit_contribution' : 'add_contribution' ?>">
                <?php if ($edit_id && $tab === 'contributions'): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="contributionModalLabel"><?= $edit_id ? 'Edit' : 'Add' ?> Contribution</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="custom_date" class="form-control" required
                                   value="<?= htmlspecialchars($edit_data['contrib_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Member *</label>
                            <select name="member_id" class="form-select" required>
                                <option value="">Select Member</option>
                                <?php foreach ($members as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= ($edit_data['member_id'] ?? 0) == $m['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contribution Type *</label>
                            <select name="type_id" class="form-select" required onchange="toggleContributionFields()">
                                <option value="">Select Type</option>
                                <?php foreach ($types as $t): ?>
                                    <option value="<?= $t['id'] ?>"
                                            data-monetary="<?= $t['is_monetary'] ? '1' : '0' ?>"
                                            <?= ($edit_data['type_id'] ?? 0) == $t['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purpose</label>
                            <select name="purpose" class="form-select">
                                <option value="">— None —</option>
                                <?php foreach ($purposes as $p): ?>
                                    <option value="<?= htmlspecialchars($p) ?>" <?= ($edit_data['purpose'] ?? '') === $p ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div id="monetaryFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Amount ($)*</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                                       value="<?= htmlspecialchars($edit_data['amount'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div id="nonMonetaryFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity *</label>
                                <input type="number" min="1" name="quantity" class="form-control"
                                       value="<?= htmlspecialchars($edit_data['quantity'] ?? '1') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Item Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="e.g. Provided 5 jerseys"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $edit_id && $tab === 'expenses' ? 'edit_expense' : 'add_expense' ?>">
                <?php if ($edit_id && $tab === 'expenses'): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                <?php endif; ?>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="expenseModalLabel"><?= $edit_id ? 'Edit' : 'Add' ?> Expense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="custom_date" class="form-control" required
                                   value="<?= htmlspecialchars($edit_data['expense_date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expense Type *</label>
                            <select name="type" class="form-select" required onchange="toggleOtherDescription(this)">
                                <?php foreach ($expense_types as $et): ?>
                                    <option value="<?= htmlspecialchars($et) ?>" <?= ($edit_data['type'] ?? '') === $et ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($et) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div id="otherDescription" class="mb-3" style="display: <?= ($edit_data['type'] ?? '') === 'Other' ? 'block' : 'none' ?>;">
                        <label class="form-label">Describe "Other" Expense *</label>
                        <input type="text" name="custom_type" class="form-control" placeholder="e.g. Referee fees"
                               value="<?= ($edit_data['type'] ?? '') === 'Other' ? htmlspecialchars($edit_data['description'] ?? '') : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details / Purpose</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="What was this expense for?"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount ($)*</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required
                               value="<?= htmlspecialchars($edit_data['amount'] ?? '') ?>" placeholder="e.g. 85.50">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle monetary / non-monetary fields
function toggleContributionFields() {
    const select = document.querySelector('#contributionModal select[name="type_id"]');
    if (!select || !select.value) {
        document.getElementById('monetaryFields').style.display = 'none';
        document.getElementById('nonMonetaryFields').style.display = 'none';
        return;
    }
    const isMonetary = select.selectedOptions[0].dataset.monetary === '1';
    document.getElementById('monetaryFields').style.display = isMonetary ? 'block' : 'none';
    document.getElementById('nonMonetaryFields').style.display = isMonetary ? 'none' : 'block';
}

// Toggle "Other" expense field
function toggleOtherDescription(select) {
    document.getElementById('otherDescription').style.display =
        select.value === 'Other' ? 'block' : 'none';
}

// Load clubs
const allClubs = <?= json_encode($clubs) ?>;
function loadClubs() {
    const type = document.getElementById('contributorType')?.value;
    const clubSelect = document.getElementById('clubSelect');
    const personSelect = document.getElementById('personSelect');
    if (!clubSelect || !personSelect) return;
    clubSelect.innerHTML = '<option value="">Select Club...</option>';
    personSelect.innerHTML = '<option value="">Select Person...</option>';
    clubSelect.disabled = true;
    personSelect.disabled = true;
    if (!type) return;
    clubSelect.disabled = false;
    allClubs.forEach(club => {
        const opt = document.createElement('option');
        opt.value = club.id;
        opt.textContent = club.name;
        clubSelect.appendChild(opt);
    });
    <?php if ($edit_id && $tab === 'player_contributions' && !empty($edit_data['club_id'])): ?>
    clubSelect.value = "<?= $edit_data['club_id'] ?>";
    loadPeople();
    <?php endif; ?>
}

// Load people
async function loadPeople() {
    const clubId = document.getElementById('clubSelect')?.value;
    const type = document.getElementById('contributorType')?.value;
    const personSelect = document.getElementById('personSelect');
    if (!personSelect) return;
    personSelect.innerHTML = '<option value="">Loading...</option>';
    personSelect.disabled = true;
    if (!clubId || !type) {
        personSelect.innerHTML = '<option value="">Select type and club first</option>';
        return;
    }
    try {
        const params = new URLSearchParams({
            page: 'contributions',
            tab: 'player_contributions',
            ajax: 'get_people',
            club_id: clubId,
            type: type
        });
        const url = '?' + params.toString();
        const resp = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const data = await resp.json();
        personSelect.innerHTML = '<option value="">Select Person...</option>';
        if (data.success && data.people?.length > 0) {
            data.people.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name + (p.role ? ` (${p.role})` : '');
                personSelect.appendChild(opt);
            });
            personSelect.disabled = false;
            <?php if ($edit_id && $tab === 'player_contributions' && !empty($edit_data['contributor_id'])): ?>
            personSelect.value = "<?= $edit_data['contributor_id'] ?>";
            <?php endif; ?>
        } else {
            personSelect.innerHTML = '<option value="">' + (data.debug?.message || 'No people found') + '</option>';
        }
    } catch (err) {
        personSelect.innerHTML = '<option value="">Error loading</option>';
        console.error('Load failed:', err);
    }
}

// Delete confirmation
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('deleteConfirmModal');
    const deleteForm = document.getElementById('deleteForm');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const type = button.getAttribute('data-type');
            const tab = button.getAttribute('data-tab');
            deleteForm.innerHTML = `
                <input type="hidden" name="action" value="delete_${type}">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="tab" value="${tab}">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            `;
        });
    }

    // Auto-toggle fields
    const typeSelect = document.querySelector('#contributionModal select[name="type_id"]');
    if (typeSelect) toggleContributionFields();

    const expenseType = document.querySelector('#expenseModal select[name="type"]');
    if (expenseType) toggleOtherDescription(expenseType);

    // Player/Mgmt modal
    const contribType = document.getElementById('contributorType');
    if (contribType) {
        loadClubs();
        contribType.addEventListener('change', loadClubs);
    }

    // Auto-open edit modal
    <?php if ($edit_id): ?>
    const modalId = '<?= $tab === 'expenses' ? 'expenseModal' : ($tab === 'player_contributions' ? 'playerMgmtModal' : 'contributionModal') ?>';
    new bootstrap.Modal(document.getElementById(modalId)).show();
    <?php endif; ?>
});
</script>