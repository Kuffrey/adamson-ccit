<?php
// app/views/dean_manage_certifications.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dean') {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../models/FacultySubmissions.php';
require_once __DIR__ . '/../models/FacultyCertification.php';

if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['submission_id'])) {
    if ($_POST['action'] === 'approve_certification') {
        $submissionId = (int)$_POST['submission_id'];
        $submission = FacultySubmissions::getById($submissionId);
        if ($submission && in_array($submission['status'], ['submitted', 'under_review'])) {
            try {
                // Mark submission as approved
                FacultySubmissions::updateStatus($submissionId, 'approved', $_SESSION['user']['id'] ?? null, $_POST['review_notes'] ?? '');

                // Parse the certification data from the submission
                $certData = json_decode($submission['content'] ?? '{}', true) ?: [];
                error_log("Certification data from submission: " . print_r($certData, true));
                
                // Ensure we have a valid certification_id
                $certificationId = $certData['certification_id'] ?? null;
                if (!$certificationId) {
                    throw new Exception("No certification_id found in submission data");
                }
                
                // Create the faculty certification award with all required fields
                $certModel = new FacultyCertification();
                $certificationAwardData = [
                    'faculty_id'      => $submission['faculty_id'],
                    'certification_id'=> $certificationId,
                    'year_earned'     => $certData['year_earned'] ?? date('Y'),
                    'year_expiry'     => $certData['year_expiry'] ?? null,
                    'credential_id'   => $certData['credential_id'] ?? null,
                    'verification_url'=> $certData['verification_url'] ?? null,
                    'description'     => $certData['description'] ?? null,
                    'status'          => 'Active',
                    'is_archived'     => 0
                ];
                
                error_log("Creating certification award with data: " . print_r($certificationAwardData, true));
                
                $result = $certModel->create($certificationAwardData);
                
                if ($result) {
                    $success_message = 'Certification approved and published to faculty_certification_award successfully!';
                    error_log("Certification award created with ID: " . $result);
                } else {
                    throw new Exception("Failed to create certification award in faculty_certification_award table");
                }
                
            } catch (Exception $e) {
                error_log("Error approving certification: " . $e->getMessage());
                $error_message = 'Error approving certification: ' . $e->getMessage();
            }
        }
    }
    
    if ($_POST['action'] === 'reject_certification') {
        $submissionId = (int)$_POST['submission_id'];
        FacultySubmissions::updateStatus($submissionId, 'rejected', $_SESSION['user']['id'] ?? null, $_POST['review_notes'] ?? '');
        $success_message = 'Certification submission rejected.';
    }
}

// Fetch pending submissions for dean approval
$pendingCerts = FacultySubmissions::getPendingByType('certification');

// Fetch approved certifications from faculty_certification_award table
$approvedCerts = (new FacultyCertification())->getAll('active');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Certifications | Dean Dashboard</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/dean.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/dean/_dean_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="topbar__btn hide-desktop" type="button" aria-label="Open menu" data-sb-open>☰</button>
      <span class="admin-topbar__title">Manage Certifications</span>
      <span class="admin-topbar__spacer"></span>
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

      <!-- Pending Faculty Certifications Submissions -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-clock me-2"></i>Pending Faculty Certifications Submissions (<?= count($pendingCerts) ?>)
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($pendingCerts)): ?>
            <div class="empty-state-card">
              <i class="fas fa-certificate fa-3x"></i>
              <h6>No pending faculty certification submissions</h6>
              <div class="text-muted">Faculty submissions for certifications will appear here for dean review.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover dashboard-table align-middle">
                <thead>
                  <tr>
                    <th>Faculty Name</th>
                    <th>Certification</th>
                    <th>Issuer</th>
                    <th>Year Earned</th>
                    <th>Year Expiry</th>
                    <th>Credential ID</th>
                    <th>Verification URL</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($pendingCerts as $item): ?>
                    <?php $cd = json_decode($item['content'] ?? '{}', true) ?: []; ?>
                    <tr>
                      <td><?= esc($item['faculty_name'] ?? $item['faculty_username'] ?? 'Faculty') ?></td>
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
                        <?= $item['submitted_at'] ? date('M j, Y g:i A', strtotime($item['submitted_at'])) : '-' ?>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm" role="group">
                          <button type="button" 
                                  class="btn btn-success" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#approveModal<?= $item['id'] ?>">
                            <i class="fas fa-check"></i> Approve
                          </button>
                          <button type="button" 
                                  class="btn btn-danger" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#rejectModal<?= $item['id'] ?>">
                            <i class="fas fa-times"></i> Reject
                          </button>
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

      <!-- Approved Faculty Certifications -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="fas fa-check-circle me-2"></i>Approved Faculty Certifications
          </h5>
        </div>
        <div class="card-body">
          <?php if (empty($approvedCerts)): ?>
            <div class="empty-state-card">
              <i class="fas fa-certificate fa-3x"></i>
              <h6>No approved certifications yet</h6>
              <div class="text-muted">Once certifications are approved, they will appear here and in faculty_certifications.</div>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover dashboard-table align-middle">
                <thead>
                  <tr>
                    <th>Faculty Name</th>
                    <th>Certification</th>
                    <th>Issuer</th>
                    <th>Year Earned</th>
                    <th>Year Expiry</th>
                    <th>Credential ID</th>
                    <th>Verification URL</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($approvedCerts as $cert): ?>
                    <?php
                      // Only show certifications with status 'Active' and not archived
                      if (strtolower($cert['status'] ?? '') !== 'active' || !empty($cert['is_archived'])) continue;
                    ?>
                    <tr>
                      <td><?= esc($cert['faculty_name'] ?? '') ?></td>
                      <td><?= esc($cert['cert_title'] ?? '') ?></td>
                      <td><?= esc($cert['issuer'] ?? '') ?></td>
                      <td><?= esc($cert['year_earned'] ?? '') ?></td>
                      <td><?= esc($cert['year_expiry'] ?? '') ?></td>
                      <td><?= esc($cert['credential_id'] ?? '') ?></td>
                      <td>
                        <?php if (!empty($cert['verification_url'])): ?>
                          <a href="<?= esc($cert['verification_url']) ?>" target="_blank" rel="noopener">View</a>
                        <?php else: ?>
                          <span class="no-notes text-muted">No link</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge badge--status badge--<?= strtolower($cert['status'] ?? 'active') ?>">
                          <?= esc($cert['status'] ?? 'Active') ?>
                        </span>
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

<?php foreach ($pendingCerts as $item): ?>
  <?php $cd = json_decode($item['content'] ?? '{}', true) ?: []; ?>
  <!-- Approve Modal -->
  <div class="modal fade" id="approveModal<?= $item['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Approve Certification</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="submission_id" value="<?= $item['id'] ?>">
            <input type="hidden" name="action" value="approve_certification">
            <p><strong>Title:</strong> <?= esc($item['title']) ?></p>
            <p><strong>Faculty:</strong> <?= esc($item['faculty_name'] ?? 'Unknown') ?></p>
            <p><strong>Issuer:</strong> <?= esc($cd['issuer'] ?? '') ?></p>
            <div class="mt-3">
              <label class="form-label">Approval Notes (Optional)</label>
              <textarea class="form-control" name="review_notes" rows="3" 
                        placeholder="Add any comments about this approval..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-check"></i> Approve Certification
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reject Modal -->
  <div class="modal fade" id="rejectModal<?= $item['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reject Certification</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <input type="hidden" name="submission_id" value="<?= $item['id'] ?>">
            <input type="hidden" name="action" value="reject_certification">
            <p><strong>Title:</strong> <?= esc($item['title']) ?></p>
            <p><strong>Faculty:</strong> <?= esc($item['faculty_name'] ?? 'Unknown') ?></p>
            <div class="mb-3">
              <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
              <textarea class="form-control" name="review_notes" rows="3" required
                        placeholder="Please provide a clear reason for rejection..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-times"></i> Reject
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>