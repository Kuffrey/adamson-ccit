<?php /* programs_dual_degree.php — Dual Degree (CS + IE) with clear CCIT emphasis and one primary CTA */ ?>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/programs/dual.jpg" alt="Adamson x MUST dual-degree collaboration">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Programs</p>
      <h1 class="subhero__title">Dual Degree: Computer Science &amp; Information Engineering</h1>
      <p class="subhero__lead">Earn two degrees through Adamson University and Minghsin University of Science and Technology (Taiwan).</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV ============ -->
  <nav class="subnav" aria-label="Programs sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=programs_undergraduate">Undergraduate</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=programs_dual_degree" aria-current="page">Dual Degree</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=programs_graduate_studies">Graduate Studies</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ PROGRAM SECTION ============ -->
  <section class="content">
    <div class="container">
      <div class="prog__grid prog__grid--single">

        <!-- CS + IE Dual Degree -->
        <article class="prog__card" id="csie">
          <header class="prog__head">
            <span class="badge" aria-hidden="true">Dual Degree</span>
            <h2 class="prog__title">B.S. Computer Science &amp; B.S. Information Engineering</h2>
          </header>

          <p class="prog__summary">
            A unified pathway that blends computing theory with the engineering of information systems and networks—delivered by Adamson University
            and Minghsin University of Science and Technology (MUST), Taiwan.
          </p>

          <!-- Degrees & Partner -->
          <section class="callout">
            <h3 class="callout__title">Partner &amp; Degrees awarded</h3>
            <ul class="chips chips--grid" role="list">
              <li class="chip chip--em">Adamson: B.S. Computer Science</li>
              <li class="chip chip--em">MUST (Taiwan): B.S. Information Engineering</li>
            </ul>
          </section>

          <!-- Highlights -->
          <section class="high">
            <h3 class="high__title">Program highlights</h3>
            <ul class="high__list" role="list">
              <li>Two credentials that strengthen global readiness and industry credibility.</li>
              <li>Balanced preparation: algorithms, software systems, data &amp; information engineering.</li>
              <li>Research and industry linkages across PH and Taiwan.</li>
              <li>Cross-cultural experience and potential international apprenticeships.</li>
            </ul>
          </section>

          <!-- What you'll study -->
          <section class="domains">
            <h3 class="domains__title">What you’ll study (sample domains)</h3>
            <div class="domains__grid">
              <div class="dom">
                <h4>Software Systems</h4>
                <p>Programming, software engineering, OS, databases, web/app development.</p>
              </div>
              <div class="dom">
                <h4>Networks &amp; Security</h4>
                <p>Computer networks, information security, distributed systems.</p>
              </div>
              <div class="dom">
                <h4>Intelligent Media</h4>
                <p>AI, machine learning, computer vision, HCI, graphics &amp; animation.</p>
              </div>
              <div class="dom">
                <h4>Scientific Computing</h4>
                <p>Modeling &amp; simulation, data analytics, bio/medical informatics.</p>
              </div>
              <div class="dom">
                <h4>Theory &amp; Foundations</h4>
                <p>Discrete math, algorithms, formal languages, information theory.</p>
              </div>
            </div>
          </section>

          <!-- Partner note -->
          <section class="note note--soft">
            <p><strong>About MUST:</strong> Minghsin University of Science and Technology (Hsinchu County, Taiwan) is known for science &amp; tech programs,
            strong industry collaboration, and applied research culture.</p>
          </section>

          <!-- Actions: one primary + quiet secondary links -->
          <div class="prog__footer">
            <a class="btn btn--outline-blue" href="https://www.adamson.edu.ph/v1/?page=dual-degree-cs-ie-home&col=13" target="_blank" rel="noopener">Learn More</a>
            <nav class="mini-links" aria-label="Dual Degree links">
              <a href="https://www.adamson.edu.ph/v1/?page=curriculum&cid=76&curryear=2023" target="_blank" rel="noopener">View Curriculum</a>
              <a href="/adamson-ccit/public/index.php?page=admission_freshman">Apply</a>
            </nav>
          </div>
        </article>

      </div>
    </div>
  </section>

  <!-- ============ CTA (reuse site pattern) ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Still exploring your options?</h2>
        <p>Compare programs and find your best fit with the Career Pathway Generator.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=career_pathway_generator">Launch the Tool</a>
    </div>
  </section>

</main>

<style>
  :root{
    --green:#00713D; --blue:#003169; --blue2:#0080c9;
    --ink:#111827; --muted:#4b5563; --edge:#e6e9ef;
    --bg:#fff; --bg-alt:#f6f8fb; --maxw:1200px;
    --callout-bg:#f4f7ff; --callout-bd:#dbe5fb;
  }
  main{font-family:"Inter",system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif}
  .container{max-width:var(--maxw);margin-inline:auto;padding:0 20px}

  /* SUB-HERO (solid) */
  .subhero{position:relative;color:#fff;background:#0a204b;min-height:320px;overflow:clip}
  .subhero__media{position:absolute;inset:0;overflow:hidden}
  .subhero__media img{width:100%;height:100%;object-fit:cover;display:block;transform:scale(1.02)}
  .subhero__scrim{position:absolute;inset:0;background:rgba(0,0,0,.45)}
  .subhero__inner{position:relative;z-index:2;display:flex;flex-direction:column;justify-content:center;min-height:inherit;padding:36px 0}
  .eyebrow{display:inline-block;background:#e8f5ee;border:1px solid #d6e9df;color:#0b234c;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;letter-spacing:.05em;text-transform:uppercase}
  .subhero__title{margin:12px 0 6px;font-weight:900;font-size:clamp(28px,4.6vw,44px);line-height:1.08}
  .subhero__lead{margin:0;opacity:.95;max-width:70ch}

  /* SUBNAV (pills) */
  .subnav{background:#fff;border-bottom:1px solid var(--edge)}
  .subnav__list{margin:0;padding:10px 0;list-style:none;display:flex;gap:8px;flex-wrap:wrap}
  .subnav__list a{
    display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;
    font:600 13px/1 "Inter",system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif;
    text-transform:uppercase;letter-spacing:.03em;color:#0b234c;text-decoration:none;border:1px solid transparent;
  }
  .subnav__list a:hover{background:#f5f7fb;border-color:#e3e8f2}
  .subnav__list .is-active a, .subnav__list a[aria-current="page"]{background:var(--green);color:#fff;border-color:var(--green)}

  /* GRID (single column center) */
  .content{background:#fff}
  .prog__grid{display:grid;gap:16px;grid-template-columns:repeat(3,1fr);padding: clamp(24px,5vw,48px) 0}
  .prog__grid--single{grid-template-columns:1fr;max-width:900px;margin-inline:auto}
  @media (max-width:980px){ .prog__grid{grid-template-columns:1fr} }

  /* CARD */
  .prog__card{
    border:1px solid var(--edge);border-radius:16px;background:#fff;padding:16px;
    display:flex;flex-direction:column;gap:12px;height:100%;
    transition:box-shadow .12s ease, transform .12s ease;
  }
  .prog__card:hover{box-shadow:0 10px 30px rgba(0,0,0,.08);transform:translateY(-2px)}
  .prog__head{display:flex;align-items:center;gap:10px}
  .badge{
    display:inline-block;padding:6px 10px;border-radius:999px;border:1px solid var(--edge);
    font:800 12px/1 "Inter",system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif;
    text-transform:uppercase;letter-spacing:.04em;color:#0b234c;background:#f8fafc;
  }
  .prog__title{margin:0;color:#0b234c;font-weight:900;font-size:18px}
  .prog__summary{margin:0;color:#374151}

  /* CALLOUT (degrees) */
  .callout{border:1px solid var(--callout-bd);background:var(--callout-bg);border-radius:14px;padding:14px}
  .callout__title{margin:0 0 8px;font-weight:900;color:#0b234c;font-size:12px;text-transform:uppercase;letter-spacing:.06em}
  .chips{margin:0;padding:0;list-style:none;display:flex;flex-wrap:wrap;gap:8px}
  .chips--grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
  @media (max-width:560px){ .chips--grid{grid-template-columns:1fr} }
  .chip{
    padding:8px 10px;border:1px solid var(--edge);border-radius:999px;background:#fff;color:#0b234c;
    font:700 12px/1 "Inter",system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif;letter-spacing:.02em;text-align:center;
  }
  .chip--em{border-color:var(--blue)}

  /* HIGHLIGHTS */
  .high__title{margin:0 0 6px;font-weight:900;color:#0b234c;font-size:14px}
  .high__list{margin:0;padding-left:18px;color:#374151}
  .high__list li{margin:6px 0}

  /* DOMAINS */
  .domains__title{margin:0 0 6px;font-weight:900;color:#0b234c;font-size:14px}
  .domains__grid{display:grid;gap:10px;grid-template-columns:repeat(3,1fr)}
  .dom{border:1px solid var(--edge);border-radius:12px;padding:12px;background:#fff}
  .dom h4{margin:0 0 4px;color:#0b234c;font-size:14px}
  .dom p{margin:0;color:#475569}
  @media (max-width:900px){ .domains__grid{grid-template-columns:1fr 1fr} }
  @media (max-width:560px){ .domains__grid{grid-template-columns:1fr} }

  /* NOTE */
  .note{border-left:4px solid var(--blue2);background:#f6fbff;border:1px solid #e6f0fb;border-left-color:var(--blue2);border-radius:10px;padding:10px 12px}
  .note--soft{border-left-color:#9cc7ff}

  /* FOOTER ACTIONS */
  .prog__footer{
    margin-top:auto;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    padding-top:10px;border-top:1px solid var(--edge);
  }
  .mini-links{display:flex;gap:10px;flex-wrap:wrap}
  .mini-links a{
    font:700 12px/1 "Inter",system-ui,-apple-system,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif;
    color:#0b234c;text-decoration:none;opacity:.9;
  }
  .mini-links a:hover{text-decoration:underline;opacity:1}
  .mini-links a + a::before{content:"•";margin:0 6px;color:#9aa3b2}

  /* CTA */
  .cta{background:var(--bg-alt);border-top:1px solid var(--edge);border-bottom:1px solid var(--edge)}
  .cta__inner{padding: clamp(28px,6vw,56px) 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
  .cta__inner h2{margin:0 0 6px;font-size:clamp(20px,3vw,28px);font-weight:900;color:#0b234c}
  .cta__inner p{margin:0;color:#4b5563}

  /* BUTTONS */
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 16px;border-radius:12px;font-weight:800;text-transform:uppercase;letter-spacing:.02em;text-decoration:none;border:2px solid transparent}
  .btn--solid{background:var(--green);color:#fff;border-color:var(--green)}
  .btn--outline-blue{background:transparent;color:var(--blue);border-color:var(--blue)}
  .btn--outline-blue:hover{background:#eef2ff}
</style>
