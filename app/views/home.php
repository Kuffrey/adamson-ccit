<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css">
  <script src="/adamson-ccit/public/assets/js/home.js" defer></script>
  <style>
    /* Program cards without images - professional solid color design */
    .prog__header {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 28px 28px 20px;
      border-bottom: 1px solid #e2e8f0;
    }
    .prog__icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 56px;
      height: 56px;
      background: #003169;
      border-radius: 16px;
      color: white;
      flex-shrink: 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px rgba(0, 49, 105, 0.15);
    }
    .prog__header h3 {
      margin: 0;
      font-size: 1.35rem;
      font-weight: 800;
      color: #003169;
      line-height: 1.3;
      transition: all 0.3s ease;
      letter-spacing: -0.02em;
    }
    .prog__body {
      padding: 20px 28px 28px;
    }
    .prog__body p {
      margin: 0;
      color: #64748b;
      line-height: 1.7;
      font-size: 1rem;
    }
    .prog__card:hover .prog__icon {
      background: #0080c9;
      transform: translateY(-4px) scale(1.1);
      box-shadow: 0 12px 28px rgba(0, 128, 201, 0.25);
    }
    .prog__card:hover .prog__header h3 {
      color: #0080c9;
      transform: translateY(-2px);
    }
    .prog__card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      overflow: hidden;
      background: white;
      text-decoration: none;
      display: block;
      cursor: pointer;
      position: relative;
    }
    .prog__card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: #003169;
      transform: scaleX(0);
      transition: transform 0.3s ease;
      transform-origin: left;
    }
    .prog__card:hover::before {
      transform: scaleX(1);
    }
    .prog__card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 40px rgba(0, 49, 105, 0.18);
      border-color: #0080c9;
    }
    .prog__card:active {
      transform: translateY(-2px);
      transition-duration: 0.1s;
    }
    
    /* Enhanced Why Choose CCIT section */
    .why__card {
      display: block !important;
      text-decoration: none;
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s ease;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(0, 49, 105, 0.06);
    }
    .why__card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 60px rgba(0, 49, 105, 0.15);
      border-color: #0080c9;
      text-decoration: none;
    }
    .why__image {
      position: relative;
      height: 220px;
      overflow: hidden;
    }
    .why__image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
      filter: brightness(1.1);
    }
    .why__card:hover .why__image img {
      transform: scale(1.08);
    }
    .why__overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(0, 49, 105, 0.85) 0%, rgba(0, 128, 201, 0.75) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: all 0.3s ease;
    }
    .why__card:hover .why__overlay {
      opacity: 1;
    }
    .why__overlay .why__ico {
      background: rgba(255, 255, 255, 0.2) !important;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.3);
      transform: scale(1.2);
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1.2); }
      50% { transform: scale(1.3); }
    }
    .why__content {
      padding: 28px;
    }
    .why__header {
      display: flex;
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 20px;
    }
    .why__ico {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      background: #0080c9;
      border-radius: 20px;
      color: white;
      flex-shrink: 0;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 128, 201, 0.3);
    }
    .why__card:hover .why__content .why__ico {
      background: #003169;
      transform: translateY(-4px) scale(1.05);
      box-shadow: 0 8px 25px rgba(0, 49, 105, 0.4);
    }
    .why__header h3 {
      margin: 0;
      font-size: 1.4rem;
      font-weight: 800;
      color: #003169;
      line-height: 1.3;
      transition: all 0.3s ease;
      letter-spacing: -0.02em;
    }
    .why__card:hover .why__header h3 {
      color: #0080c9;
      transform: translateY(-2px);
    }
    .why__body {
      margin: 0;
    }
    .why__body p {
      margin: 0 0 16px 0;
      color: #64748b;
      line-height: 1.7;
      font-size: 1rem;
    }
    .why__link {
      display: inline-flex;
      align-items: center;
      font-weight: 700;
      color: #0080c9;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid transparent;
    }
    .why__card:hover .why__link {
      color: #003169;
      border-bottom-color: #003169;
      transform: translateX(4px);
    }
    /* Special styling for cards without images */
    .why__card:not(:has(.why__image)) .why__header {
      align-items: flex-start;
    }
    .why__card:not(:has(.why__image)) .why__ico {
      background: #00713D;
      box-shadow: 0 4px 15px rgba(0, 113, 61, 0.3);
    }
    .why__card:not(:has(.why__image)) .why__ico:hover {
      background: #003169;
      box-shadow: 0 8px 25px rgba(0, 49, 105, 0.4);
    }
    /* Additional polish for the section */
    .why .sec__head h2 {
      font-size: 2.5rem;
      font-weight: 900;
      color: #003169;
      margin-bottom: 12px;
      letter-spacing: -0.02em;
    }
    .why .sec__kicker {
      font-size: 1.2rem;
      color: #64748b;
      font-weight: 400;
      line-height: 1.6;
    }
    
    /* Partners section highlighting */
    .partners {
      scroll-margin-top: 100px; /* Native scroll padding */
      transition: all 0.5s ease;
    }
    .partners.highlighted {
      background: rgba(0, 128, 201, 0.02);
      box-shadow: 0 12px 40px rgba(0, 128, 201, 0.2);
    }
  </style>
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
  
  // Event modal setup
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

  // Smooth scrolling for internal links (like #partners)
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      const targetId = href.substring(1);
      const targetElement = document.getElementById(targetId);
      
      console.log('Anchor clicked:', href);
      console.log('Target ID:', targetId);
      console.log('Target element found:', !!targetElement);
      
      if (targetElement) {
        e.preventDefault();
        
        // Calculate offset for sticky headers if any
        const offset = 100; // Increased offset for better visibility
        const elementPosition = targetElement.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;

        console.log('Scrolling to position:', offsetPosition);

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
        
        // Add a highlight effect to the target section
        targetElement.style.transition = 'all 0.5s ease';
        targetElement.style.transform = 'scale(1.01)';
        targetElement.style.boxShadow = '0 12px 40px rgba(0, 128, 201, 0.2)';
        targetElement.style.backgroundColor = 'rgba(0, 128, 201, 0.02)';
        
        setTimeout(() => {
          targetElement.style.transform = '';
          targetElement.style.boxShadow = '';
          targetElement.style.backgroundColor = '';
        }, 1500);
      } else {
        console.error('Target element not found for:', targetId);
        // List all available IDs for debugging
        const allIds = Array.from(document.querySelectorAll('[id]')).map(el => el.id);
        console.log('Available IDs:', allIds);
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