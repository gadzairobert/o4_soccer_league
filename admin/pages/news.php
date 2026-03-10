<?php
// admin/pages/news.php
define('IN_DASHBOARD', true);
$newsDir = '../uploads/news/';
if (!is_dir($newsDir)) mkdir($newsDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__.'/../actions/news.php';
}

$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_data = [];
if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold text-dark">News & Updates</h2>
    <button class="btn btn-success shadow-sm px-4 btn-lg" data-bs-toggle="modal" data-bs-target="#newsModal">
        <i class="bi bi-plus-lg me-2"></i>Add News
    </button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($news_list)): ?>
    <div class="text-center py-5">
        <div class="bg-light rounded-4 d-inline-block p-5 shadow-sm">
            <h4 class="text-muted mb-3">No news published yet</h4>
            <p class="text-muted">Click "Add News" to share the latest updates!</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($news_list as $news): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm hover-shadow transition-all news-card h-100">
                    <div class="row g-0 align-items-center">
                        <!-- Image -->
                        <div class="col-lg-3">
                            <div class="news-img-wrapper">
                                <?php if ($news['image']): ?>
                                    <img src="../uploads/news/<?= htmlspecialchars($news['image']) ?>" 
                                         class="img-fluid rounded-start w-100 h-100" 
                                         style="object-fit: cover;" 
                                         alt="<?= htmlspecialchars($news['title']) ?>">
                                <?php else: ?>
                                    <div class="bg-secondary d-flex align-items-center justify-content-center rounded-start text-white h-100">
                                        <h3 class="mb-0 opacity-75">NEWS</h3>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="col-lg-9">
                            <div class="card-body py-4 px-5">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="card-title mb-2 fw-bold text-dark">
                                            <?= htmlspecialchars($news['title']) ?>
                                        </h4>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <?= date('F j, Y \a\t g:i A', strtotime($news['created_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge <?= $news['is_published'] ? 'bg-success' : 'bg-secondary' ?> fs-6 px-3 py-2">
                                            <?= $news['is_published'] ? 'Published' : 'Draft' ?>
                                        </span>
                                    </div>
                                </div>

                                <p class="text-muted mb-4" style="line-height: 1.7;">
                                    <?= nl2br(htmlspecialchars(substr(strip_tags($news['content']), 0, 280))) ?>
                                    <?= strlen(strip_tags($news['content'])) > 280 ? '...' : '' ?>
                                </p>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="?page=news&edit_id=<?= $news['id'] ?>" 
                                       class="btn btn-warning btn-sm px-4">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('Permanently delete this news article?');">
                                        <input type="hidden" name="action" value="delete_news">
                                        <input type="hidden" name="id" value="<?= $news['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm px-4">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL — Add/Edit News -->
<div class="modal fade" id="newsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?= $edit_id ? 'edit_news' : 'add_news' ?>">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id" value="<?= $edit_id ?>">
                    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_data['image'] ?? '') ?>">
                <?php endif; ?>

                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold fs-4">
                        <?= $edit_id ? 'Edit News Article' : 'Add New Article' ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-5">
                    <div class="row g-5">
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">Article Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" 
                                       placeholder="Enter a catchy headline..." required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">Full Content <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control" rows="16" 
                                          placeholder="Write your full article here..." required><?= htmlspecialchars($edit_data['content'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="text-center">
                                <label class="form-label fw-bold d-block mb-4 fs-5">Featured Image</label>

                                <?php if (!empty($edit_data['image'])): ?>
                                    <div class="mb-4 position-relative">
                                        <img src="../uploads/news/<?= htmlspecialchars($edit_data['image']) ?>" 
                                             class="img-fluid rounded shadow-lg" 
                                             style="max-height:280px; object-fit:cover;">
                                        <p class="text-success small mt-3 fw-bold">Current Image</p>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-4 border-dashed" 
                                         style="height:280px;">
                                        <div class="text-center">
                                            <i class="bi bi-image fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No image selected</p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-3">
                                    Recommended: 1200×800px • JPG/PNG • Max 5MB
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer with Publish Status -->
                <div class="modal-footer bg-light border-0 px-5 py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <label class="form-label fw-bold mb-2 d-block">Publish Status:</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="is_published" id="publish_yes" value="1" 
                                   <?= (!isset($edit_data['is_published']) || $edit_data['is_published'] == 1) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-success fw-bold" for="publish_yes">
                                Published
                            </label>

                            <input type="radio" class="btn-check" name="is_published" id="publish_no" value="0"
                                   <?= (isset($edit_data['is_published']) && $edit_data['is_published'] == 0) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary fw-bold" for="publish_no">
                                Draft
                            </label>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary px-5 me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <?= $edit_id ? 'Update Article' : 'Save Article' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto-open modal on edit -->
<?php if ($edit_id): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('newsModal')).show();
    });
</script>
<?php endif; ?>

<!-- Close modal after success/error -->
<?php if ($success || $error): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('newsModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
</script>
<?php endif; ?>

<!-- Styles -->
<style>
    .news-card {
        border-radius: 1rem !important;
        overflow: hidden;
        transition: all 0.4s ease;
    }
    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
    }
    .news-img-wrapper {
        height: 240px;
        overflow: hidden;
    }
    .news-img-wrapper img {
        transition: transform 0.6s ease;
    }
    .news-card:hover .news-img-wrapper img {
        transform: scale(1.08);
    }
    .border-dashed {
        border: 3px dashed #dee2e6 !important;
    }
    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;
    }
    @media (max-width: 992px) {
        .news-img-wrapper { height: 200px; }
    }
</style>