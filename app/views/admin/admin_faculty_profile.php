<?php
// app/views/admin/admin_faculty_profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || !in_array(($_SESSION['user']['role'] ?? ''), ['admin','dean'], true)) {
  header('Location: ?page=login_admin'); exit;
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
        
        // Log detailed file information for debugging
        error_log("=== FILE UPLOAD DEBUG ===");
        error_log("File name: " . $file['name']);
        error_log("File type (MIME): " . $file['type']);
        error_log("File size: " . $file['size']);
        error_log("File error: " . $file['error']);
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-adobe-dng', 'image/dng', 'application/octet-stream'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'dng'];
        $maxSize = 20 * 1024 * 1024; // 20MB
        
        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        error_log("File extension: " . $extension);
        
        // Validate file type - check both MIME type and extension for DNG files
        $mimeTypeValid = in_array($file['type'], $allowedTypes);
        $extensionValid = in_array($extension, $allowedExtensions);
        
        error_log("MIME type valid: " . ($mimeTypeValid ? 'YES' : 'NO'));
        error_log("Extension valid: " . ($extensionValid ? 'YES' : 'NO'));
        
        if (!$mimeTypeValid && !$extensionValid) {
            error_log("File upload rejected - Type: " . $file['type'] . ", Extension: " . $extension);
            throw new Exception('Invalid file type. Please upload a JPEG, PNG, GIF, WebP, or DNG image.');
        }
        
        error_log("File validation passed!");
        error_log("=========================");
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/adamson-ccit/public/uploads/faculty/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Debug: Log the actual upload directory path
        error_log("Upload directory: " . $uploadDir);
        error_log("Upload directory realpath: " . realpath(dirname($uploadDir)));
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'faculty_' . uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Debug: Log the file path and check if tmp file exists
        error_log("Upload file path: " . $filepath);
        error_log("Temp file exists: " . (file_exists($file['tmp_name']) ? 'yes' : 'no'));
        error_log("Temp file path: " . $file['tmp_name']);
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            error_log("move_uploaded_file failed. Last error: " . error_get_last()['message']);
            throw new Exception('Failed to upload file.');
        }
        
        // Verify file was actually created
        if (!file_exists($filepath)) {
            error_log("File was not created at: " . $filepath);
            throw new Exception('File upload verification failed.');
        }
        
        error_log("File successfully uploaded to: " . $filepath);
        
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
              $oldImagePath = __DIR__ . '/../../../../public' . $f['avatar_url'];
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
          $imagePath = __DIR__ . '/../../../../public' . $f['avatar_url'];
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
                <div class="col-md-6 mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" name="faculty[name]" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Department</label>
                  <select class="form-select" name="faculty[dept]" required>
                    <option value="">Select Department</option>
                    <option value="admin">Administration</option>
                    <option value="itis">IT&IS</option>
                    <option value="cs">CS</option>
                  </select>
                </div>
              </div>
              <div class="row">
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
                <div class="col-md-6 mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" class="form-control" name="faculty[title]" 
                         placeholder="e.g., Professor, PhD in Computer Science" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar URL</label>
                  <input type="url" class="form-control" name="faculty[avatar_url]" 
                         placeholder="Link to profile photo">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Avatar Initials</label>
                  <input type="text" class="form-control" name="faculty[avatar_initials]" 
                         maxlength="4" placeholder="e.g., JD">
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Profile Image Upload</label>
                  <div class="input-group">
                    <input type="file" class="form-control" name="faculty_avatar" id="avatarUpload" accept="image/*,.dng" 
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
                  <label class="form-label">Badges</label>
                  <input type="text" class="form-control" name="faculty[badges]" 
                         placeholder="e.g., Administration,PhD,Certified">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ordering</label>
                  <input type="number" class="form-control" name="faculty[ordering]" 
                         value="0" min="0">
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
                      <th>Name</th>
                      <th>Department</th>
                      <th>Role</th>
                      <th>Title</th>
                      <th>Order</th>
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
                          <?php if ($f['badges']): ?>
                            <br><small class="text-muted"><?= esc($f['badges']) ?></small>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= esc(ucfirst($f['dept'])) ?></span></td>
                        <td><?= esc($f['role']) ?></td>
                        <td><?= esc($f['title']) ?></td>
                        <td><?= esc($f['ordering']) ?></td>
                        <td>
                          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="post" class="inline-form" onsubmit="return confirm('Delete this faculty member?')">>
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
                    <div class="col-md-6">
                      <label class="form-label">Name</label>
                      <input type="text" class="form-control" name="faculty[name]" value="<?= esc($f['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Department</label>
                      <select class="form-select" name="faculty[dept]" required>
                        <option value="admin" <?= $f['dept'] === 'admin' ? 'selected' : '' ?>>Administration</option>
                        <option value="itis" <?= $f['dept'] === 'itis' ? 'selected' : '' ?>>IT&IS</option>
                        <option value="cs" <?= $f['dept'] === 'cs' ? 'selected' : '' ?>>CS</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-3">
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
                    <div class="col-md-6">
                      <label class="form-label">Title</label>
                      <input type="text" class="form-control" name="faculty[title]" value="<?= esc($f['title']) ?>" required>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-6">
                      <label class="form-label">Avatar URL</label>
                      <input type="url" class="form-control" name="faculty[avatar_url]" value="<?= esc($f['avatar_url']) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Avatar Initials</label>
                      <input type="text" class="form-control" name="faculty[avatar_initials]" value="<?= esc($f['avatar_initials']) ?>" maxlength="4">
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-md-12">
                      <label class="form-label">Update Profile Image</label>
                      <div class="input-group">
                        <input type="file" class="form-control" name="faculty_avatar_edit_<?= $f['id'] ?>" id="avatarUpload<?= $f['id'] ?>" accept="image/*,.dng" 
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
                      <label class="form-label">Badges</label>
                      <input type="text" class="form-control" name="faculty[badges]" value="<?= esc($f['badges']) ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Ordering</label>
                      <input type="number" class="form-control" name="faculty[ordering]" value="<?= esc($f['ordering']) ?>" min="0">
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
    // Validate file size (20MB)
    if (file.size > 20 * 1024 * 1024) {
      alert('File size must be less than 20MB.');
      input.value = '';
      return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-adobe-dng', 'image/dng', 'application/octet-stream'];
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'dng'];
    const fileExtension = file.name.toLowerCase().split('.').pop();
    
    if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
      alert('Please upload a JPEG, PNG, GIF, WebP, or DNG image.');
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

// Auto-generate initials from name
document.addEventListener('DOMContentLoaded', function() {
  const nameInputs = document.querySelectorAll('input[name$="[name]"]');
  nameInputs.forEach(nameInput => {
    nameInput.addEventListener('input', function() {
      const initialsField = this.closest('form').querySelector('input[name$="[avatar_initials]"]');
      if (initialsField && !initialsField.value) {
        const name = this.value.trim();
        if (name) {
          const words = name.split(' ');
          const initials = words.map(word => word.charAt(0).toUpperCase()).slice(0, 2).join('');
          initialsField.value = initials;
        }
      }
    });
  });
});
</script>