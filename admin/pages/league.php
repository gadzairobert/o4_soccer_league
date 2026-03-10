<?php
// admin/pages/league.php
$standings_sql = "
    SELECT
        c.id, 
        c.name, 
        c.logo,
        COUNT(m.id) AS played,
        COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) 
            OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) AS wins,
        COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END) AS draws,
        COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score < m.away_score) 
            OR (f.away_club_id = c.id AND m.away_score < m.home_score)) THEN 1 END) AS losses,
        COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END), 0) AS gf,
        COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END), 0) AS ga,
        (COUNT(CASE WHEN ((f.home_club_id = c.id AND m.home_score > m.away_score) 
            OR (f.away_club_id = c.id AND m.away_score > m.home_score)) THEN 1 END) * 3 +
         COUNT(CASE WHEN m.home_score = m.away_score THEN 1 END)) AS points,
        (COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.home_score ELSE m.away_score END), 0) -
         COALESCE(SUM(CASE WHEN f.home_club_id = c.id THEN m.away_score ELSE m.home_score END), 0)) AS gd
    FROM clubs c
    LEFT JOIN fixtures f ON (f.home_club_id = c.id OR f.away_club_id = c.id)
    LEFT JOIN matches m ON m.fixture_id = f.id
    where c.name not in ('Loser 1','Loser 2','Winner 1','Winner 2')
    GROUP BY c.id, c.name, c.logo
    ORDER BY points DESC, gd DESC, gf DESC, wins DESC, c.name ASC
";

$standings = $pdo->query($standings_sql)->fetchAll(PDO::FETCH_ASSOC);
$total_teams = count($standings);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        League Table
    </h2>
    <div class="text-muted small">
        Current Season • All <?= $total_teams ?> Teams
    </div>
</div>

<?php if (empty($standings)): ?>
    <div class="alert alert-info text-center py-5 border-0 shadow-sm">
        <h5>No clubs found</h5>
        <p class="mb-0">Add clubs to see the league table.</p>
    </div>
<?php else: ?>
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-dark text-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0 text-white">League Standings</h4>
                </div>
                <div class="col-auto">
                    <small class="opacity-75">P = Played • W = Win • D = Draw • L = Loss</small>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom border-3 border-dark">
                    <tr class="text-center small text-muted">
                        <th class="text-start ps-4">Pos</th>
                        <th class="text-start">Club</th>
                        <th>P</th>
                        <th>W</th>
                        <th>D</th>
                        <th>L</th>
                        <th>GF</th>
                        <th>GA</th>
                        <th>GD</th>
                        <th class="text-end pe-4"><strong>Pts</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($standings as $index => $s): 
                        $pos = $index + 1;
                        $played = $s['played'] ?? 0;

                        // Row styling based on position
                        $rowClass = '';
                        if ($pos <= 4 && $played > 0) {
                            $rowClass = 'table-success';
                        } elseif ($pos == 5 && $played > 0) {
                            $rowClass = 'table-info';
                        } elseif ($pos >= $total_teams - 2 && $total_teams > 3 && $played > 0) {
                            $rowClass = 'table-danger';
                        }
                    ?>
                        <tr class="<?= $rowClass ?> <?= $played == 0 ? 'text-muted opacity-75' : '' ?>">
                            <td class="text-start ps-4">
                                <span class="badge <?= $played > 0 ? 'bg-dark' : 'bg-secondary' ?> rounded-pill px-3">
                                    <?= $pos ?>
                                </span>
                            </td>
                            <td class="text-start">
                                <div class="d-flex align-items-center">
                                    <?php if ($s['logo']): ?>
                                        <img src="../uploads/clubs/<?= htmlspecialchars($s['logo']) ?>" 
                                             width="36" height="36" class="me-3 rounded shadow-sm" 
                                             alt="<?= htmlspecialchars($s['name']) ?>" style="object-fit: contain;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width:36px;height:36px;font-size:12px;">
                                            <?= strtoupper(substr($s['name'], 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($s['name']) ?></strong>
                                        <?php if ($played == 0): ?>
                                            <br><small class="text-muted">No matches played</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-bold"><?= $played ?></td>
                            <td class="text-center text-success fw-bold"><?= $s['wins'] ?></td>
                            <td class="text-center text-secondary"><?= $s['draws'] ?></td>
                            <td class="text-center text-danger"><?= $s['losses'] ?></td>
                            <td class="text-center"><?= $s['gf'] ?></td>
                            <td class="text-center"><?= $s['ga'] ?></td>
                            <td class="text-center <?= $s['gd'] > 0 ? 'text-success' : ($s['gd'] < 0 ? 'text-danger' : 'text-muted') ?> fw-bold">
                                <?= $s['gd'] >= 0 ? '+' : '' ?><?= $s['gd'] ?>
                            </td>
                            <td class="text-end pe-4">
                                <h4 class="mb-0 text-primary fw-bold"><?= $s['points'] ?></h4>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div class="card-footer bg-light border-top-0">
            <div class="row text-center text-sm-start small">
                <div class="col-md-10 mx-auto">
                    <div class="row g-4">
                        <div class="col-auto d-flex align-items-center">
                            <div class="bg-success rounded me-2" style="width:20px;height:20px;"></div>
                            <span>Champions League</span>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <div class="bg-info rounded me-2" style="width:20px;height:20px;"></div>
                            <span>Europa League</span>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <div class="bg-danger rounded me-2 text-white" style="width:20px;height:20px;"></div>
                            <span>Relegation</span>
                        </div>
                        <div class="col-auto d-flex align-items-center text-muted">
                            <i class="bi bi-clock me-2"></i>
                            <span>Yet to play</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    @media (max-width: 768px) {
        .table thead th { font-size: 0.8rem; }
        .table tbody td { font-size: 0.9rem; padding: 0.6rem 0.4rem; }
        .card-footer .row > div { text-align: center !important; margin-bottom: 0.5rem; }
        .card-footer small { font-size: 0.75rem; }
    }
</style>