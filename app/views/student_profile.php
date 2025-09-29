<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
require_once __DIR__ . '/../../app/models/Model.php';
require_once __DIR__ . '/../../app/models/StudentProfile.php';

$user = Auth::user();

// Get student profile data from database
$studentProfile = null;
$studentId = null;
$certStats = ['total' => 0, 'active' => 0, 'expired' => 0];

if (!empty($user['id'])) {
    $studentProfile = StudentProfile::getByUserId($user['id']);
    $studentId = $user['id'];
    $certStats = StudentProfile::getCertificationStats($user['id']);
}

// Get companies for dropdown (only active)
class _DBX extends Model { public function d(){ return parent::db(); } }
$_db = (new _DBX())->d();
$companies = $_db->query("SELECT id, name FROM companies ORDER BY name")
                 ->fetchAll(PDO::FETCH_ASSOC);

// Get existing certifications for this student
$certs = [];
if ($studentId) {
    $q = $_db->prepare("
        SELECT c.*, co.name AS company_name
        FROM student_licenses c
        JOIN companies co ON co.id = c.company_id
        WHERE c.user_id = ?
        ORDER BY COALESCE(c.expire_year,9999), COALESCE(c.expire_month,12),
                 c.issue_year DESC, c.issue_month DESC
    ");
    $q->execute([$studentId]);
    $certs = $q->fetchAll(PDO::FETCH_ASSOC);
}

// Helper functions
$fullName = StudentProfile::getFullName($studentProfile);
$avatarInitials = StudentProfile::getAvatarInitials($studentProfile);
$program = $studentProfile['program'] ?? 'CCIT Student';
$yearLevel = $studentProfile['year_level'] ?? '';
$bio = $studentProfile['bio'] ?? 'Passionate Computer Science student at Adamson University, focused on building technical expertise and professional certifications in the technology field.';
$email = $studentProfile['email'] ?? ($user['username'] . '@student.adamson.edu.ph');

?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/student-profile.css">

<main class="page-student">
  <!-- Toast Notifications -->
  <?php if (!empty($_GET['saved']) || !empty($_GET['deleted']) || !empty($_GET['error']) || !empty($_GET['password_changed'])): ?>
    <div class="toast <?= !empty($_GET['error']) ? 'toast--danger' : (!empty($_GET['deleted']) ? 'toast--danger' : 'toast--success') ?>">
      <?php if (!empty($_GET['error'])): ?>
        <?= htmlspecialchars($_GET['error']) ?>
      <?php elseif (!empty($_GET['deleted'])): ?>
        Certification deleted.
      <?php elseif (!empty($_GET['password_changed'])): ?>
        Password changed successfully!
      <?php elseif ($_GET['saved'] == '1'): ?>
        Profile updated successfully!
      <?php else: ?>
        Certification saved.
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
      <div class="profile-header-content">
        <div class="profile-avatar">
          <div class="avatar-circle">
            <?= htmlspecialchars($avatarInitials) ?>
          </div>
        </div>
        <div class="profile-info">
          <h1 class="profile-name"><?= htmlspecialchars($fullName) ?></h1>
          <p class="profile-title"><?= htmlspecialchars($program) ?></p>
          <?php if ($yearLevel): ?>
            <p class="profile-year"><?= htmlspecialchars($yearLevel) ?></p>
          <?php endif; ?>
          <p class="profile-location">📍 Adamson University</p>
          <div class="profile-stats">
            <span class="stat-item"><?= $certStats['total'] ?> Certification<?= $certStats['total'] !== 1 ? 's' : '' ?></span>
            <span class="stat-divider">•</span>
            <span class="stat-item"><?= $certStats['active'] ?> Active</span>
            <?php if ($certStats['expired'] > 0): ?>
              <span class="stat-divider">•</span>
              <span class="stat-item stat-expired"><?= $certStats['expired'] ?> Expired</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="profile-content">
      <!-- Left Column -->
      <div class="profile-sidebar">
        <!-- About Section -->
        <div class="profile-card">
          <div class="card-header-flex">
            <h3 class="card-title">About</h3>
            <div class="card-actions">
              <button class="edit-profile-btn" onclick="openEditProfileModal()" title="Edit Profile">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="edit-profile-btn" onclick="openPasswordModal()" title="Change Password">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <circle cx="12" cy="16" r="1"></circle>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </button>
            </div>
          </div>
          <p class="about-text">
            <?= htmlspecialchars($bio ?: 'No bio added yet. Click edit to add your professional summary.') ?>
          </p>
          <?php if (!empty($studentProfile['skills'])): ?>
            <div class="skills-section">
              <h4 class="skills-title">Skills</h4>
              <div class="skills-tags">
                <?php 
                $skills = explode(',', $studentProfile['skills']);
                foreach ($skills as $skill): 
                  $skill = trim($skill);
                  if ($skill):
                ?>
                  <span class="skill-tag"><?= htmlspecialchars($skill) ?></span>
                <?php 
                  endif;
                endforeach; 
                ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Career Pathway Quick Access -->
        <div class="profile-card">
          <h3 class="card-title">Career Development</h3>
          <div class="career-item">
            <div class="career-icon">🎯</div>
            <div class="career-content">
              <h4>Career Pathway Generator</h4>
              <p>Discover your ideal career path</p>
              <a href="/adamson-ccit/public/index.php?page=career_pathway_generator" class="career-link">
                Take Assessment →
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Main Content -->
      <div class="profile-main">
        <!-- Licenses & Certifications Section -->
        <div class="profile-card certifications-section">
          <div class="card-header-flex">
            <div class="card-header-left">
              <h3 class="card-title">Licenses & Certifications</h3>
              <p class="card-subtitle">
                <?= $certStats['total'] ?> certification<?= $certStats['total'] !== 1 ? 's' : '' ?>
                <?php if ($certStats['active'] > 0): ?>
                  • <?= $certStats['active'] ?> active
                <?php endif; ?>
                <?php if ($certStats['expired'] > 0): ?>
                  • <span class="expired-count"><?= $certStats['expired'] ?> expired</span>
                <?php endif; ?>
              </p>
            </div>
            <div class="card-actions">
              <div class="view-toggle">
                <button class="view-btn active" data-view="compact" title="Compact View">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                  </svg>
                </button>
                <button class="view-btn" data-view="card" title="Card View">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 4H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM10 15H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2zM21 4h-6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM21 15h-6c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h6c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2z"/>
                  </svg>
                </button>
              </div>
              <button class="add-cert-btn" data-open-cert-modal>
                <span class="add-icon">+</span>
                Add certification
              </button>
            </div>
          </div>

          <?php if (empty($certs)): ?>
            <div class="empty-state-modern">
              <div class="empty-icon">🎓</div>
              <h4>Showcase your expertise</h4>
              <p>Add professional certifications to highlight your skills and stand out to potential employers.</p>
              <button class="btn-primary" data-open-cert-modal>Add your first certification</button>
            </div>
          <?php else: ?>
            <!-- Compact List View (Default) -->
            <div class="certifications-list compact-view" id="compact-view">
              <?php foreach ($certs as $c): ?>
                <?php
                  $isExpired = $c['expires'] && $c['expire_year'] && $c['expire_month'] && 
                               ($c['expire_year'] < date('Y') || 
                                ($c['expire_year'] == date('Y') && $c['expire_month'] < date('n')));
                  $issueDate = ($c['issue_month'] && $c['issue_year']) 
                    ? date("M Y", mktime(0,0,0,$c['issue_month'],1,$c['issue_year'])) 
                    : 'N/A';
                  $expireDate = ($c['expires'] && $c['expire_month'] && $c['expire_year']) 
                    ? date("M Y", mktime(0,0,0,$c['expire_month'],1,$c['expire_year']))
                    : null;
                ?>
                <div class="cert-list-item <?= $isExpired ? 'expired' : '' ?>">
                  <div class="cert-icon-small">🎓</div>
                  <div class="cert-content-compact">
                    <div class="cert-header-compact">
                      <h4 class="cert-name-compact"><?= htmlspecialchars($c['name']) ?></h4>
                      <div class="cert-status">
                        <?php if ($isExpired): ?>
                          <span class="status-badge expired">Expired</span>
                        <?php elseif (!$c['expires']): ?>
                          <span class="status-badge permanent">No expiry</span>
                        <?php else: ?>
                          <span class="status-badge active">Active</span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <p class="cert-issuer-compact"><?= htmlspecialchars($c['company_name']) ?></p>
                    <div class="cert-meta-compact">
                      <span class="meta-item">Issued <?= $issueDate ?></span>
                      <?php if ($expireDate): ?>
                        <span class="meta-divider">•</span>
                        <span class="meta-item <?= $isExpired ? 'expired-text' : '' ?>">
                          <?= $isExpired ? 'Expired' : 'Expires' ?> <?= $expireDate ?>
                        </span>
                      <?php endif; ?>
                      <?php if (!empty($c['credential_id'])): ?>
                        <span class="meta-divider">•</span>
                        <span class="meta-item credential-id">ID: <?= htmlspecialchars($c['credential_id']) ?></span>
                      <?php endif; ?>
                    </div>
                    <?php if (!empty($c['credential_url'])): ?>
                      <div class="cert-actions-compact">
                        <a href="<?= htmlspecialchars($c['credential_url']) ?>" target="_blank" class="action-link credential-link">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15,3 21,3 21,9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                          </svg>
                          Show credential
                        </a>
                      </div>
                    <?php endif; ?>
                  </div>
                  <!-- Hover Action Buttons -->
                  <div class="cert-hover-actions">
                    <button 
                      class="action-icon-btn edit-btn" 
                      onclick='editCert(<?= json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'
                      title="Edit certification">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                    <button 
                      class="action-icon-btn delete-btn" 
                      onclick='deleteCert(<?= (int)$c['id'] ?>, "<?= htmlspecialchars($c['name']) ?>")'
                      title="Delete certification">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3,6 5,6 21,6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                      </svg>
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Card Grid View -->
            <div class="certifications-grid card-view" id="card-view" style="display: none;">
              <?php foreach ($certs as $c): ?>
                <?php
                  $isExpired = $c['expires'] && $c['expire_year'] && $c['expire_month'] && 
                               ($c['expire_year'] < date('Y') || 
                                ($c['expire_year'] == date('Y') && $c['expire_month'] < date('n')));
                  $issueDate = ($c['issue_month'] && $c['issue_year']) 
                    ? date("M Y", mktime(0,0,0,$c['issue_month'],1,$c['issue_year'])) 
                    : 'N/A';
                  $expireDate = ($c['expires'] && $c['expire_month'] && $c['expire_year']) 
                    ? date("M Y", mktime(0,0,0,$c['expire_month'],1,$c['expire_year']))
                    : null;
                ?>
                <div class="certification-card <?= $isExpired ? 'expired' : '' ?>">
                  <div class="cert-header">
                    <div class="cert-logo">🎓</div>
                    <!-- Hover Action Buttons -->
                    <div class="cert-hover-actions">
                      <button 
                        class="action-icon-btn edit-btn" 
                        onclick='editCert(<?= json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'
                        title="Edit certification">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                      </button>
                      <button 
                        class="action-icon-btn delete-btn" 
                        onclick='deleteCert(<?= (int)$c['id'] ?>, "<?= htmlspecialchars($c['name']) ?>")'
                        title="Delete certification">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="3,6 5,6 21,6"></polyline>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2h4a2 2 0 0 1 2 2v2"></path>
                          <line x1="10" y1="11" x2="10" y2="17"></line>
                          <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <div class="cert-content">
                    <h4 class="cert-name"><?= htmlspecialchars($c['name']) ?></h4>
                    <p class="cert-issuer"><?= htmlspecialchars($c['company_name']) ?></p>
                    
                    <div class="cert-dates">
                      Issued <?= $issueDate ?>
                      <?php if ($expireDate): ?>
                        • <?= $isExpired ? 'Expired' : 'Expires' ?> <?= $expireDate ?>
                      <?php else: ?>
                        • No expiration date
                      <?php endif; ?>
                    </div>

                    <?php if (!empty($c['credential_id'])): ?>
                      <div class="cert-credential-id">
                        Credential ID: <?= htmlspecialchars($c['credential_id']) ?>
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($c['credential_url'])): ?>
                      <div class="cert-actions-bottom">
                        <a href="<?= htmlspecialchars($c['credential_url']) ?>" target="_blank" class="verify-cert-btn">
                          Show credential
                        </a>
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($c['visibility'])): ?>
                      <div class="cert-visibility">
                        <span class="visibility-badge <?= $c['visibility'] === 'private' ? 'private' : 'public' ?>">
                          <?= $c['visibility'] === 'private' ? '👁️ Only me' : '🌐 Public' ?>
                        </span>
                      </div>
                    <?php endif; ?>
                    
                    <?php if ($isExpired): ?>
                      <div class="expired-overlay">
                        <span class="expired-badge">Expired</span>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

    </div>
</div>

<!-- Profile Edit Modal -->
<div id="profileModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Profile</h3>
      <button type="button" class="modal-close" onclick="closeProfileModal()">✕</button>
    </div>
    <form id="profileForm" action="/adamson-ccit/public/index.php?page=student_profile_save" method="POST">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

      <label>Full Name</label>
      <div class="readonly-field">
        <?= htmlspecialchars($fullName) ?>
      </div>

      <label>Program*</label>
      <input type="text" name="program" value="<?= htmlspecialchars($studentProfile['program'] ?? '') ?>" required>

      <label>Year Level*</label>
      <select name="year_level" required>
   <option value="">Select Year</option>
   <option value="1st Year" <?= ($studentProfile['year_level'] ?? '') === '1st Year' ? 'selected' : '' ?>>1st Year</option>
   <option value="2nd Year" <?= ($studentProfile['year_level'] ?? '') === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
   <option value="3rd Year" <?= ($studentProfile['year_level'] ?? '') === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
   <option value="4th Year" <?= ($studentProfile['year_level'] ?? '') === '4th Year' ? 'selected' : '' ?>>4th Year</option>
   <option value="Graduate" <?= ($studentProfile['year_level'] ?? '') === 'Graduate' ? 'selected' : '' ?>>Graduate</option>
      </select>

      <label>About/Bio</label>
      <textarea name="bio" rows="4" placeholder="Tell us about yourself, your interests, and career goals..."><?= htmlspecialchars($studentProfile['bio'] ?? '') ?></textarea>

      <label>Skills (comma-separated)</label>
      <input type="text" name="skills" value="<?= htmlspecialchars($studentProfile['skills'] ?? '') ?>" placeholder="Programming, Web Development, Database Management">

      <div class="modal-actions">
   <button type="button" class="btn btn--outline" onclick="closeProfileModal()">Cancel</button>
   <button type="submit" class="btn btn--solid">Save Profile</button>
      </div>
    </form>
  </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Change Password</h3>
      <button type="button" class="modal-close" onclick="closePasswordModal()">✕</button>
    </div>
    <form id="passwordForm" action="/adamson-ccit/public/index.php?page=change_password" method="POST">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

      <div class="form-group">
        <label>Current Password*</label>
        <input type="password" name="current_password" required autocomplete="current-password">
      </div>

      <div class="form-group">
        <label>New Password*</label>
        <input type="password" name="new_password" required minlength="6" autocomplete="new-password">
        <div class="security-note">
          Password must be at least 6 characters long. Use a combination of letters, numbers, and symbols for better security.
        </div>
      </div>

      <div class="form-group">
        <label>Confirm New Password*</label>
        <input type="password" name="confirm_password" required minlength="6" autocomplete="new-password">
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn--outline" onclick="closePasswordModal()">Cancel</button>
        <button type="submit" class="btn btn--solid">Change Password</button>
      </div>
    </form>
  </div>
</div>

  <!-- Modal (place this INSIDE <main class="page-student">, right above </main>) -->
<div id="certModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>License or Certification</h3>
      <button type="button" class="modal-close">✕</button>
    </div>

<form id="certForm" action="/adamson-ccit/public/index.php?page=student_cert_save" method="POST">
      <input type="hidden" name="mode" value="create">
      <input type="hidden" name="id">

      <label>Name*</label>
      <input type="text" name="name" required>

      <label>Issuing organization*</label>
      <select name="company_id" required>
        <option value="">Select company…</option>
        <?php foreach ($companies as $co): ?>
          <option value="<?= (int)$co['id'] ?>"><?= htmlspecialchars($co['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <div class="form-row">
        <div>
          <label>Issue month</label>
          <select name="issue_month">
            <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option>$m</option>"; ?>
          </select>
        </div>
        <div>
          <label>Issue year</label>
          <input type="number" name="issue_year" min="1950" max="<?= date('Y')+1 ?>">
        </div>
      </div>

      <div class="form-row">
        <input type="checkbox" id="no-expire" name="no_expire" value="1" checked>
        <label for="no-expire">This credential does not expire</label>
      </div>

      <div id="expireRow" class="form-row disabled">
        <div>
          <label>Expiration month</label>
          <select name="expire_month">
            <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option>$m</option>"; ?>
          </select>
        </div>
        <div>
          <label>Expiration year</label>
          <input type="number" name="expire_year" min="<?= date('Y')-1 ?>" max="<?= date('Y')+15 ?>">
        </div>
      </div>

      <label>Credential ID</label>
      <input type="text" name="credential_id">

      <label>Credential URL</label>
      <input type="url" name="credential_url" placeholder="https://...">

      <label>Visibility</label>
      <select name="visibility">
        <option value="public">Public</option>
        <option value="private">Only me</option>
      </select>

      <div class="modal-actions">
        <button type="button" class="btn btn--outline modal-close">Exit</button>
        <button type="submit" class="btn btn--solid">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Delete Certification</h3>
      <button type="button" class="modal-close" onclick="closeDeleteModal()">✕</button>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to delete this certification?</p>
      <p><strong id="deleteCertName">Certification Name</strong></p>
      <p class="text-danger"><small>This action cannot be undone.</small></p>
    </div>
    <div class="modal-actions">
      <button type="button" class="btn btn--outline" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="btn btn--danger" onclick="confirmDelete()" id="deleteConfirmBtn">Delete</button>
    </div>
  </div>
</div>

</main>


<style>

  
  /* Force correct modal layout on this page only */
  .page-student .modal { 
    position: fixed !important; inset: 0 !important;
    display: none !important;      /* hidden until JS opens it */
    align-items: center !important; justify-content: center !important;
    background: rgba(0,0,0,.55) !important; z-index: 10000 !important;
  }
  .page-student .modal.show { display: flex !important; } /* open state */

  .page-student .modal-content {
    width: min(560px, 94vw); max-height: 85vh; overflow: auto;
    background: #ffffff; color:#111827;
    border: 1px solid #e5e7eb; border-radius: 16px; padding: 18px;
    box-shadow: 0 24px 60px rgba(0,0,0,.18);
  }
  .page-student .modal-header {
    display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:10px;
  }
  .page-student .modal-close { background:none; border:none; color:#6b7280; font-size:20px; cursor:pointer; }
  .page-student .modal-close:hover { color:#111827; }

  /* Form cosmetics (won’t fight your site styles) */
  #certForm label { display:block; font-weight:600; font-size:.95rem; margin:8px 0 6px; }
  #certForm input[type="text"], #certForm input[type="url"], #certForm input[type="number"], #certForm select {
    width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:.95rem;
  }
  #certForm .form-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  #certForm .form-row.disabled { opacity:.55; pointer-events:none; }

  .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:14px; }

  .btn--danger {
    background: #dc3545;
    color: white;
    border: 1px solid #dc3545;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
  }
  .btn--danger:hover {
    background: #c82333;
    border: 1px solid #c82333;
  }
  .text-danger {
    color: #dc3545;
  }
  #profileForm .readonly-field {
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 12px;
    color: #6b7280;
    margin-bottom: 8px;
  }
</style>

<script>
const certModal = document.getElementById('certModal');
const certForm  = document.getElementById('certForm');
const noExpire  = document.getElementById('no-expire');
const expireRow = document.getElementById('expireRow');

// Menu toggle functionality
function toggleCertMenu(certId) {
  const menu = document.getElementById(`cert-menu-${certId}`);
  const allMenus = document.querySelectorAll('.cert-menu');
  
  // Close all other menus
  allMenus.forEach(m => {
    if (m !== menu) m.classList.remove('show');
  });
  
  // Toggle current menu
  if (menu) {
    menu.classList.toggle('show');
  }
}

// Close menus when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.cert-actions-menu')) {
    document.querySelectorAll('.cert-menu').forEach(menu => {
      menu.classList.remove('show');
    });
  }
});

function openModal() {
  certModal.classList.add('show');
  document.body.classList.add('modal-open');
  // focus first input
  const first = certForm.querySelector('input[name="name"]');
  if (first) setTimeout(()=>first.focus(), 50);
}

function closeModal() {
  certModal.classList.remove('show');
  document.body.classList.remove('modal-open');
  // reset to create mode if no id
  if (!certForm.id.value) certForm.reset();
}

// Open
document.querySelectorAll('[data-open-cert-modal]').forEach(btn=>{
  btn.addEventListener('click', ()=> {
    certForm.mode.value = 'create';
    certForm.id.value = '';
    openModal();
  });
});

// Close (X/Cancel/backdrop)
document.querySelectorAll('.modal-close').forEach(b=> b.addEventListener('click', closeModal));
certModal.addEventListener('click',(e)=>{ if(e.target===certModal) closeModal(); });
// Esc key
document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape' && certModal.classList.contains('show')) closeModal(); });

// Expiration toggle
if(noExpire && expireRow){
  const toggle=()=> expireRow.classList.toggle('disabled', noExpire.checked);
  noExpire.addEventListener('change', toggle); toggle();
}

// Prevent double-submit & give lightweight feedback
certForm.addEventListener('submit', ()=>{
  const btn = certForm.querySelector('button[type="submit"]');
  if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
});

// Edit prefill
function editCert(c){
  certForm.mode.value = 'update';
  certForm.id.value   = c.id;
  certForm.name.value = c.name || '';
  certForm.company_id.value = c.company_id || '';
  certForm.issue_month.value = c.issue_month || '';
  certForm.issue_year.value  = c.issue_year  || '';
  noExpire.checked = (String(c.expires)==='0');
  expireRow.classList.toggle('disabled', noExpire.checked);
  certForm.expire_month.value = c.expire_month || '';
  certForm.expire_year.value  = c.expire_year  || '';
  certForm.credential_id.value  = c.credential_id || '';
  certForm.credential_url.value = c.credential_url || '';
  if (certForm.visibility && c.visibility) certForm.visibility.value = c.visibility;
  openModal();
}

// Make functions available globally
window.editCert = editCert;
window.toggleCertMenu = toggleCertMenu;

// Delete Modal Functions
const deleteModal = document.getElementById('deleteModal');
let currentDeleteId = null;

function deleteCert(certId, certName) {
  currentDeleteId = certId;
  document.getElementById('deleteCertName').textContent = certName;
  deleteModal.classList.add('show');
  document.body.classList.add('modal-open');
}

function closeDeleteModal() {
  deleteModal.classList.remove('show');
  document.body.classList.remove('modal-open');
  currentDeleteId = null;
}

function confirmDelete() {
  if (!currentDeleteId) {
    console.error('No certification ID to delete');
    return;
  }
  
  console.log('Deleting certification ID:', currentDeleteId);
  
  // Disable button and show loading
  const deleteBtn = document.getElementById('deleteConfirmBtn');
  deleteBtn.disabled = true;
  deleteBtn.textContent = 'Deleting...';
  
  // Create and submit form
  const form = document.createElement('form');
  form.method = 'GET';
  form.action = '/adamson-ccit/public/index.php';
  
  const pageInput = document.createElement('input');
  pageInput.type = 'hidden';
  pageInput.name = 'page';
  pageInput.value = 'student_cert_delete';
  form.appendChild(pageInput);
  
  const idInput = document.createElement('input');
  idInput.type = 'hidden';
  idInput.name = 'id';
  idInput.value = currentDeleteId;
  form.appendChild(idInput);
  
  document.body.appendChild(form);
  form.submit();
}

// Make delete functions available globally
window.deleteCert = deleteCert;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;

// Profile Edit Modal Functions
const profileModal = document.getElementById('profileModal');
const profileForm = document.getElementById('profileForm');

function openEditProfileModal() {
  profileModal.classList.add('show');
  document.body.classList.add('modal-open');
}

function closeProfileModal() {
  profileModal.classList.remove('show');
  document.body.classList.remove('modal-open');
}

// Make profile functions available globally
window.openEditProfileModal = openEditProfileModal;
window.closeProfileModal = closeProfileModal;

// Password Modal Functions
const passwordModal = document.getElementById('passwordModal');
const passwordForm = document.getElementById('passwordForm');

function openPasswordModal() {
  passwordModal.classList.add('show');
  document.body.classList.add('modal-open');
}

function closePasswordModal() {
  passwordModal.classList.remove('show');
  document.body.classList.remove('modal-open');
  passwordForm.reset();
}

// Make password functions available globally
window.openPasswordModal = openPasswordModal;
window.closePasswordModal = closePasswordModal;

// Password form validation
passwordForm.addEventListener('submit', function(e) {
  const newPassword = this.querySelector('[name="new_password"]').value;
  const confirmPassword = this.querySelector('[name="confirm_password"]').value;
  
  if (newPassword !== confirmPassword) {
    e.preventDefault();
    alert('New password and confirm password do not match!');
    return false;
  }
  
  if (newPassword.length < 6) {
    e.preventDefault();
    alert('New password must be at least 6 characters long!');
    return false;
  }
  
  // Show loading state
  const btn = this.querySelector('button[type="submit"]');
  if (btn) { 
    btn.disabled = true; 
    btn.textContent = 'Changing Password…'; 
  }
});

// Close profile modal with escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    if (profileModal.classList.contains('show')) {
      closeProfileModal();
    }
    if (passwordModal.classList.contains('show')) {
      closePasswordModal();
    }
    if (deleteModal.classList.contains('show')) {
      closeDeleteModal();
    }
  }
});

// Close modals by clicking backdrop
profileModal.addEventListener('click', (e) => {
  if (e.target === profileModal) closeProfileModal();
});

passwordModal.addEventListener('click', (e) => {
  if (e.target === passwordModal) closePasswordModal();
});

deleteModal.addEventListener('click', (e) => {
  if (e.target === deleteModal) closeDeleteModal();
});

// Profile form submission
profileForm.addEventListener('submit', function(e) {
  console.log('Profile form submitted');
  console.log('Form data:', new FormData(this));
  
  const btn = this.querySelector('button[type="submit"]');
  if (btn) { 
    btn.disabled = true; 
    btn.textContent = 'Saving…'; 
  }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function(){
  const t = document.querySelector(".toast");
  if (!t) return;

  // auto-hide after 3s
  setTimeout(() => {
    t.classList.add("hide");
    setTimeout(() => t.remove(), 300);
  }, 3000);

  // remove query params so toast won’t reappear on refresh
  if (history.replaceState) {
    const url = new URL(window.location);
    url.searchParams.delete("saved");
    url.searchParams.delete("deleted");
    history.replaceState({}, "", url.toString());
  }
});
</script>

    </main>
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script src="/adamson-ccit/public/assets/js/student-profile.js"></script>
<script>
  lucide.createIcons();
</script>
</body>
</html>
