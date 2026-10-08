<div class="row g-3">
    <div class="col-12">
        <form method="post" action="index.php?route=products/store" enctype="multipart/form-data" id="productCreateForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div class="row g-3">
                <!-- Columna Izquierda: Información Principal y Clasificación -->
                <div class="col-lg-7">
                    <!-- Tarjeta: Datos Principales -->
                    <div class="card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center bg-white border-bottom">
                            <span class="fw-semibold text-dark fs-13"><i class="ti ti-package me-1 text-primary"></i> Identificación del Producto</span>
                            <span class="badge bg-primary-subtle text-primary font-monospace">Nuevo</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fs-12 mb-1">Nombre del producto <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="product-name" class="form-control form-control-sm" required placeholder="Ej. Cable Cu 2.5mm Libre de Halógenos">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 mb-1">Código SKU</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="sku" id="product-sku" class="form-control font-monospace" placeholder="SKU-0001">
                                        <button class="btn btn-outline-secondary" type="button" id="btn-generate-sku" title="Generar SKU sugerido"><i class="ti ti-sparkles"></i> Auto</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 mb-1">Proveedor <span class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplier-select" class="form-select form-select-sm" required>
                                        <option value="">Seleccionar proveedor</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?php echo (int)$supplier['id']; ?>" data-code="<?php echo e($supplier['code'] ?? ''); ?>">
                                                <?php echo e($supplier['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="form-label fs-12 mb-1">Descripción / Ficha técnica</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Especificaciones técnicas o detalles del producto"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Clasificación y Códigos Automáticos -->
                    <div class="card mb-3">
                        <div class="card-header py-2 bg-white border-bottom">
                            <span class="fw-semibold text-dark fs-13"><i class="ti ti-category me-1 text-primary"></i> Clasificación y Códigos Relacionales</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label fs-12 mb-1">Familia <span class="text-danger">*</span></label>
                                    <select name="family_id" id="family-select" class="form-select form-select-sm" required>
                                        <option value="">Seleccionar familia</option>
                                        <?php foreach ($families as $family): ?>
                                            <option value="<?php echo (int)$family['id']; ?>" data-code="<?php echo e($family['code'] ?? ''); ?>">
                                                <?php echo e($family['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-12 mb-1">Subfamilia <span class="text-danger">*</span></label>
                                    <select name="subfamily_id" id="subfamily-select" class="form-select form-select-sm" required>
                                        <option value="">Seleccionar subfamilia</option>
                                        <?php foreach ($subfamilies as $subfamily): ?>
                                            <option value="<?php echo (int)$subfamily['id']; ?>" data-family="<?php echo (int)$subfamily['family_id']; ?>" data-code="<?php echo e($subfamily['code'] ?? ''); ?>">
                                                <?php echo e($subfamily['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-12 mb-1">Empresa Competencia de Referencia <span class="text-danger">*</span></label>
                                    <select name="competitor_company_id" id="competitor-company-select" class="form-select form-select-sm" required>
                                        <option value="">Seleccionar empresa competencia</option>
                                        <?php foreach ($competitors as $competitor): ?>
                                            <option value="<?php echo (int)$competitor['id']; ?>" data-code="<?php echo e($competitor['code'] ?? ''); ?>">
                                                <?php echo e($competitor['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="p-2 bg-light rounded border">
                                        <label class="form-label fs-11 text-muted mb-1 text-uppercase fw-semibold">Código Competencia (Preview)</label>
                                        <input type="text" name="competition_code" id="competition-code" class="form-control form-control-sm bg-white font-monospace fs-12" readonly placeholder="Auto al guardar">
                                        <div class="text-muted fs-11 mt-1">Se asignará el correlativo al guardar</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="p-2 bg-light rounded border">
                                        <label class="form-label fs-11 text-muted mb-1 text-uppercase fw-semibold">Código Proveedor (Preview)</label>
                                        <input type="text" name="supplier_code" id="supplier-code" class="form-control form-control-sm bg-white font-monospace fs-12" readonly placeholder="Auto al guardar">
                                        <div class="text-muted fs-11 mt-1">Se asignará el correlativo al guardar</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Finanzas, Inventario e Imágenes -->
                <div class="col-lg-5">
                    <!-- Tarjeta: Precios y Calculadora de Rentabilidad en Vivo -->
                    <div class="card mb-3 border-primary-subtle shadow-sm">
                        <div class="card-header py-2 bg-primary-subtle border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-primary fs-13"><i class="ti ti-chart-line me-1"></i> Precios y Rentabilidad</span>
                            <span id="margin-badge" class="badge bg-success-subtle text-success fs-11">Margen: 0%</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fs-12 mb-1">Costo de compra ($) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="cost" id="product-cost" class="form-control text-end fw-semibold" step="0.01" min="0" required value="0">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fs-12 mb-1">Precio venta neto ($) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" id="product-price" class="form-control text-end fw-bold text-primary" step="0.01" min="0" required value="0">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fs-12 mb-1">Precio proveedor ($)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text text-muted">$</span>
                                        <input type="number" name="supplier_price" id="product-supplier-price" class="form-control text-end" step="0.01" min="0" value="0">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fs-12 mb-1">Precio competencia ($)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text text-muted">$</span>
                                        <input type="number" name="competition_price" id="product-competition-price" class="form-control text-end" step="0.01" min="0" value="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen automático de Rentabilidad -->
                            <div class="mt-3 p-2 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center fs-12 mb-1">
                                    <span class="text-muted">Ganancia bruta estimada:</span>
                                    <strong id="profit-amount" class="text-dark">$0</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-12">
                                    <span class="text-muted">Margen sobre venta:</span>
                                    <strong id="profit-percent" class="text-success">0.0%</strong>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div id="margin-progress" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Control de Stock y Estado -->
                    <div class="card mb-3">
                        <div class="card-header py-2 bg-white border-bottom">
                            <span class="fw-semibold text-dark fs-13"><i class="ti ti-box me-1 text-primary"></i> Stock y Estado</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-4">
                                    <label class="form-label fs-12 mb-1">Stock Inicial</label>
                                    <input type="number" name="stock" id="product-stock" class="form-control form-control-sm text-center fw-bold" min="0" value="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fs-12 mb-1">Stock Mínimo</label>
                                    <input type="number" name="stock_min" id="product-stock-min" class="form-control form-control-sm text-center" min="0" value="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fs-12 mb-1">Estado</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="activo" selected>Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-1">
                                    <div id="stock-alert-box" class="fs-11 py-1 px-2 rounded" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Imágenes del Producto -->
                    <div class="card mb-3">
                        <div class="card-header py-2 bg-white border-bottom">
                            <span class="fw-semibold text-dark fs-13"><i class="ti ti-photo me-1 text-primary"></i> Fotografías</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fs-11 text-muted mb-1">Foto Principal</label>
                                    <div class="d-flex flex-column align-items-center p-2 border border-dashed rounded bg-light text-center">
                                        <img id="preview-photo-1" src="assets/images/placeholder-image.png" alt="Foto 1" class="rounded mb-2" style="width: 60px; height: 60px; object-fit: contain; background: #fff; border: 1px solid #e2e8f0;" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' fill=\'%2394a3b8\' viewBox=\'0 0 16 16\'><path d=\'M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z\'/><path d=\'M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z\'/></svg>'">
                                        <input type="file" name="photo_1" id="file-photo-1" class="form-control form-control-sm fs-11" accept="image/png,image/jpeg,image/webp">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fs-11 text-muted mb-1">Foto Secundaria</label>
                                    <div class="d-flex flex-column align-items-center p-2 border border-dashed rounded bg-light text-center">
                                        <img id="preview-photo-2" src="assets/images/placeholder-image.png" alt="Foto 2" class="rounded mb-2" style="width: 60px; height: 60px; object-fit: contain; background: #fff; border: 1px solid #e2e8f0;" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' fill=\'%2394a3b8\' viewBox=\'0 0 16 16\'><path d=\'M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z\'/><path d=\'M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z\'/></svg>'">
                                        <input type="file" name="photo_2" id="file-photo-2" class="form-control form-control-sm fs-11" accept="image/png,image/jpeg,image/webp">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barra de Acciones Inferior -->
                <div class="col-12">
                    <div class="card p-3 bg-white border d-flex flex-row justify-content-between align-items-center">
                        <div class="text-muted fs-12">
                            <i class="ti ti-info-circle me-1"></i> Los códigos correlativos se generarán automáticamente al guardar.
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="index.php?route=products" class="btn btn-sm btn-light px-3"><i class="ti ti-arrow-left me-1"></i> Volver</a>
                            <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold"><i class="ti ti-check me-1"></i> Guardar producto</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    // Selectores
    const competitorSelect = document.getElementById('competitor-company-select');
    const supplierSelect = document.getElementById('supplier-select');
    const familySelect = document.getElementById('family-select');
    const subfamilySelect = document.getElementById('subfamily-select');
    const competitionCodeInput = document.getElementById('competition-code');
    const supplierCodeInput = document.getElementById('supplier-code');
    const costInput = document.getElementById('product-cost');
    const priceInput = document.getElementById('product-price');
    const stockInput = document.getElementById('product-stock');
    const stockMinInput = document.getElementById('product-stock-min');
    const stockAlertBox = document.getElementById('stock-alert-box');
    const profitAmount = document.getElementById('profit-amount');
    const profitPercent = document.getElementById('profit-percent');
    const marginBadge = document.getElementById('margin-badge');
    const marginProgress = document.getElementById('margin-progress');
    const btnGenerateSku = document.getElementById('btn-generate-sku');
    const skuInput = document.getElementById('product-sku');
    const filePhoto1 = document.getElementById('file-photo-1');
    const previewPhoto1 = document.getElementById('preview-photo-1');
    const filePhoto2 = document.getElementById('file-photo-2');
    const previewPhoto2 = document.getElementById('preview-photo-2');

    // 1. Filtrado dinámico de subfamilias
    function filterSubfamilies() {
        const familyId = familySelect ? familySelect.value : '';
        if (!subfamilySelect) return;

        Array.from(subfamilySelect.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            const belongs = option.dataset.family === familyId || familyId === '';
            option.hidden = !belongs;
        });

        if (familyId && subfamilySelect.selectedOptions[0]?.hidden) {
            subfamilySelect.value = '';
        }
    }

    // 2. Previews de códigos relacionales
    function updateCodesPreview() {
        const competitorCode = competitorSelect?.selectedOptions[0]?.dataset.code || '';
        const supplierCode = supplierSelect?.selectedOptions[0]?.dataset.code || '';
        const familyCode = familySelect?.selectedOptions[0]?.dataset.code || '';
        const subfamilyCode = subfamilySelect?.selectedOptions[0]?.dataset.code || '';

        if (competitionCodeInput) {
            if (competitorCode && familyCode && subfamilyCode) {
                competitionCodeInput.value = `${competitorCode}-${familyCode}-${subfamilyCode}-####`;
            } else {
                competitionCodeInput.value = '';
            }
        }
        if (supplierCodeInput) {
            if (supplierCode && familyCode && subfamilyCode) {
                supplierCodeInput.value = `${supplierCode}-${familyCode}-${subfamilyCode}-####`;
            } else {
                supplierCodeInput.value = '';
            }
        }
    }

    // 3. Calculadora de Rentabilidad & Margen en Vivo
    function calculateMargin() {
        const cost = parseFloat(costInput?.value) || 0;
        const price = parseFloat(priceInput?.value) || 0;

        const profit = price - cost;
        const marginPct = price > 0 ? ((profit / price) * 100) : 0;

        const formatClp = (val) => new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(val);

        if (profitAmount) profitAmount.textContent = formatClp(profit);
        if (profitPercent) profitPercent.textContent = marginPct.toFixed(1) + '%';

        // Estilos dinámicos según el margen
        if (marginBadge && marginProgress) {
            marginBadge.className = 'badge fs-11 ';
            marginProgress.className = 'progress-bar ';
            const clampedWidth = Math.max(0, Math.min(100, marginPct));
            marginProgress.style.width = clampedWidth + '%';

            if (marginPct >= 30) {
                marginBadge.classList.add('bg-success-subtle', 'text-success');
                marginProgress.classList.add('bg-success');
                marginBadge.textContent = `Margen Alto: ${marginPct.toFixed(1)}%`;
            } else if (marginPct >= 15) {
                marginBadge.classList.add('bg-primary-subtle', 'text-primary');
                marginProgress.classList.add('bg-primary');
                marginBadge.textContent = `Margen Bueno: ${marginPct.toFixed(1)}%`;
            } else if (marginPct > 0) {
                marginBadge.classList.add('bg-warning-subtle', 'text-warning');
                marginProgress.classList.add('bg-warning');
                marginBadge.textContent = `Margen Bajo: ${marginPct.toFixed(1)}%`;
            } else {
                marginBadge.classList.add('bg-danger-subtle', 'text-danger');
                marginProgress.classList.add('bg-danger');
                marginBadge.textContent = `Sin Ganancia: ${marginPct.toFixed(1)}%`;
            }
        }
    }

    // 4. Verificación y alerta de stock
    function checkStock() {
        if (!stockAlertBox) return;
        const stock = parseInt(stockInput?.value, 10) || 0;
        const stockMin = parseInt(stockMinInput?.value, 10) || 0;

        if (stock === 0) {
            stockAlertBox.style.display = 'block';
            stockAlertBox.className = 'fs-11 py-1 px-2 rounded bg-danger-subtle text-danger fw-semibold';
            stockAlertBox.innerHTML = '<i class="ti ti-alert-triangle me-1"></i> Sin existencias (Stock inicial en 0)';
        } else if (stock <= stockMin) {
            stockAlertBox.style.display = 'block';
            stockAlertBox.className = 'fs-11 py-1 px-2 rounded bg-warning-subtle text-warning fw-semibold';
            stockAlertBox.innerHTML = `<i class="ti ti-alert-circle me-1"></i> Stock bajo (≤ mínimo de ${stockMin} un.)`;
        } else {
            stockAlertBox.style.display = 'block';
            stockAlertBox.className = 'fs-11 py-1 px-2 rounded bg-success-subtle text-success';
            stockAlertBox.innerHTML = '<i class="ti ti-check me-1"></i> Nivel de stock óptimo';
        }
    }

    // 5. Generador de SKU inteligente
    if (btnGenerateSku && skuInput) {
        btnGenerateSku.addEventListener('click', () => {
            const famCode = familySelect?.selectedOptions[0]?.dataset.code || 'PRD';
            const subCode = subfamilySelect?.selectedOptions[0]?.dataset.code || 'GEN';
            const randomSuffix = Math.floor(1000 + Math.random() * 9000);
            skuInput.value = `${famCode.substring(0, 3).toUpperCase()}-${subCode.substring(0, 3).toUpperCase()}-${randomSuffix}`;
            skuInput.focus();
        });
    }

    // 6. Previews de fotos instantáneos al seleccionar archivo
    function setupImagePreview(fileInput, previewImg) {
        if (!fileInput || !previewImg) return;
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImg.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    setupImagePreview(filePhoto1, previewPhoto1);
    setupImagePreview(filePhoto2, previewPhoto2);

    // Event Listeners
    familySelect?.addEventListener('change', () => {
        filterSubfamilies();
        updateCodesPreview();
    });
    subfamilySelect?.addEventListener('change', updateCodesPreview);
    competitorSelect?.addEventListener('change', updateCodesPreview);
    supplierSelect?.addEventListener('change', updateCodesPreview);

    costInput?.addEventListener('input', calculateMargin);
    priceInput?.addEventListener('input', calculateMargin);
    stockInput?.addEventListener('input', checkStock);
    stockMinInput?.addEventListener('input', checkStock);

    // Inicializaciones al cargar
    filterSubfamilies();
    calculateMargin();
    checkStock();
})();
</script>
