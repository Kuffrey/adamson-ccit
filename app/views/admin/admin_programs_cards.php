<?php
// Reusable Programs CMS (UG/Grad) — list + add + edit + status tabs
if (!function_exists('esc')) { function esc($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
$tabs = [
  'all'       => 'All ('.(int)($counts['all'] ?? 0).')',
  'draft'     => 'Drafts ('.(int)($counts['draft'] ?? 0).')',
  'published' => 'Published ('.(int)($counts['published'] ?? 0).')',
  'archived'  => 'Archived ('.(int)($counts['archived'] ?? 0).')',
];
$title = $level === 'graduate' ? 'Programs → Graduate Studies' : 'Programs → Undergraduate';
?>
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
<link rel="stylesheet" href="/adamson-ccit/public/assets/css/admin-dashboard.css">

<div class="admin-cms-layout">
  <?php include __DIR__ . '/_admin_sidebar.php'; ?>

  <main class="admin-main">
    <header class="admin-topbar">
      <span class="admin-topbar__title"><?= esc($title) ?></span>
      <div class="admin-topbar__spacer"></div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__avatar"><?= esc(strtoupper($username[0] ?? 'A')) ?></span>
        <span class="admin-topbar__name"><?= esc($username) ?></span>
      </div>
    </header>

    <section class="admin-cms-section">
      <h1 class="admin-cms-section__title"><?= esc($level === 'graduate' ? 'Graduate Program Cards' : 'Undergraduate Program Cards') ?></h1>
      <p class="intro">Plain text only. The public site renders layout HTML automatically.</p>

      <?php if (!empty($notice)): ?>
        <p class="notice <?= str_starts_with($notice, 'Error:') ? 'error' : 'success' ?>"><?= esc($notice) ?></p>
      <?php endif; ?>

      <!-- Tabs -->
      <nav class="tbar" aria-label="Filters">
        <div class="tbar__inner">
          <?php foreach ($tabs as $key=>$label): ?>
            <a class="pill<?= $status === $key ? ' is-active' : '' ?>"
               href="?page=admin_programs_<?= esc($level) ?>&status=<?= esc($key) ?>"><?= esc($label) ?></a>
          <?php endforeach; ?>
        </div>
      </nav>

      <!-- Add Card -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Add Program Card</h2>
        <form class="admin-cms-form" method="post" id="addForm" autocomplete="off">
          <input type="hidden" name="add_card" value="1">
          <div class="form-section">
            <div class="field"><label>Position</label><input type="number" name="card[position]" value="999"></div>
            <div class="field"><label>Slug / ID</label><input type="text" name="card[slug]" placeholder="e.g. bscs"></div>
            <div class="field">
              <label>Status</label>
              <select name="card[status]">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="field"><label>Badge</label><input type="text" name="card[badge]" placeholder="BSCS / MIT"></div>
            <div class="field"><label>Title</label><input type="text" name="card[title]" required></div>
            <div class="field"><label>Title (Muted Suffix)</label><input type="text" name="card[title_muted]" placeholder="(Dual)"></div>
          </div>
          <div class="form-section">
            <div class="field"><label>Summary</label><textarea name="card[summary]" rows="3"></textarea></div>
          </div>
          <div class="form-row">
            <div class="field"><label>Pillbox Title</label><input type="text" name="card[pillbox_title]" placeholder="Specializations"></div>
            <div class="field"><label>Pills (one per line)</label><textarea name="card[pills]" rows="3"></textarea></div>
            <div class="field"><label>Image URL (optional)</label><input type="text" name="card[image_url]"></div>
          </div>
          <div class="form-row">
            <div class="field"><label>Learn More URL</label><input type="text" name="card[learn_more_url]"></div>
            <div class="field field-checkbox">
              <label><input type="checkbox" name="card[learn_more_external]" value="1" checked> Open in new tab</label>
            </div>
            <div class="field"><label>Curriculum URL</label><input type="text" name="card[curriculum_url]"></div>
            <div class="field field-checkbox">
              <label><input type="checkbox" name="card[curriculum_external]" value="1" checked> Open in new tab</label>
            </div>
            <div class="field"><label>Apply / Inquire URL</label><input type="text" name="card[apply_url]"></div>
          </div>
          <div class="form-actions"><button class="btn btn--primary" type="submit">Add Card</button></div>
        </form>
      </div>

      <!-- Cards list -->
      <div class="cms-card">
        <h2 class="cms-card-legend">Cards</h2>
        <?php if (empty($cards)): ?>
          <p>No cards in this tab.</p>
        <?php else: foreach ($cards as $c):
          $s = strtolower($c['status'] ?? 'draft');
          $statusClass = ['draft'=>'status--draft','published'=>'status--published','archived'=>'status--archived'][$s] ?? 'status--draft';
        ?>
        <div class="cms-card-item">
          <div class="cms-card-item__thumb">
            <?php if (!empty($c['image_url'])): ?>
              <img src="<?= esc($c['image_url']) ?>" alt="">
            <?php else: ?>
              <div class="cms-card-item__placeholder" aria-hidden="true">🎓</div>
            <?php endif; ?>
          </div>
          <div class="cms-card-item__body">
            <h3><?= esc($c['title']) ?> <?= !empty($c['title_muted']) ? '<span class="prog__muted">'.esc($c['title_muted']).'</span>' : '' ?></h3>
            <p class="meta">
              <?php if (!empty($c['badge'])): ?><strong><?= esc($c['badge']) ?></strong> • <?php endif; ?>
              <span class="status-badge <?= esc($statusClass) ?>"><?= esc(ucfirst($s)) ?></span>
              <?php if (!empty($c['slug'])): ?> • <small>#<?= esc($c['slug']) ?></small><?php endif; ?>
              <?php if (!empty($c['position'])): ?> • <small>pos <?= (int)$c['position'] ?></small><?php endif; ?>
            </p>
            <?php if (!empty($c['summary'])): ?>
              <p class="excerpt"><?= esc(mb_substr($c['summary'],0,180)) ?><?= strlen((string)$c['summary'])>180?'…':'' ?></p>
            <?php endif; ?>
            <?php if (!empty($c['pillbox_title']) || !empty($c['pills'])): ?>
              <p class="meta">
                <small><?= esc($c['pillbox_title'] ?: 'Pills') ?>:
                  <?php
                    $pills = array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", (string)($c['pills'] ?? ''))));
                    echo esc(implode(' | ', $pills));
                  ?>
                </small>
              </p>
            <?php endif; ?>

            <!-- Inline edit toggle -->
            <details class="mt-12">
              <summary><strong>Edit</strong></summary>
              <form method="post" class="admin-cms-form mt-12">
                <input type="hidden" name="edit_card" value="1">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <input type="hidden" name="card[status]" value="<?= esc($c['status']) ?>">
                <div class="form-section">
                  <div class="field"><label>Position</label><input type="number" name="card[position]" value="<?= (int)$c['position'] ?>"></div>
                  <div class="field"><label>Slug</label><input type="text" name="card[slug]" value="<?= esc($c['slug']) ?>"></div>
                  <div class="field"><label>Badge</label><input type="text" name="card[badge]" value="<?= esc($c['badge']) ?>"></div>
                </div>
                <div class="form-row">
                  <div class="field"><label>Title</label><input type="text" name="card[title]" value="<?= esc($c['title']) ?>"></div>
                  <div class="field"><label>Title (Muted)</label><input type="text" name="card[title_muted]" value="<?= esc($c['title_muted']) ?>"></div>
                  <div class="field"><label>Image URL</label><input type="text" name="card[image_url]" value="<?= esc($c['image_url']) ?>"></div>
                </div>
                <div class="form-section">
                  <div class="field"><label>Summary</label><textarea name="card[summary]" rows="3"><?= esc($c['summary']) ?></textarea></div>
                </div>
                <div class="form-row">
                  <div class="field"><label>Pillbox Title</label><input type="text" name="card[pillbox_title]" value="<?= esc($c['pillbox_title']) ?>"></div>
                  <div class="field"><label>Pills (one per line)</label><textarea name="card[pills]" rows="3"><?= esc($c['pills']) ?></textarea></div>
                </div>
                <div class="form-row">
                  <div class="field"><label>Learn More URL</label><input type="text" name="card[learn_more_url]" value="<?= esc($c['learn_more_url']) ?>"></div>
                  <div class="field field-checkbox"><label><input type="checkbox" name="card[learn_more_external]" value="1" <?= !empty($c['learn_more_external'])?'checked':''; ?>> Open in new tab</label></div>
                  <div class="field"><label>Curriculum URL</label><input type="text" name="card[curriculum_url]" value="<?= esc($c['curriculum_url']) ?>"></div>
                  <div class="field field-checkbox"><label><input type="checkbox" name="card[curriculum_external]" value="1" <?= !empty($c['curriculum_external'])?'checked':''; ?>> Open in new tab</label></div>
                  <div class="field"><label>Apply / Inquire URL</label><input type="text" name="card[apply_url]" value="<?= esc($c['apply_url']) ?>"></div>
                </div>
                <div class="form-actions"><button class="btn btn--primary" type="submit">Save</button></div>
              </form>
            </details>
          </div>

          <div class="cms-card-item__actions">
            <?php if ($s !== 'draft'): ?>
              <form method="post"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn btn--ghost" name="update_status" value="draft">→ Draft</button></form>
            <?php endif; ?>
            <?php if ($s !== 'published'): ?>
              <form method="post"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn btn--success" name="update_status" value="published">✓ Publish</button></form>
            <?php endif; ?>
            <?php if ($s !== 'archived'): ?>
              <form method="post"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn btn--outline" name="update_status" value="archived">⤺ Archive</button></form>
            <?php endif; ?>
            <a class="btn btn--danger"
               href="?page=admin_programs_<?= esc($level) ?>&delete=<?= (int)$c['id'] ?>&status=<?= esc($status) ?>"
               onclick="return confirm('Delete this card?')">Delete</a>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  let dirty=false,isSubmitting=false;
  const forms = document.querySelectorAll('form');
  forms.forEach(f=>{
    f.addEventListener('input',()=>{ dirty=true; });
    f.addEventListener('submit',()=>{ isSubmitting=true; dirty=false; });
  });
  window.addEventListener('beforeunload', e=>{
    if(dirty && !isSubmitting){ e.preventDefault(); e.returnValue=''; }
  });
});
</script>
