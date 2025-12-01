// scripts.js

document.addEventListener('DOMContentLoaded', function() {
  // Mobile Navigation Toggle
  const hamburger = document.querySelector('.hamburger');
  const mainNav = document.querySelector('.main-nav');
  
  if (hamburger) {
    hamburger.addEventListener('click', function() {
      this.classList.toggle('active');
      mainNav.classList.toggle('active');
    });
  }

  // Close mobile menu when clicking outside
  document.addEventListener('click', function(event) {
    if (!event.target.closest('.main-nav') && !event.target.closest('.hamburger')) {
      hamburger.classList.remove('active');
      mainNav.classList.remove('active');
    }
  });

  // Animated counter for stats
  function animateCounter(counter, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        clearInterval(timer);
        current = target;
      }
      counter.textContent = Math.floor(current).toLocaleString();
    }, 16);
  }

  // Intersection Observer for counting animation
  const observerOptions = {
    threshold: 0.3,
    rootMargin: '0px 0px -100px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const counters = entry.target.querySelectorAll('.stat-number[data-count]');
        counters.forEach(counter => {
          const target = parseInt(counter.getAttribute('data-count'));
          if (!counter.classList.contains('counted')) {
            counter.classList.add('counted');
            animateCounter(counter, target);
          }
        });
      }
    });
  }, observerOptions);

  // Observe all sections that might contain counters
  const sectionsWithCounters = document.querySelectorAll(
    '.impact-summary, .impact-stats-section, .services-overview-section, .partners-intro'
  );
  sectionsWithCounters.forEach(section => {
    observer.observe(section);
  });

  // FAQ Accordion
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    question.addEventListener('click', () => {
      item.classList.toggle('active');
    });
  });

  // Form Submission Handling
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Simple validation
      const formData = new FormData(this);
      let isValid = true;
      
      this.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          field.style.borderColor = 'var(--danger)';
        } else {
          field.style.borderColor = '';
        }
      });
      
      if (isValid) {
        // Show success message
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Message Sent!';
        submitBtn.disabled = true;
        
        setTimeout(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
          this.reset();
        }, 3000);
      }
    });
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;
      
      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        const headerHeight = document.querySelector('.main-header').offsetHeight;
        const targetPosition = targetElement.offsetTop - headerHeight - 20;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
        
        // Close mobile menu if open
        if (hamburger.classList.contains('active')) {
          hamburger.classList.remove('active');
          mainNav.classList.remove('active');
        }
      }
    });
  });

  // Map interactions for impact page
  const mapPoints = document.querySelectorAll('.map-points .point');
  const mapRegions = document.querySelectorAll('.region');
  
  mapPoints.forEach(point => {
    point.addEventListener('mouseenter', function() {
      this.classList.add('active');
    });
    
    point.addEventListener('mouseleave', function() {
      this.classList.remove('active');
    });
    
    point.addEventListener('click', function() {
      const community = this.getAttribute('data-community');
      showCommunityInfo(community);
    });
  });
  
  mapRegions.forEach(region => {
    region.addEventListener('mouseenter', function() {
      this.style.transform = 'scale(1.05)';
      this.style.zIndex = '5';
    });
    
    region.addEventListener('mouseleave', function() {
      this.style.transform = 'scale(1)';
      this.style.zIndex = '1';
    });
    
    region.addEventListener('click', function() {
      const regionName = this.querySelector('span').textContent;
      showRegionInfo(regionName);
    });
  });

  function showCommunityInfo(community) {
    // In a real implementation, this would show detailed info
    // Showing community info (implement UI modal or details view here)
    // You could implement a modal or detailed view here
  }

  function showRegionInfo(region) {
    // In a real implementation, this would show regional info
    // Showing region info (implement UI modal or details view here)
    // You could implement a modal or detailed view here
  }

  // Add loading animation for images
  const images = document.querySelectorAll('img');
  images.forEach(img => {
    img.addEventListener('load', function() {
      this.style.opacity = '1';
    });
    img.style.opacity = '0';
    img.style.transition = 'opacity 0.3s ease';
  });

  // Parallax effect for hero section
  window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const parallax = document.querySelector('.hero-background');
    
    if (parallax) {
      parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
  });

  // Add active class to current section in navigation
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
  
  function highlightNavLink() {
    const scrollY = window.pageYOffset;
    
    sections.forEach(section => {
      const sectionHeight = section.offsetHeight;
      const sectionTop = section.offsetTop - 100;
      const sectionId = section.getAttribute('id');
      
      if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
        navLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === `#${sectionId}`) {
            link.classList.add('active');
          }
        });
      }
    });
  }
  
  window.addEventListener('scroll', highlightNavLink);

  // Initialize AOS (Animate On Scroll) if needed
  // You can add AOS library for more advanced animations
});

// Add some utility functions
function debounce(func, wait, immediate) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      timeout = null;
      if (!immediate) func(...args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func(...args);
  };
}

// Export for use in other modules if needed
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { debounce };
}



// Force images to reload
document.addEventListener('DOMContentLoaded', function() {
  const images = document.querySelectorAll('img');
  images.forEach(img => {
    // Add timestamp to bypass cache
    const src = img.src;
    if (src && !src.includes('?')) {
      img.src = src + '?t=' + new Date().getTime();
    }
  });
});
