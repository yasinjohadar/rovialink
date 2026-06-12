/**
 * Customer account dashboard: section tabs + URL hash + widget animations.
 */
(function () {
  const nav = document.getElementById('dashboard-nav');
  if (!nav) return;

  const navLinks = nav.querySelectorAll('a[data-section]');
  const sections = document.querySelectorAll('.dashboard-section');

  function animateCounter(counter) {
    if (counter.dataset.counted === '1') return;

    const finalValue = parseInt(counter.getAttribute('data-target'), 10);
    if (Number.isNaN(finalValue)) return;

    const duration = 2000;
    const startTime = performance.now();

    function updateCounter(currentTime) {
      const elapsedTime = currentTime - startTime;
      if (elapsedTime < duration) {
        const currentValue = Math.floor((elapsedTime / duration) * finalValue);
        counter.innerText = currentValue >= 1000
          ? (currentValue / 1000).toFixed(1) + 'K+'
          : currentValue;
        requestAnimationFrame(updateCounter);
      } else {
        counter.innerText = finalValue >= 1000
          ? (finalValue / 1000).toFixed(1) + 'K+'
          : finalValue;
        counter.dataset.counted = '1';
      }
    }

    requestAnimationFrame(updateCounter);
  }

  function refreshSectionAnimations(sectionEl) {
    if (!sectionEl) return;

    sectionEl.querySelectorAll('.section-fade-up').forEach((el) => {
      el.classList.remove('visible');
      requestAnimationFrame(() => {
        el.classList.add('visible');
      });
    });

    if (typeof window.observeFadeElements === 'function') {
      window.observeFadeElements(sectionEl);
    }

    const counterObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });

    sectionEl.querySelectorAll('.account-counter:not([data-counted])').forEach((counter) => {
      counterObserver.observe(counter);
    });
  }

  function showSection(sectionId) {
    if (!sectionId) sectionId = 'overview';

    navLinks.forEach((link) => {
      link.classList.toggle('active', link.getAttribute('data-section') === sectionId);
    });

    let activeSection = null;

    sections.forEach((section) => {
      const id = section.id.replace('section-', '');
      const isActive = id === sectionId;
      section.classList.toggle('d-none', !isActive);
      section.classList.toggle('active', isActive);
      if (isActive) activeSection = section;
    });

    if (activeSection) {
      refreshSectionAnimations(activeSection);
    }

    if (history.replaceState) {
      history.replaceState(null, '', '#' + sectionId);
    } else {
      window.location.hash = sectionId;
    }
  }

  navLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      showSection(link.getAttribute('data-section'));
    });
  });

  document.querySelectorAll('[data-section-link]').forEach((link) => {
    link.addEventListener('click', (e) => {
      const section = link.getAttribute('data-section-link');
      if (!section) return;

      const targetNav = nav.querySelector(`a[data-section="${section}"]`);
      if (targetNav) {
        e.preventDefault();
        showSection(section);
      }
    });
  });

  let initial = (window.location.hash || '').replace('#', '');
  if (!initial && window.location.search.includes('status=')) {
    initial = 'orders';
  }
  showSection(initial || 'overview');

  window.addEventListener('hashchange', () => {
    const hash = window.location.hash.replace('#', '');
    if (hash) showSection(hash);
  });
})();
