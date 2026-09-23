const settings = JSON.parse(document.querySelector('#studio-settings').textContent);
const feedback = document.querySelector('#workspace-feedback');
let documentDirty = false;
const showPublished = (published) => {
    feedback.replaceChildren();
    if (!published) return;
    feedback.append('Your page is published. ');
    const link = document.createElement('a');
    link.href = settings.shareAddress;
    link.target = '_blank';
    link.rel = 'noopener';
    link.textContent = 'Open your page ↗';
    feedback.append(link);
};
const onState = (page) => { if (page) { documentDirty = false; showPublished(Boolean(page.publication_id)); } };
const onChange = (change) => { documentDirty = Boolean(change.dirty); };
const onError = (error) => { feedback.textContent = error.message || 'The editor could not complete that action. Your changes remain in the editor.'; };
showPublished(settings.published);
const mountPoint = document.querySelector('#workspace');
try {
    const options = {editorAddress: settings.editorAddress, moduleAddress: settings.moduleAddress, onState, onChange, onError};
    if (settings.adapter === 'vue') {
        const [{createApp, h}, {PlatnoEditor}] = await Promise.all([import('vue'), import('/adapters/vue.js')]);
        mountPoint.replaceChildren();
        createApp({render: () => h(PlatnoEditor, options)}).mount(mountPoint);
    } else if (settings.adapter === 'react') {
        const [{createElement}, {createRoot}, {PlatnoEditor}] = await Promise.all([import('react'), import('react-dom/client'), import('/adapters/react.js')]);
        mountPoint.replaceChildren();
        createRoot(mountPoint).render(createElement(PlatnoEditor, options));
    } else if (settings.adapter === 'livewire') {
        document.addEventListener('platno:state', (event) => onState(event.detail));
        document.addEventListener('platno:change', (event) => onChange(event.detail));
        document.addEventListener('platno:error', (event) => onError(event.detail));
    } else {
        const {mountWorkspace} = await import(settings.moduleAddress);
        mountPoint.replaceChildren();
        await mountWorkspace(mountPoint, options).ready;
    }
} catch (error) {
    if (mountPoint) {
        const message = document.createElement('p');
        message.className = 'workspace-loading-error';
        message.textContent = 'The editor could not load. Reload the page to try again, or open the standalone editor.';
        const link = document.createElement('a');
        link.href = settings.editorAddress;
        link.textContent = 'Open standalone editor ↗';
        link.className = 'button';
        mountPoint.replaceChildren(message, link);
    }
    onError(error);
}
document.querySelector('#adapter').addEventListener('change', (event) => {
    if (documentDirty && !window.confirm('You have unsaved changes. Switch integration and discard them?')) {
        event.target.value = settings.adapter;
        return;
    }
    const address = new URL(location.href);
    address.searchParams.set('adapter', event.target.value);
    location.assign(address);
});
const restartDialog = document.querySelector('#restart-dialog');
document.querySelector('#restart-demo').addEventListener('click', () => restartDialog.showModal());
restartDialog.querySelector('[data-close-dialog]').addEventListener('click', () => restartDialog.close());
const expiresAt = Date.parse(settings.expiresAt);
let expirationAnnounced = false;
const checkExpiry = () => {
    if (!expirationAnnounced && Date.now() >= expiresAt) {
        expirationAnnounced = true;
        feedback.textContent = 'Your demo has expired. Its saved pages and files are no longer available. Start a new demo when you are ready.';
        feedback.setAttribute('role', 'alert');
    }
};
checkExpiry();
setInterval(checkExpiry, 30000);
