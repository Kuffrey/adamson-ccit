<?php
// app/views/faculty_submit.php - Faculty submission interface
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../models/FacultySubmissions.php';
require_once __DIR__ . '/../models/FacultyActivity.php';

// Check authentication - faculty only
if (!Auth::check() || !Auth::is('faculty')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$user = Auth::user();
$username = $user['username'] ?? 'Faculty';
$notice = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $submissionType = $_POST['submission_type'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? '';
        
        if (empty($title) || empty($submissionType)) {
            throw new Exception('Title and submission type are required.');
        }
        
        // Get faculty ID (assuming it matches user ID or we need to fetch it)
        $facultyId = $user['id']; // You might need to adjust this based on your user->faculty relationship
        
        $data = [
            'faculty_id' => $facultyId,
            'submission_type' => $submissionType,
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'category' => $category,
            'status' => 'submitted'
        ];
        
        if (FacultySubmissions::create($data)) {
            // Log the activity
            FacultyActivity::logActivity($facultyId, 'submission_create', "Submitted {$submissionType}: {$title}");
            
            $notice = ucfirst($submissionType) . ' submission sent for review successfully!';
        } else {
            throw new Exception('Failed to submit. Please try again.');
        }
        
    } catch (Exception $e) {
        $notice = 'Error: ' . $e->getMessage();
    }
}

// Get faculty's previous submissions
$facultyId = $user['id'];
$submissions = FacultySubmissions::getByFaculty($facultyId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Content | CCIT Faculty</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .submission-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        .submission-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <!-- Include your header/navigation here -->
    
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-primary">Faculty Submissions</h1>
                    <p class="lead text-muted">Submit your research, certifications, and news articles for review</p>
                    <p class="text-muted">Welcome, <strong><?= esc($username) ?></strong></p>
                </div>

                <?php if ($notice): ?>
                    <div class="alert <?= str_starts_with($notice, 'Error') ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= str_starts_with($notice, 'Error') ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> me-2"></i>
                        <?= esc($notice) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Submission Form -->
                <div class="card submission-card mb-5">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2"></i>New Submission
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="submission_type" class="form-label">Submission Type *</label>
                                    <select class="form-select" id="submission_type" name="submission_type" required>
                                        <option value="">Select submission type...</option>
                                        <option value="research">Research Project</option>
                                        <option value="certification">Professional Certification</option>
                                        <option value="news">News Article</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">Select category...</option>
                                        <option value="research">Research</option>
                                        <option value="achievement">Achievement</option>
                                        <option value="professional">Professional Development</option>
                                        <option value="academic">Academic</option>
                                        <option value="innovation">Innovation</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="Enter a descriptive title..." required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Brief Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Provide a brief description or summary..."></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="content" class="form-label">Detailed Content</label>
                                <textarea class="form-control" id="content" name="content" rows="8"
                                          placeholder="Provide detailed information, methodology, results, etc..."></textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Provide comprehensive details about your submission. This will be reviewed by the dean before publication.
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Review typically takes 2-3 business days
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                        <i class="fas fa-undo me-1"></i>Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i>Submit for Review
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Previous Submissions -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Your Submissions
                            </h5>
                            <span class="badge bg-primary"><?= count($submissions) ?> total</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($submissions)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No submissions yet</h6>
                                <p class="text-muted small">Your submitted content will appear here</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('M j, Y', strtotime($submission['submitted_at'] ?? $submission['created_at'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= esc(ucfirst($submission['submission_type'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= esc($submission['title']) ?></strong>
                                                    <?php if (!empty($submission['description'])): ?>
                                                        <br><small class="text-muted"><?= esc(substr($submission['description'], 0, 60)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = match($submission['status']) {
                                                        'draft' => 'bg-secondary',
                                                        'submitted' => 'bg-warning',
                                                        'under_review' => 'bg-info',
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        'published' => 'bg-primary',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $statusClass ?> status-badge">
                                                        <?= esc(ucfirst(str_replace('_', ' ', $submission['status']))) ?>
                                                    </span>
                                                    <?php if (!empty($submission['review_notes']) && in_array($submission['status'], ['approved', 'rejected'])): ?>
                                                        <i class="fas fa-comment-alt text-muted ms-1" 
                                                           title="<?= esc($submission['review_notes']) ?>"
                                                           data-bs-toggle="tooltip"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#viewModal<?= $submission['id'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Submission Modals -->
    <?php foreach ($submissions as $submission): ?>
    <div class="modal fade" id="viewModal<?= $submission['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= esc($submission['title']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <strong>Type:</strong> <?= esc(ucfirst($submission['submission_type'])) ?>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong> 
                                <span class="badge <?= match($submission['status']) {
                                    'draft' => 'bg-secondary',
                                    'submitted' => 'bg-warning', 
                                    'under_review' => 'bg-info',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'published' => 'bg-primary',
                                    default => 'bg-secondary'
                                } ?>">
                                    <?= esc(ucfirst(str_replace('_', ' ', $submission['status']))) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($submission['description'])): ?>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p><?= nl2br(esc($submission['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($submission['content'])): ?>
                        <div class="mb-3">
                            <strong>Content:</strong>
                            <div class="bg-light p-3 rounded">
                                <?= nl2br(esc($submission['content'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($submission['review_notes'])): ?>
                        <div class="mb-3">
                            <strong>Review Notes:</strong>
                            <div class="alert alert-info">
                                <?= nl2br(esc($submission['review_notes'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.querySelector('form').reset();
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', () => {
            const notices = document.querySelectorAll('.alert');
            if (notices.length) {
                setTimeout(() => {
                    notices.forEach(n => n.style.display = 'none');
                }, 5000);
            }
        });
    </script>
</body>
</html>