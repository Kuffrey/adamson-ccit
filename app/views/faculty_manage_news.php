<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login');
    exit;
}

// Check if user is logged in and get faculty ID
$faculty_id = $_SESSION['user']['id'] ?? null;
$faculty_username = $_SESSION['user']['username'] ?? 'Faculty';
if (!$faculty_id) {
    header('Location: ?page=login');
    exit;
}

// Get faculty's information from database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$faculty_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($faculty) {
        $firstName = $faculty['first_name'] ?? 'Faculty';
        $lastName = $faculty['last_name'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
    } else {
        $firstName = "Faculty";
        $lastName = "";
        $fullName = $faculty_username;
    }
} catch (PDOException $e) {
    error_log("Error getting faculty info: " . $e->getMessage());
    $firstName = "Faculty";
    $lastName = "";
    $fullName = $faculty_username;
}

if (isset($_POST['add_news'])) {
    try {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? 'news';
        
        if ($title === '')   throw new Exception("Article title is required and cannot be empty");
        if ($content === '') throw new Exception("Article content is required and cannot be empty");
        if (empty($faculty_id)) throw new Exception("Faculty ID is missing from session");
        
        require_once __DIR__ . '/../models/FacultySubmissions.php';
        
        $submissionData = [
            'faculty_id' => (int)$faculty_id,
            'submission_type' => 'news',
            'title' => $title,
            'description' => $content,
            'content' => $content,
            'category' => $category,
            'status' => 'submitted'
        ];
        
        $submissionId = FacultySubmissions::create($submissionData);
        
        if ($submissionId && $submissionId > 0) {
            $success_message = "News submitted for dean approval successfully! (ID: $submissionId)";
        } else {
            $error_message = "Failed to create submission record. Please contact the system administrator.";
        }
    } catch (Exception $e) {
        $error_message = "Submission failed: " . $e->getMessage();
        error_log("EXCEPTION in news submission: " . $e->getMessage());
    }
    
    // Redirect to prevent form resubmission
    $redirect_url = "?page=faculty_manage_news";
    if (isset($success_message)) {
        $redirect_url .= "&success=" . urlencode($success_message);
    } elseif (isset($error_message)) {
        $redirect_url .= "&error=" . urlencode($error_message);
    }
    header('Location: ' . $redirect_url);
    exit;
}

// Handle redirect messages
if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

// Get faculty's submitted news
try {
    $submittedNews = [];
    require_once __DIR__ . '/../models/FacultySubmissions.php';
    $submittedNews = FacultySubmissions::getByFacultyAndType($faculty_id, 'news');
} catch (Exception $e) {
    $submittedNews = [];
    error_log("Error fetching news submissions: " . $e->getMessage());
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>News Submission | Faculty Dashboard</title>

  <!-- Vendor CSS (icons + modal behavior) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* ===== Layout glue to align with inline-styled sidebar ===== */
    html, body { height: 100%; margin: 0; }
    body { background: #f5f7fb; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; }

    /* Put sidebar and main next to each other */
    .admin-cms-layout {
      display: flex;
      min-height: 100vh;
      align-items: stretch;
      width: 100%;
    }

    /* Main area fills remaining space */
    .admin-main {
      flex: 1 1 auto;
      min-width: 0; /* prevent overflow when table is wide */
      display: flex;
      flex-direction: column;
      background: #fafbfc;
    }

    /* Topbar that complements your sidebar palette */
    .admin-topbar {
      position: sticky;
      top: 0;
      z-index: 800;
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.9rem 1.25rem;
      background: #ffffff;
      border-bottom: 1px solid #e5e7eb;
    }
    .admin-topbar__title { font-weight: 700; font-size: 1rem; color:#111827; }
    .admin-topbar__spacer { flex: 1; }
    .admin-topbar__user { display:flex; align-items:center; gap:.6rem; }
    .admin-topbar__avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background:#008040; color:#fff; display:flex; align-items:center; justify-content:center;
      font-size:.9rem; font-weight:700;
    }
    .admin-topbar__name { font-weight:600; font-size:.95rem; color:#111827; }

    /* ===== Content area (kept your existing look, just tightened) ===== */
    .admin-cms-section { padding: 1.5rem; }

    .page-header {
      display:flex; align-items:center; justify-content:space-between;
      margin-bottom:1.25rem; padding-bottom:0.75rem; border-bottom:1px solid #e5e7eb;
    }
    .page-title { margin:0; font-size:1.5rem; font-weight:700; color:#111827; display:flex; gap:.6rem; align-items:center; }

    .btn-primary {
      display:inline-flex; align-items:center; gap:.5rem; cursor:pointer;
      background:#008040; color:#fff; border:0; border-radius:8px; padding:.65rem 1.1rem;
      font-size:.9rem; font-weight:600; transition:.2s;
    }
    .btn-primary:hover { background:#006d37; transform: translateY(-1px); box-shadow:0 4px 12px rgba(0,128,64,.12); }

    .btn-secondary {
      display:inline-flex; align-items:center; gap:.5rem; cursor:pointer;
      background:#f3f4f6; color:#374151; border:1px solid #d1d5db; border-radius:8px; padding:.6rem 1rem;
      font-size:.9rem; font-weight:500; transition:.2s;
    }
    .btn-secondary:hover { background:#e5e7eb; }

    .content-card {
      background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;
      box-shadow:0 1px 3px rgba(0,0,0,.05);
    }
    .card-header { padding:1rem 1.25rem; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
    .card-title { margin:0; display:flex; align-items:center; gap:.6rem; font-weight:700; color:#111827; font-size:1.05rem; }

    .empty-state { text-align:center; padding:3rem 1rem; color:#6b7280; }
    .empty-state i { font-size:2.25rem; color:#d1d5db; margin-bottom:.6rem; }
    .empty-state h4 { margin:0 0 .25rem; font-size:1.05rem; font-weight:700; color:#374151; }

    .table-container { overflow-x:auto; }
    .data-table { width:100%; min-width: 1000px; border-collapse: collapse; }
    .data-table th {
      background:#f9fafb; padding:0.85rem 0.75rem; text-align:left;
      font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
      color:#374151; border-bottom:1px solid #e5e7eb; white-space:nowrap;
    }
    .data-table td { padding:0.9rem 0.75rem; border-bottom:1px solid #f3f4f6; vertical-align:top; }
    .data-table tbody tr:hover { background:#f9fafb; }

    .item-title { font-weight:700; color:#111827; margin-bottom:.2rem; }
    .item-preview { font-size:.9rem; color:#6b7280; line-height:1.45; }
    .category-tag {
      display:inline-flex; align-items:center; padding:.25rem .6rem; background:#f3f4f6; color:#374151;
      border-radius:999px; font-size:.75rem; font-weight:600;
    }
    .status-badge {
      display:inline-flex; align-items:center; padding:.25rem .6rem; border-radius:999px;
      font-size:.75rem; font-weight:700; text-transform:capitalize;
    }
    .status-draft { background:#f3f4f6; color:#6b7280; }
    .status-submitted { background:#fef3c7; color:#b45309; }
    .status-approved { background:#d1fae5; color:#059669; }
    .status-rejected { background:#fee2e2; color:#dc2626; }

    .date-text { font-size:.9rem; color:#374151; font-weight:600; }
    .date-sub { font-size:.75rem; color:#6b7280; margin-top:2px; }

    .review-notes-full { font-size:.9rem; color:#374151; line-height:1.45; max-height:60px; overflow:auto; word-wrap:break-word; }
    .no-notes { font-size:.9rem; color:#9ca3af; font-style:italic; }

    /* Modal polish */
    .modal-content { border:none; border-radius:12px; box-shadow:0 20px 25px -5px rgba(0,0,0,.1); }
    .modal-header { padding:1rem 1.25rem; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
    .modal-body { padding:1.25rem; }
    .modal-footer { padding:1rem 1.25rem; background:#f9fafb; border-top:1px solid #e5e7eb; gap:.6rem; }

    .form-grid { display:grid; gap:1rem; }
    @media (min-width:640px){ .form-grid { grid-template-columns: repeat(2, 1fr); } .form-grid .form-group:first-child, .form-grid .form-group:nth-child(4){ grid-column: span 2; } }
    .form-label { margin-bottom:.4rem; font-size:.9rem; font-weight:600; color:#374151; }
    .optional { color:#9ca3af; font-weight:400; }
    .form-control, .form-select {
      padding:.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:.9rem; background:#fff; transition:border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus { outline:none; border-color:#008040; box-shadow: 0 0 0 3px rgba(0,128,64,.12); }

    .submission-note {
      display:flex; align-items:center; gap:.5rem; padding:0.8rem 0.9rem;
      background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; font-size:.9rem; color:#1e40af;
    }

    .alert { border-radius:10px; padding: .9rem 1rem; }
    .alert-success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .alert-danger  { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

    /* Mobile spacing */
    @media (max-width: 768px){
      .admin-cms-section { padding: 1rem; }
      .page-header { flex-direction:column; align-items:stretch; gap:.75rem; }
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">News Submission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper(($firstName[0] ?? 'F') . ($lastName[0] ?? ''))) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= esc($success_message) ?></div>
      <?php endif; ?>
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= esc($error_message) ?></div>
      <?php endif; ?>
      
      <!-- Page Header with Add Button -->
      <div class="page-header">
        <h1 class="page-title">
          <i class="fas fa-newspaper"></i>
          <span>News Management</span>
        </h1>
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
          <i class="fas fa-plus"></i>
          <span>Add News</span>
        </button>
      </div>

      <!-- Submissions Table -->
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-list"></i>
            <span>Your Submissions (<?= count($submittedNews) ?>)</span>
          </h3>
        </div>
        
        <?php if (empty($submittedNews)): ?>
          <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h4>No submissions yet</h4>
            <p>Start by submitting your first news article</p>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:45%;">Title &amp; Content</th>
                  <th style="width:8%;">Category</th>
                  <th style="width:8%;">Status</th>
                  <th style="width:12%;">Date</th>
                  <th style="width:27%;">Review Notes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($submittedNews as $item): ?>
                  <tr>
                    <td>
                      <div class="item-title"><?= esc($item['title']) ?></div>
                      <div class="item-preview"><?= esc(mb_substr($item['content'], 0, 150)) ?>...</div>
                    </td>
                    <td><span class="category-tag"><?= esc($item['category'] ?? 'News') ?></span></td>
                    <td>
                      <span class="status-badge status-<?= esc($item['status']) ?>">
                        <?= esc(ucfirst($item['status'])) ?>
                      </span>
                    </td>
                    <td>
                      <div class="date-text">
                        <?= $item['submitted_at'] ? date('M j, Y', strtotime($item['submitted_at'])) : '-' ?>
                        <?php if (!empty($item['submitted_at'])): ?>
                          <div class="date-sub"><?= date('g:i A', strtotime($item['submitted_at'])) ?></div>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <?php if (!empty($item['review_notes'])): ?>
                        <div class="review-notes-full"><?= esc($item['review_notes']) ?></div>
                      <?php else: ?>
                        <span class="no-notes">No review notes yet</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Add News Modal -->
      <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addNewsModalLabel">Submit News Article</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?page=faculty_manage_news" enctype="multipart/form-data">
              <div class="modal-body">
                <input type="hidden" name="add_news" value="1">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required maxlength="255" placeholder="Enter article title">
                    <small class="form-text text-muted">Maximum 255 characters</small>
                  </div>
                  
                  <div class="form-group form-group-half">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category" required>
                      <option value="news">General News</option>
                      <option value="research">Research</option>
                      <option value="achievement">Achievement</option>
                      <option value="student">Student News</option>
                    </select>
                  </div>
                  
                  <div class="form-group form-group-half">
                    <label for="image" class="form-label">Image <span class="optional">(optional)</span></label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                  </div>
                  
                  <div class="form-group">
                    <label for="content" class="form-label">Article Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="8" required minlength="10" placeholder="Write your article content here..."></textarea>
                    <small class="form-text text-muted">Minimum 10 characters required</small>
                  </div>
                  
                  <div class="submission-note">
                    <i class="fas fa-info-circle"></i>
                    <span>Your article will be reviewed by the dean before publication</span>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-primary" id="submitBtn">
                  <i class="fas fa-paper-plane"></i>
                  <span>Submit Article</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('#addNewsModal form');
  const titleInput = document.querySelector('#title');
  const contentInput = document.querySelector('#content');
  const submitBtn = document.querySelector('#submitBtn');
  
  if (form && titleInput && contentInput && submitBtn) {
    form.addEventListener('submit', function(e) {
      const title = (titleInput.value || '').trim();
      const content = (contentInput.value || '').trim();
      
      if (!title) {
        e.preventDefault();
        alert('Please enter an article title');
        titleInput.focus();
        return false;
      }
      
      if (content.length < 10) {
        e.preventDefault();
        alert('Please enter article content (minimum 10 characters)');
        contentInput.focus();
        return false;
      }
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    });
  }
});
</script>
</body>
</html>
