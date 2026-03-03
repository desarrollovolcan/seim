<script>
(() => {
    const currentRoute = new URLSearchParams(window.location.search).get('route') || '';

    const toCsvCell = (value) => {
        const text = String(value ?? '').replace(/\s+/g, ' ').trim();
        if (/[",;\n]/.test(text)) {
            return `"${text.replace(/"/g, '""')}"`;
        }
        return text;
    };

    const tableToCsv = (table) => {
        const rows = Array.from(table.querySelectorAll('tr'));
        return rows
            .map((row) => Array.from(row.querySelectorAll('th,td')).map((cell) => toCsvCell(cell.innerText)).join(';'))
            .filter((line) => line !== '')
            .join('\n');
    };

    const exportTable = (table, filePrefix) => {
        const csv = tableToCsv(table);
        if (!csv) {
            return;
        }
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const timestamp = new Date().toISOString().replace(/[T:.]/g, '-').slice(0, 19);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${filePrefix}-${timestamp}.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    };

    const attachExportButtons = () => {
        const tables = Array.from(document.querySelectorAll('.table')).filter((table) => {
            const rows = table.querySelectorAll('tbody tr');
            return rows.length > 0;
        });

        tables.forEach((table, index) => {
            if (table.dataset.exportReady === '1') {
                return;
            }
            table.dataset.exportReady = '1';

            const wrapper = table.closest('.table-responsive, .card-body, .card');
            if (!wrapper) {
                return;
            }

            const toolbar = document.createElement('div');
            toolbar.className = 'd-flex justify-content-end mb-2';
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-success btn-sm';
            button.innerHTML = '<i data-lucide="file-spreadsheet" class="me-1" style="width:16px;height:16px"></i>Exportar Excel';
            button.addEventListener('click', () => exportTable(table, currentRoute.replace(/\//g, '-') || `listado-${index + 1}`));
            toolbar.appendChild(button);

            if (wrapper.firstChild) {
                wrapper.insertBefore(toolbar, wrapper.firstChild);
            } else {
                wrapper.appendChild(toolbar);
            }
        });
    };

    const shouldSkipForm = (form) => {
        const action = form.getAttribute('action') || '';
        const searchable = form.querySelectorAll('input,select,textarea').length < 4;
        return action.includes('delete') || action.includes('status') || searchable;
    };

    const attachFormPrintButtons = () => {
        const forms = Array.from(document.querySelectorAll('form[method="post"], form[method="POST"]'));
        forms.forEach((form) => {
            if (form.dataset.reportReady === '1' || shouldSkipForm(form)) {
                return;
            }
            form.dataset.reportReady = '1';

            if (!form.querySelector('input[name="report_source"]')) {
                const sourceInput = document.createElement('input');
                sourceInput.type = 'hidden';
                sourceInput.name = 'report_source';
                sourceInput.value = 'formulario';
                form.appendChild(sourceInput);
            }

            if (!form.querySelector('input[name="report_template"]')) {
                const templateInput = document.createElement('input');
                templateInput.type = 'hidden';
                templateInput.name = 'report_template';
                templateInput.value = currentRoute.includes('quotes') || currentRoute.includes('invoices')
                    ? 'informeIcargaInvoice.php'
                    : 'informeIcargaEspanol.php';
                form.appendChild(templateInput);
            }

            let csrf = form.querySelector('input[name="csrf_token"]');
            if (!csrf) {
                csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = 'csrf_token';
                csrf.value = '<?php echo e(csrf_token()); ?>';
                form.appendChild(csrf);
            }

            const actions = document.createElement('div');
            actions.className = 'd-flex flex-wrap gap-2 mt-3 report-actions-auto';

            const printBtn = document.createElement('button');
            printBtn.type = 'submit';
            printBtn.className = 'btn btn-primary';
            printBtn.setAttribute('formtarget', '_blank');
            printBtn.setAttribute('formmethod', 'post');
            printBtn.setAttribute('formaction', 'index.php?route=reports/print-form');
            printBtn.textContent = 'Imprimir informe';

            const pdfBtn = document.createElement('button');
            pdfBtn.type = 'submit';
            pdfBtn.className = 'btn btn-outline-primary';
            pdfBtn.setAttribute('formtarget', '_blank');
            pdfBtn.setAttribute('formmethod', 'post');
            pdfBtn.setAttribute('formaction', 'index.php?route=reports/download');
            pdfBtn.textContent = 'Descargar PDF';

            actions.appendChild(pdfBtn);
            actions.appendChild(printBtn);
            form.appendChild(actions);
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        attachExportButtons();
        attachFormPrintButtons();
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
})();
</script>
