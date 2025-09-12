<?php
/* footer.php — dynamic footer */

if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

/* --- Load DB config directly --- */
$config = require __DIR__ . '/../../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

require_once __DIR__ . '/../../models/FooterRepository.php';

$settings   = FooterRepository::getSettings($pdo);
$quickLinks = FooterRepository::getQuickLinks($pdo);
$socials    = FooterRepository::getSocials($pdo);

/* Helper: choose SVG icon by platform (keeps your exact look) */
function social_svg(string $platform): string {
  switch (strtolower($platform)) {
    case 'facebook':
      return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="#fff" d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.19 8.44 9.94v-7.03H7.9v-2.9h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88c1.09 0 2.23.2 2.23.2v2.46h-1.25c-1.23 0-1.62.77-1.62 1.56v1.87h2.76l-.44 2.9h-2.32V22c4.78-.75 8.44-4.92 8.44-9.94z"/></svg>';
    case 'tiktok':
      return '<svg viewBox="0 0 48 48" width="20" height="20" aria-hidden="true"><path fill="#fff" d="M41.5 17.5c-3.6 0-6.5-2.9-6.5-6.5V6.5c0-1-0.7-1.5-1.5-1.5h-4c-1 0-1.5 0.7-1.5 1.5v25.1c0 2.2-1.8 4-4 4s-4-1.8-4-4 1.8-4 4-4c0.8 0 1.5-0.7 1.5-1.5v-4c0-1-0.7-1.5-1.5-1.5-5.2 0-9.5 4.3-9.5 9.5s4.3 9.5 9.5 9.5 9.5-4.3 9.5-9.5V19c2.1 1.1 4.4 1.7 6.5 1.7 1 0 1.5-0.7 1.5-1.5v-1.7c0-1-0.7-1.5-1.5-1.5z"/></svg>';
    case 'x':
    case 'twitter':
      return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="#fff" d="M3 3l8.1 9.3L3.5 21H6l5.8-6.6L16.7 21H21l-8.5-9.8L20.5 3H18L12.8 9.2 8 3H3z"/></svg>';
    case 'youtube':
      return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="#fff" d="M23.5 6.2s-.2-1.6-.8-2.3c-.8-.8-1.7-.9-2.1-1C17.6 2.5 12 2.5 12 2.5h0s-5.6 0-8.6.4c-.4.1-1.3.2-2.1 1-.6.7-.8 2.3-.8 2.3S0 8.1 0 10v1.9c0 1.9.2 3.8.2 3.8s.2 1.6.8 2.3c.8.8 1.9.8 2.4.9 1.7.2 7.4.4 7.4.4s5.6 0 8.6-.4c.4-.1 1.3-.2 2.1-1 .6-.7.8-2.3.8-2.3S24 13.9 24 12V10c0-1.9-.5-3.8-.5-3.8zM9.5 14.5V7.9l6.3 3.3-6.3 3.3z"/></svg>';
    case 'instagram':
      return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="#fff" d="M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm0 2h10c1.7 0 3 1.3 3 3v10c0 1.7-1.3 3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3zm5 2.8A5.2 5.2 0 1 0 17.2 12 5.2 5.2 0 0 0 12 6.8zm0 2A3.2 3.2 0 1 1 8.8 12 3.2 3.2 0 0 1 12 8.8zM18 6.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>';
    case 'linkedin':
      return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="#fff" d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM0 8h5v16H0V8zm7.5 0h4.8v2.2h.1c.7-1.3 2.3-2.7 4.7-2.7 5 0 5.9 3.3 5.9 7.6V24h-5v-7.3c0-1.8 0-4.1-2.5-4.1s-2.9 2-2.9 4v7.4h-5V8z"/></svg>';
    default:
      return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="#fff"/></svg>';
  }
}
?>

<!-- Font (same as header) -->
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

<!-- Font Awesome 6 Free CDN for footer icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
  integrity="sha512-papmQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw=="
  crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
  :root{
    /* Mirror header vars */
    --green:#00713D;         /* PRIMARY */
    --blue:#003169;          /* SECONDARY */
    --blue-accent:#0080c9;   /* Accent */
    --ink:#111827;
    --maxw:1200px;
  }

  /* ===== Sticky footer layout ===== */
  html, body { height: 100%; margin: 0; }
  body {
    display: flex;
    flex-direction: column;
    min-height: 100dvh;
    font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, Arial, sans-serif;
  }
  main { flex: 1 0 auto; }
  .ccit-footer { margin-top: auto; }

  .ccit-footer{
    background: var(--blue);
    color:#e5eef7;
    border-top: 4px solid var(--green);
  }

  .ccit-footer__wrap{
    max-width: var(--maxw);
    margin-inline: auto;
    padding: 32px 20px 26px;
    display: grid;
    grid-template-columns: 1.4fr 1fr auto;
    gap: clamp(20px, 3.2vw, 40px);
    align-items: start;
  }

  /* Section titles */
  .ccit-footer h3,
  .ccit-socialrow__title{
    margin: 0 0 12px;
    font-weight: 800;
    font-size: 16px;
    letter-spacing: .02em;
    color: #fff;
    position: relative;
  }
  .ccit-footer h3::after,
  .ccit-socialrow__title::after{
    content:"";
    display:block;
    width:36px; height:3px;
    margin-top:8px;
    background: linear-gradient(90deg, var(--green), var(--blue-accent));
    border-radius: 999px;
  }

  /* Contact */
  .ccit-contact__list{
    list-style:none; margin:0; padding:0; display:grid; gap:12px;
  }
  .ccit-contact__list li{
    display:flex; gap:10px; align-items:flex-start; line-height:1.5;
    color:#eaf2fb;
  }
  .ccit-contact__list svg{ flex:0 0 18px; margin-top:2px; }
  .ccit-contact a{
    color:#eaf2fb; text-decoration:none;
  }
  .ccit-contact a:hover{ color:#fff; text-decoration:underline; }

  /* Quick links */
  .ccit-links ul{ list-style:none; margin:0; padding:0; display:grid; gap:8px; }
  .ccit-links a{
    color:#eaf2fb; text-decoration:none; font-weight:600;
  }
  .ccit-links a:hover{
    color:#ffffff; text-decoration:underline;
  }

  /* Social */
  .ccit-socialrow{ display:flex; flex-direction:column; gap:12px; align-items:flex-start; }
  .ccit-socialrow__icons{ display:flex; gap:10px; }
  .ccit-ico{
    display:inline-flex; align-items:center; justify-content:center;
    width:40px; height:40px; border-radius:10px;
    background: rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.16);
    transition: transform .12s ease, background .12s ease;
  }
  .ccit-ico:hover{ transform: translateY(-1px); background: rgba(255,255,255,.12); }
  .ccit-ico svg{ display:block; }

  /* Legal bar */
  .ccit-legal{
    border-top: 1px solid rgba(255,255,255,.14);
    background: #06214f; /* darker than --blue for separation */
    color:#cfe0f5;
  }
  .ccit-legal--centered{
    display:flex; flex-direction:column; gap:8px; align-items:center; text-align:center;
    padding: 14px 20px 18px;
  }
  .ccit-legal__copyright{ font-size:13px; }
  .ccit-legal__seps{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:center; }
  .ccit-legal__pipe{ opacity:.6; }
  .ccit-legal__seps a{
    color:#dbe8f9; text-decoration:none; font-weight:600; font-size:13px;
  }
  .ccit-legal__seps a:hover{ color:#ffffff; text-decoration:underline; }

  /* Footer links focus ring (match header feel) */
  .ccit-footer a:focus-visible{
    outline:3px solid color-mix(in oklab, var(--green) 75%, white);
    outline-offset:2px; border-radius:8px;
  }

  /* Responsive */
  @media (max-width: 960px){
    .ccit-footer__wrap{ grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 640px){
    .ccit-footer__wrap{ grid-template-columns: 1fr; }
    .ccit-socialrow{ align-items:center; }
    .ccit-socialrow__title{ text-align:center; width:100%; }
  }
</style>

<footer class="ccit-footer">
  <div class="ccit-footer__wrap">
    <!-- Contact -->
    <div class="ccit-contact">
      <h3>Contact Us</h3>
      <ul class="ccit-contact__list">
        <li>
          <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="#fff" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
          <span>
            <?= e($settings['org_name'] ?? 'Adamson University'); ?><br>
            <?= e($settings['addr_line1'] ?? '900 San Marcelino Street, Ermita'); ?><br>
            <?= e($settings['addr_line2'] ?? '1000 Manila, Philippines'); ?>
          </span>
        </li>
        <li>
          <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="#fff" d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.11-.21c1.21.49 2.53.76 3.88.76a1 1 0 0 1 1 1v3.5a1 1 0 0 1-1 1C10.07 22 2 13.93 2 4.5A1 1 0 0 1 3 3.5h3.5a1 1 0 0 1 1 1c0 1.35.27 2.67.76 3.88a1 1 0 0 1-.21 1.11l-2.2 2.2Z"/></svg>
          <a href="tel:<?= e($settings['phone_tel'] ?? '+63285242011'); ?>">
            <?= e($settings['phone_display'] ?? '(02) 8524-20-11 loc. 324'); ?>
          </a>
        </li>
        <li>
          <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="#fff" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/></svg>
          <a href="mailto:<?= e($settings['email'] ?? 'ccit@adamson.edu.ph'); ?>">
            <?= e($settings['email'] ?? 'ccit@adamson.edu.ph'); ?>
          </a>
        </li>
        <li style="display:flex; align-items:center; gap:10px;">
          <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="#fff" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 2c1.657 0 3 1.343 3 3h-6c0-1.657 1.343-3 3-3zm-7.938 7A8.003 8.003 0 0 1 12 4v4H4.062zm0 2H12v4H4.062A8.003 8.003 0 0 1 4.062 11zm1.938 5.062A7.963 7.963 0 0 1 12 20v-4H6zm2.062-1.938H12v-4H8.062A7.963 7.963 0 0 1 8.062 14.062zM12 20a8.003 8.003 0 0 1-7.938-7H12v7.938zM20 12a8.003 8.003 0 0 1-7.938 7V12h7.938zm0-2h-7.938V4.062A8.003 8.003 0 0 1 20 12zm-1.938-5.062A7.963 7.963 0 0 1 12 4v4h4.938A7.963 7.963 0 0 1 18.062 6.938zM16 12v4h-4v-4h4zm-4-2V6h4v4h-4zm0 6v4h4v-4h-4z"/></svg>
          <a href="<?= e($settings['website_url'] ?? 'https://www.adamson.edu.ph/cfe/'); ?>" target="_blank" rel="noopener" style="color:#c8e6c9; word-break:break-all;">
            <?= e($settings['website_label'] ?? 'https://www.adamson.edu.ph/cfe/'); ?>
          </a>
        </li>
      </ul>
    </div>

    <!-- Quick Links -->
    <div class="ccit-links">
      <h3>Quick Links</h3>
      <ul>
        <?php if ($quickLinks): ?>
          <?php foreach ($quickLinks as $lk): ?>
            <li>
              <a href="<?= e($lk['url']); ?>" <?= !empty($lk['is_external']) ? 'target="_blank" rel="noopener"' : '' ?>>
                <?= e($lk['label']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback if DB empty -->
          <li><a href="/adamson-ccit/public/index.php?page=admission_freshman">Freshman Admission</a></li>
          <li><a href="/adamson-ccit/public/index.php?page=admission_transfer">Transferee Admission</a></li>
          <li><a href="/adamson-ccit/public/index.php?page=admission_foreign">Foreign Admission</a></li>
          <li><a href="/adamson-ccit/public/index.php?page=admission_requirements">Requirements</a></li>
          <li><a href="/adamson-ccit/public/index.php?page=admission_faq">Admission FAQ</a></li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Social -->
    <div class="ccit-socialrow" aria-label="Social media">
      <span class="ccit-socialrow__title">Stay Connected</span>
      <div class="ccit-socialrow__icons">
        <?php if ($socials): ?>
          <?php foreach ($socials as $s): ?>
            <a href="<?= e($s['url']); ?>" target="_blank" rel="noopener" aria-label="<?= e($s['label']); ?>" class="ccit-ico">
              <?= social_svg($s['platform']); ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback if DB empty -->
          <a href="https://www.facebook.com/p/Adamson-University-College-of-Computing-and-Information-Technology-61566114474650/"
             target="_blank" rel="noopener" aria-label="Facebook" class="ccit-ico">
            <?= social_svg('facebook'); ?>
          </a>
          <a href="https://www.tiktok.com/@adamsonuccit" target="_blank" rel="noopener" aria-label="TikTok" class="ccit-ico">
            <?= social_svg('tiktok'); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="ccit-legal ccit-legal--centered">
    <div class="ccit-legal__copyright">
      &copy; <?= date('Y'); ?> <?= e($settings['org_name'] ?? 'Adamson University'); ?> | <?= e($settings['college_name'] ?? 'College of Computing and Information Technology'); ?>
    </div>
    <div class="ccit-legal__seps ccit-legal__seps--centered">
      <a href="#"><?= e($settings['legal_note'] ?? 'All Rights Reserved'); ?></a>
      <span class="ccit-legal__pipe">|</span>
      <a href="#"><?= e($settings['powered_by'] ?? 'Powered by Information Technology Center'); ?></a>
      <span class="ccit-legal__pipe">|</span>
      <a href="#"><?= e($settings['trademark_note'] ?? 'Trademark Notice'); ?></a>
      <span class="ccit-legal__pipe">|</span>
      <a href="<?= e($settings['sitemap_url'] ?? '#'); ?>"><?= e($settings['sitemap_label'] ?? 'Site Map'); ?></a>
    </div>
  </div>
</footer>

<script src="/adamson-ccit/public/assets/js/chatbot.js"></script>
