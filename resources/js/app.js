import { initRelativeTimes } from './relative-time';

document.addEventListener('DOMContentLoaded', () => {
    initRelativeTimes();
    initFetchForm();
});

function initFetchForm() {
    const form = document.getElementById('fetch-form');

    if (! form) {
        return;
    }

    form.addEventListener('submit', () => {
        if (form.dataset.submitting === 'true') {
            return;
        }

        form.dataset.submitting = 'true';

        const urlInput = form.querySelector('#fetch-url');
        urlInput?.setAttribute('readonly', 'readonly');
        urlInput?.classList.add('pointer-events-none');

        const button = form.querySelector('#fetch-submit');
        button?.setAttribute('disabled', 'disabled');

        form.setAttribute('aria-busy', 'true');

        form.querySelector('#fetch-submit-label')?.classList.add('hidden');

        const loading = form.querySelector('#fetch-submit-loading');
        loading?.classList.remove('hidden');
        loading?.classList.add('inline-flex');
    });
}
