<?php
ob_start();
require_once 'config.php';
include 'includes/header.php';
include 'includes/properties.php';
$staff_id = (int)($_GET['staff_id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT m.*, c.name AS club_name, c.logo AS club_logo
    FROM management m
    LEFT JOIN clubs c ON m.club_id = c.id
    WHERE m.id = ? AND m.is_active = 1
");
$stmt->execute([$staff_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$staff) {
    ?>
    <div class="container py-5 text-center">
        <div style="background:#2c3e50;padding:2.5rem;box-shadow:0 8px 28px rgba(0,0,0,0.4);border:1px solid #444;display:inline-block;color:#e0e0e0;max-width:90%;">
            <i class="bi bi-person-x" style="font-size:4.5rem;color:#e74c3c;"></i>
            <h3 class="mt-3 text-danger">Staff Member Not Found</h3>
            <p class="text-muted mb-2">The management member with ID <code><?= $staff_id ?></code> does not exist or is inactive.</p>
            <a href="index.php" class="btn btn-outline-primary px-4">Back to Home</a>
        </div>
    </div>
    <?php
    include 'includes/footer.php';
    ob_end_flush();
    exit;
}
$age = 'N/A';
if (!empty($staff['date_of_birth']) && $staff['date_of_birth'] !== '0000-00-00') {
    try {
        $dob = new DateTime($staff['date_of_birth']);
        $now = new DateTime();
        $age = $now->diff($dob)->y . ' years';
    } catch (Exception $e) {}
}
$displayPhoto = $staff['photo']
    ? "uploads/management/" . htmlspecialchars($staff['photo'])
    : "https://via.placeholder.com/500/2c3e50/white?text=" . substr($staff['full_name'],0,2);
$displayClubLogo = $staff['club_logo']
    ? "uploads/clubs/" . htmlspecialchars($staff['club_logo'])
    : "https://via.placeholder.com/60/2c3e50/white?text=" . substr($staff['club_name']??'FA',0,2);
?>
<style>
    /* DARK THEME - CONSISTENT WITH PLAYER_PROFILE.PHP & FIXTURES.PHP */
    html, body {
        background-color: #1e272e !important;
        color: #e0e0e0;
        overflow-x: hidden;
    }

    .staff-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }

    /* Side-by-side layout on large screens, full-width stacked on small */
    .main-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 2.5rem;
        margin-top: 2rem;
        align-items: flex-start;
        justify-content: center;
    }

    .profile-section {
        flex: 2 1 650px;
        min-width: 0;
        max-width: 100%;
    }

    .card-section {
        flex: 1 1 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100%;
    }

    /* Profile Card - Dark */
    .player-profile-card {
        background: #2c3e50;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.4);
        border: 1px solid #444;
        width: 100%;
    }

    /* Header */
    .section-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: #fff;
        padding: 1.6rem 1.8rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .section-header h1 {
        margin: 0;
        font-size: 1.95rem;
        font-weight: 700;
    }

    .role-badge {
        background: rgba(255,255,255,0.15);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
    }

    /* Body */
    .player-body {
        padding: 1.8rem 2rem 2rem;
        text-align: center;
    }

    .player-photo-wrapper {
        width: 260px;
        height: 260px;
        margin: 0 auto 1.5rem auto;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 14px 38px rgba(0,0,0,0.4);
        border: 8px solid #2c3e50;
        background: #2c3e50;
    }

    .player-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        background: #34495e;
        padding: 1rem;
        border-left: 5px solid #3498db;
        color: #ecf0f1;
    }

    .info-item strong {
        display: block;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        color: #bdc3c7;
        margin-bottom: 6px;
    }

    .info-item span {
        font-size: 1.1rem;
        font-weight: 600;
        color: #ecf0f1;
    }

    .club-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        background: #3498db;
        border-radius: 50px;
        font-weight: 600;
        color: #fff;
        text-decoration: none;
        transition: all .3s;
    }

    .club-link:hover {
        background: #5ab6f0;
        transform: translateY(-2px);
    }

    .club-link img {
        width: 36px;
        height: 36px;
        object-fit: contain;
        border-radius: 50%;
    }

    /* Card Section - Dark */
    .card-container {
        width: 100%;
        max-width: 460px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.4);
        background: #2c3e50;
        border: 1px solid #444;
        overflow: hidden;
    }

    #staff-card-preview {
        width: 100%;
        height: 620px;
        border: none;
        background: #34495e;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Action buttons - Always in one row */
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
        gap: 1.8rem;
    }

    .action-btn {
        flex: 0 1 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.8rem;
        text-decoration: none;
        color: #bdc3c7;
        font-weight: 600;
        font-size: 1rem;
        transition: transform 0.3s;
        background: none;
        border: none;
        cursor: pointer;
    }

    .action-btn:hover {
        transform: translateY(-6px);
        color: #ecf0f1;
    }

    .action-btn i {
        font-size: 3.2rem;
        width: 90px;
        height: 90px;
        background: #34495e;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .action-btn span {
        font-size: 0.95rem;
    }

    #share-fallback-staff {
        margin-top: 1.2rem;
        text-align: center;
        color: #95a5a6;
        font-size: 0.9rem;
        display: none;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .main-layout {
            flex-direction: column;
            align-items: stretch;
            gap: 2rem;
        }

        .profile-section, .card-section {
            max-width: 100%;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .player-photo-wrapper {
            width: 240px;
            height: 240px;
        }
    }

    @media (max-width: 768px) {
        /* Full-width sections on small screens */
        .container.staff-page-wrapper {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: none !important;
        }

        .main-layout {
            margin-left: 1rem;
            margin-right: 1rem;
            gap: 1.5rem;
        }

        .player-profile-card,
        .card-container {
            border-left: none;
            border-right: none;
            border-radius: 0;
        }

        .action-buttons {
            gap: 1.2rem;
        }

        .action-btn i {
            font-size: 2.8rem;
            width: 80px;
            height: 80px;
        }
    }

    @media (max-width: 576px) {
        .section-header {
            padding: 1.2rem 1rem;
            flex-direction: column;
            text-align: center;
        }

        .section-header h1 {
            font-size: 1.6rem;
        }

        .player-body {
            padding: 1.4rem 1.2rem;
        }

        .player-photo-wrapper {
            width: 200px;
            height: 200px;
            border-width: 6px;
        }

        #staff-card-preview {
            height: 500px;
        }

        .action-btn {
            flex: 0 1 100px;
        }

        .action-btn i {
            font-size: 2.6rem;
            width: 75px;
            height: 75px;
        }

        .action-btn span {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 400px) {
        .main-layout {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        .action-buttons {
            gap: 1rem;
        }

        .action-btn i {
            font-size: 2.4rem;
            width: 70px;
            height: 70px;
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<div class="container staff-page-wrapper">
    <div class="main-layout">
        <!-- Left: Staff Profile -->
        <div class="profile-section">
            <div class="player-profile-card">
                <div class="section-header">
                    <div>
                        <h1><?= htmlspecialchars($staff['full_name']) ?></h1>
                    </div>
                    <div class="role-badge"><?= htmlspecialchars($staff['role']) ?></div>
                </div>
                <div class="player-body">
                    <div class="player-photo-wrapper">
                        <img src="<?= $displayPhoto ?>"
                             alt="<?= htmlspecialchars($staff['full_name']) ?>"
                             class="player-photo"
                             onerror="this.src='https://via.placeholder.com/500/2c3e50/white?text=<?= substr(htmlspecialchars($staff['full_name']),0,2) ?>'">
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Full Name</strong>
                            <span><?= htmlspecialchars($staff['full_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Age</strong>
                            <span><?= $age ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Current Club</strong>
                            <?php if ($staff['club_name']): ?>
                                <a href="clubs.php?club_id=<?= $staff['club_id'] ?>" class="club-link">
                                    <img src="<?= $displayClubLogo ?>" alt="<?= htmlspecialchars($staff['club_name']) ?>">
                                    <?= htmlspecialchars($staff['club_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Not Assigned</span>
                            <?php endif; ?>
                        </div>
                        <div class="info-item">
                            <strong>Role</strong>
                            <span><?= htmlspecialchars($staff['role']) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Status</strong>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right: Official Management Card -->
        <div class="card-section">
            <div class="card-container">
                <div class="section-header">
                    <h3>Official Management Card</h3>
                </div>
                <div id="staff-card-preview">
                    <p class="py-5 text-muted text-center">Loading card preview...</p>
                </div>
            </div>
            <div class="action-buttons">
                <a href="generate_management_card.php?staff_id=<?= $staff_id ?>&format=pdf"
                   class="action-btn"
                   target="_blank"
                   download="04SL_Management_Card_<?= $staff_id ?>.pdf">
                    <i class="bi bi-file-earmark-pdf" style="color:#e74c3c;"></i>
                </a>
                <a href="generate_management_card.php?staff_id=<?= $staff_id ?>&format=png"
                   class="action-btn"
                   target="_blank"
                   download="04SL_Management_Card_<?= $staff_id ?>.png">
                    <i class="bi bi-image" style="color:#3498db;"></i>
                </a>
                <button type="button" class="action-btn" id="share-staff">
                    <i class="bi bi-share-fill" style="color:#95a5a6;"></i>
                </button>
            </div>
            <div id="share-fallback-staff">
                <p class="text-muted small text-center mt-3">Download PNG and share manually if needed.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.getElementById('staff-card-preview');
    const pngUrl = 'generate_management_card.php?staff_id=<?= $staff_id ?>&format=png';
    const img = document.createElement('img');
    img.src = pngUrl;
    img.alt = 'Official Management Card';
    img.style.width = '100%';
    img.style.height = '100%';
    img.style.objectFit = 'contain';
    img.style.background = '#34495e';
    img.style.display = 'block';
    img.onload = function() {
        preview.innerHTML = '';
        preview.appendChild(img);
    };
    img.onerror = function() {
        preview.innerHTML = '<p class="text-danger text-center py-5 mb-0">Preview unavailable<br><small>Use download buttons below</small></p>';
    };

    // Share functionality
    const cardImageUrl = 'generate_management_card.php?staff_id=<?= $staff_id ?>&format=png&share=1';
    const staffName = "<?= addslashes(htmlspecialchars($staff['full_name'])) ?>";
    const role = "<?= addslashes(htmlspecialchars($staff['role'])) ?>";
    const caption = `Official Management Card\n${staffName}\n${role}\nWWW.04SL.ONLINE`;

    async function getImageFile() {
        const res = await fetch(cardImageUrl);
        if (!res.ok) throw new Error('Failed to fetch image');
        const blob = await res.blob();
        return new File([blob], `04SL_Management_Card_${staffName.replace(/[^a-zA-Z0-9]/g, '_')}.png`, { type: 'image/png' });
    }

    async function shareImage() {
        try {
            const file = await getImageFile();
            const shareData = {
                files: [file],
                title: 'Management Card - ' + staffName,
                text: caption
            };
            if (navigator.canShare && navigator.canShare(shareData)) {
                await navigator.share(shareData);
                return;
            }
        } catch (err) {
            console.log('Sharing failed:', err);
        }
        const fallback = document.getElementById('share-fallback-staff');
        fallback.style.display = 'block';
        setTimeout(() => fallback.style.display = 'none', 5000);
    }

    document.getElementById('share-staff').onclick = shareImage;
});
</script>
<?php include 'includes/footer.php'; ob_end_flush(); ?>