/**
 * Customer account dashboard: section tabs + URL hash.
 */
(function () {
  const nav = document.getElementById('dashboard-nav');
  if (!nav) return;

  const navLinks = nav.querySelectorAll('.nav-link[data-section]');
  const sections = document.querySelectorAll('.dashboard-section');

  function showSection(sectionId) {
    if (!sectionId) sectionId = 'overview';

    navLinks.forEach((link) => {
      link.classList.toggle('active', link.getAttribute('data-section') === sectionId);
    });

    sections.forEach((section) => {
      const id = section.id.replace('section-', '');
      section.classList.toggle('d-none', id !== sectionId);
      section.classList.toggle('active', id === sectionId);
    });

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
      e.preventDefault();
      const section = link.getAttribute('data-section-link');
      const targetNav = nav.querySelector(`.nav-link[data-section="${section}"]`);
      if (targetNav) {
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
