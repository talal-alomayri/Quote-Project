
// ─── Theme toggle ─────────────────────────────────────────────────────────────
// Persists the choice in localStorage under 'color-theme'.
// The initial class is applied by an inline <head> script to avoid FOUC.
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('theme-toggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
        });
    }
});

// ─── Singleton "Copied!" toast ────────────────────────────────────────────────
// One node reused for every copy action — avoids creating N nodes per page.
const toast = (() => {
    const el = document.createElement('div');
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', 'polite');
    el.textContent = 'Copied!';
    el.style.cssText = [
        'position:fixed',
        'bottom:1.75rem',
        'left:50%',
        'transform:translateX(-50%) translateY(1rem)',
        'background:var(--color-quote-accent, #6c63ff)',
        'color:#fff',
        'font-size:0.875rem',
        'font-weight:600',
        'padding:0.4rem 1.1rem',
        'border-radius:9999px',
        'box-shadow:0 4px 20px rgba(0,0,0,.25)',
        'opacity:0',
        'pointer-events:none',
        'transition:opacity 200ms ease, transform 200ms ease',
        'z-index:9999',
    ].join(';');
    document.body.appendChild(el);

    let hideTimer = null;

    return {
        show() {
            clearTimeout(hideTimer);
            el.style.opacity = '1';
            el.style.transform = 'translateX(-50%) translateY(0)';
            hideTimer = setTimeout(() => {
                el.style.opacity = '0';
                el.style.transform = 'translateX(-50%) translateY(1rem)';
            }, 2000);
        },
    };
})();

// ─── Copy quote to clipboard ──────────────────────────────────────────────────
// Delegated listener — works for every card on every page.
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-copy-quote]');
    if (!btn) return;

    const text = btn.dataset.copyQuote;
    if (!text) return;

    navigator.clipboard.writeText(text).then(() => {
        // Swap clipboard → checkmark icon briefly
        const iconCopy  = btn.querySelector('[data-icon-copy]');
        const iconCheck = btn.querySelector('[data-icon-check]');
        if (iconCopy && iconCheck) {
            iconCopy.classList.add('hidden');
            iconCheck.classList.remove('hidden');
            setTimeout(() => {
                iconCopy.classList.remove('hidden');
                iconCheck.classList.add('hidden');
            }, 2000);
        }
        toast.show();
    }).catch(() => {
        // Fallback for browsers that deny clipboard access
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;opacity:0;pointer-events:none';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        toast.show();
    });
});

// ─── Live search / filter ─────────────────────────────────────────────────────
// Works with any [data-quotes-grid] container that holds card wrappers.
// Each wrapper must contain a <blockquote data-quote-body="…" data-quote-author="…">.
document.addEventListener('input', (e) => {
    const input = e.target.closest('[data-quotes-search]');
    if (!input) return;

    const query = input.value.trim().toLowerCase();
    const gridId = input.dataset.quotesSearch;
    const grid = document.getElementById(gridId);
    if (!grid) return;

    // Each direct child of the grid is one card wrapper (or the empty-state div).
    const wrappers = Array.from(grid.children);
    let visibleCount = 0;

    wrappers.forEach((wrapper) => {
        // The empty-state element has data-empty-state — always keep it outside
        // the filter loop; we manage its visibility separately below.
        if (wrapper.dataset.emptyState !== undefined) return;

        const card = wrapper.querySelector('blockquote[data-quote-body]')
                  ?? wrapper; // quote-card IS the blockquote on my-quotes / liked-quotes

        const body   = (card.dataset.quoteBody   ?? '').toLowerCase();
        const author = (card.dataset.quoteAuthor  ?? '').toLowerCase();
        const match  = !query || body.includes(query) || author.includes(query);

        wrapper.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });

    // Show / hide the no-results message
    let noResults = grid.querySelector('[data-no-results]');
    if (!noResults) {
        noResults = document.createElement('div');
        noResults.dataset.noResults = '';
        noResults.className = 'col-span-full py-10 text-center text-quote-text-secondary-light dark:text-quote-text-secondary-dark';
        noResults.textContent = 'No quotes match your search.';
        grid.appendChild(noResults);
    }
    noResults.style.display = (query && visibleCount === 0) ? '' : 'none';
});
