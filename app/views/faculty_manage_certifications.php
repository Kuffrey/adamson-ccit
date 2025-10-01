<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'faculty') {
    header('Location: ?page=login');
    exit;
}
require_once __DIR__ . '/../models/FacultySubmissions.php';
require_once __DIR__ . '/../models/FacultyCertification.php';

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

$notice = '';

// Initialize arrays first to prevent foreach errors
$myCertifications = [];
$availableCertifications = [];
$mySubmissions = [];

try {
    $facultyCertification = new FacultyCertification();
    
    // Load available certifications early - needed for both display and form processing
    $availableCertifications = FacultyCertification::getAllCertifications();
    if (!is_array($availableCertifications)) {
        $availableCertifications = [];
    }
} catch (Exception $e) {
    $notice = 'Error initializing models: ' . $e->getMessage();
    $availableCertifications = [];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log the POST data
    error_log("Faculty certification form submission: " . print_r($_POST, true));
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_certification':
                    // Get certification details from the selected certification
                    $certificationId = $_POST['certification_id'] ?? null;
                    $selectedCert = null;
                    
                    // Enhanced debugging
                    error_log("=== ADD CERTIFICATION DEBUG ===");
                    error_log("Raw certification_id from POST: " . var_export($_POST['certification_id'] ?? 'NOT SET', true));
                    error_log("Processed certificationId: " . var_export($certificationId, true));
                    error_log("certificationId is empty: " . (empty($certificationId) ? 'YES' : 'NO'));
                    
                    // Reload available certifications for form processing
                    try {
                        $currentAvailableCerts = FacultyCertification::getAllCertifications();
                        if (!is_array($currentAvailableCerts)) {
                            $currentAvailableCerts = [];
                        }
                        error_log("Available certifications count: " . count($currentAvailableCerts));
                    } catch (Exception $e) {
                        $currentAvailableCerts = [];
                        error_log("Error loading certifications: " . $e->getMessage());
                    }
                    
                    if ($certificationId && !empty($currentAvailableCerts)) {
                        error_log("Searching for certification ID: $certificationId");
                        foreach ($currentAvailableCerts as $cert) {
                            error_log("Comparing: cert[id]={$cert['id']} (type: " . gettype($cert['id']) . ") with certificationId=$certificationId (type: " . gettype($certificationId) . ")");
                            if ($cert['id'] == $certificationId) {
                                $selectedCert = $cert;
                                error_log("MATCH FOUND!");
                                break;
                            }
                        }
                    } else {
                        error_log("Skipping search - certificationId empty or no available certs");
                    }
                    
                    if (!$selectedCert) {
                        // Debug information
                        $debugInfo = "Certification ID: " . ($certificationId ?? 'NULL') . 
                                   ", Available certs: " . count($currentAvailableCerts ?? []);
                        error_log("Certification selection error: " . $debugInfo);
                        $notice = 'Error: Please select a valid certification. (Debug: ' . $debugInfo . ')';
                        break;
                    }
                    
                    // Create certification data for submission approval
                    $certificationData = [
                        'cert_title' => $selectedCert['cert_title'],
                        'issuer' => $selectedCert['issuer'],
                        'certification_id' => $certificationId,
                        'year_earned' => $_POST['year_earned'] ?? '',
                        'year_expiry' => $_POST['year_expiry'] ?? '',
                        'credential_id' => $_POST['credential_id'] ?? '',
                        'verification_url' => $_POST['verification_url'] ?? '',
                        'description' => $_POST['description'] ?? ''
                    ];
                    
                    // Create submission for dean approval
                    $success = FacultySubmissions::create([
                        'faculty_id' => $faculty_id,
                        'submission_type' => 'certification',
                        'title' => $selectedCert['cert_title'],
                        'description' => $_POST['description'] ?? '',
                        'content' => json_encode($certificationData),
                        'category' => 'Professional Certification',
                        'status' => 'submitted'
                    ]);
                    
                    if ($success) {
                        $success_message = 'Certification submitted for dean approval successfully!';
                    } else {
                        $error_message = 'Error: Failed to submit certification.';
                    }
                    break;
                    
                case 'delete_certification':
                    // Only allow deletion of own certifications
                    $cert = $facultyCertification->getById($_POST['id']);
                    if ($cert && $cert['faculty_id'] == $faculty_id) {
                        $facultyCertification->delete($_POST['id']);
                        $success_message = 'Certification deleted successfully!';
                    } else {
                        $error_message = 'Error: Certification not found or access denied.';
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . $e->getMessage();
    }
}

// Get faculty's published certifications (approved and public)
try {
    // Initialize arrays first
    $myCertifications = [];
    $availableCertifications = [];
    
    $allCertifications = $facultyCertification->getAll();
    $myCertifications = array_filter($allCertifications, function($cert) use ($faculty_id) {
        return $cert['faculty_id'] == $faculty_id;
    });
    
    // Get available certifications for dropdown - use try-catch for this specific call
    try {
        $availableCertifications = FacultyCertification::getAllCertifications();
        if (!is_array($availableCertifications)) {
            $availableCertifications = [];
        }
    } catch (Exception $certException) {
        $availableCertifications = [];
        error_log("Error loading available certifications: " . $certException->getMessage());
    }
} catch (Exception $e) {
    $myCertifications = [];
    $availableCertifications = [];
    $error_message = 'Error loading certifications: ' . $e->getMessage();
}

// Get faculty's certification submissions (pending approval)
try {
    $allSubmissions = FacultySubmissions::getByFacultyId($faculty_id);
    $mySubmissions = array_filter($allSubmissions, function($submission) {
        return $submission['submission_type'] === 'certification';
    });
    
    // Sort by most recent first
    usort($mySubmissions, function($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
} catch (Exception $e) {
    $mySubmissions = [];
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Certifications | Faculty Dashboard</title>
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
    .status-active { background: #d1fae5; color: #059669; }
    .status-expired { background: #fee2e2; color: #dc2626; }
    
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
      <span class="admin-topbar__title">Manage Certifications</span>
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
          <i class="fas fa-certificate"></i>
          <span>Certification Management</span>
        </h1>
        <button type="button" class="btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
          <i class="fas fa-plus"></i>
          <span>Add Certification</span>
        </button>
      </div>

      <!-- Published Certifications Card -->
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
                  <th style="width: 35%;">Certification & Issuer</th>
                  <th style="width: 12%;">Year Earned</th>
                  <th style="width: 12%;">Year Expiry</th>
                  <th style="width: 10%;">Status</th>
                  <th style="width: 20%;">Credential Info</th>
                  <th style="width: 11%;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($myCertifications as $cert): ?>
                  <tr>
                    <td>
                      <div class="item-title"><?= esc($cert['cert_title'] ?? 'Unknown') ?></div>
                      <div class="item-subtitle"><?= esc($cert['issuer'] ?? 'Unknown') ?></div>
                    </td>
                    <td>
                      <div class="date-text"><?= esc($cert['year_earned'] ?? '') ?></div>
                    </td>
                    <td>
                      <div class="date-text"><?= esc($cert['year_expiry'] ?? 'N/A') ?></div>
                    </td>
                    <td>
                      <span class="status-badge status-<?= strtolower($cert['status'] ?? 'active') ?>">
                        <?= esc($cert['status'] ?? 'Active') ?>
                      </span>
                    </td>
                    <td>
                      <?php if (!empty($cert['credential_id'])): ?>
                        <div class="item-subtitle">ID: <?= esc($cert['credential_id']) ?></div>
                      <?php endif; ?>
                      <?php if (!empty($cert['verification_url'])): ?>
                        <a href="<?= esc($cert['verification_url']) ?>" target="_blank" class="item-subtitle" style="color: #008040; text-decoration: none;">
                          View Certificate →
                        </a>
                      <?php endif; ?>
                    </td>
                    <td>
                      <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this certification? This will remove it from public view and cannot be undone.')">
                        <input type="hidden" name="action" value="delete_certification">
                        <input type="hidden" name="id" value="<?= $cert['id'] ?>">
                        <button type="submit" class="btn-danger-sm" title="Delete Certification">
                          <i class="fas fa-trash-alt me-1"></i>
                          Delete
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

      <!-- Pending Submissions Card -->
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
                  <th style="width: 35%;">Certification & Issuer</th>
                  <th style="width: 12%;">Year Info</th>
                  <th style="width: 12%;">Submitted</th>
                  <th style="width: 8%;">Status</th>
                  <th style="width: 33%;">Review Notes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($mySubmissions as $submission): ?>
                  <?php
                  $certData = json_decode($submission['content'] ?? '{}', true) ?: [];
                  ?>
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
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">
                          <?= $submission['submitted_at'] ? date('g:i A', strtotime($submission['submitted_at'])) : '' ?>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="status-badge status-<?= esc($submission['status']) ?>">
                        <?= esc(ucfirst($submission['status'])) ?>
                      </span>
                      <?php if ($submission['status'] === 'approved'): ?>
                        <div class="item-subtitle" style="color: #059669; margin-top: 4px;">Will appear in public view soon</div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($submission['review_notes'])): ?>
                        <div class="review-notes-full">
                          <?= esc($submission['review_notes']) ?>
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
                    <label for="certification_id" class="form-label">
                      <i class="fas fa-certificate"></i> Certification
                    </label>
                    <select class="form-control" name="certification_id" id="certification_id" required>
                      <option value="">Select Certification</option>
                      <?php if (!empty($availableCertifications) && is_array($availableCertifications)): ?>
                        <?php foreach ($availableCertifications as $cert): ?>
                          <option value="<?= $cert['id'] ?>" data-title="<?= esc($cert['cert_title']) ?>" data-issuer="<?= esc($cert['issuer']) ?>">
                            <?= esc($cert['cert_title']) ?> (<?= esc($cert['issuer']) ?>)
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option value="" disabled>No certifications available</option>
                      <?php endif; ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="year_earned" class="form-label">
                      <i class="fas fa-calendar"></i> Year Earned
                    </label>
                    <input type="text" class="form-control" id="year_earned" name="year_earned" 
                           placeholder="2024" pattern="[0-9]{4}" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="year_expiry" class="form-label">
                      <i class="fas fa-calendar-times"></i> Year Expiry <span class="optional">(optional)</span>
                    </label>
                    <input type="text" class="form-control" id="year_expiry" name="year_expiry" 
                           placeholder="2027" pattern="[0-9]{4}">
                  </div>
                  
                  <div class="form-group">
                    <label for="credential_id" class="form-label">
                      <i class="fas fa-id-card"></i> Credential ID <span class="optional">(optional)</span>
                    </label>
                    <input type="text" class="form-control" id="credential_id" name="credential_id" 
                           placeholder="Certificate ID or Badge Number">
                  </div>
                  
                  <div class="form-group">
                    <label for="verification_url" class="form-label">
                      <i class="fas fa-link"></i> Verification URL <span class="optional">(optional)</span>
                    </label>
                    <input type="url" class="form-control" id="verification_url" name="verification_url" 
                           placeholder="https://verify.example.com/certificate/123">
                  </div>
                  
                  <div class="form-group">
                    <label for="description" class="form-label">
                      <i class="fas fa-file-alt"></i> Description <span class="optional">(optional)</span>
                    </label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Brief description of the certification..."></textarea>
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
                  <i class="fas fa-paper-plane"></i>
                  <span>Submit Certification</span>
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
  // Handle certification dropdown change
  const certificationSelect = document.querySelector('select[name="certification_id"]');
  
  if (certificationSelect) {
    certificationSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      if (selectedOption.value) {
        console.log('Selected certification:', selectedOption.dataset.title, 'by', selectedOption.dataset.issuer);
      }
    });
  }

  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
</body>
</html>
