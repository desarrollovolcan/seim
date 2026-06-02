<style>
    .petty-cash-quick-table th,
    .petty-cash-quick-table td {
        padding: .35rem .45rem;
        vertical-align: middle;
    }

    .petty-cash-quick-table .form-control,
    .petty-cash-quick-table .btn {
        min-height: 32px;
    }

    .petty-cash-upload-control {
        min-width: 150px;
    }

    .petty-cash-upload-control .upload-file-name {
        max-width: 150px;
    }
</style>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Registro rápido de caja chica</h4>
            <p class="text-muted mb-0">Carga varias boletas en formato planilla: fecha, número, detalle, valor y respaldo tributario.</p>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=petty-cash/store" id="quickPettyCashForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="entry_mode" value="quick_table">

            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label">Moneda para las filas</label>
                    <select name="quick_currency" class="form-select">
                        <option value="CLP">CLP</option>
                        <option value="USD">USD</option>
                        <option value="PEN">PEN</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Observación general del lote</label>
                    <input type="text" name="quick_notes" class="form-control" placeholder="Ej: Rendición mayo, compras operacionales, colaciones, insumos...">
                </div>
            </div>

            <div class="alert alert-info d-flex align-items-start gap-2" role="alert">
                <span class="fw-bold">Tip:</span>
                <div>Puedes pegar desde Excel/Sheets columnas en este orden: <strong>Fecha, N° Boleta, Detalle, Valor</strong>. Luego adjunta el respaldo con el botón <strong>Subir</strong> de cada fila.</div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-2 small petty-cash-quick-table" id="quickEntriesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:4%">N°</th>
                            <th style="width:12%">Fecha</th>
                            <th style="width:12%">N° Boleta</th>
                            <th>Detalle</th>
                            <th style="width:13%">Valor</th>
                            <th style="width:13%">Proveedor</th>
                            <th style="width:17%">Respaldo</th>
                            <th class="text-center" style="width:4%"></th>
                        </tr>
                    </thead>
                    <tbody id="quickEntriesBody">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <tr class="quick-entry-row">
                                <td class="text-center fw-semibold row-number"><?php echo $i; ?></td>
                                <td><input type="date" name="quick_receipt_date[]" class="form-control form-control-sm py-1 quick-date" value="<?php echo e($today); ?>"></td>
                                <td><input type="text" name="quick_receipt_number[]" class="form-control form-control-sm py-1" placeholder="136009"></td>
                                <td><input type="text" name="quick_description[]" class="form-control form-control-sm py-1 quick-description" placeholder="Compra de insumos, colación, combustible..."></td>
                                <td><input type="text" name="quick_amount[]" class="form-control form-control-sm py-1 quick-amount text-end" inputmode="decimal" placeholder="8.300"></td>
                                <td><input type="text" name="quick_supplier_name[]" class="form-control form-control-sm py-1" placeholder="Caja chica"></td>
                                <td>
                                    <div class="petty-cash-upload-control d-flex align-items-center gap-2">
                                        <input type="file" name="quick_document[]" class="d-none quick-document" accept="application/pdf,image/jpeg,image/png,image/webp">
                                        <button type="button" class="btn btn-sm btn-soft-primary upload-trigger flex-shrink-0">Subir</button>
                                        <span class="upload-file-name small text-muted text-truncate">Sin archivo</span>
                                    </div>
                                </td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-quick-row" title="Eliminar fila">✕</button></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4" class="text-end">Total lote</th>
                            <th><input type="text" id="quickBatchTotal" class="form-control form-control-sm fw-bold text-end" value="$ 0" readonly></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="addQuickRowsBtn">Agregar 5 filas</button>
                    <button type="button" class="btn btn-outline-info" id="pasteClipboardBtn">Pegar portapapeles</button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light" id="clearQuickRowsBtn">Limpiar filas vacías</button>
                    <button type="submit" class="btn btn-success">Guardar registros rápidos</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const numberFormatter = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 });

    function parseAmount(value) {
        value = String(value || '').replace(/[^0-9,.-]/g, '');
        if (value.includes(',') && value.includes('.')) value = value.replace(/\./g, '').replace(',', '.');
        else if (value.includes(',')) value = value.replace(',', '.');
        else if (/^\d{1,3}(\.\d{3})+$/.test(value)) value = value.replace(/\./g, '');
        return Math.max(0, parseFloat(value || '0') || 0);
    }

    const quickBody = document.getElementById('quickEntriesBody');
    const quickTotal = document.getElementById('quickBatchTotal');
    const quickTemplate = quickBody.querySelector('.quick-entry-row').cloneNode(true);

    function refreshQuickRows() {
        let total = 0;
        quickBody.querySelectorAll('.quick-entry-row').forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
            total += parseAmount(row.querySelector('.quick-amount').value);
        });
        quickTotal.value = numberFormatter.format(total);
    }

    function normalizeDate(value) {
        value = String(value || '').trim();
        let match = value.match(/^(\d{2})[.\/-](\d{2})[.\/-](\d{4})$/);
        if (match) return `${match[3]}-${match[2]}-${match[1]}`;
        match = value.match(/^(\d{4})[.\/-](\d{2})[.\/-](\d{2})$/);
        if (match) return `${match[1]}-${match[2]}-${match[3]}`;
        return value;
    }

    function splitClipboardRows(text) {
        return String(text || '')
            .trim()
            .split(/\r?\n/)
            .map((line) => line.split('\t').map((col) => col.trim()))
            .filter((cols) => cols.some((col) => col !== ''));
    }

    function looksLikeHeader(cols) {
        const joined = cols.join(' ').toLowerCase();
        return joined.includes('fecha') && (joined.includes('boleta') || joined.includes('detalle') || joined.includes('valor'));
    }

    function mapClipboardColumns(cols) {
        if (looksLikeHeader(cols)) return null;

        let offset = 0;
        if (cols.length >= 5 && /^n[°º]?$/i.test(cols[0])) return null;
        if (cols.length >= 5 && /^\d+$/.test(cols[0]) && /\d{2}[.\/-]\d{2}[.\/-]\d{4}|\d{4}[.\/-]\d{2}[.\/-]\d{2}/.test(cols[1] || '')) {
            offset = 1;
        }

        return {
            date: normalizeDate(cols[offset] || ''),
            number: cols[offset + 1] || '',
            description: cols[offset + 2] || '',
            amount: cols[offset + 3] || '',
            supplier: cols[offset + 4] || '',
        };
    }

    function fillQuickRow(row, data) {
        row.querySelector('.quick-date').value = data.date || '<?php echo e($today); ?>';
        row.querySelector('[name="quick_receipt_number[]"]').value = data.number || '';
        row.querySelector('.quick-description').value = data.description || '';
        row.querySelector('.quick-amount').value = data.amount || '';
        row.querySelector('[name="quick_supplier_name[]"]').value = data.supplier || '';
    }

    function pasteQuickEntries(text, startRow) {
        const rows = splitClipboardRows(text).map(mapClipboardColumns).filter(Boolean);
        if (!rows.length) return false;

        let current = startRow || quickBody.querySelector('.quick-entry-row');
        rows.forEach((data, offset) => {
            if (offset > 0) current = addQuickRow();
            fillQuickRow(current, data);
        });
        refreshQuickRows();
        return true;
    }

    function updateDocumentName(row) {
        const fileInput = row.querySelector('.quick-document');
        const fileName = row.querySelector('.upload-file-name');
        fileName.textContent = fileInput.files && fileInput.files.length ? fileInput.files[0].name : 'Sin archivo';
        fileName.classList.toggle('text-muted', !(fileInput.files && fileInput.files.length));
        fileName.classList.toggle('text-success', !!(fileInput.files && fileInput.files.length));
    }

    function bindQuickRow(row) {
        const fileInput = row.querySelector('.quick-document');

        row.querySelector('.quick-amount').addEventListener('input', refreshQuickRows);
        row.querySelector('.upload-trigger').addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => updateDocumentName(row));
        row.querySelector('.remove-quick-row').addEventListener('click', () => {
            if (quickBody.querySelectorAll('.quick-entry-row').length === 1) return;
            row.remove();
            refreshQuickRows();
        });
    }

    function addQuickRow() {
        const clone = quickTemplate.cloneNode(true);
        clone.querySelectorAll('input').forEach((input) => {
            if (input.type === 'date') input.value = '<?php echo e($today); ?>';
            else input.value = '';
        });
        clone.querySelector('.upload-file-name').textContent = 'Sin archivo';
        clone.querySelector('.upload-file-name').classList.add('text-muted');
        clone.querySelector('.upload-file-name').classList.remove('text-success');
        quickBody.appendChild(clone);
        bindQuickRow(clone);
        refreshQuickRows();
        return clone;
    }

    quickBody.addEventListener('paste', (event) => {
        const text = event.clipboardData ? event.clipboardData.getData('text') : '';
        if (!text.includes('\t') && !text.includes('\n')) return;
        const startRow = event.target.closest('.quick-entry-row');
        if (pasteQuickEntries(text, startRow)) event.preventDefault();
    });

    document.getElementById('pasteClipboardBtn').addEventListener('click', async () => {
        if (!navigator.clipboard || !navigator.clipboard.readText) {
            alert('Tu navegador no permite leer el portapapeles automáticamente. Usa Ctrl+V dentro de cualquier celda de la tabla.');
            return;
        }

        let text = '';
        try {
            text = await navigator.clipboard.readText();
        } catch (error) {
            alert('No se pudo leer el portapapeles. Haz clic en una celda de la tabla y usa Ctrl+V.');
            return;
        }

        if (!pasteQuickEntries(text, quickBody.querySelector('.quick-entry-row'))) {
            alert('No se detectaron columnas válidas. Usa: Fecha, N° Boleta, Detalle, Valor. También se acepta N°, Fecha, N° Boleta, Detalle, Valor.');
        }
    });

    document.getElementById('addQuickRowsBtn').addEventListener('click', () => {
        for (let i = 0; i < 5; i++) addQuickRow();
    });
    document.getElementById('clearQuickRowsBtn').addEventListener('click', () => {
        quickBody.querySelectorAll('.quick-entry-row').forEach((row) => {
            const hasData = Array.from(row.querySelectorAll('input')).some((input) => input.type !== 'date' && input.type !== 'file' && input.value.trim() !== '');
            if (!hasData && quickBody.querySelectorAll('.quick-entry-row').length > 1) row.remove();
        });
        refreshQuickRows();
    });
    quickBody.querySelectorAll('.quick-entry-row').forEach(bindQuickRow);
    refreshQuickRows();

})();
</script>
