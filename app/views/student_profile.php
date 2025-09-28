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
  <?php if (!empty($_GET['saved']) || !empty($_GET['deleted']) || !empty($_GET['error'])): ?>
    <div class="toast <?= !empty($_GET['error']) ? 'toast--danger' : (!empty($_GET['deleted']) ? 'toast--danger' : 'toast--success') ?>">
      <?php if (!empty($_GET['error'])): ?>
        <?= htmlspecialchars($_GET['error']) ?>
      <?php elseif (!empty($_GET['deleted'])): ?>
        Certification deleted.
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
            <button class="edit-profile-btn" onclick="openEditProfileModal()" title="Edit Profile">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
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
                    <div class="cert-actions-compact">
                      <?php if (!empty($c['credential_url'])): ?>
                        <a href="<?= htmlspecialchars($c['credential_url']) ?>" target="_blank" class="action-link">
                          Show credential
                        </a>
                      <?php endif; ?>
                      <a href="#" onclick='editCert(<?= json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>);return false;' class="action-link">
                        Edit
                      </a>
                      <form action="/adamson-ccit/public/index.php" method="get" class="inline-form"
                            onsubmit="return confirm('Delete this certification?')">
                        <input type="hidden" name="page" value="student_cert_delete">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <button type="submit" class="action-link delete-link">Delete</button>
                      </form>
                    </div>
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
                    <div class="cert-actions-menu">
                      <button class="action-menu-btn" onclick="toggleCertMenu(<?= $c['id'] ?>)">⋯</button>
                      <div class="cert-menu" id="cert-menu-<?= $c['id'] ?>">
                        <a href="#" onclick='editCert(<?= json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>);return false;' class="menu-item">
                          <span class="menu-icon">✏️</span> Edit
                        </a>
                        <form action="/adamson-ccit/public/index.php" method="get" class="menu-form"
                              onsubmit="return confirm('Delete this certification?')">
                          <input type="hidden" name="page" value="student_cert_delete">
                          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                          <button type="submit" class="menu-item delete-item">
                            <span class="menu-icon">🗑️</span> Delete
                          </button>
                        </form>
                      </div>
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
    <form id="profileForm" action="/adamson-ccit/public/index.php" method="POST">
      <input type="hidden" name="page" value="student_profile_save">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

      <label>Full Name*</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($studentProfile['full_name'] ?? '') ?>" required>

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
      <input type="text" name="skills" value="<?= htmlspecialchars($studentProfile['skills'] ?? '') ?>" 
             placeholder="Programming, Web Development, Database Management">

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($studentProfile['email'] ?? '') ?>">

      <label>Phone</label>
      <input type="tel" name="phone" value="<?= htmlspecialchars($studentProfile['phone'] ?? '') ?>">

      <label>Location</label>
      <input type="text" name="location" value="<?= htmlspecialchars($studentProfile['location'] ?? '') ?>" 
             placeholder="City, Country">

      <label>LinkedIn Profile</label>
      <input type="url" name="linkedin_url" value="<?= htmlspecialchars($studentProfile['linkedin_url'] ?? '') ?>" 
             placeholder="https://linkedin.com/in/yourprofile">

      <label>GitHub Profile</label>
      <input type="url" name="github_url" value="<?= htmlspecialchars($studentProfile['github_url'] ?? '') ?>" 
             placeholder="https://github.com/yourusername">

      <label>Portfolio Website</label>
      <input type="url" name="website_url" value="<?= htmlspecialchars($studentProfile['website_url'] ?? '') ?>" 
             placeholder="https://yourportfolio.com">

      <div class="modal-actions">
        <button type="button" class="btn btn--outline" onclick="closeProfileModal()">Cancel</button>
        <button type="submit" class="btn btn--solid">Save Profile</button>
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

<form id="certForm" action="index.php?page=student_cert_save" method="POST">
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

// Close profile modal with escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && profileModal.classList.contains('show')) {
    closeProfileModal();
  }
});

// Close profile modal by clicking backdrop
profileModal.addEventListener('click', (e) => {
  if (e.target === profileModal) closeProfileModal();
});

// Profile form submission
profileForm.addEventListener('submit', function() {
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
