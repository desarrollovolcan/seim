<div class="card mb-4" id="order-form-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Registrar orden de venta</h4>
            <p class="text-muted mb-0">Completa los datos para crear órdenes sin duplicar información del cliente.</p>
        </div>
        <a class="btn btn-outline-primary" href="#orders-list">Ver listado</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=crm/orders/store" id="order-form">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="order-client">Cliente</label>
                    <select class="form-select" id="order-client" name="client_id" data-client-select required>
                        <option value="">Selecciona cliente</option>
                        <?php foreach ($clients as $client): ?>
                            <?php
                            $contactName = $client['contact'] ?: $client['name'];
                            ?>
                            <option value="<?php echo (int)$client['id']; ?>"
                                data-contact-name="<?php echo e($contactName); ?>"
                                data-contact-email="<?php echo e($client['email'] ?? ''); ?>"
                                data-contact-phone="<?php echo e($client['phone'] ?? ''); ?>"
                                data-address="<?php echo e($client['address'] ?? ''); ?>"
                                data-rut="<?php echo e($client['rut'] ?? ''); ?>"
                                data-billing-email="<?php echo e($client['billing_email'] ?? ''); ?>">
                                <?php echo e($client['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-brief">Brief asociado</label>
                    <select class="form-select" id="order-brief" name="brief_id">
                        <option value="">Sin brief</option>
                        <?php foreach ($briefs as $brief): ?>
                            <option value="<?php echo (int)$brief['id']; ?>"><?php echo e($brief['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-number">Número de orden</label>
                    <input type="text" class="form-control" id="order-number" name="order_number" placeholder="Ej: OV-2025-001" autocomplete="off">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-contact-name">Contacto cliente</label>
                    <input type="text" class="form-control" id="order-contact-name" name="contact_name" data-client-field="contact_name" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-date">Fecha</label>
                    <input type="date" class="form-control" id="order-date" name="order_date" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-total">Total (CLP)</label>
                    <input type="number" class="form-control" id="order-total" name="total" min="0" step="0.01" placeholder="Ej: 1800000" inputmode="decimal" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="order-currency">Moneda</label>
                    <select class="form-select" id="order-currency" name="currency">
                        <option value="CLP" selected>CLP</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="order-status">Estado</label>
                    <select class="form-select" id="order-status" name="status">
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="en_ejecucion">En ejecución</option>
                        <option value="finalizada">Finalizada</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-contact-email">Correo contacto</label>
                    <input type="email" class="form-control" id="order-contact-email" name="contact_email" data-client-field="contact_email" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="order-contact-phone">Teléfono contacto</label>
                    <input type="text" class="form-control" id="order-contact-phone" name="contact_phone" data-client-field="contact_phone" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label">Detalle de productos</label>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="order-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Producto</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 20%;">Precio unitario</th>
                                    <th class="text-end" style="width: 15%;">Subtotal</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select name="order_product_id[]" class="form-select form-select-sm order-product">
                                            <option value="">Selecciona</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo (int)$product['id']; ?>" data-price="<?php echo e((float)($product['price'] ?? 0)); ?>">
                                                    <?php echo e($product['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="order_quantity[]" class="form-control form-control-sm order-qty" min="1" value="1"></td>
                                    <td><input type="number" name="order_unit_price[]" class="form-control form-control-sm order-price" step="0.01" min="0" value="0"></td>
                                    <td class="text-end order-subtotal fw-semibold">0</td>
                                    <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-order-item">Agregar producto</button>
                </div>
                <div class="col-12">
                    <label class="form-label" for="order-notes">Notas</label>
                    <textarea class="form-control" id="order-notes" name="notes" rows="3" placeholder="Condiciones comerciales, alcance y responsables"></textarea>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                <button type="reset" class="btn btn-light w-100 w-sm-auto">Limpiar formulario</button>
                <button type="submit" class="btn btn-primary w-100 w-sm-auto">Guardar orden</button>
            </div>
        </form>
    </div>
</div>

<div class="card" id="orders-list">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title mb-1">Órdenes de venta</h4>
            <p class="text-muted mb-0">Controla órdenes confirmadas y en ejecución para cada cliente.</p>
        </div>
        <a class="btn btn-outline-primary" href="#order-form-card">Nueva orden</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Orden</th>
                        <th>Cliente</th>
                        <th>Brief</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay órdenes de venta registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="text-muted"><?php echo render_id_badge($order['id'] ?? null); ?></td>
                                <td><?php echo e($order['order_number']); ?></td>
                                <td><?php echo e($order['client_name'] ?? ''); ?></td>
                                <td><?php echo e($order['brief_title'] ?? '-'); ?></td>
                                <td><?php echo e($order['order_date']); ?></td>
                                <td>
                                    <?php $status = $order['status'] ?? 'pendiente'; ?>
                                    <span class="badge bg-<?php echo $status === 'finalizada' ? 'success' : ($status === 'en_ejecucion' ? 'info' : ($status === 'confirmada' ? 'primary' : 'warning')); ?>-subtle text-<?php echo $status === 'finalizada' ? 'success' : ($status === 'en_ejecucion' ? 'info' : ($status === 'confirmada' ? 'primary' : 'warning')); ?>">
                                        <?php echo e(str_replace('_', ' ', $status)); ?>
                                    </span>
                                </td>
                                <td class="text-end"><?php echo e(format_currency((float)($order['total'] ?? 0))); ?></td>
                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-soft-primary btn-sm js-order-duplicate"
                                        data-order-client="<?php echo (int)($order['client_id'] ?? 0); ?>"
                                        data-order-brief="<?php echo (int)($order['brief_id'] ?? 0); ?>"
                                        data-order-number="<?php echo e($order['order_number'] ?? ''); ?>"
                                        data-order-date="<?php echo e($order['order_date'] ?? ''); ?>"
                                        data-order-status="<?php echo e($order['status'] ?? 'pendiente'); ?>"
                                        data-order-total="<?php echo e($order['total'] ?? ''); ?>"
                                        data-order-currency="<?php echo e($order['currency'] ?? 'CLP'); ?>"
                                        data-order-notes="<?php echo e($order['notes'] ?? ''); ?>"
                                    >
                                        Duplicar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="assets/js/pages/crm-modal-forms.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('order-form');
    if (!form) return;

    var clientSelect = document.getElementById('order-client');
    var briefSelect = document.getElementById('order-brief');
    var numberInput = document.getElementById('order-number');
    var dateInput = document.getElementById('order-date');
    var statusSelect = document.getElementById('order-status');
    var totalInput = document.getElementById('order-total');
    var currencySelect = document.getElementById('order-currency');
    var notesInput = document.getElementById('order-notes');
    var itemsTable = document.querySelector('#order-items-table tbody');
    var addItemButton = document.getElementById('add-order-item');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }).format(amount || 0);
    }

    function recalcOrderTotal() {
        var total = 0;
        itemsTable.querySelectorAll('.item-row').forEach(function (row) {
            var qty = parseFloat(row.querySelector('.order-qty').value) || 0;
            var price = parseFloat(row.querySelector('.order-price').value) || 0;
            var subtotal = qty * price;
            total += subtotal;
            row.querySelector('.order-subtotal').textContent = formatCurrency(subtotal);
        });
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }
    }

    function attachItemHandlers(row) {
        row.querySelectorAll('input, select').forEach(function (input) {
            input.addEventListener('input', recalcOrderTotal);
            input.addEventListener('change', function () {
                if (input.classList.contains('order-product')) {
                    var price = parseFloat(input.selectedOptions[0]?.dataset?.price || 0);
                    var priceInput = row.querySelector('.order-price');
                    if (priceInput && (!priceInput.value || parseFloat(priceInput.value) === 0)) {
                        priceInput.value = price;
                    }
                }
                recalcOrderTotal();
            });
        });
        row.querySelector('.remove-row')?.addEventListener('click', function () {
            if (row.parentElement.children.length > 1) {
                row.remove();
                recalcOrderTotal();
            }
        });
    }

    if (itemsTable) {
        itemsTable.querySelectorAll('.item-row').forEach(attachItemHandlers);
    }
    if (addItemButton) {
        addItemButton.addEventListener('click', function () {
            var row = itemsTable.querySelector('.item-row').cloneNode(true);
            row.querySelectorAll('select').forEach(function (select) {
                select.value = '';
            });
            row.querySelectorAll('input').forEach(function (input) {
                input.value = input.classList.contains('order-qty') ? '1' : '0';
            });
            attachItemHandlers(row);
            itemsTable.appendChild(row);
            recalcOrderTotal();
        });
    }

    document.querySelectorAll('.js-order-duplicate').forEach(function (button) {
        button.addEventListener('click', function () {
            if (clientSelect) {
                clientSelect.value = button.getAttribute('data-order-client') || '';
                clientSelect.dispatchEvent(new Event('change'));
            }
            if (briefSelect) {
                briefSelect.value = button.getAttribute('data-order-brief') || '';
            }
            if (numberInput) {
                numberInput.value = button.getAttribute('data-order-number') || '';
            }
            if (dateInput) {
                dateInput.value = button.getAttribute('data-order-date') || '';
            }
            if (statusSelect) {
                statusSelect.value = button.getAttribute('data-order-status') || 'pendiente';
            }
            recalcOrderTotal();
            if (currencySelect) {
                currencySelect.value = button.getAttribute('data-order-currency') || 'CLP';
            }
            if (notesInput) {
                notesInput.value = button.getAttribute('data-order-notes') || '';
            }

            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    recalcOrderTotal();
});
</script>
