// ============================================================
//  app.js — JavaScript customizado do site
//  Carregado após Alpine.js
// ============================================================

// Scroll suave para links âncora
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', e => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // Back-to-top button (aparece após scroll > 300px)
  const btn = document.createElement('button');
  btn.className = 'fixed bottom-6 left-6 z-40 w-10 h-10 rounded-full bg-slate-800 text-white shadow-lg opacity-0 transition-opacity hover:bg-slate-700';
  btn.innerHTML = '↑';
  btn.style.display = 'none';
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  document.body.appendChild(btn);

  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      btn.style.display = 'flex';
      btn.style.opacity = '1';
    } else {
      btn.style.opacity = '0';
      setTimeout(() => { if (window.scrollY <= 300) btn.style.display = 'none'; }, 300);
    }
  });
});
