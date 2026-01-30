<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Nueva orden de compra</h4>
                <a href="index.php?route=products/create" class="btn btn-soft-secondary btn-sm">Crear producto</a>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=purchase-orders/store" id="purchase-order-form">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Proveedor</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Selecciona proveedor</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?php echo (int)$supplier['id']; ?>"><?php echo e($supplier['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="order_date" class="form-control" value="<?php echo e($today); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select name="status" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Referencia / N° OC</label>
                            <input type="text" name="reference" class="form-control" placeholder="Orden de compra">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Productos</label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="purchase-order-items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Producto</th>
                                            <th style="width: 15%;">Cantidad</th>
                                            <th style="width: 20%;">Costo unitario</th>
                                            <th class="text-end" style="width: 20%;">Subtotal</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="item-row">
                                            <td>
                                                <select name="product_id[]" class="form-select form-select-sm product-select">
                                                    <option value="">Selecciona</option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?php echo (int)$product['id']; ?>" data-cost="<?php echo e((float)($product['cost'] ?? $product['price'] ?? 0)); ?>">
                                                            <?php echo e($product['name']); ?> (Stock: <?php echo (int)($product['stock'] ?? 0); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="quantity[]" class="form-control form-control-sm quantity-input" min="1" value="1"></td>
                                            <td><input type="number" name="unit_cost[]" class="form-control form-control-sm cost-input" step="0.01" min="0" value="0"></td>
                                            <td class="text-end item-subtotal fw-semibold">0</td>
                                            <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-item">Agregar producto</button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notas internas</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Observaciones de la orden"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column align-items-end gap-2">
                                <div class="d-flex justify-content-between w-100">
                                    <span>Total</span>
                                    <strong id="total-display"><?php echo format_currency(0); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-actions">
                                <a href="index.php?route=purchase-orders" class="btn btn-light">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar orden</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const tableBody = document.querySelector('#purchase-order-items-table tbody');
        const addButton = document.querySelector('#add-item');
        const totalDisplay = document.getElementById('total-display');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }).format(amount || 0);
        }

        function recalc() {
            let total = 0;
            tableBody.querySelectorAll('.item-row').forEach((row) => {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                const subtotal = qty * cost;
                total += subtotal;
                row.querySelector('.item-subtotal').innerText = formatCurrency(subtotal);
            });
            totalDisplay.innerText = formatCurrency(total);
        }

        function attachRowHandlers(row) {
            row.querySelectorAll('input, select').forEach((input) => {
                input.addEventListener('input', recalc);
                input.addEventListener('change', () => {
                    if (input.classList.contains('product-select')) {
                        const cost = parseFloat(input.selectedOptions[0]?.dataset?.cost || 0);
                        const costInput = row.querySelector('.cost-input');
                        if (costInput && (!costInput.value || parseFloat(costInput.value) === 0)) {
                            costInput.value = cost;
                        }
                    }
                    recalc();
                });
            });
            row.querySelector('.remove-row')?.addEventListener('click', () => {
                if (row.parentElement.children.length > 1) {
                    row.remove();
                    recalc();
                }
            });
        }

        addButton?.addEventListener('click', () => {
            const row = tableBody.querySelector('.item-row').cloneNode(true);
            row.querySelectorAll('select').forEach((select) => {
                select.value = '';
            });
            row.querySelectorAll('input').forEach((input) => {
                input.value = input.classList.contains('quantity-input') ? '1' : '0';
            });
            attachRowHandlers(row);
            tableBody.appendChild(row);
            recalc();
        });

        document.querySelectorAll('.item-row').forEach(attachRowHandlers);
        recalc();
    })();
</script>
