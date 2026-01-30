(() => {
    const storagePrefix = 'crm.form.';
    const fields = document.querySelectorAll('[data-crm-key]');

    fields.forEach((field) => {
        const key = field.dataset.crmKey;
        if (!key) {
            return;
        }

        const storedValue = sessionStorage.getItem(`${storagePrefix}${key}`);
        if (storedValue && !field.value) {
            field.value = storedValue;
        }

        const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
        field.addEventListener(eventName, () => {
            const value = field.value.trim();
            if (value) {
                sessionStorage.setItem(`${storagePrefix}${key}`, value);
            }
        });
    });
})();
