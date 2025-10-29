<?php /* compare_programs.php — Side-by-side program comparison (no column headers) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Compare Programs | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
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

      <!-- Program selectors -->
      <div class="compare-bar" role="group" aria-label="Choose programs">
        <div class="picker">
          <label for="cmp1">Choose first program</label>
          <select id="cmp1"></select>
        </div>
        <div class="picker">
          <label for="cmp2">Choose second program</label>
          <select id="cmp2"></select>
        </div>
      </div>

      <!-- Comparison table -->
      <div class="programs-summary-grid" id="programsSummaryGrid"></div>

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
    focus:'The BSIT Program includes the study of utilization of both hardware and software technologies involving planning, installing, customizing, operating, managing, administering and maintaining information technology infrastructure that provides computing solutions to address the needs of an organization.',
    tracks:['Consumer & Enterprise Application Development','Game Development','Network Infrastructure & Data Security'],
    emphases:['Software Development','Systems Administration','Technology Implementation'],
    roles: {
      primary: [
        'Web and Applications Developer',
        'Junior Database Administrator',
        'Systems Administrator',
        'Network Engineer',
        'Junior Information Security Administrator',
        'Systems Integration Personnel',
        'IT Audit Assistant',
        'Technical Support Specialist'
      ]
    }
  },
  {
    id:'bsis',
    code:'BSIS',
    name:'BS Information Systems',
    level:'Undergraduate',
    desc:'Strategic IT program combining business acumen with technical expertise for effective information systems management.',
    focus:'The BSIS Program includes the study of application and effect of information technology to organizations. Graduates of the program should be able to implement an information system which considers complex technological and organizational factors affecting it. These include components, tools, techniques, strategies, methodologies etc.',
    tracks:['Business Analytics'],
    emphases:['Business + IT','IS Management','Analytics'],
    roles: {
      primary: [
        'Organizational Process Analyst',
        'Data Analyst',
        'Solutions Specialist',
        'Systems Analyst',
        'Project Management Personnel'
      ],
      secondary: [
        'GA Specialist',
        'Systems Analyst',
        'Computer Programmer',
        'Applications Developer',
        'End User Trainer',
        'Documentation Specialist',
        'Quality Assurance Specialist'
      ]
    }
  },
  {
    id:'bscs',
    code:'BSCS',
    name:'BS Computer Science',
    level:'Undergraduate',
    desc:'Rigorous computer science program emphasizing algorithmic thinking, software engineering principles, and advanced computing concepts.',
    focus:'The BSCS Program includes the study of computing concepts and theories, algorithmic foundations, and new developments in computing. The program prepares students to design and create algorithmically complex software and develop new effective algorithms for solving computing problems.',
    tracks:['Data Science','Web Science','Computer Vision'],
    emphases:['Algorithmic Thinking','Software Engineering','Advanced Computing'],
    roles: {
      primary: [
        'Software Engineer',
        'Systems Software Developer',
        'Applications Software Developer',
        'Computer Programmer',
        'Research and Development',
        'Computing Professional'
      ],
      secondary: [
        'Systems Analyst',
        'Data Analyst',
        'Quality Assurance Specialist',
        'Software Support Specialist'
      ]
    }
  },
  {
    id:'mit',
    code:'MIT',
    name:'Master in Information Technology',
    level:'Graduate',
    desc:'Advanced IT training for leadership roles; emphasizes ethics, innovation, and social responsibility.',
    focus:'Advanced theoretical and practical IT training to prepare students for leadership roles. Emphasizes ethical practices, social responsibility, and innovation for sustainable development.',
    tracks:['Advanced Computing Practice','IT Leadership & Governance','Ethics & Social Responsibility','Innovation & Sustainable Impact'],
    emphases:['Leadership','Ethics','Innovation'],
    roles: {
      primary: [
        'IT Leadership and Governance',
        'Ethics and Social Responsibility',
        'Innovation and Sustainable Impact'
      ]
    }
  },
  {
    id:'dual',
    code:'BSCSIE',
    name:'BS Computer Science & Information Engineering',
    level:'Undergraduate',
    desc:'Accelerated dual degree program to earn CS plus Business Administration or Engineering.',
    focus:'Earn two degrees: BS Computer Science (Adamson University) and BS Information Engineering (MUST, Taiwan). The program combines advanced computing and engineering, with strong international collaboration and cross-cultural experience.',
    tracks:[],
    emphases:['CS + Business Administration','CS + Engineering'],
    roles: {
      primary: [
        'Dual international degree recognition',
        'In-depth knowledge in CS and Information Engineering',
        'Broad technical skills: programming, software, databases, security',
        'Cross-cultural experience and global competence',
        'Diverse career options in technology fields',
        'International apprenticeship opportunities'
      ]
    },
    subareas: [
      'Software Systems: Development, OS, Databases, Networking, AI, Graphics',
      'Hardware Systems: Architecture, Networks, Microprogramming, Performance',
      'Scientific Computing: Simulation, Bioinformatics, Medical Informatics, Machine Learning',
      'Computer Theory: Algorithms, Information Theory, Graph Theory, Formal Languages'
    ]
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

/* ---------- Render dynamic summary cards with complete info ---------- */
function renderSummaryCards(p1, p2){
  const grid = $('#programsSummaryGrid');
  grid.innerHTML = '';

  [p1, p2].forEach((p) => {
    const card = document.createElement('div');
    card.className = 'program-summary-card';
    card.innerHTML = `
      <h3>${p.name} (${p.code})</h3>
      <div class="focus"><strong>FOCUS:</strong> ${p.focus || p.desc}</div>
      <div class="roles-title">Summary:</div>
      <p>${p.desc}</p>
      <div class="roles-title">Level:</div>
      <p>${p.level}</p>
      <div class="roles-title">Tracks / Specializations:</div>
      <ul>${(p.tracks && p.tracks.length) ? p.tracks.map(track => `<li>${track}</li>`).join('') : '<li>—</li>'}</ul>
      <div class="roles-title">Program Emphases:</div>
      <ul>${(p.emphases && p.emphases.length) ? p.emphases.map(em => `<li>${em}</li>`).join('') : '<li>—</li>'}</ul>
      ${p.roles?.primary ? `<div class="roles-title">Primary Job Roles:</div>
      <ul>${p.roles.primary.map(role => `<li>${role}</li>`).join('')}</ul>` : ''}
      ${p.roles?.secondary ? `<div class="roles-title">Secondary Job Roles:</div>
      <ul>${p.roles.secondary.map(role => `<li>${role}</li>`).join('')}</ul>` : ''}
      ${p.subareas ? `<div class="roles-title">Sub-Areas:</div>
      <ul>${p.subareas.map(sa => `<li>${sa}</li>`).join('')}</ul>` : ''}
    `;
    grid.appendChild(card);
  });
}

/* ---------- Render two-column table comparison ---------- */
function renderSummaryCards(p1, p2){
  const grid = $('#programsSummaryGrid');
  
  const html = `
    <div class="comparison-table">
      <!-- Program Headers -->
      <div class="program-column">
        <div class="program-header">${p1.name}<br>(${p1.code})</div>
      </div>
      <div class="program-column">
        <div class="program-header">${p2.name}<br>(${p2.code})</div>
      </div>

      <!-- Overview Section -->
      <div class="section-row">Overview</div>
      
      <div class="data-row">
        <div class="data-item">
          <span class="data-label">Summary</span>
          ${p1.desc}
        </div>
        <div class="data-item">
          <span class="data-label">Summary</span>
          ${p2.desc}
        </div>
      </div>

      <div class="data-row">
        <div class="data-item focus-item">
          <span class="data-label">Focus</span>
          ${p1.focus || p1.desc}
        </div>
        <div class="data-item focus-item">
          <span class="data-label">Focus</span>
          ${p2.focus || p2.desc}
        </div>
      </div>

      <div class="data-row">
        <div class="data-item">
          <span class="data-label">Level</span>
          ${p1.level}
        </div>
        <div class="data-item">
          <span class="data-label">Level</span>
          ${p2.level}
        </div>
      </div>

      <!-- Academic Structure Section -->
      <div class="section-row">Academic Structure</div>

      <div class="data-row">
        <div class="data-item">
          <span class="data-label">Tracks / Specializations</span>
          ${(p1.tracks && p1.tracks.length) ? 
            `<ul>${p1.tracks.map(t => `<li>${t}</li>`).join('')}</ul>` : 
            '<span class="empty-cell">No specific tracks</span>'}
        </div>
        <div class="data-item">
          <span class="data-label">Tracks / Specializations</span>
          ${(p2.tracks && p2.tracks.length) ? 
            `<ul>${p2.tracks.map(t => `<li>${t}</li>`).join('')}</ul>` : 
            '<span class="empty-cell">No specific tracks</span>'}
        </div>
      </div>

      <div class="data-row">
        <div class="data-item">
          <span class="data-label">Program Emphases</span>
          ${(p1.emphases && p1.emphases.length) ? 
            `<ul>${p1.emphases.map(e => `<li>${e}</li>`).join('')}</ul>` : 
            '<span class="empty-cell">—</span>'}
        </div>
        <div class="data-item">
          <span class="data-label">Program Emphases</span>
          ${(p2.emphases && p2.emphases.length) ? 
            `<ul>${p2.emphases.map(e => `<li>${e}</li>`).join('')}</ul>` : 
            '<span class="empty-cell">—</span>'}
        </div>
      </div>

      <!-- Career Opportunities Section -->
      <div class="section-row">Career Opportunities</div>

      <div class="data-row">
        <div class="data-item">
          <span class="data-label">Primary Job Roles</span>
          ${p1.roles?.primary ? 
            `<ul>${p1.roles.primary.map(r => `<li>${r}</li>`).join('')}</ul>` : 
            '<span class="empty-cell">—</span>'}
        </div>
        <div class="data-item">
          <span class="data-label">Primary Job Roles</span>
          ${p2.roles?.primary ? 
            `<ul>${p2.roles.primary.map(r => `<li>${r}</li>`).join('')}</ul>` : 
            '<span class="empty-cell">—</span>'}
        </div>
      </div>

      ${(p1.roles?.secondary || p2.roles?.secondary) ? `
        <div class="data-row">
          <div class="data-item">
            <span class="data-label">Secondary Job Roles</span>
            ${p1.roles?.secondary ? 
              `<ul>${p1.roles.secondary.map(r => `<li>${r}</li>`).join('')}</ul>` : 
              '<span class="empty-cell">—</span>'}
          </div>
          <div class="data-item">
            <span class="data-label">Secondary Job Roles</span>
            ${p2.roles?.secondary ? 
              `<ul>${p2.roles.secondary.map(r => `<li>${r}</li>`).join('')}</ul>` : 
              '<span class="empty-cell">—</span>'}
          </div>
        </div>
      ` : ''}

      ${(p1.subareas || p2.subareas) ? `
        <div class="section-row">Specialized Areas</div>
        <div class="data-row">
          <div class="data-item">
            <span class="data-label">Sub-Areas</span>
            ${p1.subareas ? 
              `<ul>${p1.subareas.map(s => `<li>${s}</li>`).join('')}</ul>` : 
              '<span class="empty-cell">—</span>'}
          </div>
          <div class="data-item">
            <span class="data-label">Sub-Areas</span>
            ${p2.subareas ? 
              `<ul>${p2.subareas.map(s => `<li>${s}</li>`).join('')}</ul>` : 
              '<span class="empty-cell">—</span>'}
          </div>
        </div>
      ` : ''}
    </div>
  `;
  
  grid.innerHTML = html;
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
    renderSummaryCards(p1, p2);
  }

  sel1.addEventListener('change', update);
  sel2.addEventListener('change', update);

  update();
}

document.addEventListener('DOMContentLoaded', init);
</script>

</body>
</html>
