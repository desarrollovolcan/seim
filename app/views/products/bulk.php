<div class="row g-3">
    <!-- Columna Izquierda: Formulario de Carga y Configuración -->
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center bg-white border-bottom">
                <span class="fw-semibold text-dark fs-13"><i class="ti ti-file-upload me-1 text-primary"></i> Asistente de Importación Masiva</span>
                <span class="badge bg-primary-subtle text-primary">CSV / Excel</span>
            </div>
            <div class="card-body p-3">
                <form method="post" action="index.php?route=products/bulk-start" enctype="multipart/form-data" id="bulkUploadForm">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="row g-3">
                        <!-- Empresa competencia asociada -->
                        <div class="col-md-6">
                            <label class="form-label fs-12 mb-1 fw-semibold">Empresa de referencia (Competencia) <span class="text-danger">*</span></label>
                            <select name="default_competitor_company_id" id="competitor-company-select" class="form-select form-select-sm" required>
                                <option value="">Seleccionar empresa...</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo (int)$company['id']; ?>" <?php echo ((int)($company['id'] ?? 0) === (int)($_SESSION['company_id'] ?? 0)) ? 'selected' : ''; ?>>
                                        <?php echo e($company['name'] ?? ''); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text fs-11 text-muted">Afecta los códigos correlativos de los productos importados.</div>
                        </div>

                        <!-- Modo de Importación / Estrategia de duplicados -->
                        <div class="col-md-6">
                            <label class="form-label fs-12 mb-1 fw-semibold">Comportamiento con SKU existente</label>
                            <select name="import_mode" class="form-select form-select-sm">
                                <option value="upsert" selected>Actualizar datos (Recomendado)</option>
                                <option value="create_only">Omitir (No sobrescribir existentes)</option>
                                <option value="always_new">Forzar creación siempre</option>
                            </select>
                            <div class="form-text fs-11 text-muted">Si un SKU ya está en la base de datos.</div>
                        </div>

                        <!-- Dropzone de archivo -->
                        <div class="col-12">
                            <label class="form-label fs-12 mb-1 fw-semibold">Archivo de productos (.CSV o .XLSX) <span class="text-danger">*</span></label>
                            <div id="drop-zone" class="border border-2 border-dashed rounded p-4 text-center bg-light cursor-pointer position-relative" style="transition: all 0.2s ease;">
                                <input type="file" name="bulk_file" id="bulk-file-input" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                <div id="drop-zone-prompt">
                                    <i class="ti ti-cloud-upload text-primary fs-1 mb-2 d-block"></i>
                                    <p class="mb-1 fw-semibold text-dark fs-13">Arrastra tu archivo aquí o haz clic para examinar</p>
                                    <p class="text-muted fs-11 mb-0">Formatos admitidos: <strong>CSV UTF-8</strong> (separado por comas o punto y coma) o <strong>Excel (.xlsx)</strong></p>
                                </div>
                                <div id="drop-zone-file-info" class="d-none">
                                    <i class="ti ti-file-spreadsheet text-success fs-1 mb-1 d-block"></i>
                                    <div class="fw-bold text-dark fs-13" id="file-name-display">nombre_archivo.csv</div>
                                    <div class="text-muted fs-11" id="file-size-display">0 KB</div>
                                    <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1 fs-11" id="btn-remove-file">Cambiar archivo</button>
                                </div>
                            </div>
                        </div>

                        <!-- Botón de procesamiento -->
                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php?route=products" class="btn btn-sm btn-light px-3">
                                    <i class="ti ti-arrow-left me-1"></i> Volver al Catálogo
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold" id="btn-submit-bulk">
                                    <i class="ti ti-player-play me-1"></i> Iniciar Procesamiento
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tarjeta: Previsualización de Datos en Vivo -->
        <div class="card mb-3 d-none" id="preview-card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center bg-white border-bottom">
                <span class="fw-semibold text-dark fs-13"><i class="ti ti-eye me-1 text-info"></i> Vista Previa del Archivo (Primeras 5 Filas)</span>
                <span id="preview-row-count-badge" class="badge bg-info-subtle text-info fs-11">0 filas detectadas</span>
            </div>
            <div class="card-body p-2">
                <div id="preview-validation-alerts" class="mb-2"></div>
                <div class="table-responsive" style="max-height: 240px;">
                    <table class="table table-sm table-hover table-bordered fs-11 mb-0 align-middle" id="preview-table">
                        <thead class="table-light text-nowrap"></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Guía de Columnas y Descargas -->
    <div class="col-lg-5">
        <!-- Tarjeta: Descarga de Plantilla -->
        <div class="card mb-3 border-success-subtle bg-success-subtle bg-opacity-10">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1 fw-bold text-dark fs-13"><i class="ti ti-download me-1 text-success"></i> Plantilla Oficial</h6>
                    <p class="text-muted fs-11 mb-0">Descarga el formato modelo con encabezados y ejemplos listos.</p>
                </div>
                <a href="index.php?route=products/bulk-template" class="btn btn-sm btn-success text-nowrap shadow-sm">
                    <i class="ti ti-file-download me-1"></i> Descargar CSV
                </a>
            </div>
        </div>

        <!-- Tarjeta: Diccionario de Columnas -->
        <div class="card mb-3">
            <div class="card-header py-2 bg-white border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-dark fs-13"><i class="ti ti-table me-1 text-primary"></i> Columnas y Mapeo Aceptado</span>
                <span class="badge bg-light text-secondary fs-11">Guía Rápida</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped fs-11 mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Columna / Alias</th>
                                <th style="width: 25%;">Tipo</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code class="text-primary fw-bold">name</code> <span class="text-danger">*</span><br><span class="text-muted fs-10">nombre, producto</span></td>
                                <td><span class="badge bg-danger-subtle text-danger">Obligatorio</span></td>
                                <td>Nombre o título del producto.</td>
                            </tr>
                            <tr>
                                <td><code class="text-primary fw-bold">sku</code> <span class="text-danger">*</span><br><span class="text-muted fs-10">codigo, item</span></td>
                                <td><span class="badge bg-danger-subtle text-danger">Obligatorio</span></td>
                                <td>Identificador único del producto.</td>
                            </tr>
                            <tr>
                                <td><code>price</code><br><span class="text-muted fs-10">precio, venta, neto</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td>Precio de venta neto ($).</td>
                            </tr>
                            <tr>
                                <td><code>cost</code><br><span class="text-muted fs-10">costo, precio_costo</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td>Costo de compra neto ($).</td>
                            </tr>
                            <tr>
                                <td><code>stock</code>, <code>stock_min</code><br><span class="text-muted fs-10">cantidad, minimo</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td>Cantidades numéricas enteras.</td>
                            </tr>
                            <tr>
                                <td><code>supplier_code</code> / <code>name</code><br><span class="text-muted fs-10">proveedor</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td>Código o nombre exacto del proveedor.</td>
                            </tr>
                            <tr>
                                <td><code>family_code</code> / <code>name</code><br><span class="text-muted fs-10">familia</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td>Si no existe, se creará automáticamente.</td>
                            </tr>
                            <tr>
                                <td><code>subfamily_code</code> / <code>name</code><br><span class="text-muted fs-10">subfamilia</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td>Si no existe, se creará automáticamente.</td>
                            </tr>
                            <tr>
                                <td><code>status</code><br><span class="text-muted fs-10">estado</span></td>
                                <td><span class="badge bg-light text-muted">Opcional</span></td>
                                <td><code>activo</code> o <code>inactivo</code> (defecto: activo).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light p-2 fs-11 text-muted">
                <i class="ti ti-info-circle me-1 text-primary"></i> <strong>Nota:</strong> Los códigos de correlación interna (competencia y proveedor) se calculan automáticamente sin requerir configuración adicional.
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const fileInput = document.getElementById('bulk-file-input');
    const dropZone = document.getElementById('drop-zone');
    const promptBox = document.getElementById('drop-zone-prompt');
    const fileInfoBox = document.getElementById('drop-zone-file-info');
    const fileNameDisplay = document.getElementById('file-name-display');
    const fileSizeDisplay = document.getElementById('file-size-display');
    const btnRemoveFile = document.getElementById('btn-remove-file');
    const previewCard = document.getElementById('preview-card');
    const previewTable = document.getElementById('preview-table');
    const previewAlerts = document.getElementById('preview-validation-alerts');
    const previewRowCountBadge = document.getElementById('preview-row-count-badge');
    const bulkForm = document.getElementById('bulkUploadForm');
    const btnSubmit = document.getElementById('btn-submit-bulk');

    // Drag & Drop Visual Styles
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-primary', 'bg-primary-subtle');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-primary', 'bg-primary-subtle');
        }, false);
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function parseCsvLine(line, delimiter) {
        const result = [];
        let curVal = '';
        let inQuotes = false;
        for (let i = 0; i < line.length; i++) {
            const char = line[i];
            if (char === '"' || char === "'") {
                inQuotes = !inQuotes;
            } else if (char === delimiter && !inQuotes) {
                result.push(curVal.trim().replace(/^["']|["']$/g, ''));
                curVal = '';
            } else {
                curVal += char;
            }
        }
        result.push(curVal.trim().replace(/^["']|["']$/g, ''));
        return result;
    }

    function parseAndPreviewFile(file) {
        if (!file) return;

        fileNameDisplay.textContent = file.name;
        fileSizeDisplay.textContent = formatFileSize(file.size);
        promptBox.classList.add('d-none');
        fileInfoBox.classList.remove('d-none');

        if (!file.name.toLowerCase().endsWith('.csv')) {
            // Preview para XLSX no se hace vía JS simple pero se muestra que el archivo está listo
            previewCard.classList.remove('d-none');
            previewAlerts.innerHTML = `<div class="alert alert-success py-1 px-2 fs-11 mb-0"><i class="ti ti-check me-1"></i> Archivo Excel <strong>${file.name}</strong> listo para procesar.</div>`;
            previewRowCountBadge.textContent = 'Archivo Excel';
            previewTable.querySelector('thead').innerHTML = '';
            previewTable.querySelector('tbody').innerHTML = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const text = e.target.result;
            const lines = text.split(/\r\n|\n/).filter(line => line.trim().length > 0);
            if (lines.length === 0) return;

            // Detectar delimitador (coma o punto y coma)
            const firstLine = lines[0];
            const commaCount = (firstLine.match(/,/g) || []).length;
            const semiCount = (firstLine.match(/;/g) || []).length;
            const tabCount = (firstLine.match(/\t/g) || []).length;
            let delimiter = ',';
            if (semiCount > commaCount && semiCount > tabCount) delimiter = ';';
            if (tabCount > commaCount && tabCount > semiCount) delimiter = '\t';

            const headers = parseCsvLine(firstLine, delimiter).map(h => h.toLowerCase().trim());
            const hasName = headers.some(h => ['name', 'nombre', 'producto'].includes(h));
            const hasSku = headers.some(h => ['sku', 'codigo_sku', 'codigo', 'item'].includes(h));

            // Alertas de validación
            previewAlerts.innerHTML = '';
            if (hasName && hasSku) {
                previewAlerts.innerHTML = `<div class="alert alert-success py-1 px-2 fs-11 mb-2"><i class="ti ti-circle-check me-1"></i> Formato válido: Se encontraron las columnas obligatorias (Nombre y SKU).</div>`;
            } else {
                let missing = [];
                if (!hasName) missing.push('Nombre ("name" o "nombre")');
                if (!hasSku) missing.push('SKU ("sku" o "codigo")');
                previewAlerts.innerHTML = `<div class="alert alert-warning py-1 px-2 fs-11 mb-2"><i class="ti ti-alert-triangle me-1"></i> Advertencia: No se detectaron claramente las columnas: <strong>${missing.join(', ')}</strong>.</div>`;
            }

            previewRowCountBadge.textContent = `${lines.length - 1} fila(s) aproximadas`;

            // Construir encabezados
            const thead = previewTable.querySelector('thead');
            thead.innerHTML = '<tr>' + headers.map(h => {
                const isReq = ['name', 'nombre', 'sku', 'codigo'].includes(h);
                return `<th class="${isReq ? 'text-primary fw-bold' : ''}">${h}</th>`;
            }).join('') + '</tr>';

            // Construir primeras 5 filas
            const tbody = previewTable.querySelector('tbody');
            tbody.innerHTML = '';
            const previewRows = lines.slice(1, 6);
            previewRows.forEach(line => {
                const cells = parseCsvLine(line, delimiter);
                tbody.innerHTML += '<tr>' + cells.map(c => `<td>${c || '<span class="text-muted">-</span>'}</td>`).join('') + '</tr>';
            });

            previewCard.classList.remove('d-none');
        };
        reader.readAsText(file, 'UTF-8');
    }

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            parseAndPreviewFile(file);
        }
    });

    btnRemoveFile.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.value = '';
        promptBox.classList.remove('d-none');
        fileInfoBox.classList.add('d-none');
        previewCard.classList.add('d-none');
    });

    bulkForm.addEventListener('submit', () => {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Procesando archivo...';
    });
})();
</script>

