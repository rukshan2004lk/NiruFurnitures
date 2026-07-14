/**
 * NiRu-Furnitures — search.js
 * Live search dropdown in the navbar via AJAX.
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const input    = document.getElementById('liveSearch');
    const dropdown = document.getElementById('searchDropdown');

    if (!input || !dropdown) return;

    let debounceTimer = null;

    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = input.value.trim();

        if (query.length < 2) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(() => fetchResults(query), 280);
    });

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    // Keyboard navigation
    input.addEventListener('keydown', (e) => {
        const items = [...dropdown.querySelectorAll('.search-item')];
        const idx   = items.findIndex(el => el === document.activeElement);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            (items[idx + 1] || items[0])?.focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            (items[idx - 1] || items[items.length - 1])?.focus();
        } else if (e.key === 'Escape') {
            hideDropdown();
            input.blur();
        }
    });

    async function fetchResults(query) {
        dropdown.hidden = false;
        dropdown.innerHTML = '<div class="p-3 text-muted small">Searching…</div>';

        try {
            const res  = await fetch(`api/products.php?action=search&q=${encodeURIComponent(query)}&limit=6`);
            const data = await res.json();

            if (!data.products || data.products.length === 0) {
                dropdown.innerHTML = '<div class="p-3 text-muted small">No products found.</div>';
                return;
            }

            dropdown.innerHTML = data.products.map(p => `
                <a href="product.php?id=${p.id}" class="search-item" tabindex="0">
                    <img src="${p.image_url}" alt="${escHtml(p.name)}" loading="lazy">
                    <div class="flex-grow-1">
                        <div class="fw-medium text-dark small">${highlightMatch(escHtml(p.name), query)}</div>
                        <div class="text-muted" style="font-size:.75rem">${escHtml(p.category ?? '')}</div>
                    </div>
                    <div class="fw-bold text-nowrap" style="font-size:.875rem;color:#7c4b22">${p.formatted_price}</div>
                </a>`).join('') +
                `<a href="shop.php?q=${encodeURIComponent(query)}"
                    class="d-flex align-items-center gap-2 p-3 text-primary small fw-medium"
                    style="border-top:1px solid #f0ece6">
                    <i class="bi bi-search"></i>See all results for "<em>${escHtml(query)}</em>"
                </a>`;
        } catch {
            dropdown.innerHTML = '<div class="p-3 text-danger small">Search unavailable.</div>';
        }
    }

    function hideDropdown() {
        dropdown.hidden = true;
        dropdown.innerHTML = '';
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function highlightMatch(str, query) {
        const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi');
        return str.replace(re, '<mark class="bg-warning px-0">$1</mark>');
    }
});
