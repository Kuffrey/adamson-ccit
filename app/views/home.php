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

  <!-- ========================= HERO (no stats overlay) ========================= -->
  <section class="hero">
    <div class="hero__media" aria-hidden="true">
      <!-- original asset, only scaled visually (no compression) -->
      <img src="<?= htmlspecialchars($hero['bg'] ?: '/adamson-ccit/public/assets/images/career-bg.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="Adamson University campus" />
    </div>
    <!-- solid scrim for readability -->
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
        <article class="why__card">
          <div class="why__ico" aria-hidden="true">
            <?= $card['icon'] ?>
          </div>
          <h3><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
          <a class="link" href="<?= htmlspecialchars($card['link_url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($card['link_label'], ENT_QUOTES, 'UTF-8') ?></a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ========================= SPOTLIGHT ========================= -->
  <section class="spotlight">
    <div class="container spotlight__inner">
      <div class="spotlight__media">
        <img src="<?= htmlspecialchars($spotlight['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($spotlight['image_alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
        <?php if (!empty($spotlight['video_url'])): ?>
        <a class="spotlight__play" aria-label="Play video" href="<?= htmlspecialchars($spotlight['video_url'], ENT_QUOTES, 'UTF-8') ?>">
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M8 5v14l11-7z"/></svg>
        </a>
        <?php endif; ?>
      </div>
      <div class="spotlight__copy">
        <span class="eyebrow"><?= htmlspecialchars($spotlight['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        <h3><?= htmlspecialchars($spotlight['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
        <p><?= htmlspecialchars(($spotlight['description'] ?? $spotlight['blurb'] ?? $spotlight['spotlight_blurb'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="spotlight__actions">
          <?php foreach (($spotlight['actions'] ?? []) as $action): ?>
            <a href="<?= htmlspecialchars($action['url'], ENT_QUOTES, 'UTF-8') ?>" class="btn <?= htmlspecialchars($action['class'], ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php endforeach; ?>
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
          <figure class="prog__media">
            <img src="<?= htmlspecialchars($prog['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($prog['image_alt'], ENT_QUOTES, 'UTF-8') ?>" />
          </figure>
          <div class="prog__body">
            <h3><?= htmlspecialchars($prog['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><?= htmlspecialchars($prog['description'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

<!-- ========================= CCIT INDUSTRY PARTNERS (carousel + details) ========================= -->
<section class="partners" aria-labelledby="partners-title">
  <div class="container partners__head">
    <h2 id="partners-title">CCIT Industry Partners</h2>
    <p class="partners__note">Collaborations that support internships, research, and careers.</p>
  </div>

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
              $eventId = !empty($event['id']) ? $event['id'] : 'event-' . $index;
            ?>
            <li class="event">
              <time datetime="<?= htmlspecialchars($event['date'], ENT_QUOTES, 'UTF-8') ?>" class="event__date"><span><?= htmlspecialchars($event['day'], ENT_QUOTES, 'UTF-8') ?></span><?= htmlspecialchars($event['month'], ENT_QUOTES, 'UTF-8') ?></time>
              <div class="event__body">
                <a href="#" class="event-link" data-event-id="<?= htmlspecialchars($eventId, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></a>
                <small><?= htmlspecialchars($event['details'], ENT_QUOTES, 'UTF-8') ?></small>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
        </aside>
      </div>
    </div>
  </section>

  <!-- ========================= FINAL CTA ========================= -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2><?= htmlspecialchars($cta['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($cta['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <a class="btn btn--solid" href="<?= htmlspecialchars($cta['action_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cta['action_label'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
    </div>
  </section>

</main>

<!-- Event Modals -->
<?php foreach (($events ?? []) as $index => $event): 
  $eventId = !empty($event['id']) ? $event['id'] : 'event-' . $index;
?>
<div id="event-modal-<?= htmlspecialchars($eventId, ENT_QUOTES, 'UTF-8') ?>" class="event-modal" style="display: none;">
  <div class="event-modal-backdrop" onclick="closeEventModal()"></div>
  <div class="event-modal-content">
    <div class="event-modal-header">
      <h3><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></h3>
      <button class="event-modal-close" onclick="closeEventModal()" aria-label="Close">&times;</button>
    </div>
    <div class="event-modal-body">
      <div class="event-date-time">
        <div class="event-date">
          <span class="event-day"><?= htmlspecialchars($event['day'], ENT_QUOTES, 'UTF-8') ?></span>
          <span class="event-month"><?= htmlspecialchars($event['month'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="event-time">
          <?php if (!empty($event['start_time'])): ?>
            <p><strong>Time:</strong> <?= htmlspecialchars($event['start_time'], ENT_QUOTES, 'UTF-8') ?>
            <?= !empty($event['end_time']) ? ' - ' . htmlspecialchars($event['end_time'], ENT_QUOTES, 'UTF-8') : '' ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php if (!empty($event['details'])): ?>
        <p><strong>Location:</strong> <?= htmlspecialchars($event['details'], ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <?php if (!empty($event['description'])): ?>
        <div class="event-description">
          <strong>Description:</strong>
          <p><?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
      <?php else: ?>
        <div class="event-description">
          <p>More details about this event will be available soon.</p>
        </div>
      <?php endif; ?>
    </div>
    <div class="event-modal-footer">
      <button class="btn btn--solid" onclick="closeEventModal()">Close</button>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Event Modal Styles -->
<style>
.event-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999;
  background: rgba(0, 0, 0, 0.6);
  animation: fadeIn 0.3s ease;
}

.event-modal-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
}

.event-modal-content {
  position: relative;
  background: white;
  margin: 5% auto;
  padding: 0;
  width: 90%;
  max-width: 500px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  animation: slideIn 0.3s ease;
}

.event-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.event-modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #1e40af;
}

.event-modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.event-modal-close:hover {
  background: #f3f4f6;
}

.event-modal-body {
  padding: 1.5rem;
}

.event-date-time {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
}

.event-date {
  text-align: center;
  background: #1e40af;
  color: white;
  padding: 0.5rem;
  border-radius: 8px;
  min-width: 60px;
}

.event-day {
  display: block;
  font-size: 1.5rem;
  font-weight: bold;
  line-height: 1;
}

.event-month {
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.event-time {
  flex: 1;
}

.event-description {
  margin-top: 1rem;
}

.event-modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  text-align: right;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { transform: translateY(-50px) scale(0.9); opacity: 0; }
  to { transform: translateY(0) scale(1); opacity: 1; }
}

@media (max-width: 768px) {
  .event-modal-content {
    margin: 10% auto;
    width: 95%;
  }
  
  .event-date-time {
    flex-direction: column;
    text-align: center;
  }
}
</style>

</body>
<script>
// Event Modal Functions
function openEventModal(eventId) {
  console.log('Opening modal for event ID:', eventId);
  const modal = document.getElementById('event-modal-' + eventId);
  if (modal) {
    modal.style.display = 'block';
    modal.style.opacity = '1';
    modal.style.visibility = 'visible';
    document.body.style.overflow = 'hidden';
    console.log('Modal opened successfully');
    console.log('Modal element:', modal);
    console.log('Modal computed style:', window.getComputedStyle(modal));
  } else {
    console.error('Modal not found for ID:', eventId);
    // List all available modals for debugging
    const allModals = document.querySelectorAll('[id^="event-modal-"]');
    console.log('Available modals:', Array.from(allModals).map(m => m.id));
  }
}

function closeEventModal() {
  const modals = document.querySelectorAll('.event-modal');
  modals.forEach(modal => {
    modal.style.display = 'none';
  });
  document.body.style.overflow = '';
}

// Set up event listeners for event links
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM Content Loaded - Setting up event listeners');
  
  const eventLinks = document.querySelectorAll('.event-link');
  console.log('Found event links:', eventLinks.length);
  
  const allModals = document.querySelectorAll('[id^="event-modal-"]');
  console.log('Found event modals:', allModals.length);
  console.log('Modal IDs:', Array.from(allModals).map(m => m.id));
  
  eventLinks.forEach((link, index) => {
    const eventId = link.getAttribute('data-event-id');
    console.log(`Event link ${index}: ID = ${eventId}`);
    
    link.addEventListener('click', function(e) {
      e.preventDefault();
      console.log('Event link clicked, event ID:', eventId);
      if (eventId) {
        openEventModal(eventId);
      }
    });
  });
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeEventModal();
  }
});

/* Partners carousel: buttons, keyboard, drag/swipe (with threshold) + details panel (neutral default) */
(function(){
  const viewport = document.querySelector('.pc__viewport');
  const track    = document.querySelector('.pc__track');
  const frames   = Array.from(document.querySelectorAll('.pc__frame'));
  const prevBtn  = document.querySelector('.pc__btn--prev');
  const nextBtn  = document.querySelector('.pc__btn--next');

  const title = document.querySelector('.pcd__title');
  const desc  = document.querySelector('.pcd__desc');
  const link  = document.querySelector('.pcd__link');

  if(!viewport || !track || !frames.length || !title || !desc || !link) return;

  // a11y: keyboard-activatable
  frames.forEach(f => { f.tabIndex = 0; f.setAttribute('aria-pressed','false'); });

  const gapPx = () => parseFloat(getComputedStyle(track).gap || getComputedStyle(track).columnGap || 24);
  const itemWidth = () => (track.querySelector('.pc__item')?.getBoundingClientRect().width || 160);
  const pageStep = () => {
    const step = Math.max(1, Math.round((viewport.clientWidth - gapPx()) / (itemWidth() + gapPx())));
    return step * (itemWidth() + gapPx());
  };

  function updateButtons(){
    const max = viewport.scrollWidth - viewport.clientWidth - 1;
    prevBtn.disabled = viewport.scrollLeft <= 0;
    nextBtn.disabled = viewport.scrollLeft >= max;
  }

  function setDetails(fromFrame){
    const name = fromFrame?.dataset.name || 'Select a partner to learn more';
    const d    = fromFrame?.dataset.desc || 'Click a logo to see a short description here.';
    const url  = fromFrame?.dataset.url  || '';

    title.textContent = name;
    desc.textContent  = d;
    if (url && url !== '#'){ link.href = url; link.hidden = false; }
    else { link.hidden = true; }

    frames.forEach(f => { f.classList.remove('is-selected'); f.setAttribute('aria-pressed','false'); });
    if (fromFrame){ fromFrame.classList.add('is-selected'); fromFrame.setAttribute('aria-pressed','true'); }
  }

  // Buttons
  prevBtn?.addEventListener('click', () => {
    viewport.scrollBy({left: -pageStep(), behavior:'smooth'});
    setTimeout(updateButtons, 150);
  });
  nextBtn?.addEventListener('click', () => {
    viewport.scrollBy({left:  pageStep(), behavior:'smooth'});
    setTimeout(updateButtons, 150);
  });

  // Keyboard on viewport
  viewport.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight'){ e.preventDefault(); nextBtn.click(); }
    if (e.key === 'ArrowLeft'){  e.preventDefault(); prevBtn.click(); }
  });

  // Drag/swipe with click threshold (so taps still click)
  let isDown=false, dragged=false, startX=0, startScroll=0, pointerId=null;
  const DRAG_THRESHOLD = 6; // px

  viewport.addEventListener('pointerdown', (e) => {
    isDown = true; dragged = false; pointerId = e.pointerId;
    startX = e.clientX; startScroll = viewport.scrollLeft;
  });

  viewport.addEventListener('pointermove', (e) => {
    if(!isDown) return;
    const dx = e.clientX - startX;
    if(!dragged && Math.abs(dx) > DRAG_THRESHOLD){
      dragged = true;
      try { viewport.setPointerCapture(pointerId); } catch(_) {}
    }
    if(dragged){ viewport.scrollLeft = startScroll - dx; }
  });

  function endDrag(e){
    if(isDown && !dragged){
      const frame = e?.target?.closest?.('.pc__frame');
      if(frame) setDetails(frame);
    }
    isDown = false; dragged = false;
    try { viewport.releasePointerCapture(pointerId); } catch(_) {}
    setTimeout(updateButtons, 120);
  }
  viewport.addEventListener('pointerup', endDrag);
  viewport.addEventListener('pointercancel', endDrag);

  // Normal click + keyboard on cards
  frames.forEach(f => {
    f.addEventListener('click', () => setDetails(f));
    f.addEventListener('keydown', (e) => {
      if(e.key === 'Enter' || e.key === ' '){
        e.preventDefault();
        setDetails(f);
      }
    });
  });

  // Init (neutral default)
  updateButtons();
  setDetails(null);
  window.addEventListener('resize', updateButtons);
  viewport.addEventListener('scroll', updateButtons, {passive:true});
})();
</script>


</html>