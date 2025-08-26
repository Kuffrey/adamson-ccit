<?php /* student_certifications.php — Student Certifications (uses global styles) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Certifications | AdU-CCIT</title>

  <!-- Global site CSS -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/style.css"/>

  <!-- Page-specific polish -->
  <link rel="stylesheet" href="/adamson-ccit/public/assets/css/student-certifications.css"/>
</head>
<body>

<main class="page-certs">

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Students</p>
      <h1 class="subhero__title">Certifications</h1>
      <p class="subhero__lead">Industry badges aligned with CCIT courses and labs.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (Students) ============ -->
  <nav class="subnav" aria-label="Students sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li><a href="/adamson-ccit/public/index.php?page=student_organizations">Organizations</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_scholarships">Scholarships</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_research">Research</a></li>
        <li class="is-active"><a href="/adamson-ccit/public/index.php?page=student_certifications" aria-current="page">Certifications</a></li>
        <li><a href="/adamson-ccit/public/index.php?page=student_testimonials">Testimonials</a></li>
      </ul>
    </div>
  </nav>

  <!-- ============ LATEST PASSING RATES ============ -->
  <section class="content cstats section-sep" aria-labelledby="latest-rates">
    <div class="container">
      <div class="sec__head">
        <h2 id="latest-rates" class="h2">Recent Passing Rates</h2>
        <p class="sec__kicker">SY 2024–2025 • 2nd Semester</p>
      </div>

      <ul class="cstat__grid" role="list">
        <li class="cstat">
          <div class="cstat__rate">
            <span class="num">100%</span>
            <span class="tag">Passing rate</span>
          </div>
          <div class="cstat__body">
            <h3 class="cstat__title">IT Specialist — Cybersecurity</h3>
            <p class="cstat__meta">BS Computer Science</p>
          </div>
        </li>

        <li class="cstat">
          <div class="cstat__rate">
            <span class="num">99.53%</span>
            <span class="tag">Passing rate</span>
          </div>
          <div class="cstat__body">
            <h3 class="cstat__title">IT Specialist — Network Security</h3>
            <p class="cstat__meta">BS Information Technology &amp; BS Information Systems</p>
          </div>
        </li>

        <li class="cstat">
          <div class="cstat__rate">
            <span class="num">99.02%</span>
            <span class="tag">Passing rate</span>
          </div>
          <div class="cstat__body">
            <h3 class="cstat__title">IT Specialist — Networking</h3>
            <p class="cstat__meta">BS Computer Science</p>
          </div>
        </li>

        <li class="cstat">
          <div class="cstat__rate">
            <span class="num">96.11%</span>
            <span class="tag">Passing rate</span>
          </div>
          <div class="cstat__body">
            <h3 class="cstat__title">IT Specialist — Databases</h3>
            <p class="cstat__meta">BS Information Technology &amp; BS Information Systems</p>
          </div>
        </li>

        <li class="cstat">
          <div class="cstat__rate">
            <span class="num">95.62%</span>
            <span class="tag">Passing rate</span>
          </div>
          <div class="cstat__body">
            <h3 class="cstat__title">IT Specialist — Databases</h3>
            <p class="cstat__meta">BS Computer Science &amp; BSCS–BSIE (Dual)</p>
          </div>
        </li>
      </ul>

      <p class="cstat__note">If an exam isn’t listed here, its passing rate will be posted when available.</p>
    </div>
  </section>

  <!-- ============ AVAILABLE CERTIFICATIONS ============ -->
  <section class="content certs section-sep" aria-labelledby="available-certs">
    <div class="container">
      <div class="sec__head">
        <h2 id="available-certs" class="h2">Available Industry Certifications</h2>
        <p class="sec__kicker">Pearson IT Specialist series (via Certiport)</p>
      </div>

      <div class="certs__grid">
        <!-- Networking -->
        <article class="cert">
          <figure class="cert__badge">
            <img src="/adamson-ccit/public/assets/images/certs/its-networking.png" alt="IT Specialist - Networking badge">
          </figure>
          <div class="cert__body">
            <h3 class="cert__title">IT Specialist – Networking</h3>
            <p class="cert__issuer">Issued by Certiport</p>
            <p class="cert__desc">
              Foundational networking knowledge and skills: TCP/IP, networking services, topologies,
              and troubleshooting for wired and wireless environments.
            </p>
          </div>
        </article>

        <!-- Network Security -->
        <article class="cert">
          <figure class="cert__badge">
            <img src="/adamson-ccit/public/assets/images/certs/its-network-security.png" alt="IT Specialist - Network Security badge">
          </figure>
          <div class="cert__body">
            <h3 class="cert__title">IT Specialist – Network Security</h3>
            <p class="cert__issuer">Issued by Certiport</p>
            <p class="cert__desc">
              Core security principles; OS, network, and device security; secure computing practices.
            </p>
          </div>
        </article>

        <!-- Cybersecurity -->
        <article class="cert">
          <figure class="cert__badge">
            <img src="/adamson-ccit/public/assets/images/certs/its-cybersecurity.png" alt="IT Specialist - Cybersecurity badge">
          </figure>
          <div class="cert__body">
            <h3 class="cert__title">IT Specialist – Cybersecurity</h3>
            <p class="cert__issuer">Issued by Certiport</p>
            <p class="cert__desc">
              Baseline cybersecurity skills including threats, vulnerabilities, controls, and basic incident response.
            </p>
          </div>
        </article>

        <!-- Databases -->
        <article class="cert">
          <figure class="cert__badge">
            <img src="/adamson-ccit/public/assets/images/certs/its-databases.png" alt="IT Specialist - Databases badge">
          </figure>
          <div class="cert__body">
            <h3 class="cert__title">IT Specialist – Databases</h3>
            <p class="cert__issuer">Issued by Certiport</p>
            <p class="cert__desc">
              Designing and querying relational databases (e.g., MySQL, Microsoft SQL Server, Oracle).
            </p>
          </div>
        </article>
      </div>

      <div class="cnotice cnotice--inline" role="note">
        Certification windows &amp; registration are announced by the department through official CCIT channels and your instructors. Posts include dates, fees (if any), seat counts, and step-by-step registration.
      </div>
    </div>
  </section>

</main>

</body>
</html>
