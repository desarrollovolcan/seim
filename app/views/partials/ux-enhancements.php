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

    document.addEventListener('DOMContentLoaded', () => {
        attachExportButtons();
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
})();
</script>
