<?php
// Virtual Tour Page
?>

<main>
  <!-- ============ SUB-HERO ============ -->
  <section class="subhero">
    <div class="subhero__media" aria-hidden="true">
      <img src="/adamson-ccit/public/assets/images/hero-campus.jpg" alt="Adamson University campus exterior">
    </div>
    <div class="subhero__scrim" aria-hidden="true"></div>
    <div class="container subhero__inner">
      <p class="eyebrow">Explore CCIT</p>
      <h1 class="subhero__title">360° Virtual Campus Tour</h1>
      <p class="subhero__lead">Take an immersive virtual tour of our state-of-the-art facilities and campus environment.</p>
    </div>
  </section>

  <!-- ============ VIRTUAL TOUR SECTION ============ -->
  <section class="virtual-tour">
    <div class="container">
      <header class="sec__head">
        <h2>Explore Our Campus</h2>
        <p class="sec__kicker">Navigate through our facilities using the interactive 360° tour below.</p>
      </header>

      <!-- 360 Tour Embed -->
      <div class="tour-embed">
        <div class="tour-wrapper">
          <iframe 
            src="https://panoraven.com/en/embed/SanFRa53N6" 
            width="100%" 
            height="600" 
            frameborder="0" 
            allowfullscreen
            loading="lazy"
            title="Adamson CCIT 360° Virtual Campus Tour">
          </iframe>
        </div>
      </div>

      <!-- Tour Instructions -->
      <div class="tour-instructions">
        <div class="instructions-grid">
          <div class="instruction-item">
            <div class="instruction-icon">
              <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            <div class="instruction-content">
              <h3>Click & Drag</h3>
              <p>Use your mouse or finger to look around in 360°</p>
            </div>
          </div>

          <div class="instruction-item">
            <div class="instruction-icon">
              <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                <path fill="currentColor" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
              </svg>
            </div>
            <div class="instruction-content">
              <h3>Zoom In/Out</h3>
              <p>Use scroll wheel or pinch gestures to get closer</p>
            </div>
          </div>

          <div class="instruction-item">
            <div class="instruction-icon">
              <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                <path fill="currentColor" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
            <div class="instruction-content">
              <h3>Hotspots</h3>
              <p>Click on interactive points to explore different areas</p>
            </div>
          </div>

          <div class="instruction-item">
            <div class="instruction-icon">
              <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
              </svg>
            </div>
            <div class="instruction-content">
              <h3>Full Screen</h3>
              <p>Use fullscreen mode for the best immersive experience</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FACILITIES HIGHLIGHTS ============ -->
  <section class="facilities-highlight">
    <div class="container">
      <header class="sec__head">
        <h2>What You'll See</h2>
        <p class="sec__kicker">Discover our world-class facilities and learning environments.</p>
      </header>

      <div class="facilities-grid">
        <div class="facility-card">
          <div class="facility-icon">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
              <path fill="currentColor" d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
            </svg>
          </div>
          <h3>Computer Laboratories</h3>
          <p>State-of-the-art computer labs equipped with the latest technology and software for hands-on learning.</p>
        </div>

        <div class="facility-card">
          <div class="facility-icon">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
              <path fill="currentColor" d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
            </svg>
          </div>
          <h3>Lecture Halls</h3>
          <p>Modern classrooms designed for interactive learning with multimedia capabilities and comfortable seating.</p>
        </div>

        <div class="facility-card">
          <div class="facility-icon">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
              <path fill="currentColor" d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
            </svg>
          </div>
          <h3>Library & Study Areas</h3>
          <p>Extensive library resources with quiet study spaces and collaborative work areas for research and learning.</p>
        </div>

        <div class="facility-card">
          <div class="facility-icon">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
              <path fill="currentColor" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <h3>Innovation Labs</h3>
          <p>Specialized laboratories for cutting-edge research in AI, cybersecurity, and emerging technologies.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta">
    <div class="container cta__inner">
      <div>
        <h2>Ready to Experience CCIT in Person?</h2>
        <p>Schedule a campus visit or apply to become part of our innovative community.</p>
      </div>
      <div class="cta__actions">
        <a class="btn btn--solid" href="/adamson-ccit/public/index.php?page=admission_freshman">Apply Now</a>
        <a class="btn btn--outline" href="/adamson-ccit/public/index.php?page=about_history">Learn More</a>
      </div>
    </div>
  </section>

</main>

<style>
/* Virtual Tour Specific Styles */
.virtual-tour {
  padding: 4rem 0;
  background: #f8fafc;
}

.tour-embed {
  margin: 2rem 0 3rem;
}

.tour-wrapper {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  background: #fff;
}

.tour-wrapper iframe {
  display: block;
  min-height: 600px;
  border: none;
}

.tour-instructions {
  margin-top: 3rem;
}

.instructions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.instruction-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.5rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.instruction-icon {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #003169, #00713D);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.instruction-content h3 {
  margin: 0 0 0.5rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

.instruction-content p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.4;
}

.facilities-highlight {
  padding: 4rem 0;
}

.facilities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.facility-card {
  padding: 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.facility-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.facility-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #003169, #00713D);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.facility-card h3 {
  margin: 0 0 1rem;
  font-size: 1.3rem;
  font-weight: 600;
  color: #1f2937;
}

.facility-card p {
  margin: 0;
  color: #6b7280;
  line-height: 1.6;
}

.cta__actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
  .tour-wrapper iframe {
    min-height: 400px;
  }
  
  .instructions-grid {
    grid-template-columns: 1fr;
  }
  
  .instruction-item {
    padding: 1rem;
  }
  
  .facilities-grid {
    grid-template-columns: 1fr;
  }
  
  .cta__actions {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .tour-wrapper iframe {
    min-height: 300px;
  }
}
</style>