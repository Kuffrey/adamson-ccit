<?php /* compare_programs.php — Side-by-side program comparison (no column headers) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Compare Programs | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <style>
    .page-compare .content > .container{padding-block:clamp(40px,6vw,80px)}
    .fade-in{animation:fadeIn .35s ease-out}
    @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}

    :root{
      --ink:#0b234c; --sub:#475569; --edge:var(--edgec,#e6e9ef);
      --hi:#0080c9; --acc:#00713D; --bg:#ffffff; --row:#f8fafc;
    }

    /* a11y helper */
    .sr-only{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

    /* Picker bar */
    .compare-bar{
      display:grid;grid-template-columns:1fr 1fr;gap:12px;
      background:var(--bg);border:1px solid var(--edge);border-radius:14px;padding:14px;
      align-items:end;margin-bottom:18px
    }
    .picker select{
      width:100%;padding:12px 14px;border:1px solid #cfd6e2;border-radius:12px;
      background:#f8fafc;color:var(--ink);font:700 14px/1.2 Inter,system-ui
    }
    .bar-actions{grid-column:1/-1;display:flex;gap:12px;align-items:center;justify-content:flex-end}
    .switch{display:inline-flex;gap:8px;align-items:center;font-weight:700;color:var(--sub)}
    .switch input{width:18px;height:18px}

    /* Comparison shell */
    .cmp-wrap{background:var(--bg);border:1px solid var(--edge);border-radius:14px;overflow:hidden}

    /* Rows grid (no header row) */
    .cmp-rows{display:grid;grid-template-columns:.6fr 1fr 1fr}

    .sec{
      grid-column:1/-1;background:#f6f9ff;border-top:1px solid var(--edge);
      padding:12px 16px;font-weight:900;color:#003169;letter-spacing:.04em;text-transform:uppercase;font-size:12px
    }
    .row{display:contents}
    .cell{
      padding:14px 16px;border-top:1px solid var(--edge);background:#fff;color:var(--ink)
    }
    .label{font-weight:800;color:#1f2a44;background:#fff}
    .a,.b{border-left:1px solid var(--edge)}
    .alt .cell{background:var(--row)}
    .muted{color:#64748b}

    /* Diff markers */
    .diff{position:relative}
    .diff::after{
      content:"";position:absolute;right:12px;top:50%;transform:translateY(-50%);
      width:8px;height:8px;border-radius:999px;background:var(--hi)
    }

    /* Mobile */
    @media (max-width:900px){
      .compare-bar{grid-template-columns:1fr;gap:10px}
      .bar-actions{justify-content:flex-start}
      .cmp-rows{grid-template-columns:1fr}
      .a,.b{border-left:none}
      .label{border-top:1px solid var(--edge)}
    }
  </style>
</head>
<body>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Career Pathway sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=career_pathway_results">My Career Pathway Results</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=compare_programs" aria-current="page">Compare Programs</a>
        </li>
      </ul>
    </div>
  </nav>

<main class="page-compare">
  <section class="content" role="region" aria-label="Compare Programs">
    <div class="container fade-in">

      <!-- Minimal selectors -->
      <div class="compare-bar" role="group" aria-label="Choose programs">
        <div class="picker">
          <label class="sr-only" for="cmp1">Choose first program</label>
          <select id="cmp1"></select>
        </div>
        <div class="picker">
          <label class="sr-only" for="cmp2">Choose second program</label>
          <select id="cmp2"></select>
        </div>
        <div class="bar-actions">
          <label class="switch">
            <input type="checkbox" id="toggleDiff">
            <span>Show differences only</span>
          </label>
        </div>
      </div>

      <!-- Comparison (no column headers rendered) -->
      <div class="cmp-wrap">
        <div class="cmp-rows" id="cmpRows"></div>
      </div>

    </div>
  </section>
</main>

<script>
/* ---------- Program catalog ---------- */
const PROGRAMS = [
  {
    id:'bsit',
    code:'BSIT',
    name:'BS Information Technology',
    level:'Undergraduate',
    desc:'Comprehensive IT program focusing on software development, systems administration, and technology implementation in business environments.',
    tracks:['Consumer & Enterprise Application Development','Game Development','Network Infrastructure & Data Security'],
    emphases:['Software Development','Systems Administration','Technology Implementation']
  },
  {
    id:'bscs',
    code:'BSCS',
    name:'BS Computer Science',
    level:'Undergraduate',
    desc:'Rigorous computer science program emphasizing algorithmic thinking, software engineering principles, and advanced computing concepts.',
    tracks:['Data Science','Web Science','Computer Vision'],
    emphases:['Algorithmic Thinking','Software Engineering','Advanced Computing']
  },
  {
    id:'bsis',
    code:'BSIS',
    name:'BS Information Systems',
    level:'Undergraduate',
    desc:'Strategic IT program combining business acumen with technical expertise for effective information systems management.',
    tracks:['Business Analytics'],
    emphases:['Business + IT','IS Management','Analytics']
  },
  {
    id:'dual',
    code:'DUAL DEGREE',
    name:'BS CS & Information Engineering',
    level:'Undergraduate',
    desc:'Accelerated dual degree program to earn CS plus Business Administration or Engineering.',
    tracks:[],
    emphases:['CS + Business Administration','CS + Engineering']
  },
  {
    id:'mit',
    code:'MIT',
    name:'Master in Information Technology',
    level:'Graduate',
    desc:'Advanced IT training for leadership roles; emphasizes ethics, innovation, and social responsibility.',
    tracks:['Advanced Computing Practice','IT Leadership & Governance','Ethics & Social Responsibility','Innovation & Sustainable Impact'],
    emphases:['Leadership','Ethics','Innovation']
  }
];

/* ---------- Options / grouping ---------- */
const $ = (s, r=document)=>r.querySelector(s);
function makeOptionGroup(label, list){
  const og = document.createElement('optgroup'); og.label = label;
  list.forEach(p=>{
    const o = document.createElement('option');
    o.value = p.id; o.textContent = `${p.code} — ${p.name}`;
    og.appendChild(o);
  });
  return og;
}

/* Row model (kept succinct) */
const ROWS = [
  {section:'Overview'},
  {label:'Summary', key:'desc', get:(p)=>p.desc },
  {label:'Level', key:'level', get:(p)=>p.level },
  {section:'Academic Focus'},
  {label:'Tracks / Specializations', key:'tracks', get:(p)=>(p.tracks||[]).join(', ')||'—' },
  {label:'Program Emphases', key:'emphases', get:(p)=>(p.emphases||[]).join(', ')||'—' },
];

/* ---------- Render rows only (no headers) ---------- */
function renderRows(p1,p2){
  const wrap = $('#cmpRows'); wrap.innerHTML = '';
  let alt = false;

  ROWS.forEach(r=>{
    if (r.section){
      const sec = document.createElement('div');
      sec.className = 'sec';
      sec.textContent = r.section;
      wrap.appendChild(sec);
      alt = false;
      return;
    }

    const v1 = r.get(p1);
    const v2 = r.get(p2);
    const isDiff = v1 !== v2;

    const row = document.createElement('div');
    row.className = 'row'+(alt?' alt':'');
    row.dataset.diff = isDiff ? '1' : '0';
    row.innerHTML = `
      <div class="cell label">${r.label}</div>
      <div class="cell a ${isDiff?'diff':''}">${v1}</div>
      <div class="cell b ${isDiff?'diff':''}">${v2}</div>
    `;
    wrap.appendChild(row);
    alt = !alt;
  });

  applyDiffFilter();
}

/* ---------- Diff filter ---------- */
function applyDiffFilter(){
  const only = $('#toggleDiff').checked;
  const rows = Array.from(document.querySelectorAll('#cmpRows .row'));
  rows.forEach(row=>{
    const isDiff = row.dataset.diff === '1';
    row.style.display = (only && !isDiff) ? 'none' : '';
  });
}

/* ---------- Init ---------- */
function init(){
  const sel1 = $('#cmp1'), sel2 = $('#cmp2');
  const ug = PROGRAMS.filter(p=>p.level==='Undergraduate');
  const grad = PROGRAMS.filter(p=>p.level==='Graduate');

  [sel1, sel2].forEach(sel=>{
    sel.appendChild(makeOptionGroup('Undergraduate', ug));
    if (grad.length) sel.appendChild(makeOptionGroup('Graduate', grad));
  });

  // Defaults
  sel1.value = 'bsit';
  sel2.value = 'bscs';

  function update(){
    const p1 = PROGRAMS.find(p=>p.id===sel1.value);
    const p2 = PROGRAMS.find(p=>p.id===sel2.value);
    renderRows(p1,p2);
  }

  sel1.addEventListener('change', update);
  sel2.addEventListener('change', update);
  $('#toggleDiff').addEventListener('change', applyDiffFilter);

  update();
}

document.addEventListener('DOMContentLoaded', init);
</script>

</body>
</html>
