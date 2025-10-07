<?php /* career_pathway_results.php — Display personalized pathway results */ ?>
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
      box-shadow:0 4px 12px rgba(0,128,201,0.15)
    }
    
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
      background:#00713D;color:#ffffff;
      padding:4px 10px;border-radius:6px;
      font-size:0.7rem;font-weight:700;
      text-transform:uppercase;letter-spacing:0.05em;
      white-space:nowrap
    }
    
    .score{
      background:#f1f8ff;color:#003169;
      border:1px solid #0080c9;border-radius:6px;
      padding:6px 12px;font-weight:700;font-size:0.875rem;
      white-space:nowrap
    }
    
    /* Description */
    .res__desc{
      color:#64748b;font-size:0.9rem;line-height:1.6;
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
      color:#003169;padding:8px 12px;border-radius:6px;
      font-size:0.813rem;font-weight:600;
      transition:all 0.15s ease
    }
    .mini .chip:hover{
      background:#0080c9;border-color:#0080c9;color:#ffffff
    }
    
    /* Lists */
    .bullets{
      margin:0 0 0 18px;padding:0;
      color:#475569;line-height:1.7;font-size:0.9rem
    }
    .bullets li{margin-bottom:8px}
    .bullets li::marker{color:#0080c9}
    
    /* Program Chips */
    .program-list{display:flex;flex-direction:column;gap:10px}
    .program-chip{
      background:#f0f9f4;
      border:1px solid #00713D;
      color:#00713D;
      padding:12px 16px;border-radius:8px;
      font-weight:600;font-size:0.875rem;
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
  </style>
</head>
<body>

<main class="page-results">
  <!-- SUB-HERO -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Your Results</p>
      <h1 class="subhero__title">Career Pathway Recommendations</h1>
      <p class="subhero__lead">Based on your interests, skills, and goals — here are your best-fit programs and career paths.</p>
    </div>
  </section>

  <!-- RESULTS CONTENT -->
  <section class="content">
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
    tracks: [
      'Consumer & Enterprise Application Development',
      'Game Development',
      'Network Infrastructure & Data Security'
    ]
  },
  'BSCS': {
    title: 'BS Computer Science',
    desc: 'Rigorous computer science program emphasizing algorithmic thinking, software engineering principles, and advanced computing concepts.',
    specializations: [
      'Data Science',
      'Web Science',
      'Computer Vision'
    ]
  },
  'BSIS': {
    title: 'BS Information Systems',
    desc: 'Strategic IT program combining business acumen with technical expertise for effective information systems management.',
    specializations: [
      'Business Analytics'
    ]
  },
  'DUAL': {
    title: 'Dual Degree Program',
    desc: 'Accelerated dual degree program allowing students to earn two bachelor\'s degrees in Computer Science plus Business Administration or Engineering.',
    options: [
      'BS Computer Science & Information Engineering',
      'BS Computer Science & Business Administration'
    ]
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

/* ---------- Render Results ---------- */
function renderResults() {
  const container = document.getElementById('resultsContainer');
  const storedScores = sessionStorage.getItem('pathwayResults');
  
  if (!storedScores) {
    container.innerHTML = `
      <div class="no-results">
        <h3>No Results Found</h3>
        <p>Please complete the Career Pathway Generator questionnaire first.</p>
        <a href="/adamson-ccit/public/index.php?page=career_pathway_generator" class="btn btn--solid">Take the Survey</a>
      </div>`;
    return;
  }
  
  const scores = JSON.parse(storedScores);
  const entries = Object.entries(scores).sort((a,b)=>b[1]-a[1]);
  const top = entries.filter(e=>e[1]>0).slice(0,3);
  
  if(top.length === 0) {
    container.innerHTML = `
      <div class="no-results">
        <h3>Inconclusive Results</h3>
        <p>We need a bit more information to provide accurate recommendations. Please retake the survey and select more options.</p>
        <a href="/adamson-ccit/public/index.php?page=career_pathway_generator" class="btn btn--solid">Retake Survey</a>
      </div>`;
    return;
  }
  
  let html = '<div class="res__grid">';
  
  top.forEach(([key, score], idx) => {
    const cat = CATS[key];
    const rankBadge = idx === 0 ? '<span class="rank-badge">🏆 Top Match</span>' : '';
    const roles = (cat.roles||[]).map(r=>`<span class="chip">${r}</span>`).join('');
    const learn = (cat.learn||[]).map(x=>`<li>${x}</li>`).join('');
    const certs = (cat.certs||[]).map(x=>`<li>${x}</li>`).join('');
    const programs = (cat.programs||[]).map(p=>`<div class="program-chip">${p}</div>`).join('');
    
    html += `
      <article class="res">
        <div class="res__head">
          <h3>${cat.label}</h3>
          ${rankBadge}
          <span class="score">${score} pts</span>
        </div>
        <p class="res__desc">Matches your interests, preferred tasks, and career goals</p>
        
        <div class="section-box">
          <h4>🎯 Recommended CCIT Programs</h4>
          <div class="program-list">${programs}</div>
        </div>
        
        <div class="section-box">
          <h4>💼 Career Opportunities</h4>
          <div class="mini">${roles}</div>
        </div>
        
        <div class="hr"></div>
        
        <div class="section-box">
          <h4>📚 Learning Path</h4>
          <ul class="bullets">${learn}</ul>
        </div>
        
        <div class="section-box">
          <h4>🏆 Recommended Certifications</h4>
          <ul class="bullets">${certs}</ul>
        </div>
      </article>`;
  });
  
  html += '</div>';
  
  // CTA section
  html += `
    <div class="cta-box">
      <h3>Ready to Start Your Journey?</h3>
      <p>Explore our programs in detail and take the next step toward your ideal career in computing and information technology.</p>
      <div class="cta-actions">
        <a href="/adamson-ccit/public/index.php?page=programs_undergraduate" class="btn btn--solid">View All Programs</a>
        <a href="/adamson-ccit/public/index.php?page=admission_freshman" class="btn btn--outline-blue">Apply Now</a>
        <button onclick="window.print()" class="btn btn--outline-blue">Print Results</button>
        <a href="/adamson-ccit/public/index.php?page=career_pathway_generator" class="btn btn--outline-blue">Retake Survey</a>
      </div>
    </div>`;
  
  container.innerHTML = html;
}

// Render on page load
document.addEventListener('DOMContentLoaded', renderResults);
</script>

</body>
</html>
