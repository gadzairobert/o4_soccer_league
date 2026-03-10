<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$imageDir = 'uploads/admin/slideshow/';
$allSlides = [];
try {
    $stmt = $pdo->query("SELECT filename FROM slideshow_images WHERE is_active = 1 ORDER BY sort_order ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $filename = basename($row['filename']);
        $path = $imageDir . $filename;
        if (file_exists($path)) {
            $allSlides[] = $path;
        }
    }
} catch (Exception $e) {}
if (empty($allSlides) && is_dir($imageDir)) {
    foreach (glob($imageDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $file) {
        $allSlides[] = $file;
    }
}
if (empty($allSlides)) {
    $allSlides = [
        'https://images.unsplash.com/photo-1552667466-34af7da58b48?w=3780&h=1890&fit=crop&q=90',
        'https://images.unsplash.com/photo-1574629810360-7ef3099c9757?w=3780&h=1890&fit=crop&q=90',
        'https://images.unsplash.com/photo-1518098268026-4e89f1a2cd8b?w=3780&h=1890&fit=crop&q=90',
        'https://images.unsplash.com/photo-1529903383459-1d4497168f16?w=3780&h=1890&fit=crop&q=90',
    ];
}
// Fetch up to 4 latest upcoming fixtures + type
$upcomingFixtures = [];
$currentDateTime = date('Y-m-d H:i:s');
try {
    $sql = "
        (SELECT
            f.fixture_date AS fixture_date,
            home.name AS home_name,
            home.logo AS home_logo,
            away.name AS away_name,
            away.logo AS away_logo,
            cs.name AS competition_name,
            'league' AS fixture_type
         FROM fixtures f
         JOIN clubs home ON f.home_club_id = home.id
         JOIN clubs away ON f.away_club_id = away.id
         JOIN competition_seasons cs ON f.competition_season_id = cs.id
         WHERE f.fixture_date >= :now
           AND (f.status IS NULL OR f.status = '' OR f.status != 'finished')
        )
        UNION ALL
        (SELECT
            tf.tournament_date AS fixture_date,
            home.name AS home_name,
            home.logo AS home_logo,
            away.name AS away_name,
            away.logo AS away_logo,
            cs.name AS competition_name,
            'tournament' AS fixture_type
         FROM tournament_fixtures tf
         JOIN clubs home ON tf.home_club_id = home.id
         JOIN clubs away ON tf.away_club_id = away.id
         JOIN competition_seasons cs ON tf.competition_season_id = cs.id
         WHERE tf.tournament_date >= :now
           AND (tf.status IS NULL OR tf.status = '' OR tf.status != 'finished')
        )
        ORDER BY fixture_date ASC
        LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':now' => $currentDateTime]);
    $upcomingFixtures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    shuffle($upcomingFixtures);
} catch (Exception $e) {
    error_log('Slideshow fixture query error: ' . $e->getMessage());
}
// Build final slides
$finalSlides = [];
$bgIndex = 0;
$bgCount = count($allSlides);
foreach ($upcomingFixtures as $fix) {
    $bgSrc = $allSlides[$bgIndex % $bgCount];
    $bgIndex++;
    $finalSlides[] = [
        'type' => 'fixture',
        'bg_src' => $bgSrc,
        'data' => $fix
    ];
}
while (count($finalSlides) < 4) {
    $bgSrc = $allSlides[$bgIndex % $bgCount];
    $bgIndex++;
    $finalSlides[] = [
        'type' => 'fallback',
        'bg_src' => $bgSrc
    ];
}
$key = 'hero_final_nogap';
$slideCount = count($finalSlides);
if (!isset($_SESSION[$key]) || ($_SESSION[$key.'_cnt']??0) !== $slideCount) {
    $indices = range(0, $slideCount - 1);
    shuffle($indices);
    $_SESSION[$key] = array_slice($indices, 0, min(10, $slideCount));
    $_SESSION[$key.'_cnt'] = $slideCount;
}
$slides = [];
foreach ($_SESSION[$key] as $i) if (isset($finalSlides[$i])) $slides[] = $finalSlides[$i];
if (empty($slides)) $slides = $finalSlides;
$total = count($slides);
?>
<!-- SLIDESHOW CONTAINER -->
<div class="hero-fullscreen-fix">
    <div class="hero-slideshow">
        <?php foreach ($slides as $i => $slide): ?>
            <div class="hero-slide <?= $i===0 ? 'active' : '' ?>" data-index="<?= $i ?>">
                <img src="<?= htmlspecialchars($slide['bg_src']) ?>"
                     alt="04 Soccer League"
                     class="hero-image"
                     loading="<?= $i===0 ? 'eager' : 'lazy' ?>">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <?php if ($slide['type'] === 'fixture'):
                        $f = $slide['data'];
                        $homeLogo = $f['home_logo'] ? 'uploads/clubs/' . htmlspecialchars($f['home_logo']) : 'https://via.placeholder.com/140x140?text=No+Logo';
                        $awayLogo = $f['away_logo'] ? 'uploads/clubs/' . htmlspecialchars($f['away_logo']) : 'https://via.placeholder.com/140x140?text=No+Logo';
                        $dateStr = strtoupper(date('D, M j', strtotime($f['fixture_date'])));
                        $timeStr = strtoupper(date('g:i A', strtotime($f['fixture_date'])));
                        $link = $f['fixture_type'] === 'tournament' ? 'tournaments.php' : 'fixtures.php';
                    ?>
                        <div class="fixture-box">
                            <div class="competition-bar">
                                <?= htmlspecialchars($f['competition_name']) ?>
                            </div>
                            <div class="fixture-teams">
                                <div class="fixture-team home">
                                    <div class="team-name"><?= htmlspecialchars($f['home_name']) ?></div>
                                    <img src="<?= $homeLogo ?>" alt="<?= htmlspecialchars($f['home_name']) ?>" class="team-logo">
                                </div>
                                <div class="vs">VS</div>
                                <div class="fixture-team away">
                                    <div class="team-name"><?= htmlspecialchars($f['away_name']) ?></div>
                                    <img src="<?= $awayLogo ?>" alt="<?= htmlspecialchars($f['away_name']) ?>" class="team-logo">
                                </div>
                            </div>
                            <div class="fixture-details bottom">
                                <div class="date-time horizontal">
                                    <div class="date"><?= $dateStr ?></div>
                                    <div class="time"><?= $timeStr ?></div>
                                </div>
                                <a href="<?= $link ?>" class="view-all-btn">View All Fixtures</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <h3 class="hero-subtitle">Passion • Pride • Football</h3>
                        <h1 class="hero-title">04 Soccer League</h1>
                        <div class="hero-bar"></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if ($total > 1): ?>
            <!-- Arrows - hidden by default, show on hover -->
            <div class="hero-nav hero-prev" aria-label="Previous slide">
                <svg viewBox="0 0 24 24" width="48" height="48"><path fill="currentColor" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </div>
            <div class="hero-nav hero-next" aria-label="Next slide">
                <svg viewBox="0 0 24 24" width="48" height="48"><path fill="currentColor" d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </div>
            <!-- Progress bar -->
            <div class="progress-container"><div class="progress-bar"></div></div>
        <?php endif; ?>
    </div>
</div>
<style>
@import url('https://fonts.googleapis.com/css2?family=Oswald:wght@600;800;900&display=swap');

/* Removed all top margins/paddings to eliminate gap above slideshow */
.hero-fullscreen-fix { 
    width: 100vw; 
    position: relative; 
    left: 50%; 
    transform: translateX(-50%); 
    margin: 0 !important; 
    padding: 0 !important; 
    line-height: 0; 
    overflow: hidden; 
}

.hero-slideshow { 
    width: 100vw; 
    aspect-ratio: 3780 / 1890; 
    max-height: 92dvh; 
    min-height: 320px; 
    position: relative; 
    overflow: hidden; 
    background: #000; 
    font-family: 'Oswald', sans-serif; 
    margin: 0 !important; 
    padding: 0 !important; 
    display: block; 
}

.hero-image { 
    position: absolute; 
    inset: 0; 
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
    object-position: center top; 
}

.hero-overlay { 
    position: absolute; 
    inset: 0; 
    background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.55) 40%, transparent 100%); 
    z-index: 1; 
}

.hero-slide { 
    position: absolute; 
    inset: 0; 
    opacity: 0; 
    visibility: hidden; 
    transition: opacity 0.8s ease-in-out; 
}

.hero-slide.active { 
    opacity: 1; 
    visibility: visible; 
    z-index: 1; 
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 2;
    width: 94%;
    max-width: 1600px;
    color: white;
    pointer-events: none;
}

/* Fixture box reduced by ~20% overall */
.fixture-box {
    background: rgba(0, 0, 0, 0.68);
    backdrop-filter: blur(10px);
    border-radius: 16px; /* reduced from 20px */
    padding: 0;
    width: fit-content;
    max-width: 95%;
    margin: 0 auto;
    box-shadow: 0 16px 40px rgba(0,0,0,0.8); /* reduced from 20/50 */
    border: 1px solid rgba(255,255,255,0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    pointer-events: auto;
    transform: scale(0.8); /* Main 20% reduction */
    transform-origin: center;
}

.competition-bar {
    background: #1a2530;
    color: white;
    padding: 1.12rem 2.24rem; /* 20% less than original 1.4/2.8 */
    text-align: center;
    font-weight: 700;
    font-size: clamp(0.88rem, 2.4vw, 1.76rem); /* 20% reduction */
    letter-spacing: 0.28rem;
    text-transform: uppercase;
    text-shadow: 0 3px 10px rgba(0,0,0,0.9);
    width: 100%;
    box-sizing: border-box;
    line-height: 1.3;
    word-wrap: break-word;
}

.fixture-teams {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem; /* reduced from 2.5rem */
    padding: 1.44rem 2.24rem 1.12rem; /* ~20% less */
    flex-wrap: nowrap;
}

.fixture-team {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.48rem; /* reduced from 0.6rem */
    min-width: 0;
}

.team-name {
    font-weight: 900;
    font-size: clamp(0.816rem, 2.24vw, 1.904rem); /* 20% less */
    line-height: 1.1;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    text-shadow: 0 4px 12px rgba(0,0,0,0.95);
    white-space: nowrap;
}

.team-logo {
    width: clamp(44px, 6.4vw, 88px); /* 20% less */
    height: clamp(44px, 6.4vw, 88px);
    object-fit: contain;
    background: white;
    padding: 6.4px; /* reduced from 8px */
    border-radius: 9.6px;
    box-shadow: none;
    flex-shrink: 0;
}

.vs {
    font-weight: 900;
    font-size: clamp(1.6rem, 4vw, 3.6rem); /* 20% less */
    letter-spacing: 0.12em;
    text-shadow: 0 6px 18px rgba(0,0,0,0.95);
    flex-shrink: 0;
}

.fixture-details.bottom {
    padding: 0 2.24rem 1.6rem; /* reduced */
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.6rem; /* reduced from 2rem */
}

.date-time.horizontal {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1.6rem;
    font-weight: 700;
    font-size: clamp(0.64rem, 1.6vw, 1.36rem); /* 20% less */
    letter-spacing: 0;
    text-transform: uppercase;
    text-shadow: 0 3px 10px rgba(0,0,0,0.9);
}

.date-time.horizontal .time {
    font-size: clamp(0.64rem, 1.6vw, 1.36rem);
    margin-top: 0;
    opacity: 0.94;
}

.view-all-btn {
    display: inline-block;
    background: #2e8b57;
    color: white;
    font-family: 'Oswald', sans-serif;
    font-weight: 400;
    font-size: clamp(0.68rem, 1.44vw, 1.12rem); /* 20% less */
    letter-spacing: normal;
    text-transform: none;
    padding: 1.12rem 2.24rem; /* 20% less */
    text-decoration: none;
    border: none;
    border-radius: 50px;
    box-shadow: 0 6.4px 20px rgba(46,139,87,0.4);
    transition: all 0.3s ease;
    pointer-events: auto;
}

.view-all-btn:hover {
    background: #276d4a;
    transform: translateY(-3px);
    box-shadow: 0 9.6px 28px rgba(46,139,87,0.6);
}

/* Arrows */
.hero-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 56px; /* slightly smaller */
    height: 56px;
    background: rgba(255, 255, 255, 0.25);
    color: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 9999;
    pointer-events: auto;
    backdrop-filter: blur(6px);
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.hero-slideshow:hover .hero-nav {
    opacity: 1;
    visibility: visible;
}

.hero-nav:hover {
    background: rgba(255, 255, 255, 0.5);
    transform: translateY(-50%) scale(1.15);
}

.hero-prev { left: 20px; }
.hero-next { right: 20px; }

.hero-nav svg { width: 38.4px; height: 38.4px; } /* 20% smaller */

/* Progress bar */
.progress-container { 
    position: absolute; 
    bottom: 0; 
    left: 0; 
    right: 0; 
    height: 5px; 
    background: rgba(255,255,255,0.25); 
    z-index: 10; 
}

.progress-bar { height: 100%; width: 0; background: white; }

/* Mobile adjustments - also scaled down */
@media (max-width: 1023px) {
    .hero-image { object-fit: contain; background: #000; }
    .fixture-box { max-width: 95%; border-radius: 12.8px; }
    .competition-bar { padding: 0.96rem 1.44rem; font-size: clamp(0.8rem, 2.8vw, 1.52rem); letter-spacing: 0.2rem; }
    .fixture-teams { gap: 1.44rem; padding: 1.28rem 1.44rem 0.96rem; }
    .fixture-details.bottom { padding: 0 1.44rem 1.44rem; gap: 1.44rem; }
    .team-logo { width: clamp(36px, 8vw, 60px); height: clamp(36px, 8vw, 60px); padding: 4.8px; border-radius: 8px; }
    .team-name { font-size: clamp(0.72rem, 2.8vw, 1.52rem); }
    .vs { font-size: clamp(1.44rem, 4.4vw, 2.8rem); }
    .date-time.horizontal { gap: 1.2rem; }
    .view-all-btn { padding: 1.04rem 1.92rem; }
    .hero-nav { width: 48px; height: 48px; }
    .hero-nav svg { width: 32px; height: 32px; }
    .hero-prev { left: 10px; }
    .hero-next { right: 10px; }
    .hero-slideshow .hero-nav {
        opacity: 0.8;
        visibility: visible;
    }
}

/* Fallback text styles - also slightly reduced */
.hero-subtitle { 
    font-weight: 600; 
    font-size: clamp(0.64rem, 2vw, 1.6rem); 
    letter-spacing: 0.4rem; 
    text-transform: uppercase; 
    margin-bottom: 0.4rem; 
    text-shadow: 0 6px 20px rgba(0,0,0,0.95); 
}

.hero-title { 
    font-weight: 900; 
    font-size: clamp(1.44rem, 4.4vw, 4.8rem); 
    line-height: 1.04; 
    margin: 0; 
    text-shadow: 0 12px 40px rgba(0,0,0,0.95); 
}

.hero-bar { 
    width: 64px; /* reduced from 80px */
    height: 4px; 
    background: white; 
    margin: 0.96rem auto 0; 
    border-radius: 4px; 
}

<?php
$duration = 18;
$totalTime = $duration * $total;
$percent = 100 / $total;
for ($i = 0; $i < $total; $i++):
    $start = $i * $percent;
    $end = ($i + 1) * $percent;
?>
.hero-slide:nth-child(<?=$i+1?>) { animation: s<?=$i?> <?=$totalTime?>s infinite; }
@keyframes s<?=$i?> {
    0%, <?=$start?>% { opacity: 0; visibility: hidden; }
    <?=$start + 0.01?>%, <?=$end?>% { opacity: 1; visibility: visible; z-index: 1; }
    <?=$end + 0.01?>%, 100% { opacity: 0; visibility: hidden; }
}
<?php endfor; ?>
.progress-bar { animation: prog <?=$totalTime?>s linear infinite; }
@keyframes prog { from {width:0} to {width:100%} }
</style>
<script>
// Bulletproof clickable arrows with hover visibility
document.addEventListener('DOMContentLoaded', function() {
    const slideshow = document.querySelector('.hero-slideshow');
    if (!slideshow) return;
    const slides = slideshow.querySelectorAll('.hero-slide');
    const total = slides.length;
    if (total <= 1) return;
    const prevBtn = slideshow.querySelector('.hero-prev');
    const nextBtn = slideshow.querySelector('.hero-next');
    const progressBar = slideshow.querySelector('.progress-bar');
    let currentIndex = 0;
    let intervalId = null;
    const slideDuration = <?=$duration * 1000?>;
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        currentIndex = index;
        // Restart progress bar
        progressBar.style.animation = 'none';
        void progressBar.offsetWidth;
        progressBar.style.animation = `prog ${<?=$totalTime?>}s linear infinite`;
    }
    function nextSlide() {
        currentIndex = (currentIndex + 1) % total;
        showSlide(currentIndex);
    }
    function prevSlide() {
        currentIndex = (currentIndex - 1 + total) % total;
        showSlide(currentIndex);
    }
    if (nextBtn) {
        nextBtn.style.pointerEvents = 'auto';
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            nextSlide();
            restartAutoPlay();
        });
    }
    if (prevBtn) {
        prevBtn.style.pointerEvents = 'auto';
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            prevSlide();
            restartAutoPlay();
        });
    }
    function startAutoPlay() {
        intervalId = setInterval(nextSlide, slideDuration);
    }
    function restartAutoPlay() {
        clearInterval(intervalId);
        startAutoPlay();
    }
    slideshow.addEventListener('mouseenter', () => clearInterval(intervalId));
    slideshow.addEventListener('mouseleave', startAutoPlay);
    startAutoPlay();
});
</script>