// ============================================================
// DEALER MITSUBISHI — main.js
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  // Year
  const yr = document.getElementById('year');
  if (yr) yr.textContent = new Date().getFullYear();

  // ---- LOADING SCREEN ----
  const loading = document.getElementById('loadingScreen');
  if (loading) {
    setTimeout(() => loading.classList.add('hide'), 800);
    setTimeout(() => loading.remove(), 1500);
  }

  // ---- NAVBAR SCROLL ----
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
  }

  // ---- MOBILE TOGGLE ----
  const toggle = document.getElementById('mobileToggle');
  const menu   = document.getElementById('navMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      menu.classList.toggle('active');
      toggle.innerHTML = menu.classList.contains('active')
        ? '<i class="fa-solid fa-xmark"></i>'
        : '<i class="fa-solid fa-bars"></i>';
    });
    document.addEventListener('click', e => {
      if (!navbar.contains(e.target)) menu.classList.remove('active');
    });
  }

  // ---- BACK TO TOP ----
  const btt = document.getElementById('backToTop');
  if (btt) {
    window.addEventListener('scroll', () => {
      btt.classList.toggle('visible', window.scrollY > 400);
    });
    btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  // ---- SCROLL ANIMATIONS ----
  initScrollAnimations();

  // ---- COUNTER ----
  initCounters();

});

// ---- Scroll Animations ----
function initScrollAnimations() {
  const els = document.querySelectorAll('.animate-on-scroll');
  if (!els.length) return;
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('animated'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.1 });
  els.forEach(el => obs.observe(el));
}

// ---- Counters ----
function initCounters() {
  const counters = document.querySelectorAll('.counter');
  if (!counters.length) return;
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const el     = e.target;
        const target = parseInt(el.dataset.target || '0');
        const dur    = 2000;
        const step   = target / (dur / 16);
        let cur = 0;
        const timer = setInterval(() => {
          cur += step;
          if (cur >= target) { el.textContent = target; clearInterval(timer); }
          else el.textContent = Math.floor(cur);
        }, 16);
        obs.unobserve(el);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => obs.observe(c));
}

// ---- Format Rupiah ----
function formatRupiah(n) {
  return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

// ---- Alert Modal ----
function showAlert(type, title, message, callback) {
  const old = document.getElementById('sweetModal');
  if (old) old.remove();

  const icons = {
    success: { icon: 'fa-circle-check',        color: '#16a34a', bg: 'rgba(22,163,74,.1)' },
    error:   { icon: 'fa-circle-xmark',         color: '#dc2626', bg: 'rgba(220,38,38,.1)' },
    warning: { icon: 'fa-triangle-exclamation', color: '#d97706', bg: 'rgba(217,119,6,.1)' },
    info:    { icon: 'fa-circle-info',           color: '#2563eb', bg: 'rgba(37,99,235,.1)' }
  };
  const cfg = icons[type] || icons.info;

  const modal = document.createElement('div');
  modal.id = 'sweetModal';
  modal.innerHTML = `
    <div class="sweet-overlay" onclick="closeSweetModal()"></div>
    <div class="sweet-box">
      <div class="sweet-icon" style="background:${cfg.bg};color:${cfg.color};">
        <i class="fa-solid ${cfg.icon}"></i>
      </div>
      <h3 class="sweet-title">${title}</h3>
      <p class="sweet-msg">${message}</p>
      <button class="sweet-btn" onclick="closeSweetModal()" style="background:${cfg.color};">OK</button>
    </div>`;
  document.body.appendChild(modal);
  window._sweetCallback = callback || null;
  requestAnimationFrame(() => modal.querySelector('.sweet-box').classList.add('visible'));
}

window.closeSweetModal = function() {
  const modal = document.getElementById('sweetModal');
  if (modal) {
    modal.querySelector('.sweet-box').classList.remove('visible');
    setTimeout(() => {
      modal.remove();
      if (window._sweetCallback) { window._sweetCallback(); window._sweetCallback = null; }
    }, 300);
  }
};
