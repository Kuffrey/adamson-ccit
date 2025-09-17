<?php
// Session already started in public/index.php
require_once __DIR__ . '/../../app/lib/Auth.php';

$error    = $error ?? '';
$hasError = !empty($error);
?>
<main class="auth-page">
  <section class="content">
    <div class="container auth-wrap">
      <h2 class="auth-title">Login — Student</h2>

      <?php if ($hasError): ?>
        <div class="alert error" role="alert" aria-live="polite">
          <svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18">
            <circle cx="12" cy="12" r="10" fill="currentColor" opacity=".12"/>
            <path d="M12 8v6m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
          <div><?= htmlspecialchars($error) ?></div>
        </div>
      <?php endif; ?>

      <form id="loginForm" method="post" action="/adamson-ccit/public/index.php?page=login_guest_student" autocomplete="on" class="auth-card">
        <!-- USERNAME -->
        <label class="field">
          <span class="label">Username <b aria-hidden="true" class="req">*</b></span>
          <input
            type="text"
            name="username"
            required
            aria-required="true"
            aria-invalid="<?= $hasError ? 'true' : 'false' ?>"
            class="input <?= $hasError ? 'is-invalid' : '' ?>"
            autocomplete="username"
            maxlength="254"
            data-error-message="Please enter your username"
          />
        </label>

        <!-- PASSWORD with eye toggle -->
        <label class="field">
          <span class="label">Password <b aria-hidden="true" class="req">*</b></span>
          <div class="password">
            <input
              type="password"
              name="password"
              id="passwordInput"
              required
              aria-required="true"
              aria-invalid="<?= $hasError ? 'true' : 'false' ?>"
              class="input <?= $hasError ? 'is-invalid' : '' ?>"
              autocomplete="current-password"
              maxlength="128"
              data-error-message="Please enter your password"
            />
            <button type="button" id="togglePassword" class="eye" aria-label="Show password" title="Show password">
              <svg id="eyeIcon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </label>

        <!-- REMEMBER ME -->
        <label class="remember">
          <input type="checkbox" name="remember_me" class="check" />
          <span>Remember Me</span>
        </label>

        <!-- SUBMIT -->
        <button id="loginBtn" type="submit" class="btn-primary">Login</button>

        <div class="helper">Forgot your password? Contact the CCIT office.</div>
      </form>

      <p class="footnote">
        Login is optional. It unlocks your
        <a href="/adamson-ccit/public/index.php?page=student_profile">Student Profile/Portfolio</a>.
      </p>
    </div>
  </section>
</main>

<style>
  /* ============================== THEME TOKENS ============================== */
  .auth-page{
    --blue:#003169;
    --green:#00713D;
    --ink:#0b234c;
    --muted:#6b7280;
    --line:#e6e9ef;
    --card:#f9fafb;
    --focus: 0 0 0 3px rgba(37,99,235,.25); /* blue ring */
    --danger:#ef4444;
    --danger-bg:#fef2f2;
    --danger-line:#fecaca;

    font-family: "Inter",system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif;
    background:#fff;
  }

  .auth-page .content{ padding:40px 0; }
  .auth-page .auth-wrap{ max-width:420px; margin-inline:auto; padding:0 20px; }

  .auth-title{
    margin:0 0 18px;
    font-size:clamp(20px,3vw,28px);
    font-weight:900;
    color:var(--ink);
    letter-spacing:.01em;
  }

  /* ============================== ALERT ============================== */
  .alert{
    display:flex; gap:10px; align-items:flex-start;
    padding:10px 12px; border-radius:12px;
    font-size:14px; font-weight:600; line-height:1.45;
    margin:0 0 14px;
  }
  .alert svg{ color:var(--danger); flex:0 0 auto; margin-top:2px; }
  .alert.error{
    border:1px solid var(--danger-line);
    background:var(--danger-bg);
    color:#7f1d1d;
  }

  /* ============================== CARD ============================== */
  .auth-card{
    display:grid; gap:14px;
    background:var(--card);
    padding:20px;
    border:1px solid var(--line);
    border-radius:14px;
    box-shadow:0 4px 16px rgba(0,0,0,.04);
  }

  /* ============================== FIELDS ============================== */
  .field{ display:grid; gap:6px; position: relative; }
  .label{ font-weight:700; color:var(--ink); }
  .req{ color:var(--danger); font-weight:800; }

  .input{
    height:44px;
    padding:0 12px;
    font-size:16px;
    color:var(--ink);
    background:#fff;
    border:1px solid #d1d5db;
    border-radius:10px;
    transition:border-color .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .input:hover{ background:#fff; border-color:#cbd5e1; }
  .input:focus{ outline:none; border-color:#93c5fd; box-shadow:var(--focus); }
  .input.is-invalid{
    border-color:var(--danger);
    box-shadow:0 0 0 3px rgba(239,68,68,.15);
    animation: shake 0.3s;
  }
  
  /* Shake animation for invalid fields */
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
  }

  /* Password with eye */
  .password{ position:relative; display:flex; align-items:center; }
  .password .input{ width:100%; padding-right:42px; }
  .eye{
    position:absolute; right:10px;
    display:grid; place-items:center;
    width:32px; height:32px;
    border:none; background:transparent; color:var(--ink);
    cursor:pointer; border-radius:8px;
  }
  .eye:hover{ background:#eef2ff; }
  .eye:focus-visible{ outline: none; box-shadow:var(--focus); }

  /* Remember me */
  .remember{ display:flex; align-items:center; gap:8px; font-size:14px; color:var(--ink); font-weight:600; }
  .remember .check{ width:18px; height:18px; accent-color:var(--blue); }

  /* Button */
  .btn-primary{
    height:44px;
    border:none; border-radius:10px;
    background:var(--blue); color:#fff;
    font-weight:800; font-size:16px;
    cursor:pointer;
    transition: filter .15s ease, transform .02s ease, box-shadow .15s ease;
    box-shadow: 0 6px 20px rgba(0,49,105,.15);
  }
  .btn-primary:hover{ filter:brightness(1.06); }
  .btn-primary:active{ transform:translateY(1px); }
  .btn-primary:focus-visible{ outline:none; box-shadow:var(--focus); }
  .btn-primary[disabled]{ opacity:.85; cursor:not-allowed; }

  /* Helper + footnote */
  .helper{ color:#6b7280; font-size:12px; text-align:center; margin-top:2px; }
  .footnote{
    color:var(--muted); margin-top:12px; font-size:14px; text-align:center;
  }
  .footnote a{
    color:#0080c9; font-weight:800; text-decoration:none;
  }
  .footnote a:hover{ text-decoration:underline; }

  /* Field error tooltip */
  .field-error-tooltip {
    position: absolute;
    bottom: -22px;
    left: 0;
    background: var(--danger-bg);
    color: #7f1d1d;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
    animation: fadeIn 0.2s;
    white-space: nowrap;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Responsive niceties */
  @media (max-width: 480px){
    .auth-card{ padding:16px; border-radius:12px; }
  }
</style>

<script>
  // Prevent double submit + show quick loading state
  (function () {
    const form = document.getElementById('loginForm');
    const btn  = document.getElementById('loginBtn');
    const usernameInput = document.querySelector('input[name="username"]');
    const passwordInput = document.getElementById('passwordInput');
    
    if (!form || !btn || !usernameInput || !passwordInput) return;

    // Prevent tabbing from empty required fields
    usernameInput.addEventListener('keydown', function(e) {
      if (e.key === 'Tab' && !e.shiftKey && this.value.trim() === '') {
        e.preventDefault();
        this.focus();
        this.classList.add('is-invalid');
        
        // Show inline error with tooltip
        const errorMessage = this.getAttribute('data-error-message') || 'This field is required';
        showFieldError(this, errorMessage);
      }
    });

    passwordInput.addEventListener('keydown', function(e) {
      if (e.key === 'Tab' && !e.shiftKey && this.value.trim() === '') {
        e.preventDefault();
        this.focus();
        this.classList.add('is-invalid');
        
        // Show inline error with tooltip
        const errorMessage = this.getAttribute('data-error-message') || 'This field is required';
        showFieldError(this, errorMessage);
      }
    });
    
    // Function to display field error
    function showFieldError(inputElement, message) {
      // Remove any existing error tooltip
      const existingTooltip = inputElement.parentElement.querySelector('.field-error-tooltip');
      if (existingTooltip) {
        existingTooltip.remove();
      }
      
      // Create and add error tooltip
      const tooltip = document.createElement('div');
      tooltip.className = 'field-error-tooltip';
      tooltip.textContent = message;
      tooltip.setAttribute('role', 'alert');
      
      // For password field, add to parent div
      if (inputElement.id === 'passwordInput') {
        inputElement.parentElement.parentElement.appendChild(tooltip);
      } else {
        inputElement.parentElement.appendChild(tooltip);
      }
      
      // Auto-remove after 3 seconds
      setTimeout(() => {
        tooltip.remove();
      }, 3000);
    }
    
    form.addEventListener('submit', function() {
      btn.disabled = true;
      btn.textContent = 'Signing in…';
    });

    // Clear error styling as user types
    form.querySelectorAll('.input').forEach(function (el) {
      el.addEventListener('input', function () {
        el.classList.remove('is-invalid');
      });
    });
  })();

  // Password eye toggle
  (function () {
    const pwd = document.getElementById('passwordInput');
    const btn = document.getElementById('togglePassword');
    const icon = document.getElementById('eyeIcon');
    if (!pwd || !btn || !icon) return;

    let shown = false;
    btn.addEventListener('click', function () {
      shown = !shown;
      pwd.type = shown ? 'text' : 'password';
      btn.setAttribute('aria-label', shown ? 'Hide password' : 'Show password');
      btn.title = shown ? 'Hide password' : 'Show password';
      icon.innerHTML = shown
        // eye-off
        ? '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.77 21.77 0 0 1 5.06-7.94M1 1l22 22"/><circle cx="12" cy="12" r="3"/>'
        // eye
        : '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>';
    });
  })();
</script>
