<?php
// includes/management.php
global $pdo;
$stmt = $pdo->prepare("
    SELECT m.*, m.photo AS staff_photo
    FROM management m
    WHERE m.club_id = ? AND m.is_active = 1
    ORDER BY 
        FIELD(m.role, 'Coach', 'Assistant Coach', 'Referee', 'Secretary', 'Treasurer', 'Committee Member', 'Medical Aid', 'Councillor'),
        m.full_name ASC
");
$stmt->execute([$club_id]);
$management = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="management-tab" class="tab-pane" style="display: none;">
    <?php if (empty($management)): ?>
        <div class="text-center text-muted py-5">
            <p>No management or staff registered for this club yet.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle" style="font-size:0.84rem;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Date of Birth</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($management as $i => $m): 
                        $photo_url = $m['staff_photo'] 
                            ? '../uploads/management/' . htmlspecialchars($m['staff_photo']) 
                            : 'https://via.placeholder.com/40/6c757d/white?text=' . substr(explode(' ', $m['full_name'])[0], 0, 2);
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <img src="<?= $photo_url ?>" 
                                     class="rounded-circle shadow-sm" 
                                     width="40" height="40" 
                                     style="object-fit:cover;" 
                                     alt="<?= htmlspecialchars($m['full_name']) ?>">
                            </td>
                            <td class="fw-600"><?= htmlspecialchars($m['full_name']) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= htmlspecialchars($m['role']) ?></span>
                            </td>
                            <td class="text-muted small">
                                <?= $m['date_of_birth'] ? (new DateTime($m['date_of_birth']))->format('j M Y') : '—' ?>
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Card grid for larger screens -->
        <div class="d-none d-lg-block mt-4">
            <div class="row g-4">
                <?php foreach ($management as $m): 
                    $photo_url = $m['staff_photo'] 
                        ? '../uploads/management/' . htmlspecialchars($m['staff_photo']) 
                        : 'https://via.placeholder.com/120/6c757d/white?text=' . substr(explode(' ', $m['full_name'])[0], 0, 2);
                ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded shadow-sm">
                            <img src="<?= $photo_url ?>" 
                                 class="rounded-circle flex-shrink-0" 
                                 width="80" height="80" 
                                 style="object-fit:cover;" 
                                 alt="<?= htmlspecialchars($m['full_name']) ?>">
                            <div>
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($m['full_name']) ?></h6>
                                <p class="mb-1">
                                    <span class="badge bg-primary"><?= htmlspecialchars($m['role']) ?></span>
                                </p>
                                <?php if ($m['date_of_birth']): ?>
                                    <small class="text-muted">
                                        Born: <?= (new DateTime($m['date_of_birth']))->format('j F Y') ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>