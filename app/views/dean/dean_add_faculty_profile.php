<?php
// app/views/dean/dean_add_faculty_profile.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../lib/Auth.php';
require_once __DIR__ . '/../../models/FacultyProfile.php';
require_once __DIR__ . '/../../models/DeanLogs.php';

// Check authentication using Auth class
if (!Auth::check() || !Auth::is('dean')) {
    header('Location: /adamson-ccit/public/index.php?page=login');
    exit;
}

// Helper function for escaping
if (!function_exists('esc')) {
    function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

$user = Auth::user();
$username = $user['username'] ?? 'Dean';

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $dept = trim($_POST['dept'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $avatar_url = trim($_POST['avatar_url'] ?? '');
        $ordering = (int)($_POST['ordering'] ?? 0);
        
        if (empty($name) || empty($dept) || empty($role)) {
            throw new Exception('Name, Department, and Role are required fields.');
        }
        
        $data = [
            'name' => $name,
            'dept' => $dept,
            'role' => $role,
            'title' => $title,
            'avatar_url' => $avatar_url,
            'avatar_initials' => strtoupper(substr($name, 0, 2)),
            'badges' => '',
            'ordering' => $ordering
        ];
        
        if (FacultyProfile::create($data)) {
            // Log the faculty profile creation (essential CREATE operation)
            DeanLogs::logCreate(
                $user['id'] ?? null,
                'faculty_profiles',
                null, // We don't have the new ID from create method
                "Created faculty profile: {$name} ({$dept})"
            );
            
            header('Location: ?page=dean_manage_faculty_profiles&msg=Faculty profile created successfully');
            exit;
        } else {
            throw new Exception('Failed to create faculty profile.');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Faculty Profile | CCIT Dean</title>
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="/adamson-ccit/public/assets/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include __DIR__ . '/_dean_sidebar.php'; ?>
        
        <main class="admin-main">
            <?php include __DIR__ . '/_dean_header.php'; ?>
            
            <section class="admin-cms-section">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h1 class="h3 mb-0">Add Faculty Profile</h1>
                                    <p class="text-muted">Create a new faculty profile</p>
                                </div>
                                <a href="?page=dean_manage_faculty_profiles" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Profiles
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= esc($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Faculty Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?= esc($_POST['name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dept" class="form-label">Department *</label>
                                        <select class="form-control" id="dept" name="dept" required>
                                            <option value="">Select Department</option>
                                            <option value="Computer Science" <?= ($_POST['dept'] ?? '') === 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                                            <option value="Information Technology" <?= ($_POST['dept'] ?? '') === 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                                            <option value="Information Systems" <?= ($_POST['dept'] ?? '') === 'Information Systems' ? 'selected' : '' ?>>Information Systems</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">Role *</label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="Professor" <?= ($_POST['role'] ?? '') === 'Professor' ? 'selected' : '' ?>>Professor</option>
                                            <option value="Associate Professor" <?= ($_POST['role'] ?? '') === 'Associate Professor' ? 'selected' : '' ?>>Associate Professor</option>
                                            <option value="Assistant Professor" <?= ($_POST['role'] ?? '') === 'Assistant Professor' ? 'selected' : '' ?>>Assistant Professor</option>
                                            <option value="Instructor" <?= ($_POST['role'] ?? '') === 'Instructor' ? 'selected' : '' ?>>Instructor</option>
                                            <option value="Lecturer" <?= ($_POST['role'] ?? '') === 'Lecturer' ? 'selected' : '' ?>>Lecturer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= esc($_POST['title'] ?? '') ?>"
                                               placeholder="e.g., Ph.D., M.S., etc.">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="avatar_url" class="form-label">Profile Image URL</label>
                                        <input type="url" class="form-control" id="avatar_url" name="avatar_url" 
                                               value="<?= esc($_POST['avatar_url'] ?? '') ?>"
                                               placeholder="https://example.com/image.jpg">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ordering" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" id="ordering" name="ordering" 
                                               value="<?= esc($_POST['ordering'] ?? 0) ?>" min="0">
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Profile
                                    </button>
                                    <a href="?page=dean_manage_faculty_profiles" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <script src="/adamson-ccit/public/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>