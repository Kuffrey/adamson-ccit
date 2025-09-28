// Student Profile JavaScript
document.addEventListener('DOMContentLoaded', function() {
  // View Toggle Functionality
  const viewButtons = document.querySelectorAll('.view-btn');
  const compactView = document.getElementById('compact-view');
  const cardView = document.getElementById('card-view');

  // Set initial view state
  let currentView = 'compact';
  
  // Initialize view
  if (compactView && cardView) {
    compactView.style.display = 'block';
    cardView.style.display = 'none';
    
    // Set active button
    viewButtons.forEach(btn => {
      if (btn.dataset.view === 'compact') {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });
  }

  viewButtons.forEach(button => {
    button.addEventListener('click', function() {
      const viewType = this.dataset.view;
      
      // Update active states
      viewButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      currentView = viewType;
      updateView();
    });
  });

  function updateView() {
    if (!compactView || !cardView) return;
    
    if (currentView === 'compact') {
      compactView.style.display = 'block';
      cardView.style.display = 'none';
    } else {
      compactView.style.display = 'none';
      cardView.style.display = 'grid';
    }
  }

  // Smooth scroll for navigation
  const navLinks = document.querySelectorAll('a[href^="#"]');
  navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Dynamic skills display
  const skillsContainer = document.querySelector('.skills-tags');
  if (skillsContainer) {
    const skills = skillsContainer.dataset.skills;
    if (skills) {
      const skillsArray = skills.split(',').map(skill => skill.trim()).filter(skill => skill);
      skillsContainer.innerHTML = skillsArray.map(skill => 
        `<span class="skill-tag">${skill}</span>`
      ).join('');
    }
  }

  // Form handling for certification actions
  const certForms = document.querySelectorAll('.inline-form');
  certForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      const action = this.querySelector('button').textContent.trim();
      if (action === 'Delete') {
        if (!confirm('Are you sure you want to delete this certification?')) {
          e.preventDefault();
        }
      }
    });
  });

  // Add loading states for actions
  const actionButtons = document.querySelectorAll('.inline-form button');
  actionButtons.forEach(button => {
    button.addEventListener('click', function() {
      const originalText = this.textContent;
      this.textContent = 'Processing...';
      this.disabled = true;
      
      // Re-enable after a delay (fallback)
      setTimeout(() => {
        this.textContent = originalText;
        this.disabled = false;
      }, 5000);
    });
  });

  // Status badge tooltips
  const statusBadges = document.querySelectorAll('.status-badge');
  statusBadges.forEach(badge => {
    const status = badge.textContent.toLowerCase().trim();
    let tooltip = '';
    
    switch(status) {
      case 'active':
        tooltip = 'This certification is currently valid';
        break;
      case 'expired':
        tooltip = 'This certification has expired and may need renewal';
        break;
      case 'permanent':
        tooltip = 'This certification does not expire';
        break;
    }
    
    if (tooltip) {
      badge.setAttribute('title', tooltip);
    }
  });

  // Enhanced mobile navigation
  if (window.innerWidth <= 768) {
    const profileHeader = document.querySelector('.profile-header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
      if (scrollTop > lastScrollTop && scrollTop > 100) {
        // Scrolling down - add compact class
        profileHeader?.classList.add('compact');
      } else {
        // Scrolling up - remove compact class
        profileHeader?.classList.remove('compact');
      }
      
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
  }

  // Statistics animation
  const statNumbers = document.querySelectorAll('.stat-number');
  statNumbers.forEach(stat => {
    const finalNumber = parseInt(stat.textContent);
    if (finalNumber > 0) {
      let currentNumber = 0;
      const increment = Math.ceil(finalNumber / 20);
      const timer = setInterval(() => {
        currentNumber += increment;
        if (currentNumber >= finalNumber) {
          currentNumber = finalNumber;
          clearInterval(timer);
        }
        stat.textContent = currentNumber;
      }, 50);
    }
  });

  // Handle expired certifications display
  const expiredItems = document.querySelectorAll('.cert-list-item.expired, .certification-card.expired');
  const showExpiredToggle = document.createElement('button');
  
  if (expiredItems.length > 0) {
    showExpiredToggle.textContent = 'Hide Expired';
    showExpiredToggle.className = 'action-link';
    showExpiredToggle.style.marginLeft = 'auto';
    
    const cardActions = document.querySelector('.card-actions');
    if (cardActions) {
      cardActions.appendChild(showExpiredToggle);
    }

    let showingExpired = true;
    showExpiredToggle.addEventListener('click', function() {
      showingExpired = !showingExpired;
      
      expiredItems.forEach(item => {
        item.style.display = showingExpired ? 'flex' : 'none';
      });
      
      this.textContent = showingExpired ? 'Hide Expired' : 'Show Expired';
    });
  }
});

// Utility functions
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function isExpired(expiryDate) {
  if (!expiryDate) return false;
  return new Date(expiryDate) < new Date();
}

function getStatusBadgeClass(status) {
  const statusMap = {
    'active': 'active',
    'expired': 'expired', 
    'permanent': 'permanent'
  };
  return statusMap[status.toLowerCase()] || 'active';
}

// Export for global use
window.StudentProfile = {
  formatDate,
  isExpired,
  getStatusBadgeClass
};