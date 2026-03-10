<?php 
// includes/footer.php
if (!isset($pdo)) { require_once 'config.php'; }

// === 1. Competition Name ===
$competition_name = "04 SOCCER LEAGUE";
$stmt = $pdo->prepare("SELECT competition_name FROM competition_seasons WHERE is_current = 1 AND type = 'league' ORDER BY season DESC LIMIT 1");
$stmt->execute();
if ($row = $stmt->fetchColumn()) $competition_name = $row ?: $competition_name;

if ($competition_name === "04 SOCCER LEAGUE") {
    $stmt = $pdo->prepare("SELECT title FROM logos WHERE purpose = 'login_logo' AND is_active = 1 LIMIT 1");
    $stmt->execute();
    if ($title = $stmt->fetchColumn()) $competition_name = $title;
}

// === 2. Logo ===
$stmt = $pdo->prepare("SELECT filename FROM logos WHERE purpose = 'frontend_header' AND is_active = 1 LIMIT 1");
$stmt->execute();
$headerLogo = $stmt->fetchColumn();
$logoSrc = $headerLogo ? 'uploads/admin/logos/' . $headerLogo : 'uploads/logo.png';

// === 3. Social Icons ===
$socialStmt = $pdo->prepare("SELECT icon_class, url FROM social_links WHERE is_active = 1 AND LOWER(platform_name) NOT LIKE '%youtube%' AND LOWER(platform_name) NOT LIKE '%tiktok%' ORDER BY sort_order ASC");
$socialStmt->execute();
$social_links = $socialStmt->fetchAll(PDO::FETCH_ASSOC);

// === 4. Dynamic Contact Items ===
$location_name = "Bikita, Zimbabwe";
$location_url  = "https://maps.google.com/?q=Ward+24+Cape+Town";
$stmt = $pdo->prepare("SELECT platform_name, url FROM social_links WHERE is_active = 1 AND (platform_name LIKE '%Location%' OR platform_name LIKE '%Map%' OR platform_name LIKE '%Venue%') LIMIT 1");
$stmt->execute();
if ($loc = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $location_name = $loc['platform_name'];
    $location_url  = $loc['url'];
}

$email_address = "info@04sl.online";
$stmt = $pdo->prepare("SELECT url FROM social_links WHERE is_active = 1 AND platform_name = 'Email' LIMIT 1");
$stmt->execute();
if ($email = $stmt->fetchColumn()) {
    $email_address = str_replace('mailto:', '', $email);
}

$whatsapp_number = "+27821234567";
$whatsapp_url    = "https://wa.me/27821234567";
$stmt = $pdo->prepare("SELECT url FROM social_links WHERE is_active = 1 AND platform_name = 'WhatsApp' LIMIT 1");
$stmt->execute();
if ($wa = $stmt->fetchColumn()) {
    $whatsapp_url    = $wa;
    $whatsapp_number = preg_replace('/[^0-9+]/', '', parse_url($wa, PHP_URL_PATH)) ?: "+27" . substr(parse_url($wa, PHP_URL_QUERY), 1);
}

// === 5. Navigation ===
$navStmt = $pdo->prepare("SELECT name, link, target_blank FROM nav_items WHERE parent_id = 0 AND is_active = 1 ORDER BY sort_order ASC");
$navStmt->execute();
$footer_nav = $navStmt->fetchAll(PDO::FETCH_ASSOC);
?>

</div><!-- /.main-content container -->

<footer class="footer-luxury pt-5 pb-4 overflow-x-hidden">
    <div class="container">
        <div class="row g-5 text-center text-lg-start">

            <!-- ── Logo + Name ── -->
            <div class="col-lg-4 col-md-12 order-lg-1 order-2">
                <div class="text-center">
                    <a href="index.php" class="d-inline-block mb-4">
                        <div class="footer-logo-circle mx-auto">
                            <img src="<?= htmlspecialchars($logoSrc) ?>"
                                 alt="<?= htmlspecialchars($competition_name) ?>"
                                 class="img-fluid rounded-circle">
                        </div>
                    </a>
                    <h4 class="footer-league-name mb-3"><?= htmlspecialchars($competition_name) ?></h4>
                    <p class="footer-tagline mb-4 mx-auto">
                        Official home of <?= htmlspecialchars($competition_name) ?>.<br>
                        Follow live scores, fixtures, clubs, and stay connected with the soccer community.
                    </p>
                    <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                        <?php foreach ($social_links as $social): ?>
                            <a href="<?= htmlspecialchars($social['url'] ?? '#') ?>"
                               target="_blank" rel="noopener"
                               class="footer-social-icon">
                                <i class="<?= htmlspecialchars($social['icon_class']) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ── Quick Links ── -->
            <div class="col-lg-3 col-md-6 col-12 order-lg-2 order-1 text-center text-lg-start">
                <h5 class="footer-section-title">Quick Links</h5>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($footer_nav as $item): ?>
                        <li class="mb-3">
                            <a href="<?= htmlspecialchars($item['link']) ?>"
                               class="footer-link d-inline-flex align-items-center justify-content-center justify-content-lg-start"
                               <?= $item['target_blank'] ? 'target="_blank" rel="noopener"' : '' ?>>
                                <i class="bi bi-chevron-right me-2" style="font-size:0.75rem;color:var(--footer-gold);"></i>
                                <?= htmlspecialchars($item['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- ── Contact ── -->
            <div class="col-lg-2 col-md-6 col-12 order-lg-3 order-3 text-center text-lg-start">
                <h5 class="footer-section-title">Contact</h5>
                <ul class="list-unstyled mb-0">

                    <li class="d-flex align-items-start mb-4 justify-content-center justify-content-lg-start flex-wrap gap-2">
                        <i class="bi bi-geo-alt-fill flex-shrink-0 me-2 fs-5 footer-icon-gold mt-1"></i>
                        <div class="text-start">
                            <div class="footer-contact-label"><?= htmlspecialchars($location_name) ?></div>
                            <a href="<?= htmlspecialchars($location_url) ?>"
                               target="_blank" rel="noopener"
                               class="footer-link small d-block">
                                View on Google Maps
                            </a>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-4 justify-content-center justify-content-lg-start flex-wrap gap-2">
                        <i class="bi bi-envelope-fill flex-shrink-0 me-2 fs-5 footer-icon-gold"></i>
                        <a href="mailto:<?= htmlspecialchars($email_address) ?>"
                           class="footer-link text-break">
                            <?= htmlspecialchars($email_address) ?>
                        </a>
                    </li>

                    <li class="d-flex align-items-center mb-4 justify-content-center justify-content-lg-start flex-wrap gap-2">
                        <i class="bi bi-whatsapp flex-shrink-0 me-2 fs-5" style="color:#4ade80;"></i>
                        <a href="<?= htmlspecialchars($whatsapp_url) ?>"
                           target="_blank" rel="noopener"
                           class="footer-link">
                            <?= htmlspecialchars($whatsapp_number) ?>
                        </a>
                    </li>

                </ul>
            </div>

            <!-- ── Newsletter ── -->
            <div class="col-lg-3 col-md-12 order-lg-4 order-4">
                <h5 class="footer-section-title">Stay Updated</h5>
                <p class="footer-tagline mb-3">Get latest results &amp; fixtures in your inbox.</p>
                <form action="newsletter.php" method="POST"
                      class="row g-3 justify-content-center justify-content-lg-start">
                    <div class="col-12 col-sm-8 col-lg-12">
                        <input type="email" name="email"
                               placeholder="Your email address"
                               class="footer-input form-control rounded-pill px-4"
                               required>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-12">
                        <button type="submit" class="footer-subscribe-btn w-100 rounded-pill fw-bold">
                            Subscribe
                        </button>
                    </div>
                </form>
            </div>

        </div><!-- /.row -->

        <!-- ── Divider ── -->
        <div class="footer-divider my-5"></div>

        <!-- ── Bottom Bar ── -->
        <div class="text-center footer-bottom px-3">
            <p class="mb-2">© <?= date('Y') ?> <?= htmlspecialchars($competition_name) ?>. All rights reserved.</p>
            <div>
                <a href="#" class="footer-bottom-link me-3">Privacy Policy</a>
                <a href="#" class="footer-bottom-link">Terms of Service</a>
            </div>
        </div>

    </div><!-- /.container -->
</footer>

<style>
    /* ── Footer Design Tokens ── */
    :root {
        --footer-bg:      #0e0d1c;
        --footer-surface: rgba(255,255,255,0.03);
        --footer-gold:    #c9a84c;
        --footer-gold-lt: #f0d080;
        --footer-cream:   #fdf8ef;
        --footer-muted:   rgba(255,255,255,0.42);
        --footer-border:  rgba(201,168,76,0.2);
    }

    /* ── Footer Shell ── */
    .footer-luxury {
        background: linear-gradient(160deg, #0e0d1c 0%, #131228 60%, #0b0b1a 100%);
        border-top: 2px solid var(--footer-gold);
        overflow-x: hidden !important;
        position: relative;
    }
    /* Subtle gold glow at top edge */
    .footer-luxury::before {
        content: '';
        position: absolute; top: 0; left: 10%; right: 10%; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(201,168,76,0.5), transparent);
    }

    /* ── Logo Circle ── */
    .footer-logo-circle {
        width: 110px; height: 110px;
        background: white;
        border-radius: 50%;
        padding: 8px;
        border: 3px solid var(--footer-border);
        box-shadow: 0 10px 32px rgba(0,0,0,0.45), 0 0 0 1px rgba(201,168,76,0.15);
        display: inline-flex; align-items: center; justify-content: center;
        transition: box-shadow 0.3s ease, border-color 0.3s ease;
    }
    .footer-logo-circle:hover {
        border-color: var(--footer-gold);
        box-shadow: 0 14px 40px rgba(0,0,0,0.5), 0 0 0 2px rgba(201,168,76,0.3);
    }
    .footer-logo-circle img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }

    /* ── League Name ── */
    .footer-league-name {
        font-family: 'Playfair Display', serif;
        font-weight: 900;
        color: var(--footer-cream);
        letter-spacing: 0.5px;
        font-size: 1.2rem;
        margin: 0;
    }

    /* ── Tagline ── */
    .footer-tagline {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        color: var(--footer-muted);
        line-height: 1.65;
        max-width: 320px;
        margin-bottom: 0;
    }

    /* ── Social Icons ── */
    .footer-social-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--footer-border);
        color: var(--footer-gold);
        font-size: 1.15rem;
        text-decoration: none;
        transition: background 0.25s ease, border-color 0.25s ease,
                    color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
    }
    .footer-social-icon:hover {
        background: rgba(201,168,76,0.15);
        border-color: var(--footer-gold);
        color: var(--footer-gold-lt);
        transform: translateY(-4px) scale(1.1);
        box-shadow: 0 8px 20px rgba(201,168,76,0.25);
    }

    /* ── Section Titles ── */
    .footer-section-title {
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--footer-gold);
        margin-bottom: 1.4rem;
        position: relative;
        padding-bottom: 0.65rem;
        display: inline-block;
    }
    .footer-section-title::after {
        content: '';
        position: absolute; left: 0; bottom: 0;
        width: 32px; height: 2px;
        background: var(--footer-gold);
        border-radius: 2px;
    }
    /* Centre underline on mobile */
    @media (max-width: 991.98px) {
        .footer-section-title::after { left: 50%; transform: translateX(-50%); }
    }

    /* ── Nav Links ── */
    .footer-link {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--footer-muted);
        text-decoration: none;
        transition: color 0.22s ease, padding-left 0.22s ease;
    }
    .footer-link:hover { color: var(--footer-gold-lt); padding-left: 4px; }

    /* ── Contact ── */
    .footer-icon-gold { color: var(--footer-gold); }
    .footer-contact-label {
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        color: rgba(255,255,255,0.75);
        margin-bottom: 2px;
    }

    /* ── Newsletter Input ── */
    .footer-input {
        background: rgba(255,255,255,0.06) !important;
        border: 1px solid var(--footer-border) !important;
        color: var(--footer-cream) !important;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }
    .footer-input::placeholder { color: var(--footer-muted) !important; }
    .footer-input:focus {
        background: rgba(255,255,255,0.09) !important;
        border-color: var(--footer-gold) !important;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.12) !important;
        color: var(--footer-cream) !important;
        outline: none;
    }

    /* ── Subscribe Button ── */
    .footer-subscribe-btn {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.84rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #0e0d1c;
        background: var(--footer-gold);
        border: none;
        padding: 0.7rem 1.5rem;
        transition: background 0.25s ease, transform 0.22s ease, box-shadow 0.25s ease;
        box-shadow: 0 6px 20px rgba(201,168,76,0.3);
        cursor: pointer;
    }
    .footer-subscribe-btn:hover {
        background: var(--footer-gold-lt);
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(201,168,76,0.4);
    }

    /* ── Divider ── */
    .footer-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--footer-border), transparent);
    }

    /* ── Bottom Bar ── */
    .footer-bottom {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        color: var(--footer-muted);
    }
    .footer-bottom p { margin-bottom: 0.4rem; }
    .footer-bottom-link {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.82rem;
        color: var(--footer-muted);
        text-decoration: none;
        transition: color 0.22s ease;
    }
    .footer-bottom-link:hover { color: var(--footer-gold-lt); }

    /* ── Mobile fixes ── */
    @media (max-width: 576px) {
        .footer-luxury * { word-break: break-word; overflow-wrap: anywhere; }
        .footer-luxury .container { padding-left: 15px !important; padding-right: 15px !important; }
        .footer-tagline { max-width: 100%; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>