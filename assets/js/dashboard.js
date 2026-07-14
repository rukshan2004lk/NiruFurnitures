/**
 * NiRu-Furnitures — dashboard.js
 * User dashboard: order status chart, tab switching,
 * order timeline, profile preview.
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initOrderStatusChart();
    initProfileImagePreview();
    initOrderExpand();
});

// ── Order Status Doughnut Chart ───────────────────────────────
function initOrderStatusChart() {
    const canvas = document.getElementById('orderStatusChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const labels  = JSON.parse(canvas.dataset.labels  || '[]');
    const values  = JSON.parse(canvas.dataset.values  || '[]');

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    'hsl(38,92%,50%)',
                    'hsl(210,90%,52%)',
                    'hsl(280,70%,55%)',
                    'hsl(142,72%,38%)',
                    'hsl(0,72%,50%)',
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 8,
            }],
        },
        options: {
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        font: { family: 'Inter', size: 13 },
                    },
                },
            },
        },
    });
}

// ── Profile Image Preview ─────────────────────────────────────
function initProfileImagePreview() {
    const input   = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');

    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            window.showToast?.('Please select an image file.', 'error');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            window.showToast?.('File too large. Max 5 MB.', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => { preview.src = e.target.result; };
        reader.readAsDataURL(file);
    });
}

// ── Order Detail Expand/Collapse ──────────────────────────────
function initOrderExpand() {
    document.querySelectorAll('[data-order-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const orderId = btn.dataset.orderToggle;
            const detail  = document.getElementById('orderDetail_' + orderId);
            if (!detail) return;

            const isOpen = !detail.hidden;
            detail.hidden = isOpen;
            btn.querySelector('.toggle-icon').style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    });
}
