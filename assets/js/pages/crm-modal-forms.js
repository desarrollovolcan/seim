(() => {
    const fieldMap = {
        contact_name: 'contactName',
        contact_email: 'contactEmail',
        contact_phone: 'contactPhone',
        address: 'address',
        rut: 'rut',
        billing_email: 'billingEmail',
    };

    const fillClientFields = (select, force = false) => {
        const form = select.closest('form');
        if (!form) {
            return;
        }
        const option = select.options[select.selectedIndex];
        if (!option) {
            return;
        }
        Object.entries(fieldMap).forEach(([field, datasetKey]) => {
            const input = form.querySelector(`[data-client-field="${field}"]`);
            if (!input) {
                return;
            }
            if (!force && input.value) {
                return;
            }
            const value = option.dataset[datasetKey] || '';
            if (value) {
                input.value = value;
            } else if (!input.value) {
                input.value = '';
            }
        });
    };

    document.querySelectorAll('[data-client-select]').forEach((select) => {
        fillClientFields(select);
        select.addEventListener('change', () => fillClientFields(select, true));
    });
})();
