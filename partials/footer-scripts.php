<!-- Vendor js -->
<script src="assets/js/vendors.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

<script>
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }
        const message = form.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });

    const deleteModal = document.getElementById('confirmDeleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) {
                return;
            }
            const recordId = trigger.getAttribute('data-record-id') || '';
            const recordLabel = trigger.getAttribute('data-record-label') || 'este registro';
            const idInput = deleteModal.querySelector('#deleteRecordId');
            if (idInput) {
                idInput.value = recordId;
            }
            const labelTarget = deleteModal.querySelector('[data-delete-label]');
            if (labelTarget) {
                labelTarget.textContent = recordLabel;
            }
        });
    }

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js');
        });
    }

    let deferredInstallPrompt = null;
    const installButton = document.getElementById('installAppButton');

    const hideInstallButton = () => {
        if (installButton) {
            installButton.classList.add('d-none');
        }
    };

    const showInstallButton = () => {
        if (installButton) {
            installButton.classList.remove('d-none');
        }
    };

    const isStandaloneMode = () =>
        window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    if (installButton) {
        if (!isStandaloneMode()) {
            showInstallButton();
        } else {
            hideInstallButton();
        }

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredInstallPrompt = event;
            showInstallButton();
        });

        installButton.addEventListener('click', async () => {
            if (!deferredInstallPrompt) {
                alert('Para instalar la app, usa la opción \"Agregar a inicio\" en el menú de tu navegador.');
                return;
            }
            deferredInstallPrompt.prompt();
            await deferredInstallPrompt.userChoice;
            deferredInstallPrompt = null;
            hideInstallButton();
        });

        window.addEventListener('appinstalled', () => {
            deferredInstallPrompt = null;
            hideInstallButton();
        });
    }
</script>
