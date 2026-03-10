<?php
ob_start();
require_once 'config.php';
include 'includes/header.php';
include 'includes/properties.php';

$player_id = (int)($_GET['player_id'] ?? 0);
$player = getPlayerById($player_id);

if (!$player) { ?>
    <div class="container py-5 text-center">
        <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(201,168,76,0.2);border-radius:14px;padding:2.5rem;display:inline-block;">
            <i class="bi bi-person-x" style="font-size:4rem;color:rgba(201,168,76,0.4);display:block;margin-bottom:1rem;"></i>
            <h3 style="font-family:'Playfair Display',serif;color:#fdf8ef;">Player Not Found</h3>
            <p style="color:rgba(255,255,255,0.45);margin-bottom:1.2rem;">The player with ID <code><?= $player_id ?></code> does not exist.</p>
            <a href="index.php" class="btn" style="background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.4);color:#c9a84c;border-radius:50px;padding:0.5rem 2rem;font-weight:600;">Back to Home</a>
        </div>
    </div>
<?php
    include 'includes/footer.php';
    ob_end_flush();
    exit;
}

$currentLeague  = $pdo->query("SELECT id FROM competition_seasons WHERE is_current = 1 AND type = 'league' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$leagueSeasonId = $currentLeague['id'] ?? null;
$leagueStats    = getPlayerLeagueStats($player_id, $leagueSeasonId);
$tournamentStats = getPlayerTournamentStatsByYear($player_id);

$age = 'N/A';
if (!empty($player['date_of_birth']) && $player['date_of_birth'] !== '0000-00-00' && $player['date_of_birth'] !== null) {
    try {
        $dob = new DateTime($player['date_of_birth']);
        $now = new DateTime();
        $age = $now->diff($dob)->y . ' years';
    } catch (Exception $e) {}
}

$displayPhoto    = $player['photo']
    ? "uploads/players/" . htmlspecialchars($player['photo'])
    : "https://via.placeholder.com/500/1a1a2e/c9a84c?text=" . urlencode(substr($player['name'], 0, 2));
$displayClubLogo = $player['club_logo']
    ? "uploads/clubs/" . htmlspecialchars($player['club_logo'])
    : "https://via.placeholder.com/60/1a1a2e/c9a84c?text=" . urlencode(substr($player['club_name'] ?? 'FA', 0, 2));
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    /* ── Design Tokens ── */
    :root {
        --ink:        #1a1a2e;
        --gold:       #c9a84c;
        --gold-light: #f0d080;
        --cream:      #fdf8ef;
        --muted:      rgba(255,255,255,0.45);
        --border:     rgba(201,168,76,0.2);
        --card-bg:    rgba(255,255,255,0.04);
    }

    html, body {
        background-color: #1a1a2e !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.06) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(123,45,139,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: #eee;
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    footer { flex-shrink: 0; }

    /* ── Page Wrapper ── */
    .player-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .player-page-wrapper { margin-top: 0; padding: 1rem 0 3rem; width: 100%; }
    }

    /* ── Main Layout ── */
    .main-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: flex-start;
        justify-content: center;
    }
    .profile-section {
        flex: 2 1 620px;
        min-width: 0;
        max-width: 100%;
    }
    .card-section {
        flex: 1 1 380px;
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100%;
    }
    @media (max-width: 992px) {
        .main-layout { flex-direction: column; align-items: stretch; gap: 1.6rem; }
        .profile-section, .card-section { max-width: none; width: 100%; }
    }

    /* ── Profile Card ── */
    .player-profile-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        width: 100%;
    }

    /* ── Section Header ── */
    .section-header {
        background: linear-gradient(135deg, #16152b, #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.8rem;
    }
    .section-header h1, .section-header h3 {
        font-family: 'Playfair Display', serif;
        margin: 0;
        font-size: 1.7rem;
        font-weight: 900;
        color: var(--cream);
    }
    .section-header h3 { font-size: 1.1rem; }
    .section-header .position {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--gold);
        letter-spacing: 0.5px;
        margin-top: 3px;
    }
    .jersey-number {
        font-family: 'Playfair Display', serif;
        background: rgba(201,168,76,0.12);
        border: 1px solid rgba(201,168,76,0.35);
        color: var(--gold);
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 1.3rem;
        font-weight: 900;
    }

    /* ── Player Body ── */
    .player-body {
        padding: 1.8rem 2rem 2.2rem;
        text-align: center;
    }
    @media (max-width: 576px) { .player-body { padding: 1.4rem 1rem; } }

    /* ── Photo ── */
    .player-photo-wrapper {
        width: 240px; height: 240px;
        margin: 0 auto 1.6rem auto;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 16px 44px rgba(0,0,0,0.45);
        border: 4px solid var(--border);
        background: rgba(0,0,0,0.2);
        transition: border-color 0.3s;
    }
    .player-photo-wrapper:hover { border-color: var(--gold); }
    .player-photo { width: 100%; height: 100%; object-fit: cover; display: block; }
    @media (max-width: 576px) {
        .player-photo-wrapper { width: 190px; height: 190px; }
    }

    /* ── Info Grid ── */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.9rem;
        margin-bottom: 1.4rem;
        text-align: left;
    }
    @media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }

    .info-item {
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border);
        border-left: 3px solid var(--gold);
        border-radius: 8px;
        padding: 0.9rem 1rem;
    }
    .info-item strong {
        display: block;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--gold);
        margin-bottom: 5px;
    }
    .info-item span {
        font-family: 'DM Sans', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: var(--cream);
    }

    /* ── Club Link ── */
    .club-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 12px;
        background: rgba(201,168,76,0.1);
        border: 1px solid rgba(201,168,76,0.3);
        border-radius: 50px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--gold);
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .club-link:hover {
        background: rgba(201,168,76,0.2);
        border-color: var(--gold);
        color: var(--gold-light);
        transform: translateY(-2px);
    }
    .club-link img { width: 28px; height: 28px; object-fit: contain; border-radius: 50%; }

    /* ── Stats Section ── */
    .stats-section {
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.2rem;
        margin-top: 1.2rem;
        text-align: left;
    }
    .stats-section h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--cream);
        margin-bottom: 0.9rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid var(--border);
    }

    /* ── Stats Table ── */
    .stats-table {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        width: 100%;
        margin-bottom: 0;
    }
    .stats-table th {
        background: rgba(0,0,0,0.3) !important;
        color: var(--gold) !important;
        border-bottom: 1px solid var(--border) !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.6rem 0.7rem;
        text-align: center;
    }
    .stats-table td {
        background: transparent !important;
        color: rgba(255,255,255,0.8) !important;
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
        padding: 0.6rem 0.7rem;
        text-align: center;
    }
    .stats-table tbody tr:hover { background: rgba(201,168,76,0.04) !important; }
    .stats-table tbody tr:last-child td { border-bottom: none !important; }

    /* Stat colours */
    .text-success { color: #4ade80 !important; }
    .text-info    { color: #67e8f9 !important; }
    .text-warning { color: #fbbf24 !important; }
    .text-danger  { color: #f87171 !important; }
    .text-primary { color: var(--gold) !important; }
    .text-muted   { color: var(--muted) !important; }

    /* ── Official Card Container ── */
    .card-container {
        width: 100%;
        max-width: 460px;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0,0,0,0.35);
    }

    #player-card-preview {
        width: 100%;
        height: 600px;
        border: none;
        background: rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 576px) { #player-card-preview { height: 480px; } }

    /* ── Action Buttons ── */
    .action-buttons {
        margin-top: 1.6rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.6rem;
        justify-content: center;
    }
    .action-btn {
        flex: 1 1 110px;
        min-width: 100px;
        max-width: 130px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        transition: transform 0.3s ease, color 0.2s ease;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .action-btn:hover { transform: translateY(-5px); color: var(--cream); }
    .action-btn i {
        font-size: 2.8rem;
        width: 82px; height: 82px;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        transition: background 0.25s, border-color 0.25s, box-shadow 0.25s;
    }
    .action-btn:hover i {
        background: rgba(201,168,76,0.1);
        border-color: rgba(201,168,76,0.4);
        box-shadow: 0 10px 28px rgba(201,168,76,0.15);
    }

    #share-fallback {
        margin-top: 1rem;
        text-align: center;
        color: var(--muted);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.85rem;
        display: none;
    }

    /* ── Mobile Overrides ── */
    @media (max-width: 576px) {
        .player-profile-card, .card-container { border-radius: 0; border-left: none; border-right: none; }
        .section-header { padding: 1rem; flex-direction: column; text-align: center; }
        .section-header h1 { font-size: 1.5rem; }
        .action-btn i { width: 72px; height: 72px; font-size: 2.4rem; }
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="player-page-wrapper container">
    <div class="main-layout">

        <!-- ── Left: Player Profile ── -->
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
                             onerror="this.src='https://via.placeholder.com/500/1a1a2e/c9a84c?text=<?= urlencode(substr($player['name'], 0, 2)) ?>'">
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

                    <!-- League Stats -->
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

                    <!-- Tournament Stats -->
                    <?php if (!empty($tournamentStats)): ?>
                        <div class="stats-section mt-3">
                            <h4>Tournament Stats</h4>
                            <div class="table-responsive">
                                <table class="table stats-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Year</th><th>Goals</th><th>Assists</th><th>YC</th><th>RC</th><th>CS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tournamentStats as $stat): ?>
                                            <tr>
                                                <td class="fw-bold" style="color:var(--gold);"><?= $stat['year'] ?></td>
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

        <!-- ── Right: Official Player Card ── -->
        <div class="card-section">
            <div class="card-container">
                <div class="section-header">
                    <h3>Official Player Card</h3>
                </div>
                <div id="player-card-preview">
                    <p style="font-family:'DM Sans',sans-serif;font-size:0.88rem;color:var(--muted);text-align:center;padding:3rem 1rem;">Loading card preview…</p>
                </div>
            </div>

            <div class="action-buttons">
                <a href="generate_card.php?player_id=<?= $player_id ?>&format=pdf"
                   class="action-btn" target="_blank"
                   download="04SL_Player_Card_<?= $player_id ?>.pdf">
                    <i class="bi bi-file-earmark-pdf" style="color:#f87171;"></i>
                    <span>PDF</span>
                </a>
                <a href="generate_card.php?player_id=<?= $player_id ?>&format=png"
                   class="action-btn" target="_blank"
                   download="04SL_Player_Card_<?= $player_id ?>.png">
                    <i class="bi bi-image" style="color:#67e8f9;"></i>
                    <span>PNG</span>
                </a>
                <button type="button" class="action-btn" id="share-general">
                    <i class="bi bi-share-fill" style="color:var(--gold);"></i>
                    <span>Share</span>
                </button>
            </div>

            <div id="share-fallback">
                <p>Download PNG and share manually if needed.</p>
            </div>
        </div>

    </div><!-- /.main-layout -->
</div><!-- /.player-page-wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const preview = document.getElementById('player-card-preview');
    const pngUrl  = 'generate_card.php?player_id=<?= $player_id ?>&format=png';
    const img = document.createElement('img');
    img.src = pngUrl;
    img.alt = 'Official Player Card';
    img.style.cssText = 'width:100%;height:100%;object-fit:contain;background:transparent;display:block;';
    img.onload  = () => { preview.innerHTML = ''; preview.appendChild(img); };
    img.onerror = () => {
        preview.innerHTML = '<p style="color:#f87171;text-align:center;padding:3rem 1rem;font-family:\'DM Sans\',sans-serif;">Preview unavailable<br><small style="color:rgba(255,255,255,0.4);">Use the download buttons below</small></p>';
    };

    const playerName = "<?= addslashes(htmlspecialchars($player['name'])) ?>";
    const clubName   = "<?= addslashes(htmlspecialchars($player['club_name'] ?? 'Free Agent')) ?>";
    const caption    = `Official Player Card\n${playerName}\n${clubName}\nWWW.04SL.ONLINE`;
    const cardImageUrl = `generate_card.php?player_id=<?= $player_id ?>&format=png&share=1`;

    async function getImageFile() {
        const res = await fetch(cardImageUrl);
        if (!res.ok) throw new Error('Fetch failed');
        const blob = await res.blob();
        return new File([blob], `04SL_Player_Card_${playerName.replace(/[^a-zA-Z0-9]/g, '_')}.png`, { type: 'image/png' });
    }

    async function shareImage() {
        try {
            const file = await getImageFile();
            const shareData = { files: [file], title: 'Player Card - ' + playerName, text: caption };
            if (navigator.canShare && navigator.canShare(shareData)) {
                await navigator.share(shareData);
                return;
            }
        } catch (err) { console.log('Sharing failed:', err); }
        const fallback = document.getElementById('share-fallback');
        fallback.style.display = 'block';
        setTimeout(() => { fallback.style.display = 'none'; }, 5000);
    }

    document.getElementById('share-general').onclick = shareImage;
});
</script>

<?php include 'includes/footer.php'; ob_end_flush(); ?>