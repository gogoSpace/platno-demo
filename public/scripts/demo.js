const submittingForms = new WeakSet();
for (const form of document.querySelectorAll('form[action="/play"], form[action="/restart"]')) {
    form.addEventListener('submit', (event) => {
        if (submittingForms.has(form)) { event.preventDefault(); return; }
        submittingForms.add(form);
        const button = event.submitter;
        if (button) { button.setAttribute('aria-busy', 'true'); button.classList.add('is-loading'); }
    });
}
const expiryElement = document.querySelector('[data-expires-at]');
if (expiryElement) {
    const expiration = new Date(expiryElement.dataset.expiresAt);
    expiryElement.textContent = expiration.toLocaleString(undefined, {month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'});
}
