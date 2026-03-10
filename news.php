<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
// ---- Pagination ----
$perPage = 8;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$totalStmt = $pdo->query("SELECT COUNT(*) FROM news WHERE is_published = 1");
$total = $totalStmt->fetchColumn();
$pages = ceil($total / $perPage);
$newsStmt = $pdo->prepare("
    SELECT id, title, content, image, publish_date
    FROM news
    WHERE is_published = 1
    ORDER BY publish_date DESC
    LIMIT :limit OFFSET :offset
");
$newsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$newsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$newsStmt->execute();
$news = $newsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    /* DARKISH THEME - FULLY CONSISTENT */
    html, body {
        background-color: #1e272e !important;
        color: #e0e0e0;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    .about-page-wrapper {
        margin-top: -50px;
        padding-top: 20px;
    }

    /* RED HEADER FOR NEWS PAGE */
    .news-header {
        position: relative;
        background: linear-gradient(135deg, #c0392b, #e74c3c) !important;
        color: white;
        padding: 1.6rem 1.8rem;
        font-size: 1.7rem;
        font-weight: 800;
        text-align: center;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
    }
    .news-header::after {
        content: '';
        position: absolute;
        left: 0; right: 0; bottom: 0;
        height: 4px;
        background: linear-gradient(90deg, #c82333, #e74c3c, #c82333);
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .about-card {
        background: #2c3e50;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.4);
        border: 1px solid #444;
    }

    /* News Grid */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.8rem;
        padding: 2rem;
    }

    .news-card {
        background: #34495e;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        transition: all 0.35s ease;
        border: 1px solid #444;
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 12px;
    }
    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 36px rgba(0,0,0,0.6);
    }

    .news-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 200px;
        border-radius: 12px 12px 0 0;
    }
    .news-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .news-card:hover .news-img {
        transform: scale(1.1);
    }
    .news-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent 40%);
        pointer-events: none;
    }

    .news-card-body {
        padding: 1.4rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .news-title {
        font-size: 1.18rem;
        font-weight: 700;
        color: #ecf0f1;
        margin-bottom: 0.8rem;
        line-height: 1.35;
    }
    .news-excerpt {
        color: #bdc3c7;
        font-size: 0.93rem;
        line-height: 1.6;
        flex-grow: 1;
        margin-bottom: 1rem;
    }
    .news-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px dashed #555;
    }
    .news-date {
        font-size: 0.85rem;
        color: #95a5a6;
    }
    .read-more-btn {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        color: white;
        font-size: 0.84rem;
        font-weight: 600;
        padding: 0.55rem 1.4rem;
        border-radius: 2rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .read-more-btn:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220,53,69,0.5);
        color: white;
    }

    .no-news {
        text-align: center;
        padding: 5rem 2rem;
        color: #bdc3c7;
        grid-column: 1 / -1;
    }
    .no-news i {
        font-size: 4.5rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .pagination-wrapper {
        padding: 2rem;
        background: #2c3e50;
    }
    .pagination .page-link {
        background: #34495e;
        border: 1px solid #555;
        color: #ecf0f1;
        border-radius: 0.5rem;
        padding: 0.6rem 1rem;
        font-weight: 500;
    }
    .pagination .page-link:hover {
        background: #3d566e;
        color: white;
    }
    .pagination .page-item.active .page-link {
        background: #e74c3c;
        border-color: #e74c3c;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        background: #2c3e50;
        color: #777;
    }

    /* Responsive */
    @media (min-width: 1400px) { .news-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 1199px) { .news-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 992px) { .news-grid { grid-template-columns: repeat(2, 1fr); padding: 1.8rem; } }
    @media (max-width: 576px) {
        .news-grid { grid-template-columns: 1fr; padding: 1.4rem; gap: 1.4rem; }
        .news-card-body { padding: 1.2rem; }
        .news-title { font-size: 1.1rem; }
        .news-header { font-size: 1.5rem; padding: 1.4rem 1rem; }
    }
</style>

<div class="main-content">
    <div class="container about-page-wrapper">
        <div class="about-card">
            <!-- RED HEADER -->
            <div class="news-header">Latest News</div>

            <!-- NEWS GRID -->
            <div class="news-grid">
                <?php if (empty($news)): ?>
                    <div class="no-news">
                        <i class="bi bi-file-earmark-text"></i>
                        <h4 class="mt-3">No news published yet</h4>
                        <p>Check back soon for updates!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($news as $n): ?>
                        <article class="news-card">
                            <div class="news-img-wrapper">
                                <?php if ($n['image']): ?>
                                    <img src="uploads/news/<?= htmlspecialchars($n['image']) ?>"
                                         class="news-img"
                                         alt="<?= htmlspecialchars($n['title']) ?>"
                                         onerror="this.src='https://via.placeholder.com/400x250/2c3e50/ffffff?text=News'">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-muted" style="height:200px;">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="news-overlay"></div>
                            </div>
                            <div class="news-card-body">
                                <h3 class="news-title"><?= htmlspecialchars($n['title']) ?></h3>
                                <p class="news-excerpt">
                                    <?php
                                    $plain = strip_tags($n['content']);
                                    echo strlen($plain) > 140
                                        ? htmlspecialchars(substr($plain, 0, 140)) . '...'
                                        : htmlspecialchars($plain);
                                    ?>
                                </p>
                                <div class="news-footer">
                                    <span class="news-date">
                                        <?= date('M j, Y', strtotime($n['publish_date'])) ?>
                                    </span>
                                    <a href="news_article.php?id=<?= $n['id'] ?>" class="read-more-btn">Read More</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <?php if ($pages > 1): ?>
                <div class="pagination-wrapper">
                    <nav aria-label="News pagination">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
                            </li>
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($pages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page == $pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php include 'includes/footer.php'; ob_end_flush(); ?>