<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
$user = Auth::user();

/* NEW: get PDO via your Model and fetch companies + this student's certs */
require_once __DIR__ . '/../../app/models/Model.php';
class _DBX extends Model { public function d(){ return parent::db(); } }
$_db = (new _DBX())->d();

/* Companies shown in the dropdown (only active) */
$companies = $_db->query("SELECT id, name FROM companies ORDER BY name")
                 ->fetchAll(PDO::FETCH_ASSOC);

/* Resolve student id from session username */
$studentId = null;
if (!empty($user['username'])) {
  $st = $_db->prepare("SELECT id FROM users WHERE username=:u LIMIT 1");
  $st->execute([':u' => $user['username']]);
  if ($r = $st->fetch(PDO::FETCH_ASSOC)) $studentId = (int)$r['id'];
}


/* Existing certifications for this student */
/* Existing certifications for this student (read from the new table) */
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

?>

<main class="page-student">
  <section class="content">
    <div class="container">
      <?php if (!empty($_GET['saved']) || !empty($_GET['deleted'])): ?>
  <div class="toast <?= !empty($_GET['deleted']) ? 'toast--danger' : 'toast--success' ?>">
    <?= !empty($_GET['deleted']) ? 'Certification deleted.' : 'Certification saved.' ?>
  </div>
  
<?php endif; ?>

      <h2 class="page-title">Student Profile &amp; Portfolio</h2>
      <p class="welcome-text">
        Welcome, <strong><?= htmlspecialchars($user['username'] ?? 'Student') ?></strong>.
      </p>

    

      <!-- ===== Certifications Section ===== -->
      <!-- ===== Certifications Section ===== -->
<!-- ===== Certifications Section ===== -->
<div class="card">
  <div class="card-header">
    <h3>Licenses &amp; Certifications</h3>
    <button class="btn btn--outline-blue" data-open-cert-modal>+ Add</button>
  </div>
  <p class="card-subtitle">Add certifications from verified issuers like Microsoft, AWS, or Cisco.</p>

  <?php if (empty($certs)): ?>
    <p class="empty-state">No certifications yet. Click <b>+ Add</b> to include your first one.</p>
  <?php else: ?>
    <div class="cert-list">
      <?php foreach ($certs as $c): ?>
        <div class="cert-item">
          <div class="cert-icon">🎓</div>
          <div class="cert-details">
            <div class="cert-title">
              <?= htmlspecialchars($c['name']) ?>
              <?php if (!empty($c['visibility'])): ?>
                <span class="pill pill--muted">
                  <?= $c['visibility']==='private' ? 'Only me' : 'Public' ?>
                </span>
              <?php endif; ?>
            </div>

            <div class="cert-org"><?= htmlspecialchars($c['company_name']) ?></div>

            <div class="cert-meta">
              <?php
                $issued  = ($c['issue_month'] && $c['issue_year'])
                  ? date("M", mktime(0,0,0,$c['issue_month'],1)).' '.$c['issue_year']
                  : null;
                $expires = ($c['expires'] && $c['expire_month'] && $c['expire_year'])
                  ? date("M", mktime(0,0,0,$c['expire_month'],1)).' '.$c['expire_year']
                  : 'No expiry';

                echo $issued ? "Issued $issued" : "Issued —";
                echo " · ";
                echo $c['expires'] ? "Expires $expires" : "No expiry";
              ?>
            </div>

            <?php if (!empty($c['credential_id'])): ?>
              <div class="cert-id">ID: <?= htmlspecialchars($c['credential_id']) ?></div>
            <?php endif; ?>

            <div class="cert-links">
              <?php if (!empty($c['credential_url'])): ?>
                <a href="<?= htmlspecialchars($c['credential_url']) ?>" target="_blank" class="btn-mini">
                  Show credential
                </a>
              <?php endif; ?>
            </div>
          </div><!-- /.cert-details -->

        <div class="cert-actions">
  <a href="#" class="btn-link edit-link"
     onclick='editCert(<?= json_encode($c, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>);return false;'>
    Edit
  </a>

  <form action="/adamson-ccit/public/index.php" method="get" class="inline"
        onsubmit="return confirm('Delete this certification?')">
    <input type="hidden" name="page" value="student_cert_delete">
    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
    <button type="submit" class="btn-link delete-link">Delete</button>
  </form>
</div>

        </div><!-- /.cert-item -->
      <?php endforeach; ?>
    </div><!-- /.cert-list -->
  <?php endif; ?>
</div><!-- /.card (Licenses & Certifications) -->



      <!-- ===== Career Pathway ===== -->
      <div class="card">
        <h3>Career Pathway Results</h3>
        <p>Save and view your latest generator results here.</p>
        <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=career_pathway_generator">Open Generator</a>
      </div>

      <!-- ===== Documents ===== -->
      <div class="card">
        <h3>Documents</h3>
        <p>Upload supporting documents.</p>
        <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=student_settings">Manage Profile</a>
      </div>
    </div>
  </section>

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
window.editCert = editCert;
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
<div class="cert-actions">
