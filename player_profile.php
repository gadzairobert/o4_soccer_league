<?php
ob_start();
require_once 'config.php';
include 'includes/header.php';
include 'includes/properties.php';
$player_id = (int)($_GET['player_id'] ?? 0);
$player = getPlayerById($player_id);
if (!$player) {
    ?>
    <div class="container py-5 text-center">
        <div style="background:#2c3e50;padding:2.5rem;box-shadow:0 8px 28px rgba(0,0,0,0.4);border:1px solid #444;display:inline-block;color:#e0e0e0;">
            <i class="bi bi-person-x" style="font-size:4.5rem;color:#e74c3c;"></i>
            <h3 class="mt-3 text-danger">Player Not Found</h3>
            <p class="text-muted mb-2">The player with ID <code><?= $player_id ?></code> does not exist.</p>
            <a href="index.php" class="btn btn-outline-primary px-4">Back to Home</a>
        </div>
    </div>
    <?php
    include 'includes/footer.php';
    ob_end_flush();
    exit;
}
// Stats & Age
$currentLeague = $pdo->query("SELECT id FROM competition_seasons WHERE is_current = 1 AND type = 'league' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$leagueSeasonId = $currentLeague['id'] ?? null;
$leagueStats = getPlayerLeagueStats($player_id, $leagueSeasonId);
$tournamentStats = getPlayerTournamentStatsByYear($player_id);
$age = 'N/A';
if (!empty($player['date_of_birth']) && $player['date_of_birth'] !== '0000-00-00' && $player['date_of_birth'] !== null) {
    try {
        $dob = new DateTime($player['date_of_birth']);
        $now = new DateTime();
        $age = $now->diff($dob)->y . ' years';
    } catch (Exception $e) { }
}
$displayPhoto = $player['photo']
    ? "uploads/players/" . htmlspecialchars($player['photo'])
    : "https://via.placeholder.com/500/2c3e50/white?text=" . substr($player['name'],0,2);
$displayClubLogo = $player['club_logo']
    ? "uploads/clubs/" . htmlspecialchars($player['club_logo'])
    : "https://via.placeholder.com/60/2c3e50/white?text=" . substr($player['club_name']??'FA',0,2);
?>
<style>
    /* DARK THEME - CONSISTENT WITH FIXTURES.PHP */
    html, body {
        background-color: #1e272e !important;
        color: #e0e0e0;
        overflow-x: hidden;
    }

    .player-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }

    /* Side-by-side layout */
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

    .section-header h1, .section-header h3 {
        margin: 0;
        font-size: 1.95rem;
        font-weight: 700;
    }

    .section-header .position {
        font-size: 1.05rem;
        opacity: 0.9;
    }

    .jersey-number {
        background: rgba(255,255,255,0.15);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 1.4rem;
        font-weight: 800;
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
        grid-template-columns: repeat(3, 1fr);
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

    .stats-section {
        margin-top: 1.5rem;
        background: #34495e;
        padding: 1.2rem;
        border: 1px solid #444;
    }

    .stats-section h4 {
        margin-bottom: 0.8rem;
        color: #ecf0f1;
        font-weight: 700;
        font-size: 1.15rem;
    }

    .stats-table {
        font-size: 0.94rem;
        width: 100%;
        margin-bottom: 0;
        background: #2c3e50;
    }

    .stats-table th {
        background: #1a2530;
        color: #ecf0f1;
        padding: 0.6rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        text-align: center;
    }

    .stats-table td {
        padding: 0.6rem;
        text-align: center;
        background: #34495e;
        color: #ecf0f1;
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

    #player-card-preview {
        width: 100%;
        height: 620px;
        border: none;
        background: #34495e;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Action buttons */
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.8rem;
        justify-content: center;
    }

    .action-btn {
        flex: 1 1 110px;
        min-width: 110px;
        max-width: 140px;
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

    #share-fallback {
        margin-top: 1.2rem;
        text-align: center;
        color: #95a5a6;
        font-size: 0.9rem;
        display: none;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .main-layout {
            flex-direction: column;
            align-items: stretch; /* Stretch to full width */
            gap: 2rem;
        }
        .profile-section, .card-section {
            max-width: none;
            width: 100%;
        }
        .info-grid {
            grid-template-columns: 1fr;
        }
        .player-photo-wrapper {
            width: 240px;
            height: 240px;
        }
        .card-container {
            max-width: none;
        }
    }

    @media (max-width: 576px) {
        .container.player-page-wrapper {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .player-profile-card,
        .card-container {
            border-left: none;
            border-right: none;
            border-radius: 0 !important;
        }
        .section-header {
            padding: 1.2rem 1rem;
            flex-direction: column;
            text-align: center;
            border-radius: 0 !important;
        }
        .section-header h1, .section-header h3 {
            font-size: 1.6rem;
        }
        .player-body {
            padding: 1.4rem 1rem;
        }
        .player-photo-wrapper {
            width: 200px;
            height: 200px;
            border-width: 6px;
        }
        #player-card-preview {
            height: 500px;
        }
        .action-btn i {
            font-size: 2.8rem;
            width: 80px;
            height: 80px;
        }
        .action-buttons {
            gap: 1.4rem;
        }
    }

    @media (max-width: 400px) {
        .action-btn {
            flex: 1 1 100px;
            min-width: 100px;
        }
        .action-btn i {
            font-size: 2.5rem;
            width: 70px;
            height: 70px;
        }
        .action-btn span {
            font-size: 0.9rem;
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<div class="container player-page-wrapper">
    <div class="main-layout">
        <!-- Left: Player Profile -->
        <div class="profile-section">
            <div class="player-profile-card">
                <div class="section-header">
                    <div>
                        <h1><?= htmlspecialchars($player['name']) ?></h1>
                        <div class="position"><?= htmlspecialchars($player['position'] ?? 'Unknown') ?></div>
                    </div>
                    <div class="jersey-number">#<?= htmlspecialchars($player['jersey_number'] ?? '-') ?></div>
                </div>
                <div class="player-body">
                    <div class="player-photo-wrapper">
                        <img src="<?= $displayPhoto ?>"
                             alt="<?= htmlspecialchars($player['name']) ?>"
                             class="player-photo"
                             onerror="this.src='https://via.placeholder.com/500/2c3e50/white?text=<?= substr(htmlspecialchars($player['name']),0,2) ?>'">
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Full Name</strong>
                            <span><?= htmlspecialchars($player['name']) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Age</strong>
                            <span><?= $age ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Current Club</strong>
                            <?php if ($player['club_name']): ?>
                                <a href="clubs.php?club_id=<?= $player['club_id'] ?>" class="club-link">
                                    <img src="<?= $displayClubLogo ?>" alt="<?= htmlspecialchars($player['club_name']) ?>">
                                    <?= htmlspecialchars($player['club_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Free Agent</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="stats-section">
                        <h4>League Stats <?= date('Y') ?></h4>
                        <div class="table-responsive">
                            <table class="table stats-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Goals</th>
                                        <th>Assists</th>
                                        <th>YC</th>
                                        <th>RC</th>
                                        <?php if (in_array(strtoupper($player['position'] ?? ''), ['GK','GOALKEEPER'])): ?>
                                            <th>CS</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-success fw-bold"><?= $leagueStats['goals'] ?></td>
                                        <td class="text-info fw-bold"><?= $leagueStats['assists'] ?></td>
                                        <td class="text-warning fw-bold"><?= $leagueStats['yellow_cards'] ?></td>
                                        <td class="text-danger fw-bold"><?= $leagueStats['red_cards'] ?></td>
                                        <?php if (in_array(strtoupper($player['position'] ?? ''), ['GK','GOALKEEPER'])): ?>
                                            <td class="text-primary fw-bold"><?= $leagueStats['clean_sheets'] ?></td>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($tournamentStats)): ?>
                        <div class="stats-section mt-3">
                            <h4>Tournament Stats</h4>
                            <div class="table-responsive">
                                <table class="table stats-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Goals</th>
                                            <th>Assists</th>
                                            <th>YC</th>
                                            <th>RC</th>
                                            <th>CS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tournamentStats as $stat): ?>
                                            <tr>
                                                <td><strong><?= $stat['year'] ?></strong></td>
                                                <td class="text-success fw-bold"><?= (int)$stat['goals'] ?></td>
                                                <td class="text-info fw-bold"><?= (int)$stat['assists'] ?></td>
                                                <td class="text-warning fw-bold"><?= (int)$stat['yellow_cards'] ?></td>
                                                <td class="text-danger fw-bold"><?= (int)$stat['red_cards'] ?></td>
                                                <td class="text-primary fw-bold"><?= (int)$stat['clean_sheets'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Right: Official Player Card -->
        <div class="card-section">
            <div class="card-container">
                <div class="section-header">
                    <h3>Official Player Card</h3>
                </div>
                <div id="player-card-preview">
                    <p class="py-5 text-muted text-center">Loading card preview...</p>
                </div>
            </div>
            <div class="action-buttons">
                <a href="generate_card.php?player_id=<?= $player_id ?>&format=pdf"
                   class="action-btn"
                   target="_blank"
                   download="04SL_Player_Card_<?= $player_id ?>.pdf">
                    <i class="bi bi-file-earmark-pdf" style="color:#e74c3c;"></i>
                </a>
                <a href="generate_card.php?player_id=<?= $player_id ?>&format=png"
                   class="action-btn"
                   target="_blank"
                   download="04SL_Player_Card_<?= $player_id ?>.png">
                    <i class="bi bi-image" style="color:#3498db;"></i>
                </a>
                <button type="button" class="action-btn" id="share-general">
                    <i class="bi bi-share-fill" style="color:#95a5a6;"></i>
                </button>
            </div>
            <div id="share-fallback">
                <p class="text-muted small text-center mt-3">Download PNG and share manually if needed.</p>
            </div>
        </div>
    </div>
</div>

<!-- PNG Preview + Sharing Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.getElementById('player-card-preview');
    const pngUrl = 'generate_card.php?player_id=<?= $player_id ?>&format=png';
    const img = document.createElement('img');
    img.src = pngUrl;
    img.alt = 'Official Player Card';
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

    // Sharing Functionality
    const cardImageUrl = 'generate_card.php?player_id=<?= $player_id ?>&format=png&share=1';
    const playerName = "<?= addslashes(htmlspecialchars($player['name'])) ?>";
    const clubName = "<?= addslashes(htmlspecialchars($player['club_name'] ?? 'Free Agent')) ?>";
    const caption = `Official Player Card\n${playerName}\n${clubName}\nWWW.04SL.ONLINE`;

    async function getImageFile() {
        const res = await fetch(cardImageUrl);
        if (!res.ok) throw new Error('Failed to fetch image');
        const blob = await res.blob();
        return new File([blob], `04SL_Player_Card_${playerName.replace(/[^a-zA-Z0-9]/g, '_')}.png`, { type: 'image/png' });
    }

    async function shareImage() {
        try {
            const file = await getImageFile();
            const shareData = {
                files: [file],
                title: 'Player Card - ' + playerName,
                text: caption
            };
            if (navigator.canShare && navigator.canShare(shareData)) {
                await navigator.share(shareData);
                return;
            }
        } catch (err) {
            console.log('Sharing failed:', err);
        }
        const fallback = document.getElementById('share-fallback');
        fallback.style.display = 'block';
        setTimeout(() => {
            fallback.style.display = 'none';
        }, 5000);
    }

    document.getElementById('share-general').onclick = shareImage;
});
</script>
<?php include 'includes/footer.php'; ob_end_flush(); ?>