<?php
// app/views/dean/dean_manage_news.php - Dean News Management
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../lib/Auth.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../models/News.php';
require_once __DIR__ . '/../../models/DeanLogs.php';
require_once __DIR__ . '/../../models/NewsPageSettings.php';

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';
$notice = '';

// Get current data
try {
    $status = $_GET['status'] ?? 'all';
    $news = News::list($status);
    $counts = News::statusCounts();
    $settings = class_exists('NewsPageSettings') ? NewsPageSettings::getSettings() : [];
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $news = [];
    $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
    $settings = [];
}

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update news settings
        if (!empty($_POST['settings']) && class_exists('NewsPageSettings')) {
            NewsPageSettings::updateSettings($_POST['settings']);
            $notice = 'Settings updated successfully!';
        }

        // Add new news
        if (!empty($_POST['add_news']) && !empty($_POST['title'])) {
            $title = trim($_POST['title']);
            $content = trim($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'news';
            $nstatus = $_POST['status'] ?? 'draft';
            $imageUrl = null;

            // Handle image upload
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
                $newName = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
                $uploadPath = __DIR__ . '/../../../public/assets/images/news/' . $newName;

                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    $imageUrl = '/adamson-ccit/public/assets/images/news/' . $newName;
                }
            }

            $newsId = News::create($title, $content, $nstatus, $category, $imageUrl);
            
            // Log the news creation (essential CREATE operation)
            DeanLogs::logCreate(
                'news',
                $newsId,
                $user['id'] ?? null,
                "Created news article: {$title}"
            );
            
            $notice = 'News added successfully!';
        }

        // Edit existing news
        if (!empty($_POST['edit_news']) && !empty($_POST['id']) && !empty($_POST['title'])) {
            $id = (int)$_POST['id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'news';
            $nstatus = $_POST['status'] ?? 'draft';

            $updateData = [
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'status' => $nstatus
            ];

            // Handle image upload for edit
            if (!empty($_FILES['image']['tmp_name'])) {
                $imgTmp = $_FILES['image']['tmp_name'];
                $imgName = $_FILES['image']['name'];
                $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
                $newName = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
                $uploadPath = __DIR__ . '/../../../public/assets/images/news/' . $newName;

                if (move_uploaded_file($imgTmp, $uploadPath)) {
                    $updateData['image_url'] = '/adamson-ccit/public/assets/images/news/' . $newName;
                }
            }

            News::update($id, $updateData);
            
            // Log the news update (essential UPDATE operation)
            DeanLogs::logUpdate(
                $user['id'] ?? null,
                'news',
                $id,
                "Updated news article: {$title}"
            );
            
            $notice = 'News updated successfully!';
        }

        // Delete news
        if (!empty($_POST['delete_news']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            // Get news title before deletion for logging
            $newsItem = News::findById($id);
            $newsTitle = $newsItem['title'] ?? "ID: {$id}";
            
            News::delete($id);
            
            // Log the news deletion (essential DELETE operation)
            DeanLogs::logDelete(
                $user['id'] ?? null,
                'news',
                $id,
                "Deleted news article: {$newsTitle}"
            );
            
            $notice = 'News deleted successfully!';
        }

        // Refresh data after operations
        $news = News::list($status);
        $counts = News::statusCounts();

    } catch (Exception $e) {
        $notice = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-cms-layout">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-topbar">
                <span class="admin-topbar__title">News Management</span>
                <div class="admin-topbar__spacer"></div>
                <div class="admin-topbar__user">
                    <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'D')) ?></span>
                    <span class="admin-topbar__name"><?= esc($username) ?></span>
                </div>
            </header>

            <section class="admin-cms-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="admin-cms-section__title mb-1">News Management</h1>
                        <p class="text-muted mb-0">Create and manage news articles</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="badge bg-primary">Total: <?= $counts['all'] ?></div>
                        <div class="badge bg-success">Published: <?= $counts['published'] ?></div>
                        <div class="badge bg-warning">Draft: <?= $counts['draft'] ?></div>
                    </div>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Tabs -->
                <div class="card mb-4">
                    <div class="card-body">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" href="?page=dean_manage_news&status=all">
                                    All News (<?= $counts['all'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'published' ? 'active' : '' ?>" href="?page=dean_manage_news&status=published">
                                    Published (<?= $counts['published'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'draft' ? 'active' : '' ?>" href="?page=dean_manage_news&status=draft">
                                    Draft (<?= $counts['draft'] ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $status === 'archived' ? 'active' : '' ?>" href="?page=dean_manage_news&status=archived">
                                    Archived (<?= $counts['archived'] ?>)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Add New News Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New News Article</h5>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#addNewsCollapse" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="collapse" id="addNewsCollapse">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="add_news" value="1">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">News Title</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Content</label>
                                            <textarea class="form-control" id="content" name="content" rows="6"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-control" id="category" name="category">
                                                <option value="news">News</option>
                                                <option value="announcement">Announcement</option>
                                                <option value="event">Event</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="draft">Draft</option>
                                                <option value="published">Published</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Featured Image</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add News Article
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- News List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-newspaper me-2"></i>News Articles (<?= count($news) ?>)</h5>
                    </div>
                    
                    <div class="card-body">
                        <?php if (empty($news)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No news articles found.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Author</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($news as $article): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($article['image_url']): ?>
                                                            <img src="<?= esc($article['image_url']) ?>" alt="News Image" 
                                                                 class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= esc($article['title']) ?></strong>
                                                            <?php if (!empty($article['content'])): ?>
                                                                <br><small class="text-muted"><?= esc(substr($article['content'], 0, 100)) ?>...</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= esc($article['category']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $article['status'] === 'published' ? 'success' : ($article['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                                        <?= esc($article['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($article['author'] ?? 'N/A') ?></td>
                                                <td><small><?= date('M j, Y', strtotime($article['created_at'])) ?></small></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                data-bs-toggle="modal" data-bs-target="#editModal<?= $article['id'] ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;" 
                                                              onsubmit="return confirm('Are you sure you want to delete this news article?');">
                                                            <input type="hidden" name="delete_news" value="1">
                                                            <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?= $article['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit News Article</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="edit_news" value="1">
                                                            <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Title</label>
                                                                            <input type="text" class="form-control" name="title" 
                                                                                   value="<?= esc($article['title']) ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Content</label>
                                                                            <textarea class="form-control" name="content" rows="6"><?= esc($article['content']) ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Category</label>
                                                                            <select class="form-control" name="category">
                                                                                <option value="news" <?= $article['category'] === 'news' ? 'selected' : '' ?>>News</option>
                                                                                <option value="announcement" <?= $article['category'] === 'announcement' ? 'selected' : '' ?>>Announcement</option>
                                                                                <option value="event" <?= $article['category'] === 'event' ? 'selected' : '' ?>>Event</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-control" name="status">
                                                                                <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                                                                <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                                                                <option value="archived" <?= $article['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Update Image</label>
                                                                            <input type="file" class="form-control" name="image" accept="image/*">
                                                                            <?php if ($article['image_url']): ?>
                                                                                <small class="text-muted">Current image will be replaced if new one is uploaded</small>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update News</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>