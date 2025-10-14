<?php /* career_pathway_results.php — Display personalized pathway results (Top Match + reasoning) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Career Pathway Results | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <style>
    /* Layout & Animation */
    .page-results .content > .container{padding-block:clamp(40px,6vw,80px)}
    .results{animation:fadeIn 0.4s ease-out}
    @keyframes fadeIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

    /* Grid */
    .res__grid{
      display:grid;gap:32px;
      grid-template-columns:repeat(auto-fit,minmax(340px,1fr));
      margin-bottom:40px
    }
    @media (max-width:768px){.res__grid{gap:24px}}

    /* Card Base */
    .res{
      border:1px solid #e2e8f0;
      border-radius:12px;
      background:#ffffff;
      padding:32px;
      transition:border-color 0.2s ease,box-shadow 0.2s ease
    }
    .res:hover{
      border-color:#0080c9;
      box-shadow:0 6px 18px rgba(0,128,201,0.14)
    }

    /* Top Match hero */
    .res--top{
      background:linear-gradient(180deg,#f8fcff 0%,#f0f9f4 100%);
      border-color:#00713D;
      box-shadow:0 10px 26px rgba(0,113,61,0.12)
    }
    .res--top .res__head h3{font-size:clamp(1.25rem,2.6vw,1.75rem)}
    .res--top .rank-badge{background:#00713D}

    /* Card Header */
    .res__head{
      display:flex;align-items:flex-start;gap:12px;
      margin-bottom:20px;flex-wrap:wrap;
      padding-bottom:20px;border-bottom:1px solid #f1f5f9
    }
    .res__head h3{
      margin:0;font-size:1.25rem;color:#003169;
      font-weight:700;flex:1;min-width:200px;line-height:1.3
    }

    /* Badges */
    .rank-badge{
      background:#0080c9;color:#ffffff;
      padding:4px 10px;border-radius:999px;
      font-size:0.72rem;font-weight:800;
      text-transform:uppercase;letter-spacing:0.05em;
      white-space:nowrap
    }

    .score{
      background:#f1f8ff;color:#003169;
      border:1px solid #0080c9;border-radius:999px;
      padding:6px 12px;font-weight:800;font-size:0.875rem;
      white-space:nowrap
    }

    /* Match meter */
    .meter{display:flex;align-items:center;gap:12px;margin:8px 0 4px}
    .meter__bar{flex:1;height:10px;background:#eef2f7;border-radius:999px;overflow:hidden}
    .meter__fill{height:100%;width:0;border-radius:999px;background:#0080c9;transition:width .6s ease}
    .meter__pct{min-width:52px;text-align:right;font-weight:800;color:#003169}

    /* Description */
    .res__desc{
      color:#64748b;font-size:0.95rem;line-height:1.65;
      margin-bottom:24px
    }

    /* Section Boxes */
    .section-box{
      background:#f8fcff;
      border:1px solid #e6f3ff;
      border-radius:8px;
      padding:20px;
      margin-bottom:20px
    }
    .section-box:last-child{margin-bottom:0}
    .section-box h4{
      margin:0 0 14px;color:#003169;
      font-size:0.95rem;font-weight:700;
      text-transform:uppercase;letter-spacing:0.03em;
      display:flex;align-items:center;gap:6px
    }

    /* Chips */
    .mini{display:flex;gap:8px;flex-wrap:wrap}
    .mini .chip{
      background:#ffffff;border:1px solid #0080c9;
      color:#003169;padding:8px 12px;border-radius:999px;
      font-size:0.813rem;font-weight:700;
      transition:all 0.15s ease
    }
    .mini .chip:hover{
      background:#0080c9;border-color:#0080c9;color:#ffffff
    }

    /* Lists */
    .bullets{
      margin:0 0 0 18px;padding:0;
      color:#475569;line-height:1.7;font-size:0.95rem
    }
    .bullets li{margin-bottom:8px}
    .bullets li::marker{color:#0080c9}

    /* Program Chips */
    .program-list{display:flex;flex-direction:column;gap:10px}
    .program-chip{
      background:#f0f9f4;
      border:1px solid #00713D;
      color:#0a5d3c;
      padding:12px 16px;border-radius:8px;
      font-weight:700;font-size:0.9rem;
      transition:all 0.15s ease
    }
    .program-chip:hover{
      background:#00713D;
      border-color:#00713D;
      color:#ffffff
    }

    /* Divider */
    .hr{height:1px;background:#e6f3ff;margin:24px 0}

    /* CTA Box */
    .cta-box{
      margin-top:56px;text-align:center;
      background:linear-gradient(135deg,#f8fcff 0%,#f0f9f4 100%);
      border:1px solid #0080c9;
      border-radius:12px;
      padding:48px 32px
    }
    .cta-box h3{
      margin:0 0 12px;font-size:1.5rem;
      color:#003169;font-weight:700
    }
    .cta-box p{
      margin:0 0 28px;font-size:1rem;
      color:#64748b;max-width:560px;
      margin-left:auto;margin-right:auto;line-height:1.6
    }
    .cta-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}

    /* No Results */
    .no-results{
      text-align:center;padding:80px 32px;
      background:#f8fcff;
      border:1px solid #e6f3ff;
      border-radius:12px
    }
    .no-results h3{
      margin:0 0 12px;font-size:1.375rem;
      color:#003169;font-weight:700
    }
    .no-results p{
      margin:0 0 28px;font-size:1rem;
      color:#64748b;line-height:1.6
    }

    /* FIX: keep chips from floating into headers */
    .page-results .res__head{position:relative}
    .page-results .res__head::after{content:"";display:block;clear:both}
    .page-results .section-box{clear:both}
    .page-results .mini{clear:both}
    .page-results .mini .chip{
      float:none !important;
      position:static !important;
      display:inline-flex !important;
      align-items:center;
      gap:6px;
    }

    /* Optional small hint text */
    .small-muted{color:#6b7280;font-size:0.85rem}
  </style>
</head>
<body>

  <!-- ============ LOCAL SUBNAV ============ -->
<nav class="subnav" aria-label="Career Pathway sub-navigation">
  <div class="container">
    <ul class="subnav__list" role="list">
      <li class="is-active">
        <a href="/adamson-ccit/public/index.php?page=career_pathway_results" aria-current="page">My Career Pathway Results</a>
      </li>
      <li>
        <a href="/adamson-ccit/public/index.php?page=compare_programs">Compare Programs</a>
      </li>
    </ul>
  </div>
</nav>

<main class="page-results">

  <!-- RESULTS CONTENT -->
  <section class="content" role="region" aria-label="Pathway Results">
    <div class="container">
      <div id="resultsContainer" class="results"></div>
    </div>
  </section>
</main>

<script>
/* ---------- CCIT Programs Info ---------- */
const PROGRAMS = {
  'BSIT': {
    title: 'BS Information Technology',
    desc: 'Comprehensive IT program focusing on software development, systems administration, and technology implementation in business environments.',
    tracks: ['Consumer & Enterprise Application Development','Game Development','Network Infrastructure & Data Security']
  },
  'BSCS': {
    title: 'BS Computer Science',
    desc: 'Rigorous computer science program emphasizing algorithmic thinking, software engineering principles, and advanced computing concepts.',
    specializations: ['Data Science','Web Science','Computer Vision']
  },
  'BSIS': {
    title: 'BS Information Systems',
    desc: 'Strategic IT program combining business acumen with technical expertise for effective information systems management.',
    specializations: ['Business Analytics']
  },
  'DUAL': {
    title: 'Dual Degree Program',
    desc: 'Accelerated dual degree program allowing students to earn two bachelor\'s degrees in Computer Science plus Business Administration or Engineering.',
    options: ['BS Computer Science & Information Engineering','BS Computer Science & Business Administration']
  }
};

/* ---------- Categories & Content ---------- */
const CATS = {
  dev:   {label:'Software & Web Development',
          roles:['Software Developer','Full-stack Developer','Back-end Developer','Mobile App Developer'],
          learn:['Programming fundamentals (OOP, DSA)','Web frameworks & APIs','Version control & testing'],
          certs:['AWS Cloud Practitioner','Microsoft AZ-900','Oracle Java','GitHub Foundations'],
          programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  mobile:{label:'Mobile & Enterprise Apps',
          roles:['Android/iOS Developer','Enterprise App Developer','Integration Engineer'],
          learn:['Mobile frameworks (Flutter/React Native)','REST/GraphQL APIs','CI/CD basics'],
          certs:['Google Associate Android Dev','Apple App Dev (Swift)','Scrum Fundamentals'],
          programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  uiux:  {label:'UI/UX & Front-end',
          roles:['UI/UX Designer','Front-end Developer','Product Designer'],
          learn:['HCI & accessibility','Prototyping & design systems','HTML/CSS/JS fundamentals'],
          certs:['Google UX Certificate','Adobe ACA','freeCodeCamp Responsive Web'],
          programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Web Science']},
  game:  {label:'Game Dev & Multimedia',
          roles:['Game Developer','Technical Artist','Interactive Media Dev'],
          learn:['Game engines (Unity/Unreal)','2D/3D assets & animation','Gameplay programming'],
          certs:['Unity User/Associate','Autodesk/Adobe badges'],
          programs:['BSIT - Game Development']},
  data:  {label:'Data & Databases',
          roles:['Data Analyst','BI Developer','Database Administrator'],
          learn:['SQL & data modeling','Python for data/ETL','Dashboards & storytelling'],
          certs:['Google Data Analytics','Microsoft DP-900','Oracle Database Foundations'],
          programs:['BSCS - Data Science','BSIS - Business Analytics']},
  sec:   {label:'Cybersecurity & Networks',
          roles:['Security Analyst','Network Admin','SOC Tier 1'],
          learn:['Networking & OS','Threats, vuln scanning','Hardening & incident basics'],
          certs:['CompTIA Security+','Cisco CCNA','(ISC)² CC'],
          programs:['BSIT - Network Infrastructure & Data Security']},
  sys:   {label:'Systems, Cloud & DevOps',
          roles:['Systems Admin','DevOps Tech','Cloud Support Associate'],
          learn:['Linux/Windows admin','Scripting & automation','Containers & cloud basics'],
          certs:['AWS Cloud Practitioner','Linux Essentials','Docker/CKA (later)'],
          programs:['BSIT - Network Infrastructure & Data Security','BSCS - Software Engineering']},
  ai:    {label:'AI & Machine Learning',
          roles:['ML Engineer (entry)','Data Scientist (jr)','Research Assistant'],
          learn:['Linear algebra & stats','ML workflows & tooling','Responsible AI basics'],
          certs:['Google ML/AI courses','Microsoft AI-900'],
          programs:['BSCS - Data Science','BSCS - Computer Vision','Dual Degree - CS & Engineering']},
  cloud: {label:'Cloud, Blockchain & Emerging Tech',
          roles:['Cloud Practitioner','Blockchain Dev (jr)','Tech Innovator'],
          learn:['Cloud services & IaC','Distributed ledgers (intro)','APIs & integrations'],
          certs:['AWS/Microsoft Fundamentals','Blockchain foundations'],
          programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  pm:    {label:'IT Project & Business Analysis',
          roles:['IT Project Coordinator','Business Analyst (jr)','Product Ops'],
          learn:['Project lifecycles & Agile','Requirements & documentation','Stakeholder comms'],
          certs:['Scrum Master (PSM I)','CAPM','Agile Fundamentals'],
          programs:['BSIS - Business Analytics','Dual Degree - CS & Business Administration']}
};

/* ---------- Utilities ---------- */
const pctText = (v) => `${Math.round(v)}%`;
const byWeightDesc = (a,b)=> b.w - a.w;

/* ---------- Render (Top Match + reasons) ---------- */
function renderResults() {
  const container = document.getElementById('resultsContainer');
  const rawScores = sessionStorage.getItem('pathwayResults');
  const rawTrace  = sessionStorage.getItem('pathwayTrace');

  if (!rawScores) {
    container.innerHTML = emptyState('No Results Found','Please complete the Career Pathway Generator questionnaire first.','Take the Survey');
    return;
  }

  let scores, trace;
  try { scores = JSON.parse(rawScores); } catch(e){ scores = null; }
  try { trace  = JSON.parse(rawTrace  || '{}'); } catch(e){ trace = {}; }

  if (!scores || typeof scores !== 'object') {
    container.innerHTML = emptyState('We hit a snag','Your saved results look corrupted. Please retake the survey.','Retake Survey');
    return;
  }

  const entries = Object.entries(scores)
    .filter(([,v]) => typeof v === 'number' && v > 0)
    .sort((a,b) => b[1] - a[1]);

  if (!entries.length) {
    container.innerHTML = emptyState('Inconclusive Results','We need a bit more information to provide accurate recommendations. Please retake the survey and select more options.','Retake Survey');
    return;
  }

  const total = entries.reduce((s,[,v]) => s + v, 0) || 1;

  const ranked = entries.map(([k,score]) => {
    const cat = CATS[k];
    return cat ? { key:k, score, pct:(score/total)*100, cat } : null;
  }).filter(Boolean);

  if (!ranked.length) {
    container.innerHTML = emptyState('Nothing to show','Your results reference categories we no longer support. Please retake the survey.','Retake Survey');
    return;
  }

  const top1 = ranked[0];
  const next = ranked.slice(1,3);

  let html = '';

  /* ===== Top Match hero ===== */
  html += `
    <article class="res res--top" aria-labelledby="topMatchTitle">
      <div class="res__head">
        <h3 id="topMatchTitle">🏆 Top Match — ${top1.cat.label}</h3>
        <span class="rank-badge">Top Match</span>
        <span class="score" aria-label="Match score">${pctText(top1.pct)}</span>
      </div>

      <div class="meter" aria-label="Match percentage">
        <div class="meter__bar" aria-hidden="true">
          <div class="meter__fill" style="width:${Math.max(6, top1.pct)}%"></div>
        </div>
        <div class="meter__pct">${pctText(top1.pct)}</div>
      </div>

      <p class="res__desc">This area aligns most strongly with your responses. Explore the suggested CCIT programs, roles, and a focused learning plan to get started.</p>

      ${programsBox(top1.cat, 2)}
      ${rolesBox(top1.cat, 6)}
      <div class="hr"></div>
      ${learnBox(top1.cat)}
      ${certsBox(top1.cat)}

      ${whyBox('🧭 Why you got this match', (trace[top1.key]||[]))}
    </article>
  `;

  /* ===== Other Strong Matches ===== */
  if (next.length) {
    html += `<div class="hr"></div><h2 class="eyebrow" style="display:block;margin:6px 0 12px;color:#003169;letter-spacing:.14em;">Other Strong Matches</h2>`;
    html += `<div class="res__grid">`;

    next.forEach(({key, cat, pct}) => {
      html += `
        <article class="res" aria-label="${cat.label} match card">
          <div class="res__head">
            <h3>${cat.label}</h3>
            <span class="score">${pctText(pct)}</span>
          </div>
          <div class="meter" aria-label="Match percentage">
            <div class="meter__bar" aria-hidden="true">
              <div class="meter__fill" style="width:${Math.max(6, pct)}%"></div>
            </div>
            <div class="meter__pct">${pctText(pct)}</div>
          </div>
          <p class="res__desc">A good fit based on your responses. Consider the program and learning focus below.</p>
          ${programsBox(cat, 1)}
          ${rolesBox(cat, 4)}
          <div class="hr"></div>
          ${learnBox(cat, 3)}
          ${certsBox(cat, 3)}
          ${whyBox('Why this also fits', (trace[key]||[]))}
        </article>
      `;
    });

    html += `</div>`;
  }

  /* ===== Methodology note ===== */
  html += `
    <div class="section-box" role="region" aria-label="Methodology">
      <h4>ℹ️ How these results were calculated</h4>
      <p class="small-muted">
        Each answer gives points to related areas (e.g., interest, preferred tasks, confidence, experience, credentials).
        We total points for every area and convert them into percentages. “Top Match” is the highest percentage.
        The “Why you got this match” list shows the strongest contributors from your answers.
      </p>
    </div>
  `;

  /* ===== CTA ===== */
  html += `
    <div class="cta-box" role="region" aria-label="Next steps">
      <h3>Ready to Start Your Journey?</h3>
      <p>Explore our programs in detail and take the next step toward your ideal career in computing and information technology.</p>
      <div class="cta-actions">
        <a href="/adamson-ccit/public/index.php?page=programs_undergraduate" class="btn btn--solid">View All Programs</a>
        <a href="/adamson-ccit/public/index.php?page=admission_freshman" class="btn btn--outline-blue">Apply Now</a>
        <button type="button" onclick="window.print()" class="btn btn--outline-blue">Download as PDF</button>
        <a href="/adamson-ccit/public/index.php?page=career_pathway_generator" class="btn btn--outline-blue">Retake Survey</a>
      </div>
    </div>
  `;

  container.innerHTML = html;
}

/* ---------- Tiny templating helpers ---------- */
function programsBox(cat, limit = 2) {
  const list = (cat.programs || []).slice(0, limit).map(p => `<div class="program-chip" tabindex="0">${p}</div>`).join('');
  const inner = list || `<span class="res__desc">No specific program listed yet.</span>`;
  return `
    <div class="section-box" role="region" aria-label="Recommended CCIT Programs">
      <h4>🎯 Recommended CCIT Programs</h4>
      <div class="program-list">${inner}</div>
    </div>
  `;
}
function rolesBox(cat, limit = 6) {
  const chips = (cat.roles || []).slice(0, limit).map(r => `<span class="chip" tabindex="0">${r}</span>`).join('');
  const inner = chips || `<span class="res__desc">Role suggestions coming soon.</span>`;
  return `
    <div class="section-box" role="region" aria-label="Career Opportunities">
      <h4>💼 Career Opportunities</h4>
      <div class="mini">${inner}</div>
    </div>
  `;
}
function learnBox(cat, limit = 999) {
  const items = (cat.learn || []).slice(0, limit).map(x => `<li>${x}</li>`).join('');
  const inner = items || `<li>Foundations in programming and problem solving</li>`;
  return `
    <div class="section-box" role="region" aria-label="Learning Path">
      <h4>📚 Learning Path</h4>
      <ul class="bullets">${inner}</ul>
    </div>
  `;
}
function certsBox(cat, limit = 999) {
  const items = (cat.certs || []).slice(0, limit).map(x => `<li>${x}</li>`).join('');
  const inner = items || `<li>Entry-level IT fundamentals</li>`;
  return `
    <div class="section-box" role="region" aria-label="Recommended Certifications">
      <h4>🏆 Recommended Certifications</h4>
      <ul class="bullets">${inner}</ul>
    </div>
  `;
}

/* Show top reasons; cap to 5 lines for clarity */
function whyBox(title, reasons) {
  if (!Array.isArray(reasons) || reasons.length === 0) {
    return `
      <div class="section-box" role="region" aria-label="Why this match">
        <h4>${title}</h4>
        <p class="small-muted">We couldn’t extract specific reasons this time (e.g., older results). Try generating again to see detailed reasoning.</p>
      </div>
    `;
  }
  const top = reasons.slice().sort(byWeightDesc).slice(0,5);
  const items = top.map(r => `<li>${escapeHTML(r.reason)}</li>`).join('');
  return `
    <div class="section-box" role="region" aria-label="Why this match">
      <h4>${title}</h4>
      <ul class="bullets">${items}</ul>
    </div>
  `;
}

/* Escape helper for safety */
function escapeHTML(s){
  return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

function emptyState(title, body, label) {
  const href = '/adamson-ccit/public/index.php?page=career_pathway_generator';
  return `
    <div class="no-results" role="alert">
      <h3>${title}</h3>
      <p>${body}</p>
      <a href="${href}" class="btn btn--solid">${label}</a>
    </div>
  `;
}

// Render on page load
document.addEventListener('DOMContentLoaded', renderResults);
</script>

</body>
</html>
