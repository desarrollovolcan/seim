<?php $isPos = $isPos ?? false; ?>
<?php if ($isPos): ?>
    <style>
        .pos-compact {
            padding: 0.5rem;
        }
        .pos-compact .card {
            border: 1px solid #e7eaf0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            overflow: hidden;
        }
        .pos-main-card,
        .pos-side-card {
            height: 50vh;
            min-height: 280px;
            display: flex;
            flex-direction: column;
        }
        .pos-compact .card-header,
        .pos-compact .card-body {
            padding: 0.4rem 0.6rem;
        }
        .pos-main-card .card-body,
        .pos-side-card .card-body {
            flex: 1 1 auto;
            overflow: auto;
        }
        .pos-compact .list-group-item {
            padding: 0.65rem 0.85rem;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .pos-compact .list-group-item-action:hover {
            background: #f1f4ff;
            transform: translateY(-1px);
        }
        .pos-compact .table-sm> :not(caption)>*>* {
            padding: 0.45rem 0.6rem;
        }
        .pos-compact .tab-pane,
        .pos-compact .tab-content {
            width: 100%;
        }
        .pos-compact .card.h-100 {
            height: 100%;
        }
        .pos-compact .pos-equal-col {
            display: flex;
        }
        .pos-compact .pos-equal-col > .card {
            flex: 1 1 auto;
            height: 100%;
        }
        .pos-compact .tab-pane .list-group-item-action > span:first-child {
            display: inline-flex;
            align-items: flex-start;
            gap: 6px;
            width: 100%;
            text-align: left;
        }
        .pos-compact .tab-pane .list-group-item-action .badge {
            flex-shrink: 0;
        }
        .pos-hero {
            background: #ffffff;
            color: #1f2a3d;
        }
        .pos-hero .card-body {
            padding: 0.4rem 0.6rem;
        }
        .pos-hero-row {
            gap: 0.3rem;
            flex-wrap: nowrap;
            overflow-x: auto;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .pos-hero-row::-webkit-scrollbar { display: none; }
        .pos-chip {
            background: #f7f9fc;
            border: 1px solid #e7eaf0;
            border-radius: 999px;
            padding: 0.28rem 0.55rem;
            font-size: 0.8rem;
            line-height: 1.05;
            white-space: nowrap;
        }
        .pos-chip small {
            color: #6b7280;
        }
        .pos-summary {
            border-radius: 14px;
            background: #fdfefe;
            border: 1px solid #edf0f5;
            padding: 1rem;
        }
        .pos-summary .summary-row + .summary-row {
            border-top: 1px dashed #e3e7ef;
            padding-top: 0.65rem;
            margin-top: 0.65rem;
        }
        .pos-badge-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .pos-actions {
            gap: 0.5rem;
        }
        .pos-glass {
            background: #f7f9fc;
            border: 1px solid #e7eaf0;
        }
        .pos-side-card .list-group {
            width: 100%;
        }
        .pos-side-card .list-group-item {
            width: 100%;
        }
    </style>
    <div class="row mb-0 pos-compact">
        <div class="col-12">
            <div class="card pos-hero">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between pos-hero-row">
                        <div class="d-flex align-items-center gap-2">
                            <span class="pos-badge-dot" style="background:#00b386; width:8px; height:8px;"></span>
                            <h6 class="mb-0 fw-semibold text-nowrap">Caja POS</h6>
                            <?php if (!empty($posSession)): ?>
                                <span class="badge bg-light text-body border text-nowrap">Sesión abierta</span>
                            <?php else: ?>
                                <span class="badge bg-light text-body border text-nowrap">Sin abrir</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-nowrap overflow-auto">
                            <span class="pos-chip text-nowrap"><small>Apertura</small> <?php echo format_currency((float)($posSession['opening_amount'] ?? 0)); ?></span>
                            <span class="pos-chip text-nowrap"><small>Recaudado</small> <?php echo format_currency(array_sum($sessionTotals)); ?></span>
                            <?php if (!empty($sessionTotals)): ?>
                                <?php foreach ($sessionTotals as $method => $total): ?>
                                    <span class="pos-chip text-capitalize text-nowrap"><?php echo e($method); ?> <?php echo format_currency((float)$total); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="pos-chip text-muted text-nowrap">Sin cobros</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-1 flex-nowrap justify-content-end overflow-auto">
                            <a href="index.php?route=products" class="btn btn-outline-secondary btn-sm text-nowrap px-2">Inventario</a>
                            <?php if (!empty($posSession)): ?>
                                <form method="post" action="index.php?route=pos/close" class="d-flex align-items-center gap-1 flex-nowrap">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="number" step="0.01" min="0" name="closing_amount" class="form-control form-control-sm text-nowrap" placeholder="Monto cierre" required style="width: 100px;">
                                    <button class="btn btn-outline-danger btn-sm text-nowrap px-2">Cerrar caja</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="index.php?route=pos/open" class="d-flex align-items-center gap-1 flex-nowrap">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="number" name="opening_amount" step="0.01" min="0" class="form-control form-control-sm text-nowrap" placeholder="Monto inicial" required style="width: 100px;">
                                    <button class="btn btn-primary btn-sm text-nowrap px-2">Abrir caja</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="row align-items-stretch gy-3 gx-1 pos-compact">
    <div class="col-12 col-xl-8 pos-equal-col">
        <div class="card h-100 pos-main-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0"><?php echo $isPos ? 'Punto de venta' : 'Nueva venta'; ?></h4>
                    <small class="text-muted">Construye el ticket, ajusta precios y define el pago en un solo lugar.</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-body border">Modo rápido</span>
                    <span class="badge bg-light text-primary border"><?php echo date('d M Y'); ?></span>
                </div>
            </div>
            <div class="card-body">
                <?php if ($isPos && (empty($posReady) || empty($posSession))): ?>
                    <div class="alert alert-warning">Abre una caja para habilitar el punto de venta. Si ves este mensaje, verifica que las migraciones de BD estén aplicadas.</div>
                <?php endif; ?>
                <form method="post" action="index.php?route=sales/store" id="sale-form">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="channel" value="<?php echo $isPos ? 'pos' : 'venta'; ?>">
                    <div class="row g-4">
                        <?php if ($isPos): ?>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="quick-sale-toggle" name="quick_sale" value="1">
                                    <label class="form-check-label" for="quick-sale-toggle">Venta rápida (sin cliente)</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-6" id="client-section">
                                    <label class="form-label">Cliente</label>
                                    <select name="client_id" class="form-select">
                                        <option value="">Consumidor final</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo (int)$client['id']; ?>"><?php echo e($client['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="sale_date" class="form-control" value="<?php echo e($today); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-select">
                                        <option value="pagado" selected>Pagado</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="borrador">Borrador</option>
                                        <option value="en_espera">En espera</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <?php
                            $siiData = [
                                'sii_document_type' => $isPos ? 'boleta_electronica' : 'factura_electronica',
                                'sii_tax_rate' => 19,
                                'sii_exempt_amount' => 0,
                            ];
                            $siiRequired = !$isPos;
                            $siiShowTaxRate = !$isPos;
                            $siiShowExemptAmount = !$isPos;
                            include __DIR__ . '/../partials/sii-document-fields.php';
                            ?>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <label class="form-label mb-0">Productos / Servicios</label>
                                    <p class="text-muted mb-0 small">Arranca con los atajos rápidos de la derecha para llenar el ticket.</p>
                                </div>
                                <span class="badge bg-light text-secondary border">Editor interactivo</span>
                            </div>
                            <div class="table-responsive rounded-3 border bg-light">
                                <table class="table table-sm align-middle mb-0" id="sale-items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 45%;">Item</th>
                                            <th style="width: 15%;">Cantidad</th>
                                            <th style="width: 20%;">Precio</th>
                                            <th class="text-end" style="width: 15%;">Subtotal</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="sale-items-body"></tbody>
                                </table>
                            </div>
                        </div>
                        <template id="sale-item-template">
                            <tr class="item-row">
                                <input type="hidden" name="item_type[]" value="product" class="item-type">
                                <input type="hidden" name="product_id[]" value="" class="product-id">
                                <input type="hidden" name="produced_product_id[]" value="" class="produced-product-id">
                                <input type="hidden" name="service_id[]" value="" class="service-id">
                                <td class="item-name fw-semibold text-wrap"></td>
                                <td><input type="number" name="quantity[]" class="form-control form-control-sm quantity-input" min="1" value="1"></td>
                                <td><input type="number" name="unit_price[]" class="form-control form-control-sm price-input" step="0.01" min="0" value="0"></td>
                                <td class="text-end item-subtotal fw-semibold">0</td>
                                <td><button type="button" class="btn btn-link text-danger p-0 remove-row" aria-label="Eliminar">✕</button></td>
                            </tr>
                        </template>
                        <div class="col-md-6">
                            <label class="form-label">Notas</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Detalles adicionales o instrucciones"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="pos-summary">
                                <div class="d-flex justify-content-between align-items-center summary-row">
                                    <span class="text-muted">Subtotal</span>
                                    <strong id="sale-subtotal"><?php echo format_currency(0); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center summary-row">
                                    <span class="text-muted">Impuestos</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <select name="apply_tax" id="apply-tax" class="form-select form-select-sm w-auto" style="width: 170px;">
                                            <option value="1" <?php echo !empty($applyTaxDefault) ? 'selected' : ''; ?>>Calcular impuesto</option>
                                            <option value="0" <?php echo empty($applyTaxDefault) ? 'selected' : ''; ?>>No calcular</option>
                                        </select>
                                        <input type="number" name="tax" id="sale-tax" class="form-control form-control-sm w-auto text-end" style="width: 140px;" step="0.01" min="0" value="<?php echo e($taxDefault ?? 0); ?>" readonly>
                                        <input type="hidden" name="tax_rate" id="tax-rate" value="<?php echo e($taxRate ?? 19); ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center summary-row">
                                    <span class="text-muted">Forma de pago</span>
                                    <select name="payment_method" class="form-select form-select-sm w-auto" style="width: 180px;">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-between align-items-center summary-row">
                                    <span class="fw-semibold">Total a cobrar</span>
                                    <span class="h5 mb-0" id="sale-total"><?php echo format_currency(0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary" <?php echo ($isPos && (empty($posSession) || empty($posReady))) ? 'disabled' : ''; ?>><?php echo $isPos ? 'Cobrar venta' : 'Guardar venta'; ?></button>
                            <button type="button" class="btn btn-outline-secondary" id="mark-hold" <?php echo ($isPos && (empty($posSession) || empty($posReady))) ? 'disabled' : ''; ?>>Marcar en espera</button>
                            <a href="index.php?route=<?php echo $isPos ? 'pos' : 'sales'; ?>" class="btn btn-light ms-2">Cancelar</a>
                        </div>
                    </div>
                
    <?php if (!$isPos): ?>
        <?php
        $reportTemplate = 'informeIcargaEspanol.php';
        $reportSource = 'sales/create';
        include __DIR__ . '/../partials/report-download.php';
        ?>
    <?php endif; ?>
</form>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4 pos-equal-col px-0">
        <div class="card h-100 pos-side-card">
            <div class="card-header">
                <h6 class="card-title mb-0">Productos</h6>
            </div>
            <div class="card-body p-0 d-flex flex-column">
                <div class="p-2 d-flex flex-column gap-2">
                    <?php if ($isPos): ?>
                        <select class="form-select form-select-sm" id="product-type-filter">
                            <option value="all">Todos los productos</option>
                            <option value="produced">Productos fabricados</option>
                            <option value="regular">Productos normales</option>
                        </select>
                    <?php endif; ?>
                    <input type="text" class="form-control form-control-sm w-100" id="search-products" placeholder="Buscar producto">
                </div>
                <div class="flex-grow-1 overflow-auto w-100">
                    <div class="px-3 pt-2 pb-1 text-uppercase small text-muted product-group-title" data-group-type="regular">Productos normales</div>
                    <div class="list-group list-group-flush" data-product-group="regular">
                        <?php foreach ($products as $product): ?>
                            <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center add-product w-100"
                                    data-product-id="<?php echo (int)$product['id']; ?>"
                                    data-product-type="regular"
                                    data-price="<?php echo e((float)($product['price'] ?? 0)); ?>"
                                    data-name="<?php echo e(strtolower($product['name'] ?? '')); ?>"
                                    data-label="<?php echo e($product['name']); ?>">
                                <span class="flex-grow-1">
                                    <?php echo e($product['name']); ?>
                                    <?php if (!empty($product['sku'])): ?>
                                        <small class="text-muted ms-1">(#<?php echo e($product['sku']); ?>)</small>
                                    <?php endif; ?>
                                </span>
                                <span class="badge bg-light text-body"><?php echo format_currency((float)($product['price'] ?? 0)); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="px-3 pt-3 pb-1 text-uppercase small text-muted product-group-title" data-group-type="produced">Productos fabricados</div>
                    <div class="list-group list-group-flush" data-product-group="produced">
                        <?php foreach ($producedProducts as $product): ?>
                            <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center add-product w-100"
                                    data-produced-product-id="<?php echo (int)$product['id']; ?>"
                                    data-product-type="produced"
                                    data-price="<?php echo e((float)($product['price'] ?? 0)); ?>"
                                    data-name="<?php echo e(strtolower($product['name'] ?? '')); ?>"
                                    data-label="<?php echo e($product['name']); ?>">
                                <span class="flex-grow-1">
                                    <?php echo e($product['name']); ?>
                                    <?php if (!empty($product['sku'])): ?>
                                        <small class="text-muted ms-1">(#<?php echo e($product['sku']); ?>)</small>
                                    <?php endif; ?>
                                    <span class="badge bg-soft-info text-info ms-2">Fabricado</span>
                                </span>
                                <span class="badge bg-light text-body"><?php echo format_currency((float)($product['price'] ?? 0)); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($isPos): ?>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Historial de ventas (sesión activa)</h5>
                        <small class="text-muted">Ventas recientes vinculadas a la caja abierta.</small>
                    </div>
                    <a href="index.php?route=sales" class="btn btn-soft-secondary btn-sm">Ver todas</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentSessionSales)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSessionSales as $sale): ?>
                                        <tr>
                                            <td class="fw-semibold"><?php echo e($sale['numero']); ?></td>
                                            <td><?php echo e($sale['client_name'] ?? 'Consumidor final'); ?></td>
                                            <td><?php echo e(date('d/m/Y', strtotime((string)$sale['sale_date']))); ?></td>
                                            <td class="text-end"><?php echo format_currency((float)($sale['total'] ?? 0)); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-body border text-capitalize"><?php echo e(str_replace('_', ' ', $sale['status'] ?? '')); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aún no hay ventas registradas en esta sesión.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    (function() {
        const tableBody = document.getElementById('sale-items-body');
        const rowTemplate = document.getElementById('sale-item-template');
        const subtotalDisplay = document.getElementById('sale-subtotal');
        const totalDisplay = document.getElementById('sale-total');
        const taxInput = document.getElementById('sale-tax');
        const applyTaxSelect = document.getElementById('apply-tax');
        const taxRateInput = document.getElementById('tax-rate');
        const statusSelect = document.querySelector('select[name=\"status\"]');
        const holdButton = document.getElementById('mark-hold');
        const productSelectors = document.querySelectorAll('.add-product');
        const searchProducts = document.getElementById('search-products');
        const productTypeFilter = document.getElementById('product-type-filter');
        const productGroupTitles = document.querySelectorAll('.product-group-title');
        const productGroups = document.querySelectorAll('[data-product-group]');
        const mainCard = document.querySelector('.pos-main-card');
        const sideCard = document.querySelector('.pos-side-card');
        const clientSelect = document.querySelector('select[name="client_id"]');
        const quickSaleToggle = document.getElementById('quick-sale-toggle');
        const clientSection = document.getElementById('client-section');
        const siiCard = document.querySelector('[data-sii-warning]')?.closest('.card');
        const clientSiiMap = <?php echo json_encode(array_reduce($clients ?? [], static function (array $carry, array $client): array {
            $carry[$client['id']] = [
                'rut' => $client['rut'] ?? '',
                'name' => $client['name'] ?? '',
                'giro' => $client['giro'] ?? '',
                'address' => $client['address'] ?? '',
                'commune' => $client['commune'] ?? '',
            ];
            return $carry;
        }, []), JSON_UNESCAPED_UNICODE); ?>;
        const siiInputs = {
            sii_receiver_rut: document.querySelector('[name="sii_receiver_rut"]'),
            sii_receiver_name: document.querySelector('[name="sii_receiver_name"]'),
            sii_receiver_giro: document.querySelector('[name="sii_receiver_giro"]'),
            sii_receiver_address: document.querySelector('[name="sii_receiver_address"]'),
            sii_receiver_commune: document.querySelector('[name="sii_receiver_commune"]'),
        };
        const siiWarning = document.querySelector('[data-sii-warning]');
        const siiWarningText = document.querySelector('[data-sii-warning-text]');
        const siiWarningLink = document.querySelector('[data-sii-warning-link]');
        const siiRequiredFields = [
            { key: 'rut', label: 'RUT' },
            { key: 'name', label: 'Razón social' },
            { key: 'giro', label: 'Giro' },
            { key: 'address', label: 'Dirección' },
            { key: 'commune', label: 'Comuna' },
        ];

        const updateSiiWarning = (data, clientId) => {
            if (!siiWarning || !siiWarningText || !siiWarningLink) {
                return;
            }
            const missing = siiRequiredFields.filter((field) => !(data?.[field.key] || '').trim());
            if (missing.length === 0 || !clientId) {
                siiWarning.classList.add('d-none');
                return;
            }
            siiWarningText.textContent = `Completa en la ficha del cliente: ${missing.map((field) => field.label).join(', ')}.`;
            siiWarningLink.href = `index.php?route=clients/edit&id=${clientId}`;
            siiWarning.classList.remove('d-none');
        };

        const applyClientSii = (clientId, force = false) => {
            const data = clientSiiMap?.[clientId];
            if (!data) {
                updateSiiWarning({}, clientId);
                return;
            }
            if (siiInputs.sii_receiver_rut) siiInputs.sii_receiver_rut.value = data.rut || '';
            if (siiInputs.sii_receiver_name) siiInputs.sii_receiver_name.value = data.name || '';
            if (siiInputs.sii_receiver_giro) siiInputs.sii_receiver_giro.value = data.giro || '';
            if (siiInputs.sii_receiver_address) siiInputs.sii_receiver_address.value = data.address || '';
            if (siiInputs.sii_receiver_commune) siiInputs.sii_receiver_commune.value = data.commune || '';
            updateSiiWarning(data, clientId);
        };

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }).format(amount || 0);
        }

        function recalc() {
            let subtotal = 0;
            tableBody.querySelectorAll('.item-row').forEach((row) => {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const total = qty * price;
                subtotal += total;
                row.querySelector('.item-subtotal').innerText = formatCurrency(total);
            });
            subtotalDisplay.innerText = formatCurrency(subtotal);
            const rate = parseFloat(taxRateInput?.value) || 0;
            const shouldApplyTax = applyTaxSelect?.value === '1';
            const tax = shouldApplyTax ? Math.round(subtotal * rate) / 100 : 0;
            if (taxInput) {
                taxInput.value = tax.toFixed(2);
            }
            totalDisplay.innerText = formatCurrency(subtotal + tax);
        }

        function addRow({ type, productId = '', serviceId = '', price = 0, name = '' }) {
            if (!rowTemplate?.content) return null;
            const clone = rowTemplate.content.cloneNode(true);
            const row = clone.querySelector('.item-row');
            row.querySelector('.item-type').value = type;
            row.querySelector('.product-id').value = productId;
            row.querySelector('.produced-product-id').value = '';
            if (type === 'produced_product') {
                row.querySelector('.product-id').value = '';
                row.querySelector('.produced-product-id').value = productId;
            }
            row.querySelector('.service-id').value = serviceId;
            row.querySelector('.price-input').value = price;
            row.querySelector('.item-name').innerText = name || 'Item';
            row.querySelector('.quantity-input').value = 1;
            tableBody.appendChild(clone);
            recalc();
            return row;
        }

        tableBody.addEventListener('input', (event) => {
            if (event.target.classList.contains('quantity-input') || event.target.classList.contains('price-input')) {
                recalc();
            }
        });

        tableBody.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-row')) {
                event.target.closest('.item-row')?.remove();
                recalc();
            }
        });

        applyTaxSelect?.addEventListener('change', recalc);
        clientSelect?.addEventListener('change', () => {
            applyClientSii(Number(clientSelect?.value || 0));
        });
        holdButton?.addEventListener('click', () => {
            if (statusSelect) {
                statusSelect.value = 'en_espera';
            }
        });
        productSelectors.forEach((button) => {
            button.addEventListener('click', () => {
                const productType = button.dataset.productType || 'regular';
                const productId = productType === 'produced'
                    ? button.dataset.producedProductId
                    : button.dataset.productId;
                const price = button.dataset.price || 0;
                const name = button.dataset.label || button.innerText.trim();
                addRow({
                    type: productType === 'produced' ? 'produced_product' : 'product',
                    productId,
                    serviceId: '',
                    price,
                    name,
                });
            });
        });
        function toggleQuickSale(forceState = null) {
            if (!quickSaleToggle) {
                return;
            }
            const enabled = forceState ?? quickSaleToggle.checked;
            if (clientSection) {
                clientSection.style.display = enabled ? 'none' : '';
            }
            if (siiCard) {
                siiCard.style.display = enabled ? 'none' : '';
            }
            if (enabled && clientSelect) {
                clientSelect.value = '';
                applyClientSii(0, true);
            }
            if (!enabled) {
                applyClientSii(Number(clientSelect?.value || 0), true);
            }
        }
        quickSaleToggle?.addEventListener('change', () => toggleQuickSale());
        function filterList() {
            const term = (searchProducts?.value || '').toLowerCase();
            const type = productTypeFilter?.value || 'all';
            productSelectors.forEach((el) => {
                const name = (el.dataset.name || '').toLowerCase();
                const productType = el.dataset.productType || 'regular';
                const matchesTerm = name.includes(term);
                const matchesType = type === 'all' || (type === 'produced' && productType === 'produced') || (type === 'regular' && productType === 'regular');
                el.style.display = matchesTerm && matchesType ? '' : 'none';
            });
            productGroups.forEach((group) => {
                const groupType = group.dataset.productGroup;
                const hasVisible = Array.from(group.querySelectorAll('.add-product')).some((item) => item.style.display !== 'none');
                group.style.display = hasVisible ? '' : 'none';
                const title = Array.from(productGroupTitles).find((el) => el.dataset.groupType === groupType);
                if (title) {
                    title.style.display = hasVisible ? '' : 'none';
                }
            });
        }
        searchProducts?.addEventListener('input', filterList);
        productTypeFilter?.addEventListener('change', filterList);
        function syncCardHeights() {
            if (mainCard && sideCard) {
                sideCard.style.minHeight = `${mainCard.clientHeight}px`;
            }
        }
        window.addEventListener('resize', syncCardHeights);
        syncCardHeights();
        applyClientSii(Number(clientSelect?.value || 0));
        toggleQuickSale();
        filterList();
        recalc();

        const printSaleId = <?php echo (int)($printSaleId ?? 0); ?>;
        if (printSaleId) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('print_sale_id');
            window.history.replaceState({}, document.title, currentUrl.toString());
            const copiesInput = window.prompt('¿Cuántas copias deseas imprimir?', '1');
            if (copiesInput !== null) {
                const copies = Number.parseInt(copiesInput, 10);
                if (Number.isFinite(copies) && copies > 0) {
                    const printUrl = `index.php?route=sales/receipt&id=${printSaleId}&copies=${copies}`;
                    window.open(printUrl, '_blank', 'noopener,noreferrer');
                }
            }
        }
    })();
</script>
