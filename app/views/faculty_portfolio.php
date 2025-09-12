<?php
require_once __DIR__ . '/../../app/lib/Auth.php';
require_once __DIR__ . '/../../app/models/Model.php';

$user = Auth::user();

class _DBX extends Model { public function d(){ return parent::db(); } }
$_db = (new _DBX())->d();

$facultyId = null;
if (!empty($user['username'])) {
    $st = $_db->prepare("SELECT id FROM users WHERE username=:u LIMIT 1");
    $st->execute([':u' => $user['username']]);
    if ($r = $st->fetch(PDO::FETCH_ASSOC)) $facultyId = (int)$r['id'];
}

// Fetch companies for issuing organizations dropdown
$companies = $_db->query("SELECT id, name FROM companies ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch faculty certifications / portfolio items
$portfolioItems = [];
if ($facultyId) {
    $q = $_db->prepare("
        SELECT c.*, co.name AS company_name
        FROM faculty_portfolio c
        JOIN companies co ON co.id = c.company_id
        WHERE c.user_id = ?
        ORDER BY COALESCE(c.expire_year,9999), COALESCE(c.expire_month,12),
                 c.issue_year DESC, c.issue_month DESC
    ");
    $q->execute([$facultyId]);
    $portfolioItems = $q->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Faculty Portfolio</title>
  <style>
    /* Basic styles for the modal and form */
    body.modal-open {
      overflow: hidden;
    }
    .page-faculty-portfolio {
      font-family: Arial, sans-serif;
      padding: 1rem;
      max-width: 900px;
      margin: auto;
    }
    .card {
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-top: 1rem;
      padding: 1rem;
    }
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .btn {
      cursor: pointer;
      border: none;
      border-radius: 4px;
      padding: 0.4rem 1rem;
      font-size: 0.9rem;
    }
    .btn--outline-blue {
      background: transparent;
      color: #007bff;
      border: 1px solid #007bff;
    }
    .btn--outline-blue:hover {
      background: #007bff;
      color: white;
    }
    .btn--solid {
      background: #007bff;
      color: white;
    }
    .btn--solid:hover {
      background: #0056b3;
    }
    .btn-mini {
      background: #007bff;
      color: white;
      padding: 0.2rem 0.5rem;
      font-size: 0.75rem;
      border-radius: 3px;
      text-decoration: none;
      display: inline-block;
      margin-top: 0.25rem;
    }
    .btn-link {
      background: none;
      color: #007bff;
      padding: 0;
      font-size: 0.9rem;
      cursor: pointer;
      text-decoration: underline;
    }
    .btn-link.delete-link {
      color: #dc3545;
    }
    .btn-link:hover {
      opacity: 0.8;
    }
    .cert-list {
      margin-top: 1rem;
    }
    .cert-item {
      display: flex;
      align-items: flex-start;
      border-bottom: 1px solid #ddd;
      padding: 0.5rem 0;
    }
    .cert-icon {
      font-size: 1.5rem;
      margin-right: 1rem;
      user-select: none;
    }
    .cert-details {
      flex-grow: 1;
    }
    .cert-title {
      font-weight: bold;
      font-size: 1.1rem;
    }
    .cert-org {
      color: #555;
      font-size: 0.9rem;
      margin-bottom: 0.2rem;
    }
    .cert-meta {
      font-size: 0.85rem;
      color: #666;
    }
    .cert-actions {
      margin-left: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      min-width: 75px;
    }
    .empty-state {
      font-style: italic;
      color: #666;
      margin-top: 1rem;
    }
    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .form-row > div {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    label {
      font-weight: 600;
      margin-bottom: 0.25rem;
    }
    input[type="text"],
    input[type="number"],
    input[type="url"],
    select {
      padding: 0.4rem 0.5rem;
      font-size: 1rem;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    input[type="checkbox"] {
      margin-right: 0.5rem;
      margin-top: 0.2rem;
    }
    .modal {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.4);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      padding: 1rem;
    }
    .modal.show {
      display: flex;
    }
    .modal-content {
      background: white;
      border-radius: 8px;
      max-width: 500px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      padding: 1.5rem;
      position: relative;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      line-height: 1;
      padding: 0;
      user-select: none;
    }
    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 0.5rem;
      margin-top: 1rem;
    }
    .disabled {
      opacity: 0.5;
      pointer-events: none;
    }
    .inline {
      display: inline;
    }
  </style>
</head>
<body>

<main class="page-faculty-portfolio">
  <section class="content">
    <div class="container">
      <h2 class="page-title">Faculty Portfolio</h2>
      <p>Welcome, <strong><?= htmlspecialchars($user['username'] ?? 'Faculty') ?></strong>.</p>

      <div class="card">
        <div class="card-header">
          <h3>Licenses &amp; Certifications</h3>
          <button class="btn btn--outline-blue" data-open-portfolio-modal>+ Add</button>
        </div>
        <p class="card-subtitle">Add your professional certifications and portfolio items.</p>

        <?php if (empty($portfolioItems)): ?>
          <p class="empty-state">No portfolio items yet. Click <b>+ Add</b> to include your first one.</p>
        <?php else: ?>
          <div class="cert-list">
            <?php foreach ($portfolioItems as $item): ?>
              <div class="cert-item">
                <div class="cert-icon">🎓</div>
                <div class="cert-details">
                  <div class="cert-title"><?= htmlspecialchars($item['name']) ?></div>
                  <div class="cert-org"><?= htmlspecialchars($item['company_name']) ?></div>
                  <div class="cert-meta">
                    <?php
                      $issued = ($item['issue_month'] && $item['issue_year'])
                        ? date("M", mktime(0,0,0,$item['issue_month'],1)) . ' ' . $item['issue_year']
                        : 'Issued —';

                      $expires = (!$item['expires'] && $item['expire_month'] && $item['expire_year'])
                        ? date("M", mktime(0,0,0,$item['expire_month'],1)) . ' ' . $item['expire_year']
                        : 'No expiry';

                      echo "Issued $issued · Expires $expires";
                    ?>
                  </div>
                  <?php if (!empty($item['credential_url'])): ?>
                    <a href="<?= htmlspecialchars($item['credential_url']) ?>" target="_blank" class="btn-mini">Show credential</a>
                  <?php endif; ?>
                </div>
                <div class="cert-actions">
                  <a href="#" class="btn-link edit-link"
                     onclick='editPortfolioItem(<?= json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>);return false;'>Edit</a>

                  <form action="index.php" method="get" class="inline" onsubmit="return confirm('Delete this portfolio item?')">
                    <input type="hidden" name="page" value="faculty_portfolio_delete">
                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                    <button type="submit" class="btn-link delete-link">Delete</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Modal -->
  <div id="portfolioModal" class="modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Portfolio Item</h3>
        <button type="button" class="modal-close" aria-label="Close modal">✕</button>
      </div>

      <form id="portfolioForm" action="index.php?page=faculty_portfolio_save" method="POST">
        <input type="hidden" name="mode" value="create">
        <input type="hidden" name="id" value="">

        <label for="nameInput">Name*</label>
        <input type="text" id="nameInput" name="name" required>

        <label for="companySelect">Issuing organization*</label>
        <select id="companySelect" name="company_id" required>
          <option value="">Select company…</option>
          <?php foreach ($companies as $co): ?>
            <option value="<?= (int)$co['id'] ?>"><?= htmlspecialchars($co['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <div class="form-row">
          <div>
            <label for="issueMonth">Issue month</label>
            <select id="issueMonth" name="issue_month">
              <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option value=\"$m\">$m</option>"; ?>
            </select>
          </div>
          <div>
            <label for="issueYear">Issue year</label>
            <input id="issueYear" type="number" name="issue_year" min="1950" max="<?= date('Y')+1 ?>">
          </div>
        </div>

        <div class="form-row">
          <input type="checkbox" id="no-expire" name="no_expire" value="1" checked>
          <label for="no-expire">This credential does not expire</label>
        </div>

        <div id="expireRow" class="form-row disabled">
          <div>
            <label for="expireMonth">Expiration month</label>
            <select id="expireMonth" name="expire_month">
              <option value="">Month</option><?php for ($m=1;$m<=12;$m++) echo "<option value=\"$m\">$m</option>"; ?>
            </select>
          </div>
          <div>
            <label for="expireYear">Expiration year</label>
            <input id="expireYear" type="number" name="expire_year" min="<?= date('Y')-1 ?>" max="<?= date('Y')+15 ?>">
          </div>
        </div>

        <label for="credentialId">Credential ID</label>
        <input id="credentialId" type="text" name="credential_id">

        <label for="credentialUrl">Credential URL</label>
        <input id="credentialUrl" type="url" name="credential_url" placeholder="https://...">

        <label for="visibility">Visibility</label>
        <select id="visibility" name="visibility">
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

<script>
const portfolioModal = document.getElementById('portfolioModal');
const portfolioForm  = document.getElementById('portfolioForm');
const noExpire       = document.getElementById('no-expire');
const expireRow      = document.getElementById('expireRow');

function openModal() {
  portfolioModal.classList.add('show');
  document.body.classList.add('modal-open');
  portfolioModal.setAttribute('aria-hidden', 'false');
  // focus first input
  const first = portfolioForm.querySelector('input[name="name"]');
  if (first) setTimeout(() => first.focus(), 50);
}

function closeModal() {
  portfolioModal.classList.remove('show');
  document.body.classList.remove('modal-open');
  portfolioModal.setAttribute('aria-hidden', 'true');
  portfolioForm.reset();
  portfolioForm.mode.value = 'create';
  portfolioForm.id.value = '';
}

// Open modal when clicking the add button
document.querySelectorAll('[data-open-portfolio-modal]').forEach(btn => {
  btn.addEventListener('click', () => {
    portfolioForm.mode.value = 'create';
    portfolioForm.id.value = '';
    portfolioForm.reset();
    noExpire.checked = true;
    expireRow.classList.add('disabled');
    openModal();
  });
});

// Close modal buttons and backdrop click
document.querySelectorAll('.modal-close').forEach(btn => btn.addEventListener('click', closeModal));
portfolioModal.addEventListener('click', (e) => {
  if (e.target === portfolioModal) closeModal();
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && portfolioModal.classList.contains('show')) closeModal();
});

// Toggle expiration fields
if (noExpire && expireRow) {
  const toggleExpire = () => {
    if (noExpire.checked) {
      expireRow.classList.add('disabled');
      // Clear expiration inputs if disabled
      portfolioForm.expire_month.value = '';
      portfolioForm.expire_year.value = '';
    } else {
      expireRow.classList.remove('disabled');
    }
  };
  noExpire.addEventListener('change', toggleExpire);
  toggleExpire();
}

// Prevent double submit & feedback on save button
portfolioForm.addEventListener('submit', () => {
  const btn = portfolioForm.querySelector('button[type="submit"]');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Saving…';
  }
});

// Prefill form for editing
function editPortfolioItem(item) {
  portfolioForm.mode.value = 'update';
  portfolioForm.id.value = item.id || '';
  portfolioForm.name.value = item.name || '';
  portfolioForm.company_id.value = item.company_id || '';
  portfolioForm.issue_month.value = item.issue_month || '';
  portfolioForm.issue_year.value = item.issue_year || '';
  
  // expires: 0 means no expiry (true on checkbox)
  if (typeof item.expires !== 'undefined') {
    noExpire.checked = (String(item.expires) === '1' || String(item.expires) === 'true'); // If expires=1 means no expiry
  } else {
    noExpire.checked = true;
  }
  if (noExpire.checked) {
    expireRow.classList.add('disabled');
    portfolioForm.expire_month.value = '';
    portfolioForm.expire_year.value = '';
  } else {
    expireRow.classList.remove('disabled');
    portfolioForm.expire_month.value = item.expire_month || '';
    portfolioForm.expire_year.value = item.expire_year || '';
  }

  portfolioForm.credential_id.value = item.credential_id || '';
  portfolioForm.credential_url.value = item.credential_url || '';
  if (portfolioForm.visibility && item.visibility) {
    portfolioForm.visibility.value = item.visibility;
  }
  openModal();
}
window.editPortfolioItem = editPortfolioItem;
</script>

</body>
</html>
