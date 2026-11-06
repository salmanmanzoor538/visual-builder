document.querySelectorAll('[data-width]').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelector('[data-canvas]').style.width = button.dataset.width;
        document.querySelectorAll('[data-width]').forEach((item) => item.classList.toggle('active', item === button));
    });
});

document.querySelectorAll('[data-confirm]').forEach((button) => {
    button.addEventListener('click', (event) => {
        if (! window.confirm(button.dataset.confirm)) event.preventDefault();
    });
});

const editor = document.querySelector('[data-editor]');
if (editor) {
    const canvas = editor.querySelector('[data-canvas]');
    const search = editor.querySelector('[data-library-search]');
    let zoom = 1;

    editor.querySelectorAll('[data-library-tab]').forEach((tab) => tab.addEventListener('click', () => {
        editor.querySelectorAll('[data-library-tab]').forEach((item) => item.classList.toggle('active', item === tab));
        editor.querySelectorAll('[data-library-panel]').forEach((panel) => { panel.hidden = panel.dataset.libraryPanel !== tab.dataset.libraryTab; });
    }));

    search?.addEventListener('input', () => {
        const query = search.value.trim().toLowerCase();
        editor.querySelectorAll('[data-library-item]').forEach((item) => { item.hidden = ! item.dataset.libraryItem.includes(query); });
    });

    editor.querySelectorAll('[data-zoom]').forEach((button) => button.addEventListener('click', () => {
        zoom = Math.min(1.3, Math.max(.5, zoom + Number(button.dataset.zoom)));
        canvas.style.transform = `scale(${zoom})`;
        canvas.style.transformOrigin = 'top center';
        editor.querySelector('.zoom-control output').textContent = `${Math.round(zoom * 100)}%`;
    }));

    editor.querySelectorAll('[data-block-type]').forEach((tile) => tile.addEventListener('dragstart', (event) => {
        event.dataTransfer.setData('text/studioforge-form', tile.closest('form').action);
        event.dataTransfer.setData('text/studioforge-type', tile.dataset.blockType);
    }));
    canvas.addEventListener('dragover', (event) => { event.preventDefault(); canvas.classList.add('drop-ready'); });
    canvas.addEventListener('dragleave', () => canvas.classList.remove('drop-ready'));
    canvas.addEventListener('drop', (event) => {
        event.preventDefault(); canvas.classList.remove('drop-ready');
        const type = event.dataTransfer.getData('text/studioforge-type');
        const source = [...editor.querySelectorAll('[data-block-type]')].find((item) => item.dataset.blockType === type)?.closest('form');
        if (! source) return;
        const parent = source.querySelector('[name="parent_id"]');
        if (parent && ! parent.value) { parent.focus(); parent.setCustomValidity('Choose a parent before dragging this block.'); parent.reportValidity(); return; }
        source.requestSubmit();
    });

    editor.querySelectorAll('[data-autosave]').forEach((form) => {
        let timer;
        form.querySelectorAll('input,textarea,select').forEach((field) => field.addEventListener('input', () => {
            window.clearTimeout(timer);
            editor.querySelector('[data-save-state]').textContent = 'Unsaved changes';
            window.onbeforeunload = () => true;
            timer = window.setTimeout(() => { editor.querySelector('[data-save-state]').textContent = 'Saving…'; window.onbeforeunload = null; form.requestSubmit(); }, 900);
        }));
    });

    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
            event.preventDefault(); document.querySelector('form[action*="checkpoint"]')?.requestSubmit();
        }
    });
}
