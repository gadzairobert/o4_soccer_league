<?php
// search_results.php
require_once 'config.php';
require_once 'includes/properties.php';
require_once 'includes/header.php';

// Get and sanitize search term
$q = trim($_GET['q'] ?? '');
$minLength = 2;
?>
<div class="container py-5">
    <h2 class="mb-4">
        Search Results 
        <?php if ($q): ?>
            for: <strong class="text-primary"><?= htmlspecialchars($q) ?></strong>
        <?php else: ?>
            <span class="text-muted">(no term entered)</span>
        <?php endif; ?>
    </h2>

    <?php if ($q && strlen($q) >= $minLength): ?>
        <?php
        $players = searchPlayers($q);
        $clubs   = searchClubs($q);
        ?>

        <?php if ($players || $clubs): ?>
            <div class="row g-3">
                <!-- Players -->
                <?php foreach ($players as $p): 
                    // Safely get values with null coalescing
                    $playerId     = $p['id'] ?? 0;
                    $playerName   = $p['name'] ?? 'Unknown Player';
                    $playerPhoto  = $p['photo'] ?? '';
                    $clubName     = $p['club_name'] ?? '';

                    // Build photo URL safely
                    $photoUrl = !empty($playerPhoto)
                        ? 'uploads/players/' . htmlspecialchars($playerPhoto)
                        : 'https://via.placeholder.com/60/2c3e50/white?text=' . substr($playerName, 0, 2);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="player_profile.php?player_id=<?= $playerId ?>" class="text-decoration-none">
                            <div class="card h-100 shadow-sm hover-shadow border-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <img src="<?= $photoUrl ?>" alt="<?= htmlspecialchars($playerName) ?>" 
                                         class="rounded-circle" width="56" height="56" style="object-fit:cover;">
                                    <div class="flex-grow-1">
                                        <strong class="d-block"><?= htmlspecialchars($playerName) ?></strong>
                                        <?php if ($clubName): ?>
                                            <small class="text-muted"><?= htmlspecialchars($clubName) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Free Agent</small>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-primary fs-6">Player</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

                <!-- Clubs -->
                <?php foreach ($clubs as $c): 
                    $clubId   = $c['id'] ?? 0;
                    $clubName = $c['name'] ?? 'Unknown Club';
                    $clubLogo = $c['logo'] ?? '';

                    $logoUrl = !empty($clubLogo)
                        ? 'uploads/clubs/' . htmlspecialchars($clubLogo)
                        : 'https://via.placeholder.com/60/2c3e50/white?text=' . substr($clubName, 0, 2);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="clubs.php?club_id=<?= $clubId ?>" class="text-decoration-none">
                            <div class="card h-100 shadow-sm hover-shadow border-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <img src="<?= $logoUrl ?>" alt="<?= htmlspecialchars($clubName) ?>" 
                                         class="rounded" width="56" height="56" 
                                         style="object-fit:contain;background:#fff;padding:4px;">
                                    <div>
                                        <strong><?= htmlspecialchars($clubName) ?></strong>
                                    </div>
                                    <span class="badge bg-success fs-6">Club</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-emoji-frown fs-1"></i><br><br>
                <strong>No results found</strong> for "<em><?= htmlspecialchars($q) ?></em>"
                <p class="mt-3 text-muted">Try searching by player name, club name, or partial words.</p>
            </div>
        <?php endif; ?>

    <?php elseif ($q && strlen($q) < $minLength): ?>
        <div class="alert alert-warning text-center">
            Please enter at least <?= $minLength ?> characters to search.
        </div>

    <?php else: ?>
        <div class="alert alert-light text-center py-5 border-dashed">
            <i class="bi bi-search fs-1 text-muted"></i><br><br>
            <h4 class="text-muted">Start typing to search for players and clubs</h4>
            <p class="text-muted">You can search by name, nickname, or club.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-shadow { transition: all 0.3s ease; }
    .hover-shadow:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 28px rgba(0,0,0,0.15) !important; 
    }
    .border-dashed { border: 2px dashed #dee2e6 !important; background: #fafafa; }
</style>

<?php require_once 'includes/footer.php'; ?>