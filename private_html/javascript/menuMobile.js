const mobileMenuBtn = document.querySelector('.menu-toggle');
const mobileMenu = document.querySelector('.mobile-nav');
const mobileLinks = document.querySelectorAll('.mobile-nav a');
const siteHeader = document.querySelector('.site-header');
const hero = document.querySelector('.hero');

const atualizarNavbar = () => {
    const estaNoTopo = window.scrollY <= 24;

    if (siteHeader) {
        siteHeader.classList.toggle('navbar-visivel', !estaNoTopo);
    }

    if (estaNoTopo && mobileMenu && mobileMenuBtn) {
        mobileMenu.classList.remove('open');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
    }
};

const atualizarZoomHero = () => {
    if (!hero) {
        return;
    }

    const limite = Math.max(hero.offsetHeight, 1);
    const progresso = Math.min(Math.max(window.scrollY / limite, 0), 1);
    const zoom = 1 + progresso * 0.28;
    hero.style.setProperty('--zoom-hero', zoom.toFixed(3));
};

const atualizarPagina = () => {
    atualizarNavbar();
    atualizarZoomHero();
};

window.addEventListener('scroll', atualizarPagina, { passive: true });
atualizarPagina();

if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        mobileMenuBtn.setAttribute('aria-expanded', String(isOpen));
        mobileMenu.setAttribute('aria-hidden', String(!isOpen));
    });

    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            mobileMenu.setAttribute('aria-hidden', 'true');
        });
    });
}

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});