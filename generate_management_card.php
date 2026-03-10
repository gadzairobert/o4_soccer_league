<?php
require_once 'config.php';
include 'includes/properties.php';
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$staff_id = (int)($_GET['staff_id'] ?? 0);
$format = strtolower($_GET['format'] ?? 'pdf');

// If accessed directly in browser without format=png, and likely on mobile, show friendly viewer
$isDirectView = !isset($_GET['format']) || $format === 'pdf';

if ($staff_id <= 0) {
    header('Content-Type: text/plain', true, 400);
    die('Invalid staff ID');
}

// Fetch staff member
$stmt = $pdo->prepare("
    SELECT m.*, c.name AS club_name, c.logo AS club_logo
    FROM management m
    LEFT JOIN clubs c ON m.club_id = c.id
    WHERE m.id = ? AND m.is_active = 1
");
$stmt->execute([$staff_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    header('Content-Type: text/plain', true, 404);
    die('Staff member not found');
}

/* ===============================
   ID NUMBER & CREATED DATE (fallback to N/A)
================================ */
$idNumber = 'N/A'; // Management may not have ID number

$createdDate = 'N/A';
if (!empty($staff['created_at'])) {
    $createdDate = date('Ymd', strtotime($staff['created_at']));
}

/* ===============================
   DYNAMIC BASE URL
================================ */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/';

/* ===============================
   IMAGES
================================ */
$staffPhoto = !empty($staff['photo'])
    ? $baseUrl . 'uploads/management/' . $staff['photo']
    : $baseUrl . 'uploads/player_cards/default_bg.jpg'; // Reuse default if needed

$clubLogo = !empty($staff['club_logo'])
    ? $baseUrl . 'uploads/clubs/' . $staff['club_logo']
    : 'https://via.placeholder.com/200?text=Club';

$leagueLogo = $baseUrl . 'uploads/clubs/league_logo.png';
$qrCode     = $baseUrl . 'uploads/clubs/barcode.png';
$topWave    = $baseUrl . 'uploads/player_cards/top_wave.png';
$bottomWave = $baseUrl . 'uploads/player_cards/bottom_wave.png';
$redDivider = $baseUrl . 'uploads/player_cards/red_wave.png';

/* ===============================
   STAFF DATA
================================ */
$dob = (!empty($staff['date_of_birth']) && $staff['date_of_birth'] !== '0000-00-00')
    ? date('d.m.Y', strtotime($staff['date_of_birth']))
    : 'N/A';

$sequence = $createdDate . ' / ' . $staff_id;

/* ===============================
   CARD HTML - Adapted for Management
================================ */
$cardHtml = '
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@page { margin: 0; }
body { margin: 0; padding: 0; }

.card {
    width: 1688px;
    height: 2110px;
    position: relative;
    background: #fff;
    overflow: hidden;
    font-family: Arial, Helvetica, sans-serif;
}

.top-wave {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 400px;
    background: url("' . $topWave . '") no-repeat top center;
    background-size: cover;
}

.bottom-wave {
    position: absolute;
    bottom: 0; left: 0;
    width: 100%; height: 400px;
    background: url("' . $bottomWave . '") no-repeat bottom center;
    background-size: cover;
}

.full-watermark {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 1600px;
    opacity: 0.08;
}

.league-logo {
    position: absolute;
    top: 24px; left: 50%;
    transform: translateX(-50%);
    width: 500px;
}

.staff-photo {
    position: absolute;
    top: 510px; left: 50%;
    transform: translateX(-50%);
    width: 1000px;
    height: 1000px;
    border-radius: 50%;
    overflow: hidden;
    border: 30px solid #fff;
    box-shadow: 0 20px 50px rgba(0,0,0,0.4);
}

.staff-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.red-divider {
    position: absolute;
    top: 1450px; left: 50%;
    transform: translateX(-50%);
    width: 1500px;
    height: 120px;
    background: url("' . $redDivider . '") no-repeat center;
    background-size: contain;
}

.staff-name {
    position: absolute;
    top: 1570px; left: 50%;
    transform: translateX(-50%);
    font-size: 80px;
    font-weight: bold;
    color: #000080;
    text-transform: uppercase;
    text-align: center;
    width: 100%;
}

.staff-role {
    position: absolute;
    top: 1670px; left: 50%;
    transform: translateX(-50%);
    font-size: 85px;
    font-weight: bold;
    color: #c0392b;
    text-align: center;
    width: 100%;
}

.club-name {
    position: absolute;
    top: 1780px; left: 50%;
    transform: translateX(-50%);
    font-size: 75px;
    text-align: center;
    width: 100%;
}

.dob {
    position: absolute;
    top: 1880px; left: 50%;
    transform: translateX(-50%);
    font-size: 65px;
    text-align: center;
}

.club-logo-bottom {
    position: absolute;
    bottom: 90px;
    left: 1px;
    width: 440px;
}

.qr-code {
    position: absolute;
    bottom: 90px;
    right: 50px;
    width: 264px;
}

.website {
    position: absolute;
    bottom: 20px;
    left: 50px;
    font-size: 35px;
    font-weight: bold;
}

.sequence {
    position: absolute;
    bottom: 20px;
    right: 50px;
    font-size: 35px;
    font-weight: bold;
}
</style>
</head>

<body>
<div class="card">
    <div class="top-wave"></div>
    <div class="bottom-wave"></div>

    <div class="full-watermark">
        <img src="' . $leagueLogo . '" style="width:100%;">
    </div>

    <img src="' . $leagueLogo . '" class="league-logo">

    <div class="staff-photo">
        <img src="' . $staffPhoto . '">
    </div>

    <div class="red-divider"></div>

    <div class="staff-name">' . htmlspecialchars($staff['full_name']) . '</div>
    <div class="staff-role">' . htmlspecialchars($staff['role']) . '</div>
    <div class="club-name">' . htmlspecialchars($staff['club_name'] ?? 'Not Assigned') . '</div>
    <div class="dob">' . $dob . '</div>

    <div class="club-logo-bottom">
        <img src="' . $clubLogo . '" style="width:100%;">
    </div>

    <div class="qr-code">
        <img src="' . $qrCode . '" style="width:100%;">
    </div>

    <div class="website">WWW.04SL.ONLINE</div>
    <div class="sequence">' . $sequence . '</div>
</div>
</body>
</html>';

/* ===============================
   RENDER PDF
================================ */
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('dpi', 288);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($cardHtml);
$dompdf->setPaper([0, 0, 422, 528], 'portrait');
$dompdf->render();

$output = $dompdf->output();
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

/* ===============================
   OUTPUT
================================ */
if ($format === 'pdf') {
    // Mobile-friendly viewer if opened directly
    if ($isDirectView && !isset($_GET['download']) && strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== false) {
        echo '<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Card - ' . htmlspecialchars($staff['full_name']) . '</title>
    <style>body,html{margin:0;padding:0;height:100%;overflow:hidden;background:#000;}</style>
</head>
<body>
    <iframe src="https://docs.google.com/viewer?url=' . urlencode($currentUrl . '&download=1') . '&embedded=true" 
            width="100%" height="100%" style="border:none;"></iframe>
</body>
</html>';
        exit;
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="04SL_Management_Card_' . $staff_id . '.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $output;
    exit;
}

if ($format === 'png') {
    if (!extension_loaded('imagick')) {
        header('Content-Type: text/html');
        echo '<h3>Server Error</h3><p>PNG export requires the Imagick PHP extension.</p>';
        exit;
    }

    try {
        $imagick = new Imagick();
        $imagick->setResolution(300, 300);
        $imagick->readImageBlob($output . '[0]');

        $imagick->setImageFormat('png');
        $imagick->setImageCompressionQuality(100);

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="04SL_Management_Card_' . $staff_id . '.png"');
        echo $imagick->getImageBlob();

        $imagick->clear();
        $imagick->destroy();
        exit;
    } catch (Exception $e) {
        header('Content-Type: text/html');
        echo '<h3>PNG Generation Failed</h3><p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        exit;
    }
}

// Invalid format
header('Content-Type: text/html');
echo '<h3>Invalid Request</h3><p>Unsupported format.</p>';