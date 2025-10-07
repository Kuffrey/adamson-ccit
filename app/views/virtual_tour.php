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
            src="https://panoraven.com/en/embed/D9fAkLEOv1" 
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