<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login');
    exit;
}
require_once __DIR__ . '/../models/Announcement.php';

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
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$faculty_username]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($faculty) {
        $firstName = $faculty['first_name'];
        $lastName = $faculty['last_name'];
        $fullName = $firstName . ' ' . $lastName;
    } else {
        $firstName = "Faculty";
        $lastName = "";
        $fullName = $faculty_username;
    }
} catch (PDOException $e) {
    $firstName = "Faculty";
    $lastName = "";
    $fullName = $faculty_username;
}

$dept_id = $_SESSION['user']['department_id'] ?? 0;

if (isset($_POST['add_announcement'])) {
    try {
        $imageUrl = null;

        // Handle image upload
        if (!empty($_FILES['image']['tmp_name'])) {
            $imgTmp = $_FILES['image']['tmp_name'];
            $imgName = $_FILES['image']['name'];
            $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);
            $newName = 'announcement_' . time() . '_' . rand(1000, 9999) . '.' . $imgExt;
            $uploadPath = __DIR__ . '/../../public/assets/images/announcements/' . $newName;

            // Create directory if it doesn't exist
            $uploadDir = dirname($uploadPath);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($imgTmp, $uploadPath)) {
                $imageUrl = '/adamson-ccit/public/assets/images/announcements/' . $newName;
            }
        }

        // Create announcement with draft status
        $announcementId = Announcement::create(
            $_POST['title'],
            $_POST['content'],
            'draft',
            $_POST['category'] ?? 'general',
            $_POST['date'] ?? date('Y-m-d'),
            $imageUrl
        );
        
        // Automatically submit for dean approval
        if ($announcementId) {
            require_once __DIR__ . '/../models/FacultySubmissions.php';
            try {
                FacultySubmissions::createFromAnnouncement($announcementId, $faculty_id);
                $success_message = "Announcement submitted for dean approval successfully!";
            } catch (Exception $e) {
                error_log("Failed to create submission for announcement: " . $e->getMessage());
                $error_message = "Announcement created but failed to submit for approval.";
            }
        }
    } catch (Exception $e) {
        $error_message = "Failed to create announcement: " . $e->getMessage();
    }
    
    header('Location: ?page=faculty_manage_announcements');
    exit;
}

// Get faculty's submitted announcements
try {
    $submittedAnnouncements = [];
    require_once __DIR__ . '/../models/FacultySubmissions.php';
    $submissions = FacultySubmissions::getByFacultyId($faculty_id);
    foreach ($submissions as $submission) {
        if ($submission['submission_type'] === 'announcement') {
            $submittedAnnouncements[] = $submission;
        }
    }
} catch (Exception $e) {
    $submittedAnnouncements = [];
    error_log("Error fetching announcement submissions: " . $e->getMessage());
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Announcements | Faculty Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css" />
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #fafbfc;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .admin-cms-section {
      max-width: 100%;
      margin: 0;
      padding: 2rem 3rem;
    }
    
    /* Page Header */
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .page-title {
      margin: 0;
      font-size: 1.875rem;
      font-weight: 600;
      color: #111827;
    }
    
    /* Buttons */
    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #008040;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.2s;
      cursor: pointer;
    }
    
    .btn-primary:hover {
      background: #006d37;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 128, 64, 0.15);
    }
    
    .btn-secondary {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.2s;
      cursor: pointer;
    }
    
    .btn-secondary:hover {
      background: #e5e7eb;
      color: #111827;
    }
    
    /* Content Card */
    .content-card {
      background: white;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
      padding: 1.5rem;
      border-bottom: 1px solid #e5e7eb;
      background: #f9fafb;
    }
    
    .card-title {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin: 0;
      font-size: 1.125rem;
      font-weight: 600;
      color: #111827;
    }
    
    .card-title i {
      color: #008040;
      font-size: 1rem;
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #6b7280;
    }
    
    .empty-state i {
      font-size: 3rem;
      color: #d1d5db;
      margin-bottom: 1rem;
    }
    
    .empty-state h4 {
      margin: 0 0 0.5rem 0;
      font-size: 1.125rem;
      font-weight: 600;
      color: #374151;
    }
    
    .empty-state p {
      margin: 0;
      font-size: 0.875rem;
    }
    
    /* Table */
    .table-container {
      overflow-x: auto;
      margin: 0 -1rem;
      padding: 0 1rem;
    }
    
    .data-table {
      width: 100%;
      min-width: 1200px;
      border-collapse: collapse;
    }
    
    .data-table th {
      background: #f9fafb;
      padding: 1rem 0.75rem;
      text-align: left;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #374151;
      border-bottom: 1px solid #e5e7eb;
      white-space: nowrap;
    }
    
    .data-table td {
      padding: 1rem 0.75rem;
      border-bottom: 1px solid #f3f4f6;
      vertical-align: top;
    }
    
    .data-table tbody tr:hover {
      background: #f9fafb;
    }
    
    .item-title {
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.25rem;
    }
    
    .item-preview {
      font-size: 0.875rem;
      color: #6b7280;
      line-height: 1.4;
    }
    
    .category-tag {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      background: #f3f4f6;
      color: #374151;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    
    .status-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: capitalize;
    }
    
    .status-draft { background: #f3f4f6; color: #6b7280; }
    .status-submitted { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #059669; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
    
    .date-text {
      font-size: 0.875rem;
      color: #374151;
      font-weight: 500;
    }
    
    .review-notes-full {
      font-size: 0.875rem;
      color: #374151;
      line-height: 1.4;
      max-height: 60px;
      overflow-y: auto;
      word-wrap: break-word;
    }
    
    .no-notes {
      font-size: 0.875rem;
      color: #9ca3af;
      font-style: italic;
    }
    
    /* Modal */
    .modal-content {
      border: none;
      border-radius: 12px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
      padding: 1.5rem;
      border-bottom: 1px solid #e5e7eb;
      background: #f9fafb;
    }
    
    .modal-title {
      margin: 0;
      font-size: 1.125rem;
      font-weight: 600;
      color: #111827;
    }
    
    .modal-body {
      padding: 1.5rem;
    }
    
    .modal-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid #e5e7eb;
      background: #f9fafb;
      display: flex;
      gap: 0.75rem;
      justify-content: flex-end;
    }
    
    /* Form */
    .form-grid {
      display: grid;
      gap: 1.5rem;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
    }
    
    .form-group-half {
      grid-column: span 1;
    }
    
    .form-label {
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: #374151;
    }
    
    .optional {
      color: #9ca3af;
      font-weight: 400;
    }
    
    .form-control, .form-select {
      padding: 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.875rem;
      transition: all 0.2s;
      background: white;
    }
    
    .form-control:focus, .form-select:focus {
      outline: none;
      border-color: #008040;
      box-shadow: 0 0 0 3px rgba(0, 128, 64, 0.1);
    }
    
    .submission-note {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 1rem;
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 8px;
      font-size: 0.875rem;
      color: #1e40af;
    }
    
    .submission-note i {
      color: #3b82f6;
    }
    
    /* Alerts */
    .alert {
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
    }
    
    .alert-success {
      background: #d1fae5;
      color: #059669;
      border: 1px solid #a7f3d0;
    }
    
    .alert-danger {
      background: #fee2e2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .admin-cms-section {
        padding: 1rem;
      }
      
      .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .data-table {
        font-size: 0.875rem;
      }
      
      .data-table th,
      .data-table td {
        padding: 0.75rem 0.5rem;
      }
      
      .form-grid {
        gap: 1rem;
      }
    }
    
    @media (min-width: 640px) {
      .form-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .form-group:first-child,
      .form-group:nth-child(4),
      .form-group:nth-child(5) {
        grid-column: span 2;
      }
    }
  </style>
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Submit Announcements</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($firstName[0] . $lastName[0])) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= esc($success_message) ?></div>
      <?php endif; ?>
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= esc($error_message) ?></div>
      <?php endif; ?>
      
      <!-- Page Header with Add Button -->
      <div class="page-header">
        <h1 class="page-title">
          <i class="fas fa-bullhorn"></i>
          <span>Announcements Management</span>
        </h1>
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
          <i class="fas fa-plus"></i>
          <span>Add Announcement</span>
        </button>
      </div>

      
      <!-- Submissions Table -->
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-list"></i>
            <span>Your Submissions (<?= count($submittedAnnouncements) ?>)</span>
          </h3>
        </div>
        
        <?php if (empty($submittedAnnouncements)): ?>
          <div class="empty-state">
            <i class="fas fa-bullhorn"></i>
            <h4>No submissions yet</h4>
            <p>Start by submitting your first announcement</p>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 45%;">Title & Content</th>
                  <th style="width: 8%;">Category</th>
                  <th style="width: 8%;">Status</th>
                  <th style="width: 12%;">Date</th>
                  <th style="width: 27%;">Review Notes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($submittedAnnouncements as $item): ?>
                  <tr>
                    <td>
                      <div class="item-title"><?= esc($item['title']) ?></div>
                      <div class="item-preview"><?= esc(substr($item['content'], 0, 150)) ?>...</div>
                    </td>
                    <td>
                      <span class="category-tag"><?= esc($item['category'] ?? 'General') ?></span>
                    </td>
                    <td>
                      <span class="status-badge status-<?= esc($item['status']) ?>">
                        <?= esc(ucfirst($item['status'])) ?>
                      </span>
                    </td>
                    <td>
                      <div class="date-text">
                        <?= $item['submitted_at'] ? date('M j, Y', strtotime($item['submitted_at'])) : '-' ?>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">
                          <?= $item['submitted_at'] ? date('g:i A', strtotime($item['submitted_at'])) : '' ?>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php if (!empty($item['review_notes'])): ?>
                        <div class="review-notes-full">
                          <?= esc($item['review_notes']) ?>
                        </div>
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

      <!-- Add Announcement Modal -->
      <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addAnnouncementModalLabel">Submit Announcement</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?page=faculty_manage_announcements" enctype="multipart/form-data">
              <div class="modal-body">
                <input type="hidden" name="add_announcement" value="1">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="title" class="form-label">Announcement Title</label>
                    <input type="text" class="form-control" id="title" name="title" required placeholder="Enter announcement title">
                  </div>
                  
                  <div class="form-group form-group-half">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                      <option value="general">General</option>
                      <option value="academic">Academic</option>
                      <option value="registration">Registration</option>
                      <option value="deadline">Deadline</option>
                      <option value="holiday">Holiday</option>
                      <option value="emergency">Emergency</option>
                    </select>
                  </div>
                  
                  <div class="form-group form-group-half">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="6" placeholder="Write your announcement content here..."></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="image" class="form-label">Image <span class="optional">(optional)</span></label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                  </div>
                  
                  <div class="submission-note">
                    <i class="fas fa-info-circle"></i>
                    <span>Your announcement will be reviewed by the dean before publication</span>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-primary">
                  <i class="fas fa-paper-plane"></i>
                  <span>Submit Announcement</span>
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
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
</body>
</html>