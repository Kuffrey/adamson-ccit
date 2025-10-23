<?php
// app/views/admin/admin_faculty_profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login'); exit;
}

require_once __DIR__ . '/../../models/FacultyProfilePageSettings.php';
require_once __DIR__ . '/../../models/FacultyProfile.php';

if (!function_exists('esc')) {
    function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('uploadFacultyAvatar')) {
    function uploadFacultyAvatar($fileInput) {
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $file = $_FILES[$fileInput];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes) && !in_array($extension, $allowedExtensions)) {
            throw new Exception('Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.');
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../../public/uploads/faculty/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = 'faculty_' . uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload file.');
        }
        
        // Return relative URL for database storage
        return '/adamson-ccit/public/uploads/faculty/' . $filename;
    }
}

$user = $_SESSION['user'] ?? [];
$username = $user['username'] ?? 'Admin';

$notice = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['settings'])) {
      FacultyProfilePageSettings::updateSettings($_POST['settings']);
      $notice = 'Page settings saved.';
    } elseif (isset($_POST['add_faculty'])) {
      $facultyData = $_POST['faculty'] ?? [];
      
      // Handle avatar upload
      try {
        $uploadedAvatar = uploadFacultyAvatar('faculty_avatar');
        if ($uploadedAvatar) {
          $facultyData['avatar_url'] = $uploadedAvatar;
          error_log("Avatar uploaded successfully: " . $uploadedAvatar);
        } else {
          error_log("No avatar uploaded");
        }
      } catch (Exception $e) {
        error_log("Avatar upload error: " . $e->getMessage());
        $notice = 'Faculty added but avatar upload failed: ' . $e->getMessage();
      }
      
      error_log("Faculty data before create: " . print_r($facultyData, true));
      
      FacultyProfile::create($facultyData);
      if (empty($notice)) {
        $notice = 'Faculty member added successfully.';
      }
    } elseif (isset($_POST['edit_faculty'])) {
      $facultyData = $_POST['faculty'] ?? [];
      $facultyId = (int)$_POST['id'];
      
      // Handle avatar upload for edit
      try {
        $uploadedAvatar = uploadFacultyAvatar('faculty_avatar_edit_' . $facultyId);
        if ($uploadedAvatar) {
          // Get current faculty data to delete old image if needed
          $currentFaculty = FacultyProfile::getAll();
          foreach ($currentFaculty as $f) {
            if ($f['id'] == $facultyId && !empty($f['avatar_url']) && strpos($f['avatar_url'], '/uploads/faculty/') !== false) {
              $oldImagePath = __DIR__ . '/../../../public' . str_replace('/adamson-ccit/public', '', $f['avatar_url']);
              if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
              }
            }
          }
          $facultyData['avatar_url'] = $uploadedAvatar;
        }
      } catch (Exception $e) {
        $notice = 'Faculty updated but avatar upload failed: ' . $e->getMessage();
      }
      
      FacultyProfile::update($facultyId, $facultyData);
      if (empty($notice)) {
        $notice = 'Faculty member updated successfully.';
      }
    } elseif (isset($_POST['delete_faculty'])) {
      $facultyId = (int)$_POST['id'];
      
      // Delete associated image file
      $currentFaculty = FacultyProfile::getAll();
      foreach ($currentFaculty as $f) {
        if ($f['id'] == $facultyId && !empty($f['avatar_url']) && strpos($f['avatar_url'], '/uploads/faculty/') !== false) {
          $imagePath = __DIR__ . '/../../../public' . str_replace('/adamson-ccit/public', '', $f['avatar_url']);
          if (file_exists($imagePath)) {
            unlink($imagePath);
          }
        }
      }
      
      FacultyProfile::delete($facultyId);
      $notice = 'Faculty member deleted.';
    }
  } catch (Throwable $e) {
    $notice = 'Error: ' . $e->getMessage();
  }
}

$settings = FacultyProfilePageSettings::getSettings();
$faculty = FacultyProfile::getAll();
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-faculty.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title">Faculty → Profile</span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title">Faculty Profile Management</h1>
      
      <?php if ($notice): ?>
        <div class="alert alert-<?= str_starts_with($notice, 'Error:') ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
          <?= esc($notice) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Page Settings Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Page Settings</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#pageSettingsCard" aria-expanded="false" aria-controls="pageSettingsCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="pageSettingsCard">
          <div class="card-body">
            <form method="post" autocomplete="off">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Subhero Image URL</label>
                  <input type="text" class="form-control" name="settings[subhero_image_url]" 
                         value="<?= esc($settings['subhero_image_url'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Subhero Lead Text</label>
                  <input type="text" class="form-control" name="settings[subhero_lead]" 
                         value="<?= esc($settings['subhero_lead'] ?? '') ?>">
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Add New Faculty Card -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add New Faculty Member</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#addFacultyCard" aria-expanded="false" aria-controls="addFacultyCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse" id="addFacultyCard">
          <div class="card-body">
            <form method="post" enctype="multipart/form-data" autocomplete="off">
              <input type="hidden" name="add_faculty" value="1">
              <div class="row">
                <div class="col-md-2 mb-3">
                  <label class="form-label">Prefix</label>
                  <select class="form-select" name="faculty[prefix]">
                    <option value="">No Prefix</option>
                    <option value="Dr.">Dr.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Mr.">Mr.</option>
                    <option value="Mrs.">Mrs.</option>
                    <option value="Ms.">Ms.</option>
                    <option value="Miss.">Miss.</option>
                    <option value="Rev.">Rev.</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" name="faculty[first_name]" placeholder="e.g., John Michael">
                </div>
                <div class="col-md-2 mb-3">
                  <label class="form-label">Middle Initial</label>
                  <input type="text" class="form-control" name="faculty[middle_initial]" maxlength="10" placeholder="e.g., A. or Antonio">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Surname (Last Name) *</label>
                  <input type="text" class="form-control" name="faculty[surname]" required placeholder="e.g., Dela Cruz">
                </div>
                <div class="col-md-2 mb-3">
                  <label class="form-label">Suffix</label>
                  <select class="form-select" name="faculty[suffix]">
                    <option value="">No Suffix</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                    <option value="PhD">PhD</option>
                    <option value="MIT">MIT</option>
                    <option value="MSIT">MSIT</option>
                    <option value="CPA">CPA</option>
                    <option value="Esq.">Esq.</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Full Name (Auto-generated)</label>
                  <input type="text" class="form-control" name="faculty[name]" readonly placeholder="Will be generated from all name components">
                  <small class="form-text text-muted">This field is automatically populated from the name components above.</small>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Department</label>
                  <select class="form-select" name="faculty[dept]" required>
                    <option value="">Select Department</option>
                    <option value="admin">Administration</option>
                    <option value="itis">IT&IS</option>
                    <option value="cs">CS</option>
                    <option value="mit">MIT</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Role</label>
                  <select class="form-select" name="faculty[role]" required>
                    <option value="">Select Role</option>
                    <option value="dean">Dean</option>
                    <option value="chair">Chairperson</option>
                    <option value="full">Full-Time Faculty</option>
                    <option value="part">Part-Time Faculty</option>
                    <option value="lecturer">Special Lecturer</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar Initials (Auto-generated)</label>
                  <input type="text" class="form-control" name="faculty[avatar_initials]" 
                         maxlength="4" placeholder="e.g., JD" readonly>
                  <small class="form-text text-muted">Auto-generated from name components.</small>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Profile Image Upload</label>
                  <div class="input-group">
                    <input type="file" class="form-control" name="faculty_avatar" id="avatarUpload" accept="image/*" 
                           onchange="previewAvatar(this, 'avatarPreview')">
                    <button type="button" class="btn btn-outline-secondary" onclick="clearAvatar('avatarUpload', 'avatarPreview')">
                      <i class="fas fa-times"></i> Clear
                    </button>
                  </div>
                  <div class="mt-2">
                    <img id="avatarPreview" src="" alt="Avatar Preview" 
                         style="max-width: 120px; max-height: 120px; border-radius: 50%; display: none; border: 3px solid #dee2e6;">
                  </div>
                  <small class="form-text text-muted">Upload an image file or provide a URL above. Recommended: square image, at least 200x200px.</small>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar URL (Optional)</label>
                  <input type="url" class="form-control" name="faculty[avatar_url]" 
                         placeholder="Link to profile photo">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Badges</label>
                  <input type="text" class="form-control" name="faculty[badges]" 
                         placeholder="e.g., Administration,PhD,Certified">
                  <small class="form-text text-muted">Comma-separated tags/badges for this faculty member.</small>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Faculty Member
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Existing Faculty Card -->
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Existing Faculty Members (<?= count($faculty) ?>)</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#facultyListCard" aria-expanded="true" aria-controls="facultyListCard">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
        
        <div class="collapse show" id="facultyListCard">
          <div class="card-body">
            <?php if (empty($faculty)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No faculty members added yet.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>Avatar</th>
                      <th>Name (Role-Based Ordering)</th>
                      <th>Department</th>
                      <th>Role & Order</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($faculty as $f): ?>
                      <tr>
                        <td>
                          <?php if (!empty($f['avatar_url'])): ?>
                            <img src="<?= esc($f['avatar_url']) ?>" alt="Avatar" 
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;">
                          <?php else: ?>
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #6c757d, #495057); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 2px solid #dee2e6;">
                              <?= esc($f['avatar_initials'] ?? substr($f['name'], 0, 2)) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <strong><?= esc($f['name']) ?></strong>
                          <?php if (!empty($f['surname'])): ?>
                            <br><small class="text-muted">Surname: <?= esc($f['surname']) ?></small>
                          <?php endif; ?>
                          <?php if (!empty($f['badges'])): ?>
                            <br><small class="text-muted"><?= esc($f['badges']) ?></small>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= esc(ucfirst($f['dept'])) ?></span></td>
                        <td>
                          <span class="badge bg-<?= $f['role'] === 'dean' ? 'danger' : ($f['role'] === 'chair' ? 'warning' : 'info') ?>">
                            <?= esc(ucfirst($f['role'])) ?>
                          </span>
                          <br><small class="text-muted">Order: <?= esc($f['role_order'] ?? 'N/A') ?></small>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="inline-form" onsubmit="return confirm('Delete this faculty member?')">
                            <input type="hidden" name="delete_faculty" value="1">
                            <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i>
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
        </div>
      </div>

      <!-- Edit Modals -->
      <?php foreach ($faculty as $f): ?>
        <div class="modal fade" id="editModal<?= $f['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit Faculty Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                  <input type="hidden" name="edit_faculty" value="1">
                  <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                  <div class="row">
                    <div class="col-md-2">
                      <label class="form-label">Prefix</label>
                      <select class="form-select" name="faculty[prefix]">
                        <option value="">No Prefix</option>
                        <option value="Dr." <?= ($f['prefix'] ?? '') === 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                        <option value="Prof." <?= ($f['prefix'] ?? '') === 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                        <option value="Mr." <?= ($f['prefix'] ?? '') === 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                        <option value="Mrs." <?= ($f['prefix'] ?? '') === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                        <option value="Ms." <?= ($f['prefix'] ?? '') === 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                        <option value="Miss." <?= ($f['prefix'] ?? '') === 'Miss.' ? 'selected' : '' ?>>Miss.</option>
                        <option value="Rev." <?= ($f['prefix'] ?? '') === 'Rev.' ? 'selected' : '' ?>>Rev.</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">First Name</label>
                      <input type="text" class="form-control" name="faculty[first_name]" value="<?= esc($f['first_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Middle Initial</label>
                      <input type="text" class="form-control" name="faculty[middle_initial]" value="<?= esc($f['middle_initial'] ?? '') ?>" maxlength="10">
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">Surname (Last Name) *</label>
                      <input type="text" class="form-control" name="faculty[surname]" value="<?= esc($f['surname'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Suffix</label>
                      <select class="form-select" name="faculty[suffix]">
                        <option value="">No Suffix</option>
                        <option value="Jr." <?= ($f['suffix'] ?? '') === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                        <option value="Sr." <?= ($f['suffix'] ?? '') === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                        <option value="III" <?= ($f['suffix'] ?? '') === 'III' ? 'selected' : '' ?>>III</option>
                        <option value="IV" <?= ($f['suffix'] ?? '') === 'IV' ? 'selected' : '' ?>>IV</option>
                        <option value="V" <?= ($f['suffix'] ?? '') === 'V' ? 'selected' : '' ?>>V</option>
                        <option value="PhD" <?= ($f['suffix'] ?? '') === 'PhD' ? 'selected' : '' ?>>PhD</option>
                        <option value="MIT" <?= ($f['suffix'] ?? '') === 'MIT' ? 'selected' : '' ?>>MIT</option>
                        <option value="MSIT" <?= ($f['suffix'] ?? '') === 'MSIT' ? 'selected' : '' ?>>MSIT</option>
                        <option value="CPA" <?= ($f['suffix'] ?? '') === 'CPA' ? 'selected' : '' ?>>CPA</option>
                        <option value="Esq." <?= ($f['suffix'] ?? '') === 'Esq.' ? 'selected' : '' ?>>Esq.</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <label class="form-label">Full Name (Auto-generated)</label>
                      <input type="text" class="form-control" name="faculty[name]" value="<?= esc($f['name']) ?>" readonly>
                      <small class="form-text text-muted">This field is automatically populated from the name components above.</small>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Department</label>
                      <select class="form-select" name="faculty[dept]" required>
                        <option value="admin" <?= $f['dept'] === 'admin' ? 'selected' : '' ?>>Administration</option>
                        <option value="itis" <?= $f['dept'] === 'itis' ? 'selected' : '' ?>>IT&IS</option>
                        <option value="cs" <?= $f['dept'] === 'cs' ? 'selected' : '' ?>>CS</option>
                        <option value="mit" <?= $f['dept'] === 'mit' ? 'selected' : '' ?>>MIT</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Role</label>
                      <select class="form-select" name="faculty[role]" required>
                        <option value="dean" <?= $f['role'] === 'dean' ? 'selected' : '' ?>>Dean</option>
                        <option value="chair" <?= $f['role'] === 'chair' ? 'selected' : '' ?>>Chairperson</option>
                        <option value="full" <?= $f['role'] === 'full' ? 'selected' : '' ?>>Full-Time Faculty</option>
                        <option value="part" <?= $f['role'] === 'part' ? 'selected' : '' ?>>Part-Time Faculty</option>
                        <option value="lecturer" <?= $f['role'] === 'lecturer' ? 'selected' : '' ?>>Special Lecturer</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Avatar Initials (Auto-generated)</label>
                      <input type="text" class="form-control" name="faculty[avatar_initials]" value="<?= esc($f['avatar_initials'] ?? '') ?>" maxlength="4" readonly>
                      <small class="form-text text-muted">Auto-generated from name components.</small>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <label class="form-label">Update Profile Image</label>
                      <div class="input-group">
                        <input type="file" class="form-control" name="faculty_avatar_edit_<?= $f['id'] ?>" id="avatarUpload<?= $f['id'] ?>" accept="image/*" 
                               onchange="previewAvatar(this, 'avatarPreview<?= $f['id'] ?>')">
                        <button type="button" class="btn btn-outline-secondary" 
                                onclick="clearAvatar('avatarUpload<?= $f['id'] ?>', 'avatarPreview<?= $f['id'] ?>')">
                          <i class="fas fa-times"></i> Clear
                        </button>
                      </div>
                      <div class="mt-2 d-flex align-items-center gap-3">
                        <?php if (!empty($f['avatar_url'])): ?>
                          <div>
                            <small class="text-muted d-block">Current Avatar:</small>
                            <img src="<?= esc($f['avatar_url']) ?>" alt="Current Avatar" 
                                 style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;">
                          </div>
                        <?php endif; ?>
                        <div>
                          <small class="text-muted d-block">New Preview:</small>
                          <img id="avatarPreview<?= $f['id'] ?>" src="" alt="New Avatar Preview" 
                               style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; display: none; border: 2px solid #dee2e6;">
                        </div>
                      </div>
                      <small class="form-text text-muted">Upload a new image file or update the URL above.</small>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Avatar URL (Optional)</label>
                      <input type="url" class="form-control" name="faculty[avatar_url]" value="<?= esc($f['avatar_url']) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Badges</label>
                      <input type="text" class="form-control" name="faculty[badges]" value="<?= esc($f['badges'] ?? '') ?>">
                      <small class="form-text text-muted">Comma-separated tags/badges for this faculty member.</small>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image preview and management functions
function previewAvatar(input, previewId) {
  const preview = document.getElementById(previewId);
  const file = input.files[0];
  
  if (file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must be less than 5MB.');
      input.value = '';
      return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const fileExtension = file.name.toLowerCase().split('.').pop();
    
    if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
      alert('Please upload a JPEG, PNG, GIF, or WebP image.');
      input.value = '';
      return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
      
      // Clear the avatar URL field when file is uploaded
      const urlField = input.closest('form').querySelector('input[name$="[avatar_url]"]');
      if (urlField) {
        urlField.value = '';
      }
    };
    reader.readAsDataURL(file);
  }
}

function clearAvatar(inputId, previewId) {
  const input = document.getElementById(inputId);
  const preview = document.getElementById(previewId);
  
  input.value = '';
  preview.src = '';
  preview.style.display = 'none';
  
  // Clear the avatar URL field if it exists
  const urlField = input.closest('form').querySelector('input[name$="[avatar_url]"]');
  if (urlField) {
    urlField.value = '';
  }
}

// Auto-generate full name and initials
document.addEventListener('DOMContentLoaded', function() {
  // Function to update full name from components
  function updateFullName(form) {
    const prefix = form.querySelector('select[name$="[prefix]"]')?.value || '';
    const firstName = form.querySelector('input[name$="[first_name]"]')?.value || '';
    const middleInitial = form.querySelector('input[name$="[middle_initial]"]')?.value || '';
    const surname = form.querySelector('input[name$="[surname]"]')?.value || '';
    const suffix = form.querySelector('select[name$="[suffix]"]')?.value || '';
    const nameField = form.querySelector('input[name$="[name]"]');
    
    if (nameField) {
      // Build name parts
      const nameParts = [prefix, firstName, middleInitial, surname].filter(part => part.trim());
      let fullName = nameParts.join(' ');
      
      // Handle suffix with proper comma rules
      if (suffix.trim()) {
        // Generational suffixes that don't need commas
        const generationalSuffixes = ['Jr.', 'Jr', 'Sr.', 'Sr', 'II', 'III', 'IV', 'V', '2nd', '3rd', '4th', '5th'];
        
        if (generationalSuffixes.includes(suffix)) {
          fullName += ' ' + suffix;
        } else {
          // Academic titles and other suffixes get commas
          fullName += ', ' + suffix;
        }
      }
      
      nameField.value = fullName;
    }
  }
  
  // Function to update initials from name components
  function updateInitials(form) {
    const firstName = form.querySelector('input[name$="[first_name]"]')?.value || '';
    const surname = form.querySelector('input[name$="[surname]"]')?.value || '';
    const initialsField = form.querySelector('input[name$="[avatar_initials]"]');
    
    if (initialsField) {
      const firstInitial = firstName.charAt(0).toUpperCase();
      const lastInitial = surname.charAt(0).toUpperCase();
      
      // Use only first and last initials
      const initials = firstInitial + lastInitial;
      initialsField.value = initials.substring(0, 4);
    }
  }
  
  // Add event listeners to all forms
  document.querySelectorAll('form').forEach(form => {
    // Initialize initials for existing forms
    updateInitials(form);
    
    // Listen for changes to name components
    ['select[name$="[prefix]"]', 'input[name$="[first_name]"]', 'input[name$="[middle_initial]"]', 'input[name$="[surname]"]', 'select[name$="[suffix]"]'].forEach(selector => {
      const element = form.querySelector(selector);
      if (element) {
        element.addEventListener('input', () => {
          updateFullName(form);
          updateInitials(form);
        });
        element.addEventListener('change', () => {
          updateFullName(form);
          updateInitials(form);
        });
      }
    });
  });
});

// Auto-generate badges from department and role
function updateBadges(form) {
    const deptField = form.querySelector('select[name$="[dept]"], select[name="faculty[dept]"]');
    const roleField = form.querySelector('select[name$="[role]"], select[name="faculty[role]"]');
    const badgesField = form.querySelector('input[name$="[badges]"], input[name="faculty[badges]"]');

    if (deptField && roleField && badgesField) {
        const dept = deptField.options[deptField.selectedIndex].text.trim();
        const role = roleField.options[roleField.selectedIndex].text.trim();

        // Combine department and role into badges
        const badges = [dept, role].filter(Boolean).join(', ');
        badgesField.value = badges;
    }
}

// Extend badge updater to handle edit modals
function initializeBadgeUpdater() {
    document.querySelectorAll('form').forEach(form => {
        const deptField = form.querySelector('select[name$="[dept]"], select[name="faculty[dept]"]');
        const roleField = form.querySelector('select[name$="[role]"], select[name="faculty[role]"]');
        const badgesField = form.querySelector('input[name$="[badges]"], input[name="faculty[badges]"]');

        if (deptField) {
            deptField.addEventListener('change', () => updateBadges(form));
        }

        if (roleField) {
            roleField.addEventListener('change', () => updateBadges(form));
        }

        // Initialize badges on page load
        if (deptField && roleField && badgesField) {
            updateBadges(form);
        }
    });
}

document.addEventListener('DOMContentLoaded', initializeBadgeUpdater);
</script>