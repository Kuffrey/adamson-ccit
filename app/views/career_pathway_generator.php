<?php /* career_pathway_generator.php — CCIT Career Pathway Generator (draft) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Career Pathway Generator | AdU-CCIT</title>
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>
  <style>
    /* small page-only polish, reusing your tokens */
    .page-career .content > .container{padding-block:clamp(22px,4vw,44px)}
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
    .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
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
    .pillhint{font:800 11px/1 "Inter",system-ui;text-transform:uppercase;letter-spacing:.05em;color:#475569}
    .note-inline{margin-top:10px;border:1px dashed var(--edgec);background:#fafbfd;color:#6b7280;border-radius:12px;padding:10px}
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

  <!-- LOCAL SUBNAV (optional: align with Faculty pages if you link it there)
  <nav class="subnav" aria-label="Planner sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li class="is-active"><a href="#" aria-current="page">Generator</a></li>
      </ul>
    </div>
  </nav>
  -->

  <!-- FORM -->
  <section class="content section-sep">
    <div class="container">
      <form id="cpForm" novalidate>

        <!-- Section 1 -->
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
                echo '<label class="opt"><input type="radio" name="q2" value="'.$opt[0].'" '.($i===0?'required':'').'><span>'.$opt[1].'</span></label>';
              }
            ?>
          </div>
        </div>

        <!-- Section 2 -->
        <div class="q">
          <h3>3) How do you feel about solving technical problems like bugs or errors in code?</h3>
          <div class="opts">
            <label class="opt"><input type="radio" name="q3" value="high" required><span>Very confident – I enjoy debugging and solving issues</span></label>
            <label class="opt"><input type="radio" name="q3" value="mid"><span>Somewhat confident – I like following logic and solving puzzles</span></label>
            <label class="opt"><input type="radio" name="q3" value="low"><span>Not confident – I prefer creative or design-oriented work</span></label>
          </div>
        </div>

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

        <div class="q">
          <h3>5) When working on IT-related projects, what role do you usually prefer?</h3>
          <div class="opts">
            <label class="opt"><input type="radio" name="q5" value="dev" required><span>Writing the code or logic</span></label>
            <label class="opt"><input type="radio" name="q5" value="uiux"><span>Designing how the app or interface looks</span></label>
            <label class="opt"><input type="radio" name="q5" value="data"><span>Making sure data is accurate and organized</span></label>
            <label class="opt"><input type="radio" name="q5" value="sys"><span>Setting up or troubleshooting the tech</span></label>
            <label class="opt"><input type="radio" name="q5" value="pm"><span>Planning the timeline or managing the team</span></label>
          </div>
        </div>

        <!-- Section 3 -->
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
              foreach($q6 as $i=>$opt){
                echo '<label class="opt"><input type="radio" name="q6" value="'.$opt[0].'" '.($i===0?'required':'').'><span>'.$opt[1].'</span></label>';
              }
            ?>
          </div>
        </div>

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

        <!-- Section 4 -->
        <div class="q">
          <h3>8) Have you had any practical IT experience? <span class="muted">(Select all that apply)</span></h3>
          <div class="opts cols-2">
            <label class="opt"><input type="checkbox" name="q8[]" value="intern"><span>Yes, internship or immersion in a tech company</span></label>
            <label class="opt"><input type="checkbox" name="q8[]" value="freelance"><span>Yes, freelance or self-initiated coding projects</span></label>
            <label class="opt"><input type="checkbox" name="q8[]" value="school"><span>Yes, school-based group tech projects or hackathons</span></label>
            <label class="opt"><input type="checkbox" name="q8[]" value="none"><span>No, but I’m eager to gain experience</span></label>
          </div>
        </div>

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

        <div class="q">
          <h3>10) Which of these activities has felt most rewarding or natural for you?</h3>
          <div class="opts">
            <label class="opt"><input type="radio" name="q10" value="dev" required><span>Writing code or solving algorithm problems</span></label>
            <label class="opt"><input type="radio" name="q10" value="uiux"><span>Creating designs, wireframes, or animations</span></label>
            <label class="opt"><input type="radio" name="q10" value="sec"><span>Setting up networks or troubleshooting systems</span></label>
            <label class="opt"><input type="radio" name="q10" value="data"><span>Analyzing and interpreting data</span></label>
            <label class="opt"><input type="radio" name="q10" value="pm"><span>Planning tasks or leading a group project</span></label>
            <label class="opt"><input type="radio" name="q10" value="game"><span>Creating games or interactive apps</span></label>
            <label class="opt"><input type="radio" name="q10" value="undecided"><span>I’m still figuring it out</span></label>
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn btn--solid">Generate Pathway</button>
          <button type="button" id="btnReset" class="btn btn--outline-blue">Reset</button>
          <button type="button" id="btnPrint" class="btn btn--outline-blue">Print / Save</button>
        </div>
      </form>

      <!-- RESULTS -->
      <section id="cpResults" class="results section-sep" aria-live="polite" aria-labelledby="res-head">
        <div class="container">
          <div class="sec__head">
            <h2 id="res-head">Your Top Matches</h2>
            <p class="sec__kicker">Based on your interests, preferences, and goals.</p>
          </div>
          <div id="resGrid" class="res__grid"></div>

          <div id="resAlso" class="note-inline" hidden></div>
        </div>
      </section>
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
  dev:   {label:'Software & Web Development',
          roles:['Software Developer','Full-stack Developer','Back-end Developer','Mobile App Developer'],
          learn:['Programming fundamentals (OOP, DSA)','Web frameworks & APIs','Version control & testing'],
          certs:['AWS Cloud Practitioner','Microsoft AZ-900','Oracle Java','GitHub Foundations']},
  mobile:{label:'Mobile & Enterprise Apps',
          roles:['Android/iOS Developer','Enterprise App Developer','Integration Engineer'],
          learn:['Mobile frameworks (Flutter/React Native)','REST/GraphQL APIs','CI/CD basics'],
          certs:['Google Associate Android Dev','Apple App Dev (Swift)','Scrum Fundamentals']},
  uiux:  {label:'UI/UX & Front-end',
          roles:['UI/UX Designer','Front-end Developer','Product Designer'],
          learn:['HCI & accessibility','Prototyping & design systems','HTML/CSS/JS fundamentals'],
          certs:['Google UX Certificate','Adobe ACA','freeCodeCamp Responsive Web']},
  game:  {label:'Game Dev & Multimedia',
          roles:['Game Developer','Technical Artist','Interactive Media Dev'],
          learn:['Game engines (Unity/Unreal)','2D/3D assets & animation','Gameplay programming'],
          certs:['Unity User/Associate','Autodesk/Adobe badges']},
  data:  {label:'Data & Databases',
          roles:['Data Analyst','BI Developer','Database Administrator'],
          learn:['SQL & data modeling','Python for data/ETL','Dashboards & storytelling'],
          certs:['Google Data Analytics','Microsoft DP-900','Oracle Database Foundations']},
  sec:   {label:'Cybersecurity & Networks',
          roles:['Security Analyst','Network Admin','SOC Tier 1'],
          learn:['Networking & OS','Threats, vuln scanning','Hardening & incident basics'],
          certs:['CompTIA Security+','Cisco CCNA','(ISC)² CC']},
  sys:   {label:'Systems, Cloud & DevOps',
          roles:['Systems Admin','DevOps Tech','Cloud Support Associate'],
          learn:['Linux/Windows admin','Scripting & automation','Containers & cloud basics'],
          certs:['AWS Cloud Practitioner','Linux Essentials','Docker/CKA (later)']},
  ai:    {label:'AI & Machine Learning',
          roles:['ML Engineer (entry)','Data Scientist (jr)','Research Assistant'],
          learn:['Linear algebra & stats','ML workflows & tooling','Responsible AI basics'],
          certs:['Google ML/AI courses','Microsoft AI-900']},
  cloud: {label:'Cloud, Blockchain & Emerging Tech',
          roles:['Cloud Practitioner','Blockchain Dev (jr)','Tech Innovator'],
          learn:['Cloud services & IaC','Distributed ledgers (intro)','APIs & integrations'],
          certs:['AWS/Microsoft Fundamentals','Blockchain foundations']},
  pm:    {label:'IT Project & Business Analysis',
          roles:['IT Project Coordinator','Business Analyst (jr)','Product Ops'],
          learn:['Project lifecycles & Agile','Requirements & documentation','Stakeholder comms'],
          certs:['Scrum Master (PSM I)','CAPM','Agile Fundamentals']}
};

/* ---------- scoring weights ---------- */
function baseScores(){ return {dev:0,mobile:0,uiux:0,game:0,data:0,sec:0,sys:0,ai:0,cloud:0,pm:0}; }

function scoreFromForm(fd){
  const s = baseScores();

  // Q1 up to 3: strong interest
  (fd.getAll('q1[]')||[]).forEach(v=>{
    add(s, v, 3);
    if(v==='mobile') add(s,'dev',1);
    if(v==='cloud') add(s,'sys',1);
  });

  // Q2 single: task you enjoy
  const q2 = fd.get('q2');
  if(q2){ add(s, q2, 2); if(q2==='startup') addAll(s,{dev:1,uiux:1,cloud:1}); }

  // Q3 debug confidence
  const q3 = fd.get('q3');
  if(q3==='high') addAll(s,{dev:2,data:1,sec:1,sys:1,ai:1});
  else if(q3==='mid') addAll(s,{dev:1,data:1,sys:1});
  else if(q3==='low') addAll(s,{uiux:2,pm:1});

  // Q4 up to 3 subjects
  (fd.getAll('q4[]')||[]).forEach(v=>{
    if(v==='logic') addAll(s,{dev:1,data:1,ai:1,sec:1});
    else add(s, v==='prog' ? 'dev' : v, 2);
  });

  // Q5 role preference
  const q5 = fd.get('q5'); if(q5) add(s,q5,2);

  // Q6 workplace aspiration
  const q6 = fd.get('q6');
  const map6 = {startup:{dev:1,uiux:1,cloud:1,pm:1}};
  if(q6){
    if(map6[q6]) addAll(s,map6[q6]);
    else add(s,q6,2);
  }

  // Q7 careers up to 3
  (fd.getAll('q7[]')||[]).forEach(v=> add(s,v,3));

  // Q8 experience
  (fd.getAll('q8[]')||[]).forEach(v=>{
    if(v==='intern') addAll(s,{dev:1,uiux:1,data:1,sec:1,sys:1,cloud:1,pm:1});
    if(v==='freelance') addAll(s,{dev:2,uiux:1,mobile:1,game:1});
    if(v==='school') addAll(s,{dev:1,pm:1,data:1,sec:1});
  });

  // Q9 certificates
  (fd.getAll('q9[]')||[]).forEach(v=>{
    if(v==='prog') addAll(s,{dev:2,uiux:1,mobile:1});
    if(v==='sec') addAll(s,{sec:2,sys:1});
    if(v==='uiux') addAll(s,{uiux:2,game:1});
    if(v==='dbcloud') addAll(s,{data:2,cloud:1,sys:1});
    if(v==='game') addAll(s,{game:2,dev:1});
    if(v==='pm') addAll(s,{pm:2});
  });

  // Q10 natural activity
  const q10 = fd.get('q10');
  if(q10 && q10!=='undecided') add(s,q10,2);
  if(q10==='undecided') addAll(s,{pm:1,uiux:1,dev:1}); // gentle nudge to broad starters

  return s;
}

function add(obj, key, w){ if(key && key in obj) obj[key]+=w; }
function addAll(obj, m){ Object.entries(m).forEach(([k,v])=>add(obj,k,v)); }

/* ---------- rendering ---------- */
function renderResults(scores){
  const entries = Object.entries(scores).sort((a,b)=>b[1]-a[1]);
  const top = entries.filter(e=>e[1]>0).slice(0,3);
  const also = entries.slice(3,5).filter(e=>e[1]>0);

  const resGrid = document.getElementById('resGrid');
  resGrid.innerHTML = '';

  if(top.length===0){
    resGrid.innerHTML = '<div class="res"><div class="res__head"><strong>No clear match yet</strong></div><p class="muted">Try selecting at least a few interests and roles, then generate again.</p></div>';
  }else{
    top.forEach(([k,score])=>{
      const c = CATS[k];
      const roles = (c.roles||[]).slice(0,4).map(r=>`<span class="chip">${r}</span>`).join('');
      const learn = (c.learn||[]).map(x=>`<li>${x}</li>`).join('');
      const certs = (c.certs||[]).map(x=>`<li>${x}</li>`).join('');
      const html = `
        <article class="res">
          <div class="res__head">
            <h3 class="h3" style="margin:0">${c.label}</h3>
            <span class="score">Score: ${score}</span>
          </div>
          <p class="muted">Why this appears: matches your interests, preferred tasks, and target roles.</p>
          <div class="mini">${roles}</div>
          <div class="hr"></div>
          <h4 class="h4" style="margin:0 0 6px;color:#0b234c">Suggested next steps</h4>
          <ul class="bullets" style="margin:0">
            ${learn}
          </ul>
          <p class="muted" style="margin:8px 0 6px">Starter certifications / badges</p>
          <ul class="bullets" style="margin:0">
            ${certs}
          </ul>
        </article>`;
      resGrid.insertAdjacentHTML('beforeend', html);
    });
  }

  const alsoBox = document.getElementById('resAlso');
  if(also.length){
    const chips = also.map(([k,sc])=>`<span class="chip">${CATS[k].label} · ${sc}</span>`).join(' ');
    alsoBox.innerHTML = `Also consider: ${chips}`;
    alsoBox.hidden = false;
  }else{
    alsoBox.hidden = true;
  }

  document.getElementById('cpResults').style.display = 'block';
}

/* ---------- form handlers ---------- */
const form = document.getElementById('cpForm');
form.addEventListener('submit', e=>{
  e.preventDefault();
  const fd = new FormData(form);
  // minimal validation: ensure at least one of Q1/Q4/Q7 has a pick
  const anySeed = (fd.getAll('q1[]').length + fd.getAll('q4[]').length + fd.getAll('q7[]').length) > 0;
  if(!anySeed){
    alert('Please select at least one interest (Q1, Q4 or Q7) so we can tailor your results.');
    return;
  }
  const scores = scoreFromForm(fd);
  renderResults(scores);
  window.scrollTo({top: document.getElementById('cpResults').offsetTop - 20, behavior: 'smooth'});
});

document.getElementById('btnReset').addEventListener('click', ()=>{
  form.reset();
  // re-enable any disabled boxes (after limiters)
  document.querySelectorAll('.opts[data-limit] input[type="checkbox"]').forEach(b=> b.disabled=false);
  document.getElementById('cpResults').style.display='none';
});

document.getElementById('btnPrint').addEventListener('click', ()=> window.print());
</script>

</body>
</html>
