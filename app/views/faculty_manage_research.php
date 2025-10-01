<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login');
    exit;
}
require_once __DIR__ . '/../models/FacultySubmissions.php';

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

$notice = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['add_research'])) {
      // Create a faculty submission for research
      $submissionData = [
        'faculty_id' => $faculty_id,
        'submission_type' => 'research',
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'category' => $_POST['type'] ?? 'journal',
        'content' => json_encode([
          'dept' => $_POST['dept'] ?? '',
          'type' => $_POST['type'] ?? '',
          'year' => $_POST['year'] ?? '',
          'authors' => $_POST['authors'] ?? '',
          'venue' => $_POST['venue'] ?? '',
          'pdf_url' => $_POST['pdf_url'] ?? '',
          'view_url' => $_POST['view_url'] ?? '',
          'image_url' => $_POST['image_url'] ?? ''
        ]),
        'status' => 'submitted'
      ];
      FacultySubmissions::create($submissionData);
      $success_message = 'Research submitted for dean approval successfully!';
    } elseif (isset($_POST['delete_submission']) && isset($_POST['submission_id'])) {
      $submissionId = (int)$_POST['submission_id'];
      // Only allow delete if owned by this faculty and status is not approved
      $submission = FacultySubmissions::getById($submissionId);
      if ($submission && $submission['faculty_id'] == $faculty_id && $submission['status'] !== 'approved') {
        FacultySubmissions::delete($submissionId);
        $success_message = 'Research submission deleted successfully!';
      } else {
        $error_message = 'Error: Cannot delete this research submission.';
      }
    }
  } catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage();
  }
}

// Get faculty research submissions
$research = FacultySubmissions::getByFacultyAndType($faculty_id, 'research');

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Research | Faculty Dashboard</title>
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
      margin-bottom: 2rem;
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
      min-width: 1000px;
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
    
    .item-subtitle {
      font-size: 0.875rem;
      color: #6b7280;
      line-height: 1.4;
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
    .status-under_review { background: #dbeafe; color: #1d4ed8; }
    
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
    
    /* Action button */
    .btn-danger-sm {
      background: #dc2626;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 0.5rem 0.875rem;
      font-size: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }
    
    .btn-danger-sm:hover {
      background: #b91c1c;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
    }
    
    /* External link buttons */
    .btn-link-sm {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      border-radius: 4px;
      text-decoration: none;
      transition: all 0.2s;
    }
    
    .btn-pdf {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }
    
    .btn-pdf:hover {
      background: #fee2e2;
      color: #b91c1c;
    }
    
    .btn-view {
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid #bfdbfe;
    }
    
    .btn-view:hover {
      background: #dbeafe;
      color: #1d4ed8;
    }
    
    .type-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: capitalize;
      background: #e0f2fe;
      color: #0369a1;
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
      .form-group:nth-child(6),
      .form-group:nth-child(7) {
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
      <span class="admin-topbar__title">Submit Research</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($firstName[0] . $lastName[0])) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <!-- Page Header -->
      <div class="page-header">
        <h1 class="page-title">
          <i class="fas fa-microscope"></i>
          Research Management
        </h1>
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addResearchModal">
          <i class="fas fa-plus"></i>
          Submit New Research
        </button>
      </div>

      <!-- Alerts -->
      <?php if (isset($success_message) && !empty($success_message)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <?php echo htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($error_message) && !empty($error_message)): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <!-- Research List -->
      <div class="content-card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-list"></i>
            Your Research Submissions
          </h2>
        </div>

        <?php if (empty($research)): ?>
          <div class="empty-state">
            <i class="fas fa-microscope"></i>
            <h4>No research submissions yet</h4>
            <p>Get started by submitting your first research publication above.</p>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Research Details</th>
                  <th>Type & Department</th>
                  <th>Publication</th>
                  <th>Status</th>
                  <th>Year</th>
                  <th>Links</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($research as $item): ?>
                  <?php 
                    $researchData = json_decode($item['content'] ?? '{}', true) ?: [];
                  ?>
                  <tr>
                    <td>
                      <div class="item-title"><?= esc($item['title']) ?></div>
                      <?php if (!empty($researchData['authors'])): ?>
                        <div class="item-subtitle">Authors: <?= esc($researchData['authors']) ?></div>
                      <?php endif; ?>
                      <?php if (!empty($researchData['description'])): ?>
                        <div class="item-subtitle" style="margin-top: 0.25rem;">
                          <?= esc(substr($researchData['description'], 0, 100)) ?><?= strlen($researchData['description']) > 100 ? '...' : '' ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="type-badge"><?= esc(ucfirst($researchData['type'] ?? 'journal')) ?></div>
                      <?php if (!empty($researchData['dept'])): ?>
                        <div class="item-subtitle" style="margin-top: 0.25rem;">
                          <?= esc(strtoupper($researchData['dept'])) ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="item-subtitle">
                        <?php if (!empty($researchData['venue'])): ?>
                          <strong><?= esc($researchData['venue']) ?></strong>
                        <?php else: ?>
                          <span class="no-notes">No venue specified</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <span class="status-badge status-<?= esc($item['status']) ?>">
                        <?= esc(ucfirst($item['status'])) ?>
                      </span>
                      <?php if ($item['status'] === 'rejected' && !empty($item['review_notes'])): ?>
                        <div class="item-subtitle" style="margin-top: 0.25rem; color: #dc2626;">
                          <i class="fas fa-exclamation-triangle"></i> 
                          <?= esc($item['review_notes']) ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="date-text">
                        <?= esc($researchData['year'] ?? '-') ?>
                      </div>
                    </td>
                    <td>
                      <div class="item-subtitle">
                        <?php if (!empty($researchData['pdf_url'])): ?>
                          <a href="<?= esc($researchData['pdf_url']) ?>" target="_blank" class="btn-link-sm btn-pdf">
                            <i class="fas fa-file-pdf"></i>
                            PDF
                          </a>
                        <?php endif; ?>
                        <?php if (!empty($researchData['view_url'])): ?>
                          <a href="<?= esc($researchData['view_url']) ?>" target="_blank" class="btn-link-sm btn-view">
                            <i class="fas fa-external-link-alt"></i>
                            View
                          </a>
                        <?php endif; ?>
                        <?php if (empty($researchData['pdf_url']) && empty($researchData['view_url'])): ?>
                          <span class="no-notes">No links</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <?php if (in_array($item['status'], ['submitted', 'rejected'])): ?>
                        <form method="post" style="display:inline;">
                          <input type="hidden" name="delete_submission" value="1">
                          <input type="hidden" name="submission_id" value="<?= $item['id'] ?>">
                          <button type="submit"
                                  class="btn-danger-sm"
                                  title="Delete this research submission"
                                  onclick="return confirm('Delete this research submission?')">
                            <i class="fas fa-trash"></i>
                            Delete Submission
                          </button>
                        </form>
                      <?php elseif ($item['status'] === 'approved'): ?>
                        <span class="status-badge status-approved">
                          <i class="fas fa-check"></i> Published
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <!-- Add Research Modal -->
      <div class="modal fade" id="addResearchModal" tabindex="-1" aria-labelledby="addResearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="post" action="?page=faculty_manage_research">
              <div class="modal-header">
                <h5 class="modal-title" id="addResearchModalLabel">
                  <i class="fas fa-plus"></i> Submit New Research
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="submission-note">
                  <i class="fas fa-info-circle"></i>
                  Your research will be submitted to the dean for review and approval.
                </div>
                
                <div class="form-grid">
                  <div class="form-group">
                    <label for="title" class="form-label">Research Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter research title" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="dept" class="form-label">Department</label>
                    <select id="dept" name="dept" class="form-select" required>
                      <option value="">Select Department</option>
                      <option value="itis">IT&IS</option>
                      <option value="cs">CS</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="type" class="form-label">Research Type</label>
                    <select id="type" name="type" class="form-select" required>
                      <option value="">Select Type</option>
                      <option value="journal">Journal Article</option>
                      <option value="conference">Conference Paper</option>
                      <option value="chapter">Book Chapter</option>
                      <option value="patent">Patent</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="year" class="form-label">Year</label>
                    <input type="text" id="year" name="year" class="form-control" placeholder="e.g., 2024" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="authors" class="form-label">Authors</label>
                    <textarea id="authors" name="authors" class="form-control" rows="2" placeholder="e.g., John Doe, Jane Smith" required></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="venue" class="form-label">Venue</label>
                    <input type="text" id="venue" name="venue" class="form-control" placeholder="e.g., IEEE Transactions on Computers" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="pdf_url" class="form-label">PDF URL <span class="optional">(Optional)</span></label>
                    <input type="url" id="pdf_url" name="pdf_url" class="form-control" placeholder="Link to PDF">
                  </div>
                  
                  <div class="form-group">
                    <label for="view_url" class="form-label">View URL <span class="optional">(Optional)</span></label>
                    <input type="url" id="view_url" name="view_url" class="form-control" placeholder="Link to publication">
                  </div>
                  
                  <div class="form-group">
                    <label for="image_url" class="form-label">Image URL <span class="optional">(Optional)</span></label>
                    <input type="url" id="image_url" name="image_url" class="form-control" placeholder="Link to research poster/image">
                  </div>
                  
                  <div class="form-group">
                    <label for="description" class="form-label">Research Description/Abstract</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter research description or abstract" required></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                  <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" name="add_research" class="btn-primary">
                  <i class="fas fa-paper-plane"></i> Submit for Approval
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
</body>
</html>
