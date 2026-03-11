<?php
ob_start();
require 'config.php';
include 'includes/header.php';
include 'includes/gif_slideshow.php';
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
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --gold:        #c9a84c;
        --gold-light:  #f0d080;
        --gold-dark:   #9a6f1e;
        --cream:       #fdf8ef;
        --dark-panel:  #1a1a2e;
        --dark-tab:    #16152b;
        --dark-deeper: #0f0e22;
        --border:      rgba(201,168,76,0.22);
        --muted:       #6b7280;
        --text-main:   #1a1a2e;
        --text-soft:   #4b5563;
    }

    /* ── PAGE BACKGROUND: LIGHT ── */
    html, body {
        background-color: #f0ede8 !important;
        background-image:
            radial-gradient(ellipse at 20% 10%, rgba(201,168,76,0.07) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 90%, rgba(180,160,120,0.05) 0%, transparent 50%);
        background-attachment: fixed;
        color: var(--text-main);
        overflow-x: hidden;
    }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    .news-page-wrapper {
        max-width: 100%;
        margin: -38px auto 0;
        padding: 6px 1.5rem 4rem;
    }
    @media (max-width: 767px) {
        .news-page-wrapper {
            margin-top: 0;
            padding: 1rem 0 3rem;
            width: 100%;
        }
    }

    /* ── Outer Card — LIGHT ── */
    .news-outer-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }
    @media (max-width: 767px) {
        .news-outer-card {
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
    }

    /* ── Page Header — DARK ── */
    .news-page-header {
        background: linear-gradient(135deg, var(--dark-tab), #24224a);
        border-bottom: 2px solid var(--gold);
        padding: 1rem 1.6rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .news-page-header .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--cream);
        margin: 0;
    }
    .news-page-header .article-count {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--gold);
        background: rgba(201,168,76,0.12);
        border: 1px solid rgba(201,168,76,0.3);
        padding: 0.25rem 0.85rem;
        border-radius: 20px;
        white-space: nowrap;
    }

    /* ── News Grid ── */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.8rem;
        padding: 2rem;
    }
    @media (min-width: 1400px) { .news-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 1199px) { .news-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 992px)  { .news-grid { grid-template-columns: repeat(2, 1fr); padding: 1.5rem; } }
    @media (max-width: 576px)  { .news-grid { grid-template-columns: 1fr; padding: 1.2rem; gap: 1.2rem; } }

    /* ── News Card — LIGHT ── */
    .news-card {
        background: #f9f7f2;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .news-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(0,0,0,0.12);
        border-color: rgba(201,168,76,0.45);
    }

    /* ── Card Image ── */
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
    .news-card:hover .news-img { transform: scale(1.08); }
    .news-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.5), transparent 45%);
        pointer-events: none;
    }
    .news-img-placeholder {
        height: 200px;
        background: #f0ede8;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #e5e7eb;
    }
    .news-img-placeholder i { font-size: 2.5rem; color: rgba(154,111,30,0.3); }

    /* ── Card Body — LIGHT ── */
    .news-card-body {
        padding: 1.3rem 1.4rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        background: #f9f7f2;
    }
    .news-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.08rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.75rem;
        line-height: 1.35;
    }
    .news-excerpt {
        font-family: 'DM Sans', sans-serif;
        color: var(--text-soft);
        font-size: 0.9rem;
        line-height: 1.65;
        flex-grow: 1;
        margin-bottom: 1rem;
    }

    /* ── Card Footer — LIGHT ── */
    .news-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 0.9rem;
        border-top: 1px solid #e5e7eb;
    }
    .news-date {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gold-dark);
        letter-spacing: 0.5px;
    }
    .read-more-btn {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--gold-dark);
        background: rgba(201,168,76,0.1);
        border: 1px solid rgba(201,168,76,0.3);
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.25s ease;
        white-space: nowrap;
    }
    .read-more-btn:hover {
        background: rgba(201,168,76,0.2);
        border-color: var(--gold);
        color: var(--gold-dark);
        transform: translateY(-1px);
    }

    /* ── Empty State ── */
    .no-news {
        text-align: center;
        padding: 5rem 2rem;
        font-family: 'DM Sans', sans-serif;
        grid-column: 1 / -1;
    }
    .no-news i {
        font-size: 3.5rem;
        color: rgba(154,111,30,0.3);
        display: block;
        margin-bottom: 1rem;
    }
    .no-news h4 {
        font-family: 'Playfair Display', serif;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }
    .no-news p { color: var(--muted); margin: 0; }

    /* ── Pagination — DARK footer ── */
    .pagination-wrapper {
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, var(--dark-deeper), var(--dark-tab));
        border-top: 1px solid rgba(201,168,76,0.15);
    }
    .pagination .page-link {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(201,168,76,0.25);
        color: var(--cream);
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 6px !important;
        padding: 0.5rem 0.95rem;
        margin: 0 2px;
        transition: all 0.2s ease;
    }
    .pagination .page-link:hover {
        background: rgba(201,168,76,0.15);
        border-color: rgba(201,168,76,0.5);
        color: var(--gold-light);
    }
    .pagination .page-item.active .page-link {
        background: rgba(201,168,76,0.2);
        border-color: var(--gold);
        color: var(--gold);
    }
    .pagination .page-item.disabled .page-link {
        background: transparent;
        color: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.08);
    }
</style>

<div class="main-content">
    <div class="news-page-wrapper">
        <div class="news-outer-card">

            <!-- ── Header ── -->
            <div class="news-page-header">
                <span class="page-title">Latest News</span>
                <?php if (!empty($news)): ?>
                    <span class="article-count"><?= $total ?> Article<?= $total !== 1 ? 's' : '' ?></span>
                <?php endif; ?>
            </div>

            <!-- ── Grid ── -->
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
                                         onerror="this.src='https://via.placeholder.com/400x250/f0ede8/9a6f1e?text=News'">
                                    <div class="news-overlay"></div>
                                <?php else: ?>
                                    <div class="news-img-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="news-card-body">
                                <h3 class="news-title"><?= htmlspecialchars($n['title']) ?></h3>
                                <p class="news-excerpt">
                                    <?php
                                    $plain = strip_tags($n['content']);
                                    echo strlen($plain) > 140
                                        ? htmlspecialchars(substr($plain, 0, 140)) . '…'
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

            <!-- ── Pagination ── -->
            <?php if ($pages > 1): ?>
                <div class="pagination-wrapper">
                    <nav aria-label="News pagination">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">← Prev</a>
                            </li>
                            <?php
                            $start = max(1, $page - 2);
                            $end   = min($pages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page == $pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Next →</a>
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