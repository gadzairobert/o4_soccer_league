<?php 
// includes/footer.php – FINAL: NO HORIZONTAL SCROLL ON MOBILE
if (!isset($pdo)) { require_once 'config.php'; }

// === 1. Competition Name ===
$competition_name = "04 SOCCER LEAGUE";
$stmt = $pdo->prepare("SELECT competition_name FROM competition_seasons WHERE is_current = 1 and type= 'league' ORDER BY season DESC LIMIT 1");
$stmt->execute();
if ($row = $stmt->fetchColumn()) $competition_name = $row ?: $competition_name;

// Fallback
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
    $whatsapp_url = $wa;
    $whatsapp_number = preg_replace('/[^0-9+]/', '', parse_url($wa, PHP_URL_PATH)) ?: "+27" . substr(parse_url($wa, PHP_URL_QUERY), 1);
}

// === 5. Navigation ===
$navStmt = $pdo->prepare("SELECT name, link, target_blank FROM nav_items WHERE parent_id = 0 AND is_active = 1 ORDER BY sort_order ASC");
$navStmt->execute();
$footer_nav = $navStmt->fetchAll(PDO::FETCH_ASSOC);
?>

</div>

<footer class="footer-modern bg-dark text-white pt-5 pb-5 overflow-x-hidden">
    <div class="container">
        <div class="row g-5 text-center text-lg-start">

            <!-- LOGO + NAME -->
            <div class="col-lg-4 col-md-12 order-lg-1 order-2">
                <div class="text-center">
                    <a href="index.php" class="d-inline-block mb-4">
                        <div class="logo-circle-bg mx-auto">
                            <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($competition_name) ?>" class="img-fluid rounded-circle">
                        </div>
                    </a>
                    <h4 class="fw-bold mb-3"><?= htmlspecialchars($competition_name) ?></h4>
                    <p class="small opacity-90 mb-4 mx-auto" style="max-width: 320px;">
                        Official home of <?= htmlspecialchars($competition_name) ?>.<br>
                        Follow live scores, fixtures, clubs, and stay connected with the soccer community.
                    </p>
                    <div class="d-flex justify-content-center gap-4 mb-4 flex-wrap">
                        <?php foreach ($social_links as $social): ?>
                            <a href="<?= htmlspecialchars($social['url'] ?? '#') ?>" target="_blank" rel="noopener" class="text-white fs-3 hover-scale">
                                <i class="<?= htmlspecialchars($social['icon_class']) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- QUICK LINKS -->
            <div class="col-lg-3 col-md-6 col-12 order-lg-2 order-1 text-center text-lg-start">
                <h5 class="fw-bold mb-4 text-white text-uppercase tracking-wider">Quick Links</h5>
                <ul class="list-unstyled footer-links mb-0">
                    <?php foreach ($footer_nav as $item): ?>
                        <li class="mb-3">
                            <a href="<?= htmlspecialchars($item['link']) ?>" class="text-white text-decoration-none hover-primary d-inline-flex align-items-center justify-content-center justify-content-lg-start">
                                <i class="bi bi-chevron-right me-2"></i><?= htmlspecialchars($item['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    
                </ul>
            </div>

            <!-- CONTACT – SAFE ON MOBILE -->
            <div class="col-lg-2 col-md-6 col-12 order-lg-3 order-3 text-center text-lg-start">
                <h5 class="fw-bold mb-4 text-white text-uppercase tracking-wider">Contact</h5>
                <ul class="list-unstyled small text-white mb-0">

                    <li class="d-flex align-items-start mb-4 justify-content-center justify-content-lg-start flex-wrap gap-2">
                        <i class="bi bi-geo-alt-fill flex-shrink-0 me-3 fs-5 text-primary mt-1"></i>
                        <div class="text-start text-lg-start">
                            <div class="fw-semibold"><?= htmlspecialchars($location_name) ?></div>
                            <a href="<?= htmlspecialchars($location_url) ?>" target="_blank" rel="noopener" class="text-white text-decoration-none hover-primary small d-block">
                                View on Google Maps
                            </a>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-4 justify-content-center justify-content-lg-start flex-wrap gap-2">
                        <i class="bi bi-envelope-fill flex-shrink-0 me-3 fs-5 text-primary"></i>
                        <a href="mailto:<?= htmlspecialchars($email_address) ?>" class="text-white text-decoration-none hover-primary text-break">
                            <?= htmlspecialchars($email_address) ?>
                        </a>
                    </li>

                    <li class="d-flex align-items-center mb-4 justify-content-center justify-content-lg-start flex-wrap gap-2">
                        <i class="bi bi-whatsapp flex-shrink-0 me-3 fs-5 text-success"></i>
                        <a href="<?= htmlspecialchars($whatsapp_url) ?>" target="_blank" rel="noopener" class="text-white text-decoration-none hover-primary">
                            <?= htmlspecialchars($whatsapp_number) ?>
                        </a>
                    </li>

                </ul>
            </div>

            <!-- NEWSLETTER -->
            <div class="col-lg-3 col-md-12 order-lg-4 order-4">
                <h5 class="fw-bold mb-4 text-white text-uppercase tracking-wider">Stay Updated</h5>
                <p class="small opacity-90 mb-3">Get latest results & fixtures in your inbox.</p>
                <form action="newsletter.php" method="POST" class="row g-3 justify-content-center justify-content-lg-start">
                    <div class="col-12 col-sm-8 col-lg-12">
                        <input type="email" name="email" placeholder="Your email" class="form-control form-control-lg rounded-pill px-4" required>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-12">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>

        <hr class="my-5 border-secondary opacity-25">
        <div class="text-center small opacity-75 px-3">
            <p class="mb-2">© <?= date('Y') ?> <?= htmlspecialchars($competition_name) ?>. All rights reserved.</p>
            <div>
                <a href="#" class="text-white text-decoration-none me-3 hover-primary">Privacy Policy</a>
                <a href="#" class="text-white text-decoration-none hover-primary">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-modern {
    background: linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
    border-top: 5px solid #007bff;
    overflow-x: hidden !important; /* Critical fix */
}

.logo-circle-bg {
    width: 110px; height: 110px; background: white; border-radius: 50%;
    padding: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    display: inline-flex; align-items: center; justify-content: center;
}
.logo-circle-bg img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }

.tracking-wider { letter-spacing: 1.8px; }
.text-primary { color: #007bff !important; }
.text-success { color: #25d366 !important; }
.hover-primary { transition: color 0.3s ease !important; }
.hover-primary:hover { color: #007bff !important; }
.hover-scale i { transition: transform 0.3s ease; }
.hover-scale:hover i { transform: scale(1.3); }

/* MOBILE-SPECIFIC FIXES — NO EFFECT ON DESKTOP */
@media (max-width: 576px) {
    .footer-modern {
        overflow-x: hidden !important;
    }
    .footer-modern * {
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    .footer-modern .container {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    .footer-modern ul small a {
        font-size: 0.875rem !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>