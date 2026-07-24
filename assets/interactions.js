/* ============================================================
   Dan Creatives — shared interaction script
   Mobile nav (backdrop + icon swap), sticky-nav scroll shadow,
   and IntersectionObserver-based scroll reveal. Included on
   every public page.
   ============================================================ */
(function () {
    // Ensure a backdrop element exists even on pages that don't have one in markup
    let navBackdrop = document.getElementById('navBackdrop');
    if (!navBackdrop) {
        navBackdrop = document.createElement('div');
        navBackdrop.id = 'navBackdrop';
        navBackdrop.className = 'nav-backdrop';
        document.body.appendChild(navBackdrop);
    }

    const hamburger = document.getElementById('hamburgerBtn') || document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    function closeMenu() {
        if (!navMenu) return;
        navMenu.classList.remove('active');
        navBackdrop.classList.remove('active');
        if (hamburger) hamburger.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.style.overflow = '';
    }
    function toggleMenu() {
        if (!navMenu) return;
        const open = navMenu.classList.toggle('active');
        navBackdrop.classList.toggle('active', open);
        if (hamburger) hamburger.innerHTML = open ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        document.body.style.overflow = open ? 'hidden' : '';
    }
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', toggleMenu);
        navBackdrop.addEventListener('click', closeMenu);
        navMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
    }

    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 10);
        }, { passive: true });
    }

    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && revealEls.length) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealEls.forEach(el => io.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('in-view'));
    }

    // Optional animated counters: <span class="stat-number" data-count="500" data-suffix="+">
    const counters = document.querySelectorAll('[data-count]');
    function animateCounter(el) {
        const target = parseInt(el.dataset.count, 10) || 0;
        const suffix = el.dataset.suffix || '';
        const duration = 1200;
        const start = performance.now();
        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(target * eased).toLocaleString() + suffix;
            if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }
    if (counters.length) {
        if ('IntersectionObserver' in window) {
            const cIo = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { animateCounter(entry.target); cIo.unobserve(entry.target); }
                });
            }, { threshold: 0.5 });
            counters.forEach(c => cIo.observe(c));
        } else {
            counters.forEach(animateCounter);
        }
    }

    // Optional 3D tilt: add class "tilt-el" to any wrapper around an <img> or card
    document.querySelectorAll('.tilt-el').forEach(el => {
        if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
        const target = el.querySelector('img') || el;
        el.addEventListener('mousemove', (e) => {
            const rect = el.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            target.style.transform = `perspective(900px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg) translateY(-4px)`;
        });
        el.addEventListener('mouseleave', () => { target.style.transform = ''; });
    });
})();
