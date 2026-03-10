<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/properties.php';
?>
<style>
    /* DARKISH THEME - CONSISTENT WITH THE REST OF THE SITE */
    html, body {
        background-color: #1e272e !important;
        color: #e0e0e0;
    }

    .container.py-4 { 
        margin-top: -38px !important; 
        padding-top: 6px !important; 
    }

    .gallery-card {
        background: #2c3e50;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.4);
        border: 1px solid #444;
    }

    .gallery-header {
        background: linear-gradient(135deg, #1a2530, #2c3e50);
        color: white;
        padding: 1.4rem 1.8rem;
        font-size: 1.4rem;
        font-weight: 700;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .gallery-body { 
        padding: 2rem; 
        background: #2c3e50; 
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1.5rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        transition: all 0.4s ease;
        cursor: pointer;
        aspect-ratio: 1 / 1;
    }

    .gallery-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.6);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-item:hover img { 
        transform: scale(1.12); 
    }

    .gallery-caption {
        position: absolute; 
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.9));
        color: white;
        padding: 1.8rem 0.8rem 0.8rem;
        font-weight: 600;
        text-align: center;
        font-size: 0.92rem;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .gallery-item:hover .gallery-caption { 
        opacity: 1; 
    }

    .gallery-date {
        font-size: 0.82rem;
        color: #bdc3c7;
        text-align: center;
        margin-top: 0.7rem;
    }

    /* RESPONSIVE GRID */
    @media (min-width: 1400px) { .gallery-grid { grid-template-columns: repeat(6, 1fr); } }
    @media (max-width: 1399.98px) { .gallery-grid { grid-template-columns: repeat(5, 1fr); } }
    @media (max-width: 1199.98px) { .gallery-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 991.98px)  { .gallery-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 767.98px)  { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }

    /* MOBILE */
    @media (max-width: 575.98px) {
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .gallery-body { padding: 1.5rem; }
        .gallery-header { font-size: 1.3rem; padding: 1.2rem 1rem; }
        .gallery-item { border-radius: 0; }
    }

    /* EMPTY STATE */
    .text-muted { color: #bdc3c7 !important; }

    /* MODAL - ALREADY DARK & WHITE TEXT (kept as is) */
</style>

<div class="container py-4 pb-5">
    <div class="gallery-card">
        <div class="gallery-header">
            Photo Gallery
        </div>

        <div class="gallery-body">
            <?php
            // === Your existing PHP logic (unchanged) ===
            $regularImages = getAllGalleryImages();

            $stmt = $pdo->query("
                SELECT 
                    image AS filename,
                    caption AS title,
                    '' AS description,
                    created_at AS uploaded_at
                FROM tournament_images 
                WHERE image IS NOT NULL AND image != ''
                ORDER BY created_at DESC
            ");
            $tournamentImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $allImages = [];

            foreach ($regularImages as $img) {
                $allImages[] = [
                    'path'        => 'uploads/gallery/' . $img['image'],
                    'title'       => $img['title'] ?? '',
                    'description' => $img['description'] ?? '',
                    'date'        => $img['uploaded_at'],
                    'source'      => 'gallery'
                ];
            }

            foreach ($tournamentImages as $img) {
                $allImages[] = [
                    'path'        => 'uploads/tournaments/' . $img['filename'],
                    'title'       => $img['title'] ?? '',
                    'description' => $img['description'],
                    'date'        => $img['uploaded_at'],
                    'source'      => 'tournament'
                ];
            }

            usort($allImages, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            ?>

            <?php if (empty($allImages)): ?>
                <div class="text-center py-5">
                    <p class="text-muted fs-4">No images in the gallery yet. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($allImages as $index => $img):
                        $fullPath = $img['path'];
                        $title    = htmlspecialchars($img['title'] ?: 'Untitled');
                        $desc     = htmlspecialchars($img['description']);
                        $date     = date('F j, Y', strtotime($img['date']));
                    ?>
                        <div class="gallery-item"
                             data-bs-toggle="modal"
                             data-bs-target="#galleryModal<?= $index ?>">
                            <img src="<?= $fullPath ?>" alt="<?= $title ?>" loading="lazy">
                            <?php if ($img['title']): ?>
                                <div class="gallery-caption"><?= $title ?></div>
                            <?php endif; ?>
                            <div class="gallery-date"><?= $date ?></div>
                        </div>

                        <!-- Modal for each image -->
                        <div class="modal fade" id="galleryModal<?= $index ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content bg-transparent border-0 shadow-none">
                                    <div class="text-end p-3">
                                        <button type="button"
                                                class="btn-close btn-close-white shadow-lg"
                                                data-bs-dismiss="modal"
                                                style="font-size:1.8rem; background:white; border-radius:50%; padding:10px;">
                                        </button>
                                    </div>
                                    <img src="<?= $fullPath ?>"
                                         class="img-fluid rounded shadow-lg"
                                         alt="<?= $title ?>"
                                         style="max-height:90vh; width:auto; margin:0 auto; display:block; border-radius:0;">
                                    <div class="text-center mt-4">
                                        <?php if ($img['title']): ?>
                                            <h4 class="text-white mb-2"><?= $title ?></h4>
                                        <?php endif; ?>
                                        <?php if ($desc): ?>
                                            <p class="text-white bg-dark bg-opacity-75 d-inline-block px-4 py-2 rounded mx-auto">
                                                <?= nl2br($desc) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-white mt-3 opacity-75"><?= $date ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<?php include 'includes/footer.php'; ob_end_flush(); ?>