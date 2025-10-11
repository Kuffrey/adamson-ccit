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
require_once __DIR__ . '/../../models/FacultySubmissions.php';

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

    // Get pending faculty news submissions
    $pendingNewsSubmissions = FacultySubmissions::getPendingByType('news');
} catch (Exception $e) {
    $notice = 'Error loading data: ' . $e->getMessage();
    $news = [];
    $counts = ['all' => 0, 'draft' => 0, 'published' => 0, 'archived' => 0];
    $settings = [];
    $pendingNewsSubmissions = [];
}

/* ---------- Handle POST (CRUD) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle faculty submission approval/rejection
        if (!empty($_POST['faculty_action']) && !empty($_POST['submission_id'])) {
            $submissionId = (int)$_POST['submission_id'];
            $action = $_POST['faculty_action'];
            $reviewNotes = trim($_POST['review_notes'] ?? '');

            if ($action === 'approve') {
                FacultySubmissions::updateStatus($submissionId, 'approved', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty news submission approved and published successfully!';
                DeanLogs::logApprove('faculty_submissions', $submissionId, $user['id'] ?? null, "Approved news submission: " . substr($reviewNotes, 0, 100));
            } elseif ($action === 'reject') {
                FacultySubmissions::updateStatus($submissionId, 'rejected', $user['id'] ?? null, $reviewNotes);
                $notice = 'Faculty news submission rejected.';
                DeanLogs::logReject('faculty_submissions', $submissionId, $user['id'] ?? null, "Rejected news submission: " . substr($reviewNotes, 0, 100));
            }
        }

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
            DeanLogs::logCreate('news', $newsId, $user['id'] ?? null, "Created news article: {$title}");

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
            DeanLogs::logUpdate($user['id'] ?? null, 'news', $id, "Updated news article: {$title}");

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
            DeanLogs::logDelete($user['id'] ?? null, 'news', $id, "Deleted news article: {$newsTitle}");

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage News | CCIT Dean</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="admin-layout">
    <?php include __DIR__ . '/_dean_sidebar.php'; ?>
    <main class="admin-main">
      <header class="admin-topbar">
        <button class="topbar__btn hide-desktop" type="button" aria-label="Open navigation menu" data-sb-open>
          <i class="fas fa-bars"></i>
        </button>
        <span class="admin-topbar__title">CCIT News Management</span>
        <span class="admin-topbar__spacer"></span>
      </header>

      <section class="admin-section">
        <?php if ($notice): ?>
          <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show modern-alert" role="alert">
            <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
            <?= esc($notice) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Filter Tabs -->
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
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

              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                <i class="fas fa-plus me-2"></i>Add News
              </button>
            </div>
          </div>
        </div>

        <!-- Faculty News Submissions for Approval -->
        <?php if (!empty($pendingNewsSubmissions)): ?>
          <div class="card mb-4 border-warning">
            <div class="card-header bg-warning bg-opacity-10">
              <h5 class="card-title mb-0">
                <i class="fas fa-clock text-warning me-2"></i>
                Pending Faculty News Submissions (<?= count($pendingNewsSubmissions) ?>)
              </h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Faculty</th>
                      <th>News Title</th>
                      <th>Category</th>
                      <th>Submitted</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($pendingNewsSubmissions as $submission): ?>
                    <?php
                      $sid   = (int)$submission['id'];
                      $sfac  = esc($submission['faculty_name'] ?? 'Unknown Faculty');
                      $sdept = esc($submission['department_name'] ?? '');
                      $stitle= esc($submission['title']);
                      $scat  = esc($submission['category'] ?? 'News');
                      $ssub  = date('M j, Y g:i A', strtotime($submission['submitted_at']));
                    ?>
                    <tr>
                      <td>
                        <strong><?= $sfac ?></strong><br>
                        <small class="text-muted"><?= $sdept ?></small>
                      </td>
                      <td><strong><?= $stitle ?></strong></td>
                      <td><span class="badge badge--category"><?= $scat ?></span></td>
                      <td><small><?= $ssub ?></small></td>
                      <td>
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-success btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#approveModal<?= $sid ?>">
                            <i class="fas fa-check"></i> Approve
                          </button>
                          <button type="button" class="btn btn-danger btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#rejectModal<?= $sid ?>">
                            <i class="fas fa-times"></i> Reject
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- News List -->
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="fas fa-newspaper me-2"></i>News Articles (<?= count($news) ?>)
            </h5>
          </div>
          <div class="card-body">
          <?php if (empty($news)): ?>
            <div class="empty-state-card">
              <i class="fas fa-newspaper fa-3x"></i>
              <h6>No News Articles Found</h6>
              <div class="text-muted">Start by adding a news article using the <strong>Add News</strong> button above.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
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
                  <?php
                    $aid      = (int)$article['id'];
                    $atitle   = esc($article['title'] ?? '');
                    $acat     = esc($article['category'] ?? 'Uncategorized');
                    $astatus  = esc($article['status'] ?? 'draft');
                    $aauthor  = esc($article['author'] ?? 'N/A');
                    $acreated = date('M j, Y', strtotime($article['created_at']));
                  ?>
                  <tr>
                    <td><strong><?= $atitle ?></strong></td>
                    <td><span class="badge badge--category"><?= $acat ?></span></td>
                    <td><span class="badge badge--status badge--<?= $astatus ?>"><?= $astatus ?></span></td>
                    <td><?= $aauthor ?></td>
                    <td><small><?= $acreated ?></small></td>
                    <td>
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#editModal<?= $aid ?>">
                          <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('Are you sure you want to delete this news article?');">
                          <input type="hidden" name="delete_news" value="1">
                          <input type="hidden" name="id" value="<?= $aid ?>">
                          <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
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

  <!-- =========================
       ALL MODALS RENDER HERE
       ========================= -->

  <!-- Add News Modal -->
  <div class="modal fade" id="addNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="add_news" value="1">
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add News Article</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
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
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add News Article
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Approve/Reject Modals -->
  <?php if (!empty($pendingNewsSubmissions)): ?>
    <?php foreach ($pendingNewsSubmissions as $submission): ?>
      <?php
        $sid   = (int)$submission['id'];
        $sfac  = esc($submission['faculty_name'] ?? 'Unknown Faculty');
        $stitle= esc($submission['title']);
      ?>
      <div class="modal fade" id="approveModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Approve News Submission</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
              <div class="modal-body">
                <input type="hidden" name="faculty_action" value="approve">
                <input type="hidden" name="submission_id" value="<?= $sid ?>">
                <p>Approve "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
                <p class="modal-desc approve-help"></p>
                <div class="mb-3">
                  <label class="form-label">Review Notes (Optional)</label>
                  <textarea class="form-control" name="review_notes" rows="3" placeholder="Add any feedback or notes..."></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Approve &amp; Publish</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="rejectModal<?= $sid ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Reject News Submission</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
              <div class="modal-body">
                <input type="hidden" name="faculty_action" value="reject">
                <input type="hidden" name="submission_id" value="<?= $sid ?>">
                <p>Reject "<strong><?= $stitle ?></strong>" by <?= $sfac ?>?</p>
                <div class="mb-3">
                  <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="review_notes" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Edit Modals -->
  <?php if (!empty($news)): ?>
    <?php foreach ($news as $article): ?>
      <?php
        $aid     = (int)$article['id'];
        $atitle  = esc($article['title'] ?? '');
        $acontent= esc($article['content'] ?? ($article['body'] ?? ''));
        $catSel  = $article['category'] ?? '';
        $statSel = $article['status'] ?? 'draft';
        $hasImg  = !empty($article['image_url']);
      ?>
      <div class="modal fade" id="editModal<?= $aid ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit News Article</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="edit_news" value="1">
              <input type="hidden" name="id" value="<?= $aid ?>">
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-8">
                    <div class="mb-3">
                      <label class="form-label">Title</label>
                      <input type="text" class="form-control" name="title" value="<?= $atitle ?>" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Content</label>
                      <textarea class="form-control" name="content" rows="6"><?= $acontent ?></textarea>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="mb-3">
                      <label class="form-label">Category</label>
                      <select class="form-control" name="category">
                        <option value="news" <?= $catSel === 'news' ? 'selected' : '' ?>>News</option>
                        <option value="announcement" <?= $catSel === 'announcement' ? 'selected' : '' ?>>Announcement</option>
                        <option value="event" <?= $catSel === 'event' ? 'selected' : '' ?>>Event</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <select class="form-control" name="status">
                        <option value="draft" <?= $statSel === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $statSel === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= $statSel === 'archived' ? 'selected' : '' ?>>Archived</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Update Image</label>
                      <input type="file" class="form-control" name="image" accept="image/*">
                      <?php if ($hasImg): ?>
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
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
