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
</script>
