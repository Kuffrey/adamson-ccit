<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../models/FacultySubmissions.php';
require_once __DIR__ . '/../models/FacultyCertification.php';

$faculty_id = $_SESSION['user']['id'] ?? null;
$faculty_username = $_SESSION['user']['username'] ?? 'Faculty';
if (!$faculty_id) { header('Location: ?page=login'); exit; }

// Get faculty info
try {
  $pdo = new PDO("mysql:host=localhost;dbname=adamson_ccit", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$faculty_username]);
  $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($faculty) {
    $firstName = $faculty['first_name'];
    $lastName  = $faculty['last_name'];
    $fullName  = trim($firstName . ' ' . $lastName);
  } else {
    $firstName = "Faculty"; $lastName = ""; $fullName = $faculty_username;
  }
} catch (PDOException $e) {
  $firstName = "Faculty"; $lastName = ""; $fullName = $faculty_username;
}

$notice = '';
$myCertifications = [];
$availableCertifications = [];
$mySubmissions = [];

try {
  $facultyCertification = new FacultyCertification();
  $availableCertifications = FacultyCertification::getAllCertifications();
  if (!is_array($availableCertifications)) { $availableCertifications = []; }
} catch (Exception $e) {
  $notice = 'Error initializing models: ' . $e->getMessage();
  $availableCertifications = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  error_log("Faculty certification form submission: " . print_r($_POST, true));
  try {
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'add_certification':
          $certificationId = $_POST['certification_id'] ?? null;
          $selectedCert = null;

          if ($certificationId && !empty($availableCertifications)) {
            foreach ($availableCertifications as $cert) {
              if ((string)$cert['id'] === (string)$certificationId) {
                $selectedCert = $cert; break;
              }
            }
          }
          if (!$selectedCert) {
            $error_message = 'Error: Please select a valid certification.'; break;
          }

          $certificationData = [
            'cert_title'      => $selectedCert['cert_title'],
            'issuer'          => $selectedCert['issuer'],
            'certification_id'=> $certificationId,
            'year_earned'     => $_POST['year_earned'] ?? '',
            'year_expiry'     => $_POST['year_expiry'] ?? '',
            'credential_id'   => $_POST['credential_id'] ?? '',
            'verification_url'=> $_POST['verification_url'] ?? '',
            'description'     => $_POST['description'] ?? ''
          ];

          $submissionData = [
            'faculty_id'      => $faculty_id,
            'submission_type' => 'certification',
            'title'           => $selectedCert['cert_title'],
            'description'     => $_POST['description'] ?? '',
            'content'         => json_encode($certificationData, JSON_UNESCAPED_SLASHES),
            'category'        => 'Professional Certification',
            'status'          => 'submitted'
          ];

          $submissionId = FacultySubmissions::create($submissionData);
          if ($submissionId) {
            $success_message = 'Certification submitted for dean approval successfully!';
            error_log("Certification submission created with ID: " . $submissionId);
          } else {
            $error_message = 'Error: Failed to submit certification.';
          }
          break;

        case 'delete_certification':
          $submissionId = $_POST['id'] ?? null;
          if ($submissionId) {
            $submission = FacultySubmissions::getById((int)$submissionId);
            if ($submission && $submission['faculty_id'] == $faculty_id) {
              $deleted = FacultySubmissions::delete((int)$submissionId);
              $success_message = $deleted ? 'Certification submission deleted successfully!' : 'Error: Failed to delete submission.';
            } else {
              $error_message = 'Error: Submission not found or access denied.';
            }
          }
          break;
      }
    }
  } catch (Exception $e) {
    $error_message = 'Error: ' . $e->getMessage();
    error_log("Certification submission error: " . $e->getMessage());
  }
}

// Pending submissions for this faculty
try {
  $mySubmissions = FacultySubmissions::getByFacultyAndType($faculty_id, 'certification');
  usort($mySubmissions, function($a, $b) {
    return strtotime($b['submitted_at'] ?? '0') <=> strtotime($a['submitted_at'] ?? '0');
  });
} catch (Exception $e) {
  $mySubmissions = [];
  error_log("Error fetching certification submissions: " . $e->getMessage());
}

/* Optional safety DDL (kept from your original) */
try {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS faculty_certifications (
      id INT AUTO_INCREMENT PRIMARY KEY,
      cert_title VARCHAR(255) NOT NULL,
      issuer VARCHAR(255) NOT NULL
    )
  ");
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS faculty_submissions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      faculty_id INT NOT NULL,
      submission_type VARCHAR(50) NOT NULL,
      title VARCHAR(255) NOT NULL,
      description TEXT,
      content TEXT,
      category VARCHAR(100),
      status VARCHAR(30) DEFAULT 'submitted',
      review_notes TEXT,
      submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  ");
} catch (PDOException $e) { /* ignore */ }

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Certifications Submission | Faculty Dashboard</title>

  <!-- Shared faculty layout/theme (aligns with sidebar) -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/faculty.css" />

  <!-- Vendors -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="admin-cms-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Certifications Submission</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper(($firstName[0] ?? 'F') . ($lastName[0] ?? ''))) ?></span>
        <span class="admin-topbar__name"><?= esc($fullName) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= esc($success_message) ?></div>
      <?php endif; ?>
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= esc($error_message) ?></div>
      <?php endif; ?>

      <!-- Page header -->
      <div class="page-header">
        <h1 class="page-title">
          <i class="fas fa-certificate"></i>
          <span>Certification Management</span>
        </h1>
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
          <i class="fas fa-plus"></i>
          <span>Add Certification</span>
        </button>
      </div>

      <!-- Published Certifications -->
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-certificate"></i>
            <span>My Published Certifications (<?= count($myCertifications) ?>)</span>
          </h3>
        </div>

        <?php if (empty($myCertifications)): ?>
          <div class="empty-state">
            <i class="fas fa-certificate"></i>
            <h4>No published certifications yet</h4>
            <p>Submit certifications for dean approval to have them published</p>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:35%;">Certification & Issuer</th>
                  <th style="width:12%;">Year Earned</th>
                  <th style="width:12%;">Year Expiry</th>
                  <th style="width:10%;">Status</th>
                  <th style="width:20%;">Credential Info</th>
                  <th style="width:11%;">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($myCertifications as $cert): ?>
                <tr>
                  <td>
                    <div class="item-title"><?= esc($cert['cert_title'] ?? 'Unknown') ?></div>
                    <div class="item-subtitle"><?= esc($cert['issuer'] ?? 'Unknown') ?></div>
                  </td>
                  <td><div class="date-text"><?= esc($cert['year_earned'] ?? '') ?></div></td>
                  <td><div class="date-text"><?= esc($cert['year_expiry'] ?? 'N/A') ?></div></td>
                  <td>
                    <span class="status-badge status-<?= esc(strtolower($cert['status'] ?? 'active')) ?>">
                      <?= esc($cert['status'] ?? 'Active') ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($cert['credential_id'])): ?>
                      <div class="item-subtitle">ID: <?= esc($cert['credential_id']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($cert['verification_url'])): ?>
                      <a href="<?= esc($cert['verification_url']) ?>" target="_blank" class="item-subtitle" style="text-decoration:none;">
                        View Certificate →
                      </a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <form method="post" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this certification? This will remove it from public view and cannot be undone.')">
                      <input type="hidden" name="action" value="delete_certification">
                      <input type="hidden" name="id" value="<?= (int)($cert['id'] ?? 0) ?>">
                      <button type="submit" class="btn-danger-sm" title="Delete Certification">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pending submissions -->
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-clock"></i>
            <span>Pending Dean Approval (<?= count($mySubmissions) ?>)</span>
          </h3>
        </div>

        <?php if (empty($mySubmissions)): ?>
          <div class="empty-state">
            <i class="fas fa-clock"></i>
            <h4>No pending submissions</h4>
            <p>All submitted certifications have been processed</p>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:35%;">Certification & Issuer</th>
                  <th style="width:12%;">Year Info</th>
                  <th style="width:12%;">Submitted</th>
                  <th style="width:8%;">Status</th>
                  <th style="width:33%;">Review Notes</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($mySubmissions as $submission): ?>
                <?php $certData = json_decode($submission['content'] ?? '{}', true) ?: []; ?>
                <tr>
                  <td>
                    <div class="item-title"><?= esc($submission['title']) ?></div>
                    <div class="item-subtitle"><?= esc($certData['issuer'] ?? 'Unknown') ?></div>
                    <?php if (!empty($certData['credential_id'])): ?>
                      <div class="item-subtitle">ID: <?= esc($certData['credential_id']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($certData['year_earned'])): ?>
                      <div class="date-text">Earned: <?= esc($certData['year_earned']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($certData['year_expiry'])): ?>
                      <div class="item-subtitle">Expires: <?= esc($certData['year_expiry']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="date-text">
                      <?= $submission['submitted_at'] ? date('M j, Y', strtotime($submission['submitted_at'])) : '-' ?>
                    </div>
                    <div class="date-sub">
                      <?= $submission['submitted_at'] ? date('g:i A', strtotime($submission['submitted_at'])) : '' ?>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge status-<?= esc($submission['status']) ?>">
                      <?= esc(ucfirst($submission['status'])) ?>
                    </span>
                    <?php if ($submission['status'] === 'approved'): ?>
                      <div class="item-subtitle" style="color:#059669; margin-top:4px;">Will appear in public view soon</div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($submission['review_notes'])): ?>
                      <div class="review-notes-full"><?= esc($submission['review_notes']) ?></div>
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

      <!-- Add Certification Modal -->
      <div class="modal fade" id="addCertificationModal" tabindex="-1" aria-labelledby="addCertificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addCertificationModalLabel">Submit Certification</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?page=faculty_manage_certifications">
              <div class="modal-body">
                <input type="hidden" name="action" value="add_certification">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="certification_id" class="form-label"><i class="fas fa-certificate"></i> Certification</label>
                    <select class="form-control" name="certification_id" id="certification_id" required>
                      <option value="">Select Certification</option>
                      <?php if (!empty($availableCertifications)): ?>
                        <?php foreach ($availableCertifications as $cert): ?>
                          <option value="<?= (int)$cert['id'] ?>"
                                  data-title="<?= esc($cert['cert_title']) ?>"
                                  data-issuer="<?= esc($cert['issuer']) ?>">
                            <?= esc($cert['cert_title']) ?> (<?= esc($cert['issuer']) ?>)
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option value="" disabled>No certifications available</option>
                      <?php endif; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="year_earned" class="form-label"><i class="fas fa-calendar"></i> Year Earned</label>
                    <input type="text" class="form-control" id="year_earned" name="year_earned" placeholder="2024" pattern="[0-9]{4}" required>
                  </div>

                  <div class="form-group">
                    <label for="year_expiry" class="form-label"><i class="fas fa-calendar-times"></i> Year Expiry <span class="optional">(optional)</span></label>
                    <input type="text" class="form-control" id="year_expiry" name="year_expiry" placeholder="2027" pattern="[0-9]{4}">
                  </div>

                  <div class="form-group">
                    <label for="credential_id" class="form-label"><i class="fas fa-id-card"></i> Credential ID <span class="optional">(optional)</span></label>
                    <input type="text" class="form-control" id="credential_id" name="credential_id" placeholder="Certificate ID or Badge Number">
                  </div>

                  <div class="form-group">
                    <label for="verification_url" class="form-label"><i class="fas fa-link"></i> Verification URL <span class="optional">(optional)</span></label>
                    <input type="url" class="form-control" id="verification_url" name="verification_url" placeholder="https://verify.example.com/certificate/123">
                  </div>

                  <div class="form-group">
                    <label for="description" class="form-label"><i class="fas fa-file-alt"></i> Description <span class="optional">(optional)</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of the certification..."></textarea>
                  </div>

                  <div class="submission-note">
                    <i class="fas fa-info-circle"></i>
                    <span>Your certification will be reviewed by the dean before publication</span>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-primary">
                  <i class="fas fa-paper-plane"></i> <span>Submit Certification</span>
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
  const certificationSelect = document.querySelector('#certification_id');
  if (certificationSelect) {
    certificationSelect.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      if (opt && opt.value) {
        console.log('Selected certification:', opt.dataset.title, 'by', opt.dataset.issuer);
      }
    });
  }

  // Enable any tooltips if present
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el); });
});
</script>
</body>
</html>
