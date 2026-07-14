/**
 * NiRu-Furnitures — slider.js
 * Pure Vanilla JS hero slider with:
 *  - Auto-play with configurable interval
 *  - Prev / Next buttons
 *  - Dot indicators
 *  - Touch / swipe support
 *  - Pause on hover
 *  - Keyboard navigation
 */

'use strict';

class HeroSlider {
    /**
     * @param {string}  selector    CSS selector for the slides container
     * @param {object}  options
     * @param {number}  options.interval   Auto-advance interval in ms (default 5000)
     * @param {boolean} options.loop       Loop slides (default true)
     */
    constructor(selector, options = {}) {
        this.container = document.querySelector(selector);
        if (!this.container) return;

        this.slides   = [...this.container.querySelectorAll('.hero-slide')];
        this.dots     = [...document.querySelectorAll('.slider-dot')];
        this.prevBtn  = document.getElementById('sliderPrev');
        this.nextBtn  = document.getElementById('sliderNext');
        this.interval = options.interval ?? 5000;
        this.loop     = options.loop ?? true;

        this.current  = 0;
        this.timer    = null;

        this.init();
    }

    init() {
        if (this.slides.length === 0) return;

        this.goTo(0);

        this.prevBtn?.addEventListener('click', () => { this.prev(); this.resetTimer(); });
        this.nextBtn?.addEventListener('click', () => { this.next(); this.resetTimer(); });

        this.dots.forEach((dot, i) => {
            dot.addEventListener('click', () => { this.goTo(i); this.resetTimer(); });
        });

        // Pause on hover
        this.container.addEventListener('mouseenter', () => this.pause());
        this.container.addEventListener('mouseleave', () => this.startTimer());

        // Touch / swipe
        this.initSwipe();

        // Keyboard
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft')  { this.prev(); this.resetTimer(); }
            if (e.key === 'ArrowRight') { this.next(); this.resetTimer(); }
        });

        this.startTimer();
    }

    goTo(index) {
        if (index < 0) index = this.loop ? this.slides.length - 1 : 0;
        if (index >= this.slides.length) index = this.loop ? 0 : this.slides.length - 1;

        this.slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
            slide.setAttribute('aria-hidden', i !== index);
        });

        this.dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
            dot.setAttribute('aria-current', i === index ? 'true' : 'false');
        });

        this.current = index;
    }

    next() { this.goTo(this.current + 1); }
    prev() { this.goTo(this.current - 1); }

    startTimer() {
        if (this.slides.length <= 1) return;
        this.timer = setInterval(() => this.next(), this.interval);
    }

    pause() { clearInterval(this.timer); }

    resetTimer() {
        this.pause();
        this.startTimer();
    }

    initSwipe() {
        let startX = 0;

        this.container.addEventListener('touchstart', (e) => {
            startX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.container.addEventListener('touchend', (e) => {
            const diff = startX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 50) {
                diff > 0 ? this.next() : this.prev();
                this.resetTimer();
            }
        }, { passive: true });
    }
}

// Auto-init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.heroSlider = new HeroSlider('#heroSliderContainer', { interval: 5500 });
});
