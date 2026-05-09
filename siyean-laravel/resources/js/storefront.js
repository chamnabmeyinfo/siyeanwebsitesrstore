// Storefront interactions: AJAX add-to-cart, mobile menu, account dropdown.
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

function setBadge(count) {
    document.querySelectorAll('[data-cart-badge]').forEach((el) => {
        el.textContent = count;
        el.classList.toggle('hidden', count <= 0);
    });
}

function flash(msg, isError = false) {
    const el = document.createElement('div');
    el.textContent = msg;
    el.className =
        'fixed top-20 right-4 z-50 px-4 py-2 rounded-md shadow-lg text-sm ' +
        (isError ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white');
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 2200);
}

document.addEventListener('submit', async (e) => {
    const form = e.target.closest('form[data-cart-add]');
    if (!form) return;
    e.preventDefault();
    const fd = new FormData(form);
    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.ok) {
            setBadge(data.count ?? 0);
            flash('🛒 ' + (form.dataset.addedMessage || 'Added to cart'));
        } else {
            flash(data.message || 'Could not add to cart.', true);
        }
    } catch {
        flash('Network error.', true);
    }
});

// Mobile nav toggle
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-mobile-toggle]')) {
        document.querySelector('[data-mobile-nav]')?.classList.toggle('hidden');
    }
    const accBtn = e.target.closest('[data-account-toggle]');
    if (accBtn) {
        accBtn.parentElement.querySelector('[data-account-dropdown]')?.classList.toggle('hidden');
        return;
    }
    if (!e.target.closest('[data-account-menu]')) {
        document.querySelectorAll('[data-account-dropdown]').forEach((d) => d.classList.add('hidden'));
    }
});
