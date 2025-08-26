<?php /* about_mission_vision.php — CCIT Mission & Vision with local About subnav; same clean theme */ ?>

<main>

  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">About CCIT</p>
      <h1 class="subhero__title">Vision &amp; Mission</h1>
      <p class="subhero__lead">Our purpose, our promise, and the departmental directions that guide CCIT.</p>
    </div>
  </section>

  <!-- ============ LOCAL SUBNAV (About section tabs) ============ -->
  <nav class="subnav" aria-label="About sub-navigation">
    <div class="container">
      <ul class="subnav__list" role="list">
        <li>
          <a href="/adamson-ccit/public/index.php?page=about_history">History</a>
        </li>
        <li class="is-active">
          <a href="/adamson-ccit/public/index.php?page=about_vision_mission" aria-current="page">Vision &amp; Mission</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- ============ CCIT MISSION & VISION (Top) ============ -->
  <section class="mv">
    <div class="container">
      <header class="sec__head">
        <h2>College of Computing &amp; Information Technology (CCIT)</h2>
        <p class="sec__kicker">College-wide mission and vision</p>
      </header>

      <div class="mv__grid">
        <article class="card">
          <h3 class="card__title">Vision</h3>
          <p>
            We are recognized globally for pioneering education in computing and information technologies,
            fostering innovation, and producing graduates who are leaders in the IT industry, capable of
            addressing the challenges and opportunities of a technology-focused society.
          </p>
        </article>

        <article class="card">
          <h3 class="card__title">Mission</h3>
          <p>
            As an academic unit, we provide an inclusive and dynamic learning environment that delivers
            specialized, industry-relevant curriculum led by highly qualified faculty.
          </p>
          <h4 class="card__sub">We are committed to:</h4>
          <ul class="bullets">
            <li>Prepare students for dynamic, in-demand careers through Vincentian education and hands-on experiences that adapt to technological changes;</li>
            <li>Bridge the digital skills gap by equipping students with cutting-edge knowledge and competencies in computing and advanced technologies;</li>
            <li>Enhance digitalization and innovation through rigorous research, publications, and collaborations with industry and academic partners;</li>
            <li>Strengthen our community by expanding our reach, fostering a strong alumni network, and maintaining robust industry linkages; and</li>
            <li>Support lifelong learning and professional growth through certification exams and continuous education opportunities.</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ DEPARTMENTS OVERVIEW ============ -->
  <section class="depts">
    <div class="container">
      <header class="sec__head">
        <h2>Departments</h2>
        <p class="sec__kicker">CCIT houses two departments that advance our mission.</p>
      </header>

      <div class="depts__grid">
        <!-- IT & IS Department -->
        <article class="dept card" id="it-is">
          <header class="dept__head">
            <h3 class="dept__title">Information Technology &amp; Information Systems</h3>
            <span class="dept__tag" aria-hidden="true">IT &amp; IS</span>
          </header>

          <div class="dept__body">
            <h4>Vision</h4>
            <p>
              A College dedicated in developing Christian professionals with solid foundation in the fields of Chemistry,
              Computer Science, Information Management, Information Technology, Mathematics, Natural Science, Psychology and Physics.
            </p>

            <h4>Mission</h4>
            <ul class="bullets">
              <li>To provide graduates with adequate knowledge and skills in their major field of specialization;</li>
              <li>To develop quality graduates who will be globally competitive in their chosen field;</li>
              <li>To prepare graduates for entry to industry, research and entrepreneurship.</li>
            </ul>

            <h4>Objectives</h4>
            <ul class="bullets">
              <li>To offer courses which will provide solid foundation in the Sciences particularly in Mathematics, Chemistry, Natural Sciences, Psychology, Computer Science, Information Management and Information Technology;</li>
              <li>To provide adequate coverage of major fields of specialization;</li>
              <li>To qualify a student for career in his chosen field of specialization as well as entry into advance studies in Sciences.</li>
            </ul>
          </div>

          <footer class="dept__foot">
            <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=programs_undergraduate">See Programs</a>
          </footer>
        </article>

        <!-- Computer Science Department -->
        <article class="dept card" id="compsci">
          <header class="dept__head">
            <h3 class="dept__title">Computer Science</h3>
            <span class="dept__tag" aria-hidden="true">CS</span>
          </header>

          <div class="dept__body">
            <p class="badge">Draft (for review &amp; finalization)</p>

            <h4>Vision</h4>
            <p>
              To be a nationally and regionally recognized center for excellence in Computer Science education and research,
              developing innovators who create impactful computing solutions for society.
            </p>

            <h4>Mission</h4>
            <p>
              To deliver a rigorous, research-informed curriculum grounded in algorithms, systems, and data,
              empowering students to design, build, and evaluate trustworthy software and intelligent systems
              with ethical and social responsibility.
            </p>

            <h4>Program Focus: BS in Computer Science</h4>
            <div class="cs__grid">
              <div>
                <h5>Why Computer Science?</h5>
                <ul class="bullets">
                  <li>Computing is part of everything we do;</li>
                  <li>Expertise in computing enables you to solve complex problems and make a positive impact;</li>
                  <li>Many diverse, high-growth, and creative career paths;</li>
                  <li>Opportunities for both collaborative and individual work.</li>
                </ul>
              </div>
              <div>
                <h5>What is Computer Science?</h5>
                <p>
                  The study of information and computer technology—hardware and software—including concepts and theories,
                  algorithmic foundations, implementation, and application of computing solutions.
                </p>
              </div>
            </div>

            <h4>Objectives</h4>
            <ul class="bullets">
              <li>Prepare students to be computer professionals and researchers;</li>
              <li>Develop proficiency in designing and developing robust computing solutions.</li>
            </ul>
          </div>

          <footer class="dept__foot">
            <a class="btn btn--outline-blue" href="/adamson-ccit/public/index.php?page=programs_undergraduate">Explore BSCS</a>
          </footer>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Discover Where CCIT Can Take You</h2>
        <p>Browse our programs and see how our mission becomes your pathway.</p>
      </div>
      <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=programs_undergraduate">View Programs</a>
    </div>
  </section>

</main>
