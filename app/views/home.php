<?php
require_once __DIR__ . '/../models/Event.php';

// Fetch only published events for the homepage
$events = Event::list('published');

// Ensure global variable is accessible
global $navigationLinks;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <script src="/adamson-ccit/public/assets/js/home.js" defer></script>
</head>
<body>

<main>

  <!-- ========================= HERO ========================= -->
  <section class="hero">
    <div class="hero__media" aria-hidden="true">
      <img src="<?= htmlspecialchars($hero['bg'] ?: '/adamson-ccit/public/assets/images/career-bg.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="Adamson University campus" />
    </div>
    <div class="hero__scrim" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__copy">
        <span class="hero__eyebrow"><?= htmlspecialchars($hero['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        <h1 class="hero__title">
          <?= nl2br(htmlspecialchars($hero['title'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
        </h1>
        <p class="hero__subtitle"><?= htmlspecialchars($hero['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        <div class="hero__actions">
          <?php foreach (($hero['actions'] ?? []) as $action): ?>
            <a href="<?= htmlspecialchars($action['url'], ENT_QUOTES, 'UTF-8') ?>" class="btn <?= htmlspecialchars($action['class'], ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================= QUICK ACTIONS ========================= -->
  <section class="quick">
    <div class="container">
      <ul class="quick__grid" role="list">
        <?php foreach (($quickActions ?? []) as $action): ?>
          <li>
            <a class="quick__card" href="<?= htmlspecialchars($action['url'], ENT_QUOTES, 'UTF-8') ?>">
              <span class="quick__ico" aria-hidden="true">
                <?= $action['icon'] ?>
              </span>
              <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ========================= WHY CHOOSE CCIT ========================= -->
  <section class="why">
    <div class="container">
      <header class="sec__head">
        <h2><?= htmlspecialchars($why['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="sec__kicker"><?= htmlspecialchars($why['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
      </header>

      <div class="why__grid">
        <?php foreach (($why['cards'] ?? []) as $card): ?>
        <a href="<?= htmlspecialchars($card['link_url'], ENT_QUOTES, 'UTF-8') ?>" class="why__card">
          <?php if (!empty($card['image'])): ?>
          <div class="why__image">
            <img src="<?= htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?>" />
            <div class="why__overlay">
              <div class="why__ico" aria-hidden="true">
                <?= $card['icon'] ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
          <div class="why__content">
            <div class="why__header">
              <?php if (empty($card['image'])): ?>
              <div class="why__ico" aria-hidden="true">
                <?= $card['icon'] ?>
              </div>
              <?php endif; ?>
              <h3><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            </div>
            <div class="why__body">
              <p><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
              <span class="why__link"><?= htmlspecialchars($card['link_label'] ?: 'Learn more', ENT_QUOTES, 'UTF-8') ?> →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

<!-- ========================= SPOTLIGHT ========================= -->
<section class="spotlight">
  <div class="container">
    <div class="spotlight__inner">
      <div class="spotlight__media">
        <?php if (!empty($spotlight['video_url'])): ?>
          <video controls class="spotlight__video">
            <source src="<?= htmlspecialchars($spotlight['video_url'], ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        <?php else: ?>
          <img src="<?= htmlspecialchars($spotlight['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               alt="<?= htmlspecialchars($spotlight['image_alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
        <?php endif; ?>
      </div>

      <div class="spotlight__copy">
        <span class="eyebrow"><?= htmlspecialchars($spotlight['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        <h3><?= htmlspecialchars($spotlight['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= htmlspecialchars(($spotlight['description'] ?? $spotlight['blurb'] ?? $spotlight['spotlight_blurb'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="spotlight__actions">
          <?php foreach (($spotlight['actions'] ?? []) as $action): ?>
            <a href="<?= htmlspecialchars($action['url'], ENT_QUOTES, 'UTF-8') ?>"
               class="btn <?= htmlspecialchars($action['class'], ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- ========================= PROGRAMS ========================= -->
  <section class="programs">
    <div class="container">
      <header class="sec__head">
        <h2><?= htmlspecialchars($programs['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="sec__kicker"><?= htmlspecialchars($programs['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
      </header>

      <div class="prog__grid">
        <?php foreach (($programs['list'] ?? []) as $prog): ?>
        <a class="prog__card" href="<?= htmlspecialchars($prog['url'], ENT_QUOTES, 'UTF-8') ?>">
          <div class="prog__header">
            <div class="prog__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
              </svg>
            </div>
            <h3><?= htmlspecialchars($prog['title'], ENT_QUOTES, 'UTF-8') ?></h3>
          </div>
          <div class="prog__body">
            <p><?= htmlspecialchars($prog['description'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

<!-- ========================= CCIT INDUSTRY PARTNERS (carousel + details) ========================= -->
<section class="partners" id="partners" aria-labelledby="partners-title">
  <div class="container">
  <header class="sec__head">
    <h2 id="partners-title">CCIT Industry Partners</h2>
    <p class="partners__note">Collaborations that support internships, research, and careers.</p>
  </header>

  <div class="partners-carousel" role="region" aria-label="CCIT Industry Partners slider">
    <button class="pc__btn pc__btn--prev" aria-label="Previous partners" disabled>
      <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M15.5 19 8.5 12l7-7 1.5 1.5L11.5 12l5.5 5.5z"/></svg>
    </button>

    <div class="pc__viewport" tabindex="0">
<ul class="pc__track" role="list">
  <?php foreach (($partners ?? []) as $partner): ?>
  <li class="pc__item">
    <button class="pc__frame" type="button"
            data-name="<?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8') ?>"
            data-desc="<?= htmlspecialchars($partner['description'], ENT_QUOTES, 'UTF-8') ?>"
            data-url="<?= htmlspecialchars($partner['url'], ENT_QUOTES, 'UTF-8') ?>">
      <div class="pc__logo"><img src="<?= htmlspecialchars($partner['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8') ?>"></div>
      <span class="pc__caption"><?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8') ?></span>
    </button>
  </li>
  <?php endforeach; ?>
</ul>
    </div>

    <button class="pc__btn pc__btn--next" aria-label="Next partners">
      <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="m8.5 19 7-7-7-7-1.5 1.5L12.5 12l-5.5 5.5z"/></svg>
    </button>
  </div>

  <!-- Details panel (updates when a logo is clicked) -->
  <div class="pc__details" aria-live="polite">
    <div class="container pcd__inner">
      <h3 class="pcd__title">Select a partner to learn more</h3>
      <p class="pcd__desc">Click a logo to see a short description here.</p>
      <a class="pcd__link" href="#" target="_blank" rel="noopener" hidden>Visit Website</a>
    </div>
  </div>
  </div>
</section>

  <!-- ========================= NEWS & EVENTS ========================= -->
  <section class="news">
    <div class="container">
      <header class="sec__head sec__head--row">
        <h2><?= htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <a class="link" href="<?= htmlspecialchars($news['view_all_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">View all</a>
      </header>

      <div class="news__grid">
        <?php foreach (($news['articles'] ?? []) as $article): ?>
        <article class="news__card">
          <a href="<?= htmlspecialchars($article['url'], ENT_QUOTES, 'UTF-8') ?>" class="news__media">
            <img src="<?= htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" />
            <span class="chip<?= !empty($article['chip_class']) ? ' ' . htmlspecialchars($article['chip_class'], ENT_QUOTES, 'UTF-8') : '' ?>"><?= htmlspecialchars($article['chip'], ENT_QUOTES, 'UTF-8') ?></span>
          </a>
          <div class="news__body">
            <h3><a href="<?= htmlspecialchars($article['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></a></h3>
            <p><?= htmlspecialchars($article['summary'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </article>
        <?php endforeach; ?>

        <aside class="events">
          <h3 class="events__title">Upcoming Events</h3>
          <ul class="events__list" role="list">
            <?php foreach (($events ?? []) as $index => $event):
  if (!empty($event['is_draft']) && $event['is_draft']) continue;

  // Stable DOM id used by both the link and modal
  $eventDomId = !empty($event['id']) ? (string)$event['id'] : 'x' . (string)$index;

  // Extract day and month from start_at
  $startDate = !empty($event['start_at']) ? new DateTime($event['start_at']) : null;
  $day   = $startDate ? $startDate->format('d') : 'N/A';
  $month = $startDate ? $startDate->format('M') : 'N/A';
?>
<li class="event">
  <time datetime="<?= htmlspecialchars($event['start_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="event__date">
    <span><?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?></span>
    <?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>
  </time>
  <div class="event__body">
    <a href="#" class="event-link" data-event-id="<?= htmlspecialchars($eventDomId, ENT_QUOTES, 'UTF-8') ?>">
      <?= htmlspecialchars($event['title'] ?? 'Untitled Event', ENT_QUOTES, 'UTF-8') ?>
    </a>
    <small><?= htmlspecialchars($event['details'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
  </div>
</li>
<?php endforeach; ?>

          </ul>
        </aside>
      </div>
    </div>
  </section>

  <!-- ========================= NAVIGATION LINKS ========================= -->
  <nav class="navigation">
    <ul class="navigation__list">
      <?php foreach (($navigationLinks ?? []) as $link): ?>
        <li class="navigation__item">
          <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <!-- ========================= FINAL CTA ========================= -->
  <section class="cta">
    <div class="container">
      <div class="container cta__inner">
        <div>
          <h2><?= htmlspecialchars($cta['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
          <p><?= htmlspecialchars($cta['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a class="btn btn--solid" href="<?= htmlspecialchars($cta['action_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($cta['action_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </a>
      </div>
    </div>
  </section>
</main>

<!-- Event Modals — reuse Announcement modal styles -->
<?php foreach (($events ?? []) as $index => $event):
  if (!empty($event['is_draft']) && $event['is_draft']) continue;

  $eventDomId  = !empty($event['id']) ? (string)$event['id'] : 'x' . (string)$index;
  $title       = htmlspecialchars($event['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8');
  $description = nl2br(htmlspecialchars($event['description'] ?? '', ENT_QUOTES, 'UTF-8'));
  $category    = htmlspecialchars(ucfirst($event['category'] ?? 'General'), ENT_QUOTES, 'UTF-8');
  $image       = htmlspecialchars($event['image_url'] ?? '/adamson-ccit/public/assets/images/news/sample5.jpg', ENT_QUOTES, 'UTF-8');
  $location    = htmlspecialchars($event['location'] ?? 'TBA', ENT_QUOTES, 'UTF-8');
  $startRaw    = $event['start_at'] ?? '';
  $formattedDate = $startRaw ? date('F j, Y', strtotime($startRaw)) : 'TBA';
?>
<div id="event-modal-<?= htmlspecialchars($eventDomId, ENT_QUOTES, 'UTF-8') ?>" class="modal announcement-modal" role="dialog" aria-modal="true" style="display:none;">
  <div class="modal-backdrop announcement-modal-backdrop"></div>
  <div class="modal-content announcement-modal-content">
    <div class="modal-header announcement-modal-header">
      <div class="modal-header-info announcement-header-info">
        <span class="modal-category announcement-category"><?= $category ?></span>
        <span class="modal-date announcement-date"><?= htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <button class="modal-close announcement-modal-close" aria-label="Close" data-modal-focus>&times;</button>
    </div>

    <div class="modal-body announcement-modal-body">
      <h3><?= $title ?></h3>
      <?php if ($image): ?>
        <img src="<?= $image ?>" alt="<?= $title ?>" class="announcement-image">
      <?php endif; ?>
      <?php if ($location): ?>
        <p class="announcement-excerpt"><strong>📍 Location:</strong> <?= $location ?></p>
      <?php endif; ?>
      <div class="announcement-content"><?= $description ?: '<p>More details about this event will be available soon.</p>' ?></div>
    </div>

    <div class="modal-footer announcement-modal-footer">
      <button class="btn btn--solid" onclick="closeModal()">Close</button>
    </div>
  </div>
</div>
<?php endforeach; ?>


</body>
<script>
/* ========================= MODALS (Unified w/ Announcements) ========================= */
(function () {
  window.openModal = function (id) {
    var m = document.getElementById(id);
    if (!m) return;
    m.classList.add('modal--open');
    m.style.display = 'block';
    // lock scroll
    document.body.dataset.modalScrollLock = document.body.style.overflow || '';
    document.body.style.overflow = 'hidden';
    // focus something interactable
    var f = m.querySelector('[data-modal-focus], .modal-close, a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
    if (f) { try { f.focus(); } catch(_) {} }
  };

  window.closeModal = function () {
    document.querySelectorAll('.modal, .announcement-modal').forEach(function (m) {
      m.classList.remove('modal--open');
      m.style.display = 'none';
    });
    // restore scroll
    document.body.style.overflow = document.body.dataset.modalScrollLock || '';
  };

  // Backdrop & close buttons
  document.addEventListener('click', function (e) {
    if (e.target.matches('.modal-backdrop, .announcement-modal-backdrop')) closeModal();
    if (e.target.matches('.modal-close, .announcement-modal-close')) { e.preventDefault(); closeModal(); }
  });

  // ESC key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  // Wire up homepage event links to their modals
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.event-link').forEach(function (link) {
      link.addEventListener('click', function (ev) {
        ev.preventDefault();
        var id = this.getAttribute('data-event-id');
        if (id) openModal('event-modal-' + id);
      });
    });
  });
})();

/* ========================= SMOOTH SCROLL (anchors like #partners) ========================= */
(function () {
  function smoothTo(target) {
    var offset = 100; // leave room for sticky header
    var rect = target.getBoundingClientRect();
    var y = rect.top + window.pageYOffset - offset;
    window.scrollTo({ top: y, behavior: 'smooth' });
    // subtle highlight
    target.style.transition = 'all 0.5s ease';
    target.style.transform = 'scale(1.01)';
    target.style.boxShadow = '0 12px 40px rgba(0, 128, 201, 0.2)';
    target.style.backgroundColor = 'rgba(0, 128, 201, 0.02)';
    setTimeout(function () {
      target.style.transform = '';
      target.style.boxShadow = '';
      target.style.backgroundColor = '';
    }, 1500);
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
      anchor.addEventListener('click', function (e) {
        var href = this.getAttribute('href');
        if (!href || href === '#') return;
        var id = href.slice(1);
        var target = document.getElementById(id);
        if (target) {
          e.preventDefault();
          smoothTo(target);
        }
      });
    });
  });
})();

/* ========================= PARTNERS CAROUSEL ========================= */
(function () {
  const viewport = document.querySelector('.pc__viewport');
  const track    = document.querySelector('.pc__track');
  const frames   = Array.from(document.querySelectorAll('.pc__frame'));
  const prevBtn  = document.querySelector('.pc__btn--prev');
  const nextBtn  = document.querySelector('.pc__btn--next');

  const title = document.querySelector('.pcd__title');
  const desc  = document.querySelector('.pcd__desc');
  const link  = document.querySelector('.pcd__link');

  if (!viewport || !track || !frames.length || !title || !desc || !link) return;

  // a11y: make frames keyboard-activatable
  frames.forEach(f => { f.tabIndex = 0; f.setAttribute('aria-pressed', 'false'); });

  const gapPx = () => parseFloat(getComputedStyle(track).gap || getComputedStyle(track).columnGap || 24);
  const itemWidth = () => (track.querySelector('.pc__item')?.getBoundingClientRect().width || 160);
  const pageStep = () => {
    const step = Math.max(1, Math.round((viewport.clientWidth - gapPx()) / (itemWidth() + gapPx())));
    return step * (itemWidth() + gapPx());
  };

  function updateButtons() {
    const max = viewport.scrollWidth - viewport.clientWidth - 1;
    prevBtn.disabled = viewport.scrollLeft <= 0;
    nextBtn.disabled = viewport.scrollLeft >= max;
  }

  function setDetails(fromFrame) {
    const name = fromFrame?.dataset.name || 'Select a partner to learn more';
    const d    = fromFrame?.dataset.desc || 'Click a logo to see a short description here.';
    const url  = fromFrame?.dataset.url  || '';

    title.textContent = name;
    desc.textContent  = d;
    if (url && url !== '#') { link.href = url; link.hidden = false; }
    else { link.hidden = true; }

    frames.forEach(f => { f.classList.remove('is-selected'); f.setAttribute('aria-pressed', 'false'); });
    if (fromFrame) { fromFrame.classList.add('is-selected'); fromFrame.setAttribute('aria-pressed', 'true'); }
  }

  prevBtn?.addEventListener('click', () => {
    viewport.scrollBy({ left: -pageStep(), behavior: 'smooth' });
    setTimeout(updateButtons, 150);
  });
  nextBtn?.addEventListener('click', () => {
    viewport.scrollBy({ left:  pageStep(), behavior: 'smooth' });
    setTimeout(updateButtons, 150);
  });

  // Keyboard on viewport
  viewport.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') { e.preventDefault(); nextBtn.click(); }
    if (e.key === 'ArrowLeft')  { e.preventDefault(); prevBtn.click(); }
  });

  // Drag/swipe with click threshold
  let isDown = false, dragged = false, startX = 0, startScroll = 0, pointerId = null;
  const DRAG_THRESHOLD = 6;

  viewport.addEventListener('pointerdown', (e) => {
    isDown = true; dragged = false; pointerId = e.pointerId;
    startX = e.clientX; startScroll = viewport.scrollLeft;
  });
  viewport.addEventListener('pointermove', (e) => {
    if (!isDown) return;
    const dx = e.clientX - startX;
    if (!dragged && Math.abs(dx) > DRAG_THRESHOLD) {
      dragged = true;
      try { viewport.setPointerCapture(pointerId); } catch (_) {}
    }
    if (dragged) viewport.scrollLeft = startScroll - dx;
  });
  function endDrag(e) {
    if (isDown && !dragged) {
      const frame = e?.target?.closest?.('.pc__frame');
      if (frame) setDetails(frame);
    }
    isDown = false; dragged = false;
    try { viewport.releasePointerCapture(pointerId); } catch (_) {}
    setTimeout(updateButtons, 120);
  }
  viewport.addEventListener('pointerup', endDrag);
  viewport.addEventListener('pointercancel', endDrag);

  // Click + keyboard on cards
  frames.forEach(f => {
    f.addEventListener('click', () => setDetails(f));
    f.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        setDetails(f);
      }
    });
  });

  // Init
  updateButtons();
  setDetails(null);
  window.addEventListener('resize', updateButtons);
  viewport.addEventListener('scroll', updateButtons, { passive: true });
})();
</script>

<?php
// Ensure global variable is accessible
global $navigationLinks;

// Debugging statement to log the value of $navigationLinks
error_log('Navigation Links: ' . print_r($navigationLinks, true));
?>

</html>