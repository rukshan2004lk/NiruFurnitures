/**
 * NiRu-Furnitures — navbar.js
 * Sticky scroll effects, mobile menu close-on-click,
 * active link highlighting.
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initStickyNavbar();
    initMobileMenu();
});

// ── Sticky Navbar Scroll Effect ───────────────────────────────
function initStickyNavbar() {
    const navbar = document.getElementById('mainNavbar');
    if (!navbar) return;

    const onScroll = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // Initial call
}

// ── Mobile: close navbar on link click ────────────────────────
function initMobileMenu() {
    const toggler = document.querySelector('.navbar-toggler');
    const menu    = document.getElementById('navMenu');
    if (!toggler || !menu) return;

    menu.querySelectorAll('.nav-link, .dropdown-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                const bsCollapse = bootstrap.Collapse.getInstance(menu);
                if (bsCollapse) bsCollapse.hide();
            }
        });
    });
}
