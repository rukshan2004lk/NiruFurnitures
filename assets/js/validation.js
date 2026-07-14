/**
 * NiRu-Furnitures — validation.js
 * Client-side form validation for:
 *  - Registration / Login forms
 *  - Contact form
 *  - Checkout form
 *  - Admin product form
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initBootstrapValidation();
    initPasswordStrength();
    initPasswordToggle();
    initCharCounters();
});

// ── Bootstrap 5 Native Validation ────────────────────────────
function initBootstrapValidation() {
    document.querySelectorAll('form.needs-validation').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// ── Password Strength Meter ───────────────────────────────────
function initPasswordStrength() {
    const passwordInput = document.getElementById('passwordInput');
    const strengthBar   = document.getElementById('passwordStrengthBar');
    const strengthText  = document.getElementById('passwordStrengthText');

    if (!passwordInput || !strengthBar) return;

    passwordInput.addEventListener('input', () => {
        const val      = passwordInput.value;
        const strength = getStrength(val);

        strengthBar.style.width  = `${strength.pct}%`;
        strengthBar.className    = `progress-bar ${strength.cls}`;
        if (strengthText) strengthText.textContent = strength.label;
    });

    function getStrength(password) {
        let score = 0;
        if (password.length >= 8)               score++;
        if (/[A-Z]/.test(password))             score++;
        if (/[a-z]/.test(password))             score++;
        if (/[0-9]/.test(password))             score++;
        if (/[^A-Za-z0-9]/.test(password))      score++;

        const levels = [
            { pct: 0,   cls: '',                  label: '' },
            { pct: 20,  cls: 'bg-danger',          label: 'Very Weak' },
            { pct: 40,  cls: 'bg-warning',         label: 'Weak' },
            { pct: 60,  cls: 'bg-info',            label: 'Fair' },
            { pct: 80,  cls: 'bg-primary',         label: 'Strong' },
            { pct: 100, cls: 'bg-success',         label: 'Very Strong' },
        ];
        return levels[score] ?? levels[0];
    }
}

// ── Password Confirm Match ────────────────────────────────────
const confirmInput = document.getElementById('confirmPasswordInput');
const passwordInput2 = document.getElementById('passwordInput');

if (confirmInput && passwordInput2) {
    confirmInput.addEventListener('input', () => {
        if (confirmInput.value === passwordInput2.value) {
            confirmInput.setCustomValidity('');
        } else {
            confirmInput.setCustomValidity('Passwords do not match.');
        }
    });
}

// ── Password Toggle (show/hide) ───────────────────────────────
function initPasswordToggle() {
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        const targetId = btn.dataset.togglePassword;
        const field    = document.getElementById(targetId);
        const icon     = btn.querySelector('.bi');

        if (!field || !icon) return;

        btn.addEventListener('click', () => {
            const isHidden = field.type === 'password';
            field.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    });
}

// ── Character Counter for Textareas ──────────────────────────
function initCharCounters() {
    document.querySelectorAll('textarea[maxlength]').forEach(ta => {
        const max     = parseInt(ta.getAttribute('maxlength'));
        const counter = ta.parentElement.querySelector('.char-counter');
        if (!counter) return;

        const update = () => {
            const remaining = max - ta.value.length;
            counter.textContent = `${remaining} characters remaining`;
            counter.classList.toggle('text-danger', remaining < 20);
        };

        ta.addEventListener('input', update);
        update();
    });
}

// ── Real-time Email Availability Check ───────────────────────
const emailInput = document.getElementById('registerEmail');
if (emailInput) {
    let emailTimer = null;
    const feedback = document.createElement('div');
    feedback.className = 'form-text';
    emailInput.parentElement.appendChild(feedback);

    emailInput.addEventListener('input', () => {
        clearTimeout(emailTimer);
        const val = emailInput.value.trim();
        if (!val.includes('@')) return;

        emailTimer = setTimeout(async () => {
            try {
                const res  = await fetch(`api/customers.php?action=check_email&email=${encodeURIComponent(val)}`);
                const data = await res.json();

                if (data.exists) {
                    emailInput.setCustomValidity('Email already registered.');
                    feedback.textContent = '✗ This email is already in use.';
                    feedback.className = 'form-text text-danger';
                } else {
                    emailInput.setCustomValidity('');
                    feedback.textContent = '✓ Email is available.';
                    feedback.className = 'form-text text-success';
                }
            } catch {
                feedback.textContent = '';
            }
        }, 500);
    });
}
