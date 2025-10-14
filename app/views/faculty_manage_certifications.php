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

// Fetch published certifications from faculty_certification_award table
try {
  $facultyCertification = new FacultyCertification();
  $myCertifications = $facultyCertification->getAll('active');
  $myCertifications = array_filter($myCertifications, function($cert) use ($faculty_id) {
    return isset($cert['faculty_id']) && $cert['faculty_id'] == $faculty_id;
  });
} catch (Exception $e) {
  $notice = 'Error fetching published certifications: ' . $e->getMessage();
  $myCertifications = [];
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

          // Ensure all required data is included
          $certificationData = [
            'cert_title'      => $selectedCert['cert_title'],
            'issuer'          => $selectedCert['issuer'],
            'certification_id'=> (int)$certificationId, // Ensure it's an integer
            'year_earned'     => $_POST['year_earned'] ?? date('Y'),
            'year_expiry'     => $_POST['year_expiry'] ?? null,
            'credential_id'   => $_POST['credential_id'] ?? null,
            'verification_url'=> $_POST['verification_url'] ?? null,
            'description'     => $_POST['description'] ?? null
          ];

          error_log("Faculty submitting certification data: " . print_r($certificationData, true));

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

/* ---------- Load data ---------- */
$mySubmissions = FacultySubmissions::getByFacultyAndType($faculty_id, 'certification');

// Partition by status
$pendingCerts  = [];
$reviewedCerts = [];
foreach ($mySubmissions as $item) {
  $status = strtolower($item['status'] ?? '');
  if (in_array($status, ['submitted', 'pending'])) {
    $pendingCerts[]  = $item;
  } else {
    $reviewedCerts[] = $item;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Certifications Submission | Faculty Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    td.actions-cell { white-space: nowrap; text-align: center; }
    td.actions-cell .btn-group { vertical-align: middle; }
    td.actions-cell form { display: inline-block; margin: 0; }
    td.actions-cell .btn { line-height: 1; }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/faculty/_faculty_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open menu" data-sb-open>☰</button>
      <span class="admin-topbar__title">Faculty Certifications Submission</span>
      <span class="admin-topbar__spacer"></span>
      <div class="admin-topbar__user">
      </div>
    </header>

    <section class="admin-section">
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show modern-alert" role="alert">
          <i class="fas fa-check-circle me-2"></i><?= esc($success_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i><?= esc($error_message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="alert alert-info py-2 mb-4 d-flex align-items-center" style="font-size:0.97rem;">
        <i class="fas fa-info-circle me-2"></i>
        Your certification will be reviewed by the dean before publication.
      </div>

      <!-- Header Actions -->
      <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0">
              <i class="fas fa-certificate me-2"></i>Your Certifications
            </h5>
          </div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
            <i class="fas fa-plus me-2"></i>Add Certification
          </button>
        </div>
      </div>

      <!-- Pending Dean Review -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-clock me-2"></i>Pending Dean Review
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($pendingCerts)): ?>
            <div class="empty-state-card">
              <i class="fas fa-certificate fa-3x"></i>
              <h6>No pending certification submissions</h6>
              <div class="text-muted">Add your certifications using the button above.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover dashboard-table align-middle" id="certTable">
                <thead>
                  <tr>
                    <th>Certification</th>
                    <th>Issuer</th>
                    <th>Year Earned</th>
                    <th>Year Expiry</th>
                    <th>Credential ID</th>
                    <th>Verification URL</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($pendingCerts as $item): ?>
                    <?php $cd = json_decode($item['content'] ?? '{}', true) ?: []; ?>
                    <tr data-year="<?= esc($cd['year_earned'] ?? '') ?>">
                      <td><?= esc($item['title']) ?></td>
                      <td><?= esc($cd['issuer'] ?? '') ?></td>
                      <td><?= esc($cd['year_earned'] ?? '') ?></td>
                      <td><?= esc($cd['year_expiry'] ?? '') ?></td>
                      <td><?= esc($cd['credential_id'] ?? '') ?></td>
                      <td>
                        <?php if (!empty($cd['verification_url'])): ?>
                          <a href="<?= esc($cd['verification_url']) ?>" target="_blank" rel="noopener">View</a>
                        <?php else: ?>
                          <span class="no-notes text-muted">No link</span>
                        <?php endif; ?>
                      </td>
                      <td class="actions-cell">
                        <div class="btn-group btn-group-sm" role="group">
                          <button type="button"
                                  class="btn btn-info"
                                  title="View details"
                                  data-bs-toggle="modal"
                                  data-bs-target="#viewCertModal"
                                  data-title="<?= esc($item['title']) ?>"
                                  data-issuer="<?= esc($cd['issuer'] ?? '') ?>"
                                  data-year_earned="<?= esc($cd['year_earned'] ?? '') ?>"
                                  data-year_expiry="<?= esc($cd['year_expiry'] ?? '') ?>"
                                  data-credential_id="<?= esc($cd['credential_id'] ?? '') ?>"
                                  data-verification_url="<?= esc($cd['verification_url'] ?? '') ?>">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button type="button"
                                  class="btn btn-warning"
                                  title="Edit"
                                  data-bs-toggle="modal"
                                  data-bs-target="#editCertModal"
                                  data-id="<?= (int)$item['id'] ?>"
                                  data-title="<?= esc($item['title']) ?>"
                                  data-issuer="<?= esc($cd['issuer'] ?? '') ?>"
                                  data-year_earned="<?= esc($cd['year_earned'] ?? '') ?>"
                                  data-year_expiry="<?= esc($cd['year_expiry'] ?? '') ?>"
                                  data-credential_id="<?= esc($cd['credential_id'] ?? '') ?>"
                                  data-verification_url="<?= esc($cd['verification_url'] ?? '') ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="d-inline" onsubmit="return confirm('Delete this certification?')">
                            <input type="hidden" name="action" value="delete_certification">
                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                            <button type="submit" class="btn btn-danger" title="Delete">
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

      <!-- Reviewed by Dean -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-check-circle me-2"></i>Reviewed by Dean
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($reviewedCerts)): ?>
            <div class="empty-state-card">
              <i class="fas fa-certificate fa-3x"></i>
              <h6>No reviewed certifications yet</h6>
              <div class="text-muted">Once the dean reviews your certifications, they will appear here.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover dashboard-table align-middle">
                <thead>
                  <tr>
                    <th>Certification</th>
                    <th>Issuer</th>
                    <th>Year Earned</th>
                    <th>Year Expiry</th>
                    <th>Credential ID</th>
                    <th>Verification URL</th>
                    <th>Status</th>
                    <th>Review Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($reviewedCerts as $item): ?>
                    <?php $cd = json_decode($item['content'] ?? '{}', true) ?: []; ?>
                    <tr>
                      <td><?= esc($item['title']) ?></td>
                      <td><?= esc($cd['issuer'] ?? '') ?></td>
                      <td><?= esc($cd['year_earned'] ?? '') ?></td>
                      <td><?= esc($cd['year_expiry'] ?? '') ?></td>
                      <td><?= esc($cd['credential_id'] ?? '') ?></td>
                      <td>
                        <?php if (!empty($cd['verification_url'])): ?>
                          <a href="<?= esc($cd['verification_url']) ?>" target="_blank" rel="noopener">View</a>
                        <?php else: ?>
                          <span class="no-notes text-muted">No link</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge badge--status badge--<?= strtolower($item['status']) ?>">
                          <?= esc(ucfirst($item['status'])) ?>
                        </span>
                      </td>
                      <td>
                        <?php if (!empty($item['review_notes'])): ?>
                          <div class="review-notes-full"><?= esc($item['review_notes']) ?></div>
                        <?php else: ?>
                          <span class="no-notes text-muted">No review notes</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Published Certifications -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-certificate"></i>
            <span>My Published Certifications (<?= count($myCertifications) ?>)</span>
          </h3>
        </div>
        <?php if (empty($myCertifications)): ?>
          <div class="empty-state-card">
            <i class="fas fa-certificate fa-3x"></i>
            <h6>No published certifications yet</h6>
            <div class="text-muted">Submit certifications for dean approval to have them published</div>
          </div>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table class="dashboard-table">
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
                    <span class="badge--status badge--<?= esc(strtolower($cert['status'] ?? 'active')) ?>">
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
                      <button type="submit" class="btn btn-danger btn-sm" title="Delete Certification">
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

    </section>
  </main>
</div>

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertificationModal" tabindex="-1" aria-labelledby="addCertificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="?page=faculty_manage_certifications" novalidate>
        <input type="hidden" name="action" value="add_certification">
        <div class="modal-header">
          <h5 class="modal-title" id="addCertificationModalLabel">
            <i class="fas fa-plus me-2"></i>Add Certification
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="certification_id" class="form-label">Certification <span class="text-danger">*</span></label>
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
              <div class="mb-3">
                <label for="year_earned" class="form-label">Year Earned <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="year_earned" name="year_earned" placeholder="2024" pattern="[0-9]{4}" required>
              </div>
              <div class="mb-3">
                <label for="year_expiry" class="form-label">Year Expiry <span class="text-muted">(optional)</span></label>
                <input type="text" class="form-control" id="year_expiry" name="year_expiry" placeholder="2027" pattern="[0-9]{4}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="credential_id" class="form-label">Credential ID <span class="text-muted">(optional)</span></label>
                <input type="text" class="form-control" id="credential_id" name="credential_id" placeholder="Certificate ID or Badge Number">
              </div>
              <div class="mb-3">
                <label for="verification_url" class="form-label">Verification URL <span class="text-muted">(optional)</span></label>
                <input type="url" class="form-control" id="verification_url" name="verification_url" placeholder="https://verify.example.com/certificate/123">
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-muted">(optional)</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of the certification..."></textarea>
              </div>
            </div>
          </div>
          <div class="submission-note mt-2">
            <i class="fas fa-info-circle"></i>
            <span>Your certification will be reviewed by the dean before publication</span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> <span>Submit Certification</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Certification Modal -->
<div class="modal fade" id="editCertModal" tabindex="-1" aria-labelledby="editCertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="?page=faculty_manage_certifications" novalidate>
        <input type="hidden" name="action" value="edit_certification">
        <input type="hidden" name="id" id="edit_cert_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="editCertModalLabel">
            <i class="fas fa-edit me-2"></i>Edit Certification
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_title" class="form-label">Certification <span class="text-danger">*</span></label>
                <input type="text" id="edit_title" name="title" class="form-control" required maxlength="255">
              </div>
              <div class="mb-3">
                <label for="edit_issuer" class="form-label">Issuer <span class="text-danger">*</span></label>
                <input type="text" id="edit_issuer" name="issuer" class="form-control" required maxlength="255">
              </div>
              <div class="mb-3">
                <label for="edit_year_earned" class="form-label">Year Earned <span class="text-danger">*</span></label>
                <input type="text" id="edit_year_earned" name="year_earned" class="form-control" pattern="[0-9]{4}" required>
              </div>
              <div class="mb-3">
                <label for="edit_year_expiry" class="form-label">Year Expiry <span class="text-muted">(optional)</span></label>
                <input type="text" id="edit_year_expiry" name="year_expiry" class="form-control" pattern="[0-9]{4}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="edit_credential_id" class="form-label">Credential ID <span class="text-muted">(optional)</span></label>
                <input type="text" id="edit_credential_id" name="credential_id" class="form-control">
              </div>
              <div class="mb-3">
                <label for="edit_verification_url" class="form-label">Verification URL <span class="text-muted">(optional)</span></label>
                <input type="url" id="edit_verification_url" name="verification_url" class="form-control">
              </div>
              <div class="mb-3">
                <label for="edit_description" class="form-label">Description <span class="text-muted">(optional)</span></label>
                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Certification Modal -->
<div class="modal fade" id="viewCertModal" tabindex="-1" aria-labelledby="viewCertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewCertModalLabel">
          <i class="fas fa-eye me-2"></i>Certification Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="font-size:1.05rem; color:#222;">
        <div class="mb-3">
          <strong>Certification:</strong>
          <div id="view_cert_title" class="fw-semibold" style="font-size:1.15rem; color:#003169; margin-top:2px;"></div>
        </div>
        <div class="mb-3">
          <strong>Issuer:</strong>
          <span id="view_cert_issuer" style="font-weight:500; color:#0080c9; margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Year Earned:</strong>
          <span id="view_cert_year_earned" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Year Expiry:</strong>
          <span id="view_cert_year_expiry" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Credential ID:</strong>
          <span id="view_cert_credential_id" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Verification URL:</strong>
          <span id="view_cert_verification_url" style="margin-left:4px;"></span>
        </div>
        <div class="mb-3">
          <strong>Description:</strong>
          <span id="view_cert_description" style="margin-left:4px;"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Edit modal population
  var editModal = document.getElementById('editCertModal');
  if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget || {};
      document.getElementById('edit_cert_id').value = button.getAttribute('data-id') || '';
      document.getElementById('edit_title').value = button.getAttribute('data-title') || '';
      document.getElementById('edit_issuer').value = button.getAttribute('data-issuer') || '';
      document.getElementById('edit_year_earned').value = button.getAttribute('data-year_earned') || '';
      document.getElementById('edit_year_expiry').value = button.getAttribute('data-year_expiry') || '';
      document.getElementById('edit_credential_id').value = button.getAttribute('data-credential_id') || '';
      document.getElementById('edit_verification_url').value = button.getAttribute('data-verification_url') || '';
      document.getElementById('edit_description').value = button.getAttribute('data-description') || '';
    });
  }

  // View modal population
  var viewModal = document.getElementById('viewCertModal');
  if (viewModal) {
    viewModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('view_cert_title').textContent = button.getAttribute('data-title') || '';
      document.getElementById('view_cert_issuer').textContent = button.getAttribute('data-issuer') || '';
      document.getElementById('view_cert_year_earned').textContent = button.getAttribute('data-year_earned') || '';
      document.getElementById('view_cert_year_expiry').textContent = button.getAttribute('data-year_expiry') || '';
      document.getElementById('view_cert_credential_id').textContent = button.getAttribute('data-credential_id') || '';
      var vurl = button.getAttribute('data-verification_url') || '';
      document.getElementById('view_cert_verification_url').innerHTML = vurl ? '<a href="' + vurl + '" target="_blank" rel="noopener">' + vurl + '</a>' : '<span class="no-notes text-muted">No link</span>';
      document.getElementById('view_cert_description').textContent = button.getAttribute('data-description') || '';
    });
  }

  // Year filter (if you want to add a year filter dropdown)
  var yearSel = document.getElementById('filterYear');
  var tableRows = document.querySelectorAll('#certTable tbody tr');
  if (yearSel) {
    yearSel.addEventListener('change', function() {
      var y = yearSel.value;
      tableRows.forEach(function(row) {
        if (y === 'all' || row.getAttribute('data-year') === y) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});
</script>
</body>
</html>