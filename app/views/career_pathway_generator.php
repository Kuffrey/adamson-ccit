<?php /* career_pathway_generator.php — CCIT Career Pathway Generator (stepper with prev/next, original margins) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Career Pathway Generator | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <style>
    /* keep EXACT same page spacing as before */
    .page-career .content > .container{padding-block:clamp(22px,4vw,44px)}

    /* card + question spacing from original */
    .q{border:1px solid var(--edgec);border-radius:14px;background:#fff;padding:16px}
    .q + .q{margin-top:14px}
    .q h3{margin:0 0 8px;color:#0b234c;font-weight:900}
    .q p{margin:0 0 10px;color:#475569}
    .opts{display:grid;gap:10px}
    .opts.cols-2{grid-template-columns:1fr 1fr}
    .opts.cols-3{grid-template-columns:1fr 1fr 1fr}
    @media (max-width:860px){ .opts.cols-3{grid-template-columns:1fr 1fr} }
    @media (max-width:580px){ .opts.cols-2,.opts.cols-3{grid-template-columns:1fr} }
    .opt{
      display:flex;gap:10px;align-items:flex-start;background:#f8fafc;border:1px solid var(--edgec);
      padding:10px;border-radius:12px;cursor:pointer
    }
    .opt:hover{border-color:#d8dde8}
    .opt input{margin-top:3px}
    .q .helper{color:#6b7280;font-size:13px;margin-top:6px}
    .pillhint{font:800 11px/1 "Inter",system-ui;text-transform:uppercase;letter-spacing:.05em;color:#475569}
    .note-inline{margin-top:10px;border:1px dashed var(--edgec);background:#fafbfd;color:#6b7280;border-radius:12px;padding:10px}

    /* stepper container — match original feel (no extra margins), keep bottom padding for sticky nav */


    /* show one step, no extra outer margins */
    .step{display:none}
    .step.is-active{display:block;animation:fade .25s ease}
    @keyframes fade{from{opacity:.2;transform:translateY(4px)}to{opacity:1;transform:none}}

    /* progress — trimmed spacing to match original compact rhythm */
    .progress{
      margin:0 0 14px; /* same block gap (14px) */
      background:#fff;border:1px solid var(--edgec);border-radius:12px;padding:12px 14px;
      display:flex;align-items:center;gap:12px
    }
    .progress__text{font-weight:800;color:#0b234c}
    .progress__bar{flex:1;height:10px;background:#eef2f7;border-radius:999px;overflow:hidden}
    .progress__fill{height:100%;width:0;background:#0080c9;border-radius:999px;transition:width .3s ease}

    /* nav — keep compact spacing (12px) like original actions row, no extra gradients pushing space */
    .nav{
      display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:12px;
      position:sticky;bottom:0;background:#ffffff; /* flat to avoid “extra margin” look */
      padding-top:10px;border-top:1px solid var(--edgec)
    }
    .nav .left, .nav .right{display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:10px 14px;font-weight:800;border:1px solid #cfd6e2;background:#fff;color:#0b234c}
    .btn:hover{background:#f8fafc}
    .btn--solid{background:#00713D;border-color:#00713D;color:#fff}
    .btn--solid:hover{background:#005A2F;border-color:#005A2F}
    .btn[disabled]{opacity:.5;cursor:not-allowed}
    .btn--ghost{border-color:transparent;background:transparent}

    /* (unchanged result helpers you already had) */
    .results{display:none}
    .res__grid{display:grid;gap:16px;grid-template-columns:repeat(3,1fr)}
    @media (max-width:960px){ .res__grid{grid-template-columns:1fr 1fr} }
    @media (max-width:640px){ .res__grid{grid-template-columns:1fr} }
    .res{border:1px solid var(--edgec);border-radius:14px;background:#fff;padding:16px}
    .res__head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
    .score{margin-left:auto;background:#eef2ff;border:1px solid #d8dcef;border-radius:999px;padding:6px 10px;font-weight:800;color:#0b234c}
    .mini{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
    .mini .chip{position:static;background:#f8fafc;border:1px solid var(--edgec);color:#0b234c}
    .muted{color:#6b7280}
    .hr{height:1px;background:var(--edgec);margin:10px 0}
  </style>
</head>
<body>

<main class="page-career">

  <!-- SUB-HERO -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Planner</p>
      <h1 class="subhero__title">Career Pathway Generator</h1>
      <p class="subhero__lead">Answer a quick survey and get tailored IT/CS career pathways with skills, roles, and next steps.</p>
    </div>
  </section>

  <!-- FORM (stepper) -->
  <section class="content section-sep">
    <div class="container subhero__inner" style="padding-block:clamp(22px,4vw,44px);">
      <form id="cpForm" class="stepper" novalidate>
        <!-- progress -->
        <div class="progress" aria-live="polite">
          <div class="progress__text" id="progressText">Question 1 of 10</div>
          <div class="progress__bar" aria-hidden="true"><div class="progress__fill" id="progressFill"></div></div>
        </div>

        <!-- Step 1 -->
        <div class="step is-active" data-step="1">
          <div class="q">
            <h3>1) Which areas in computing are you most drawn to? <span class="pillhint">(Select up to three)</span></h3>
            <div class="opts cols-3" data-limit="3">
              <?php
                $q1 = [
                  ['dev','Software or web development'],
                  ['game','Game design and animation'],
                  ['sec','Cybersecurity and network defense'],
                  ['uiux','User interface and user experience (UI/UX) design'],
                  ['data','Data analytics and databases'],
                  ['mobile','Mobile or enterprise application development'],
                  ['sys','Systems administration and IT infrastructure'],
                  ['ai','Artificial intelligence or machine learning'],
                  ['cloud','Blockchain, cloud, or emerging technologies'],
                ];
                foreach($q1 as $opt){
                  echo '<label class="opt"><input type="checkbox" name="q1[]" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
            <div class="helper">Tip: Pick the topics you naturally click on or read about.</div>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="step" data-step="2" data-requires="radio">
          <div class="q">
            <h3>2) What type of IT tasks do you enjoy the most?</h3>
            <div class="opts">
              <?php
                $q2 = [
                  ['uiux','Designing interactive systems or mobile apps'],
                  ['dev','Building programs and writing code'],
                  ['uiux','Designing user-friendly interfaces and web pages'],
                  ['sec','Managing networks or securing systems'],
                  ['data','Analyzing trends or large data sets'],
                  ['sys','Working with hardware or virtual machines'],
                  ['pm','Leading technical projects and teams'],
                  ['game','Developing games or digital environments'],
                ];
                foreach($q2 as $i=>$opt){
                  echo '<label class="opt"><input type="radio" name="q2" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="step" data-step="3" data-requires="radio">
          <div class="q">
            <h3>3) How do you feel about solving technical problems like bugs or errors in code?</h3>
            <div class="opts">
              <label class="opt"><input type="radio" name="q3" value="high"><span>Very confident – I enjoy debugging and solving issues</span></label>
              <label class="opt"><input type="radio" name="q3" value="mid"><span>Somewhat confident – I like following logic and solving puzzles</span></label>
              <label class="opt"><input type="radio" name="q3" value="low"><span>Not confident – I prefer creative or design-oriented work</span></label>
            </div>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="step" data-step="4">
          <div class="q">
            <h3>4) Which of these subjects or tasks do you enjoy most? <span class="pillhint">(Select up to three)</span></h3>
            <div class="opts cols-3" data-limit="3">
              <?php
                $q4 = [
                  ['dev','Coding or software development'],
                  ['uiux','Creating websites or mobile apps'],
                  ['game','Drawing, animation, or game design'],
                  ['sec','Setting up networks or securing systems'],
                  ['logic','Solving logic or math-based problems'],
                  ['data','Working with data or making reports'],
                  ['pm','Organizing group work and managing tasks'],
                ];
                foreach($q4 as $opt){
                  echo '<label class="opt"><input type="checkbox" name="q4[]" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 5 -->
        <div class="step" data-step="5" data-requires="radio">
          <div class="q">
            <h3>5) When working on IT-related projects, what role do you usually prefer?</h3>
            <div class="opts">
              <label class="opt"><input type="radio" name="q5" value="dev"><span>Writing the code or logic</span></label>
              <label class="opt"><input type="radio" name="q5" value="uiux"><span>Designing how the app or interface looks</span></label>
              <label class="opt"><input type="radio" name="q5" value="data"><span>Making sure data is accurate and organized</span></label>
              <label class="opt"><input type="radio" name="q5" value="sys"><span>Setting up or troubleshooting the tech</span></label>
              <label class="opt"><input type="radio" name="q5" value="pm"><span>Planning the timeline or managing the team</span></label>
            </div>
          </div>
        </div>

        <!-- Step 6 -->
        <div class="step" data-step="6" data-requires="radio">
          <div class="q">
            <h3>6) Where do you see yourself working in the future?</h3>
            <div class="opts">
              <?php
                $q6 = [
                  ['dev','In a software or web development company'],
                  ['game','In a game studio or multimedia design firm'],
                  ['sec','In a cybersecurity or IT security role'],
                  ['startup','In a tech startup or freelancing business'],
                  ['data','In a data-driven company or analytics team'],
                  ['sys','In a network administration or systems support team'],
                  ['pm','In a project management or IT consulting role'],
                  ['ai','In a research lab focused on AI or emerging tech'],
                ];
                foreach($q6 as $opt){
                  echo '<label class="opt"><input type="radio" name="q6" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 7 -->
        <div class="step" data-step="7">
          <div class="q">
            <h3>7) Which of the following careers appeal most to you? <span class="pillhint">(Select up to three)</span></h3>
            <div class="opts cols-3" data-limit="3">
              <?php
                $q7 = [
                  ['dev','Software or application developer'],
                  ['game','Game developer or multimedia artist'],
                  ['sec','Network administrator or security analyst'],
                  ['uiux','UI/UX designer or front-end developer'],
                  ['data','Data analyst or database administrator'],
                  ['ai','AI or machine learning specialist'],
                  ['sys','Systems engineer or DevOps technician'],
                  ['pm','IT project manager or business analyst'],
                ];
                foreach($q7 as $opt){
                  echo '<label class="opt"><input type="checkbox" name="q7[]" value="'.$opt[0].'"><span>'.$opt[1].'</span></label>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Step 8 -->
        <div class="step" data-step="8">
          <div class="q">
            <h3>8) Have you had any practical IT experience? <span class="muted">(Select all that apply)</span></h3>
            <div class="opts cols-2">
              <label class="opt"><input type="checkbox" name="q8[]" value="intern"><span>Yes, internship or immersion in a tech company</span></label>
              <label class="opt"><input type="checkbox" name="q8[]" value="freelance"><span>Yes, freelance or self-initiated coding projects</span></label>
              <label class="opt"><input type="checkbox" name="q8[]" value="school"><span>Yes, school-based group tech projects or hackathons</span></label>
              <label class="opt"><input type="checkbox" name="q8[]" value="none"><span>No, but I’m eager to gain experience</span></label>
            </div>
          </div>
        </div>

        <!-- Step 9 -->
        <div class="step" data-step="9">
          <div class="q">
            <h3>9) Have you earned any certificates or digital credentials? <span class="muted">(Select all that apply)</span></h3>
            <div class="opts cols-3">
              <label class="opt"><input type="checkbox" name="q9[]" value="prog"><span>Programming or web development</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="sec"><span>Cybersecurity or networking</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="uiux"><span>UI/UX or graphic design</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="dbcloud"><span>Database or cloud platforms</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="game"><span>Game development tools</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="pm"><span>Project or business management</span></label>
              <label class="opt"><input type="checkbox" name="q9[]" value="none"><span>None yet, but I’m interested</span></label>
            </div>
          </div>
        </div>

        <!-- Step 10 -->
        <div class="step" data-step="10" data-requires="radio">
          <div class="q">
            <h3>10) Which of these activities has felt most rewarding or natural for you?</h3>
            <div class="opts">
              <label class="opt"><input type="radio" name="q10" value="dev"><span>Writing code or solving algorithm problems</span></label>
              <label class="opt"><input type="radio" name="q10" value="uiux"><span>Creating designs, wireframes, or animations</span></label>
              <label class="opt"><input type="radio" name="q10" value="sec"><span>Setting up networks or troubleshooting systems</span></label>
              <label class="opt"><input type="radio" name="q10" value="data"><span>Analyzing and interpreting data</span></label>
              <label class="opt"><input type="radio" name="q10" value="pm"><span>Planning tasks or leading a group project</span></label>
              <label class="opt"><input type="radio" name="q10" value="game"><span>Creating games or interactive apps</span></label>
              <label class="opt"><input type="radio" name="q10" value="undecided"><span>I’m still figuring it out</span></label>
            </div>
          </div>
        </div>

        <!-- nav -->
        <div class="nav" aria-label="Question navigation">
          <div class="left">
            <button type="button" class="btn" id="btnPrev" disabled>&larr; Previous</button>
          </div>
          <div class="right">
            <button type="button" class="btn btn--solid" id="btnNext">Next &rarr;</button>
            <button type="submit" class="btn btn--solid" id="btnSubmit" style="display:none;">Generate Pathway</button>
            <button type="button" class="btn btn--ghost" id="btnReset">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>

<script>
/* ---------- helpers: limit multi-select to N per group ---------- */
document.querySelectorAll('.opts[data-limit]').forEach(group=>{
  const limit = parseInt(group.getAttribute('data-limit'),10)||3;
  const boxes = Array.from(group.querySelectorAll('input[type="checkbox"]'));
  function enforce(){
    const picked = boxes.filter(b=>b.checked);
    const disable = picked.length >= limit;
    boxes.forEach(b=>{ if(!b.checked) b.disabled = disable; });
  }
  boxes.forEach(b=> b.addEventListener('change', enforce));
});

/* ---------- model: categories & content ---------- */
const CATS = {
  dev:{label:'Software & Web Development',roles:['Software Developer','Full-stack Developer','Back-end Developer','Mobile App Developer'],learn:['Programming fundamentals (OOP, DSA)','Web frameworks & APIs','Version control & testing'],certs:['AWS Cloud Practitioner','Microsoft AZ-900','Oracle Java','GitHub Foundations'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  mobile:{label:'Mobile & Enterprise Apps',roles:['Android/iOS Developer','Enterprise App Developer','Integration Engineer'],learn:['Mobile frameworks (Flutter/React Native)','REST/GraphQL APIs','CI/CD basics'],certs:['Google Associate Android Dev','Apple App Dev (Swift)','Scrum Fundamentals'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  uiux:{label:'UI/UX & Front-end',roles:['UI/UX Designer','Front-end Developer','Product Designer'],learn:['HCI & accessibility','Prototyping & design systems','HTML/CSS/JS fundamentals'],certs:['Google UX Certificate','Adobe ACA','freeCodeCamp Responsive Web'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Web Science']},
  game:{label:'Game Dev & Multimedia',roles:['Game Developer','Technical Artist','Interactive Media Dev'],learn:['Game engines (Unity/Unreal)','2D/3D assets & animation','Gameplay programming'],certs:['Unity User/Associate','Autodesk/Adobe badges'],programs:['BSIT - Game Development']},
  data:{label:'Data & Databases',roles:['Data Analyst','BI Developer','Database Administrator'],learn:['SQL & data modeling','Python for data/ETL','Dashboards & storytelling'],certs:['Google Data Analytics','Microsoft DP-900','Oracle Database Foundations'],programs:['BSCS - Data Science','BSIS - Business Analytics']},
  sec:{label:'Cybersecurity & Networks',roles:['Security Analyst','Network Admin','SOC Tier 1'],learn:['Networking & OS','Threats, vuln scanning','Hardening & incident basics'],certs:['CompTIA Security+','Cisco CCNA','(ISC)² CC'],programs:['BSIT - Network Infrastructure & Data Security']},
  sys:{label:'Systems, Cloud & DevOps',roles:['Systems Admin','DevOps Tech','Cloud Support Associate'],learn:['Linux/Windows admin','Scripting & automation','Containers & cloud basics'],certs:['AWS Cloud Practitioner','Linux Essentials','Docker/CKA (later)'],programs:['BSIT - Network Infrastructure & Data Security','BSCS - Software Engineering']},
  ai:{label:'AI & Machine Learning',roles:['ML Engineer (entry)','Data Scientist (jr)','Research Assistant'],learn:['Linear algebra & stats','ML workflows & tooling','Responsible AI basics'],certs:['Google ML/AI courses','Microsoft AI-900'],programs:['BSCS - Data Science','BSCS - Computer Vision','Dual Degree - CS & Engineering']},
  cloud:{label:'Cloud, Blockchain & Emerging Tech',roles:['Cloud Practitioner','Blockchain Dev (jr)','Tech Innovator'],learn:['Cloud services & IaC','Distributed ledgers (intro)','APIs & integrations'],certs:['AWS/Microsoft Fundamentals','Blockchain foundations'],programs:['BSIT - Consumer & Enterprise Application Development','BSCS - Software Engineering']},
  pm:{label:'IT Project & Business Analysis',roles:['IT Project Coordinator','Business Analyst (jr)','Product Ops'],learn:['Project lifecycles & Agile','Requirements & documentation','Stakeholder comms'],certs:['Scrum Master (PSM I)','CAPM','Agile Fundamentals'],programs:['BSIS - Business Analytics','Dual Degree - CS & Business Administration']}
};

/* ---------- labels (for rationale) ---------- */
const LABELS = {
  q1:{dev:'Software or web development',game:'Game design and animation',sec:'Cybersecurity and network defense',uiux:'User interface and user experience (UI/UX) design',data:'Data analytics and databases',mobile:'Mobile or enterprise application development',sys:'Systems administration and IT infrastructure',ai:'Artificial intelligence or machine learning',cloud:'Blockchain, cloud, or emerging technologies'},
  q2:{uiux:'Designing interactive systems or mobile apps',dev:'Building programs and writing code',sec:'Managing networks or securing systems',data:'Analyzing trends or large data sets',sys:'Working with hardware or virtual machines',pm:'Leading technical projects and teams',game:'Developing games or digital environments'},
  q3:{high:'Very confident – I enjoy debugging and solving issues',mid:'Somewhat confident – I like following logic and solving puzzles',low:'Not confident – I prefer creative or design-oriented work'},
  q4:{dev:'Coding or software development',uiux:'Creating websites or mobile apps',game:'Drawing, animation, or game design',sec:'Setting up networks or securing systems',logic:'Solving logic or math-based problems',data:'Working with data or making reports',pm:'Organizing group work and managing tasks'},
  q5:{dev:'Writing the code or logic',uiux:'Designing how the app or interface looks',data:'Making sure data is accurate and organized',sys:'Setting up or troubleshooting the tech',pm:'Planning the timeline or managing the team'},
  q6:{dev:'In a software or web development company',game:'In a game studio or multimedia design firm',sec:'In a cybersecurity or IT security role',startup:'In a tech startup or freelancing business',data:'In a data-driven company or analytics team',sys:'In a network administration or systems support team',pm:'In a project management or IT consulting role',ai:'In a research lab focused on AI or emerging tech'},
  q7:{dev:'Software or application developer',game:'Game developer or multimedia artist',sec:'Network administrator or security analyst',uiux:'UI/UX designer or front-end developer',data:'Data analyst or database administrator',ai:'AI or machine learning specialist',sys:'Systems engineer or DevOps technician',pm:'IT project manager or business analyst'},
  q8:{intern:'Internship/immersion',freelance:'Freelance/self-initiated coding',school:'School-based tech projects/hackathons',none:'No experience yet'},
  q9:{prog:'Programming/web dev credential',sec:'Cybersecurity/networking credential',uiux:'UI/UX/graphic design credential',dbcloud:'Database/cloud credential',game:'Game development credential',pm:'Project/business management credential',none:'No credential yet'},
  q10:{dev:'Writing code or solving algorithm problems',uiux:'Creating designs, wireframes, or animations',sec:'Setting up networks or troubleshooting systems',data:'Analyzing and interpreting data',pm:'Planning tasks or leading a group project',game:'Creating games or interactive apps',undecided:'Undecided'}
};

/* ---------- scoring with rationale/trace ---------- */
function baseScores(){ return {dev:0,mobile:0,uiux:0,game:0,data:0,sec:0,sys:0,ai:0,cloud:0,pm:0}; }
function baseTrace(){ return {dev:[],mobile:[],uiux:[],game:[],data:[],sec:[],sys:[],ai:[],cloud:[],pm:[]}; }

function add(scores, key, w, trace, reason){
  if(key in scores){ scores[key]+=w; if(trace && reason){ trace[key].push({w, reason}); } }
}
function addAll(scores, map, trace, source){
  Object.entries(map).forEach(([k,v])=>{
    add(scores, k, v, trace, `${source} → +${v} ${CATS[k]?.label||k}`);
  });
}

function scoreFromForm(fd){
  const s = baseScores();
  const t = baseTrace();

  (fd.getAll('q1[]')||[]).forEach(v=>{
    add(s, v, 3, t, `Q1 interest: “${LABELS.q1[v]}”`);
    if(v==='mobile') add(s,'dev',1,t,`Q1 related: mobile supports development`);
    if(v==='cloud') add(s,'sys',1,t,`Q1 related: cloud links to systems/infrastructure`);
  });

  const q2 = fd.get('q2');
  if(q2){ add(s, q2, 2, t, `Q2 task preference: “${LABELS.q2[q2]}”`); }

  const q3 = fd.get('q3');
  if(q3==='high') addAll(s,{dev:2,data:1,sec:1,sys:1,ai:1}, t, `Q3 confidence: ${LABELS.q3[q3]}`);
  else if(q3==='mid') addAll(s,{dev:1,data:1,sys:1}, t, `Q3 confidence: ${LABELS.q3[q3]}`);
  else if(q3==='low') addAll(s,{uiux:2,pm:1}, t, `Q3 confidence: ${LABELS.q3[q3]}`);

  (fd.getAll('q4[]')||[]).forEach(v=>{
    if(v==='logic'){ addAll(s,{dev:1,data:1,ai:1,sec:1}, t, `Q4 subject: “${LABELS.q4[v]}”`); }
    else { add(s, v==='prog' ? 'dev' : v, 2, t, `Q4 subject: “${LABELS.q4[v]}”`); }
  });

  const q5 = fd.get('q5'); if(q5) add(s,q5,2,t,`Q5 project role: “${LABELS.q5[q5]}”`);

  const q6 = fd.get('q6');
  if(q6==='startup'){ addAll(s,{dev:1,uiux:1,cloud:1,pm:1}, t, `Q6 workplace: “${LABELS.q6[q6]}”`); }
  else if(q6){ add(s,q6,2,t,`Q6 workplace: “${LABELS.q6[q6]}”`); }

  (fd.getAll('q7[]')||[]).forEach(v=> add(s,v,3,t,`Q7 career interest: “${LABELS.q7[v]}”`));

  (fd.getAll('q8[]')||[]).forEach(v=>{
    if(v==='intern') addAll(s,{dev:1,uiux:1,data:1,sec:1,sys:1,cloud:1,pm:1}, t, `Q8 experience: ${LABELS.q8[v]}`);
    if(v==='freelance') addAll(s,{dev:2,uiux:1,mobile:1,game:1}, t, `Q8 experience: ${LABELS.q8[v]}`);
    if(v==='school') addAll(s,{dev:1,pm:1,data:1,sec:1}, t, `Q8 experience: ${LABELS.q8[v]}`);
  });

  (fd.getAll('q9[]')||[]).forEach(v=>{
    if(v==='prog') addAll(s,{dev:2,uiux:1,mobile:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='sec') addAll(s,{sec:2,sys:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='uiux') addAll(s,{uiux:2,game:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='dbcloud') addAll(s,{data:2,cloud:1,sys:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='game') addAll(s,{game:2,dev:1}, t, `Q9 credential: ${LABELS.q9[v]}`);
    if(v==='pm') addAll(s,{pm:2}, t, `Q9 credential: ${LABELS.q9[v]}`);
  });

  const q10 = fd.get('q10');
  if(q10 && q10!=='undecided') add(s,q10,2,t,`Q10 natural activity: “${LABELS.q10[q10]}”`);
  if(q10==='undecided') addAll(s,{pm:1,uiux:1,dev:1}, t, `Q10: undecided → broad starters`);

  return {scores:s, trace:t};
}

/* ---------- stepper logic ---------- */
const form = document.getElementById('cpForm');
const steps = Array.from(document.querySelectorAll('.step'));
const total = steps.length;
let current = 0;

const btnPrev   = document.getElementById('btnPrev');
const btnNext   = document.getElementById('btnNext');
const btnSubmit = document.getElementById('btnSubmit');
const btnReset  = document.getElementById('btnReset');

const progressText = document.getElementById('progressText');
const progressFill = document.getElementById('progressFill');

function updateProgress(){
  const n = current + 1;
  progressText.textContent = `Question ${n} of ${total}`;
  const pct = Math.max(6, Math.round((n-1)/(total-1)*100));
  progressFill.style.width = pct + '%';
}

function showStep(i){
  steps.forEach((s, idx)=> s.classList.toggle('is-active', idx===i));
  current = i;
  btnPrev.disabled = (current === 0);
  const last = (current === total - 1);
  btnNext.style.display   = last ? 'none' : '';
  btnSubmit.style.display = last ? '' : 'none';
  enforceStepRequirement();
  updateProgress();
  // focus first control for accessibility
  const focusable = steps[current].querySelector('input,button,select,textarea');
  if (focusable) setTimeout(()=>focusable.focus(), 50);
}

function stepHasRequirement(stepEl){
  return stepEl.hasAttribute('data-requires');
}
function stepIsSatisfied(stepEl){
  if (!stepHasRequirement(stepEl)) return true;
  const type = stepEl.getAttribute('data-requires');
  if (type === 'radio'){
    const radios = stepEl.querySelectorAll('input[type="radio"]');
    return Array.from(radios).some(r => r.checked);
  }
  return true;
}
function enforceStepRequirement(){
  const ok = stepIsSatisfied(steps[current]);
  btnNext.disabled = !ok && (current < total - 1);
  btnSubmit.disabled = !ok && (current === total - 1);
}

steps.forEach(s=> s.addEventListener('change', enforceStepRequirement));

btnPrev.addEventListener('click', ()=> showStep(Math.max(0, current-1)));
btnNext.addEventListener('click', ()=>{
  if (!stepIsSatisfied(steps[current])) { enforceStepRequirement(); return; }
  showStep(Math.min(total-1, current+1));
});
btnReset.addEventListener('click', ()=>{
  form.reset();
  document.querySelectorAll('.opts[data-limit] input[type="checkbox"]').forEach(b=> b.disabled=false);
  showStep(0);
});

form.addEventListener('keydown', (e)=>{
  if (e.key === 'Enter'){
    const isText = ['TEXTAREA','INPUT'].includes(e.target.tagName) && e.target.type==='text';
    if (isText) return;
    e.preventDefault();
    if (current < total-1) btnNext.click();
  }
});

showStep(0);

/* ---------- submit ---------- */
form.addEventListener('submit', e=>{
  e.preventDefault();
  const fd = new FormData(form);
  const anySeed = (fd.getAll('q1[]').length + fd.getAll('q4[]').length + fd.getAll('q7[]').length) > 0;
  if(!anySeed){
    alert('Please select at least one interest (Q1, Q4 or Q7) so we can tailor your results.');
    showStep(0);
    return;
  }
  const {scores, trace} = scoreFromForm(fd);
  sessionStorage.setItem('pathwayResults', JSON.stringify(scores));
  sessionStorage.setItem('pathwayTrace', JSON.stringify(trace));
  window.location.href = '/adamson-ccit/public/index.php?page=career_pathway_results';
});
</script>

</body>
</html>
