<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Datos del producto fabricado</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=produced-products/store">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio de venta</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Costo base</label>
                    <input type="number" name="cost" class="form-control" step="0.01" min="0" value="0" id="base-cost-input">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="activo" selected>Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock inicial</label>
                    <input type="number" name="stock" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock mínimo</label>
                    <input type="number" name="stock_min" class="form-control" min="0" value="0">
                </div>
                <div class="col-12">
                    <label class="form-label">Insumos necesarios para fabricar</label>
                    <p class="text-muted small mb-2">Selecciona los productos normales y su cantidad para calcular el costo base del producto fabricado.</p>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="material-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Producto</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 20%;">Costo unitario</th>
                                    <th class="text-end" style="width: 15%;">Subtotal</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="material-row">
                                    <td>
                                        <select name="input_product_id[]" class="form-select form-select-sm material-product">
                                            <option value="">Selecciona</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo (int)$product['id']; ?>" data-cost="<?php echo e((float)($product['cost'] ?? 0)); ?>">
                                                    <?php echo e($product['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="input_quantity[]" class="form-control form-control-sm material-qty" min="1" value="1"></td>
                                    <td><input type="number" name="input_unit_cost[]" class="form-control form-control-sm material-cost" step="0.01" min="0" value="0"></td>
                                    <td class="text-end material-subtotal fw-semibold">0</td>
                                    <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-material">Agregar insumo</button>
                        <span class="fw-semibold">Costo base calculado: <span id="materials-total"><?php echo format_currency(0); ?></span></span>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Notas internas o ficha corta del producto"></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <a href="index.php?route=produced-products" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar producto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        const tableBody = document.querySelector('#material-items-table tbody');
        const addMaterial = document.getElementById('add-material');
        const totalDisplay = document.getElementById('materials-total');
        const baseCostInput = document.getElementById('base-cost-input');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }).format(amount || 0);
        }

        function recalc() {
            let total = 0;
            tableBody.querySelectorAll('.material-row').forEach((row) => {
                const qty = parseFloat(row.querySelector('.material-qty').value) || 0;
                const cost = parseFloat(row.querySelector('.material-cost').value) || 0;
                const subtotal = qty * cost;
                total += subtotal;
                row.querySelector('.material-subtotal').innerText = formatCurrency(subtotal);
            });
            totalDisplay.innerText = formatCurrency(total);
            if (baseCostInput) {
                baseCostInput.value = total.toFixed(2);
            }
        }

        function attachRowHandlers(row) {
            row.querySelectorAll('input, select').forEach((input) => {
                input.addEventListener('input', recalc);
                input.addEventListener('change', () => {
                    if (input.classList.contains('material-product')) {
                        const cost = parseFloat(input.selectedOptions[0]?.dataset?.cost || 0);
                        const costInput = row.querySelector('.material-cost');
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

        addMaterial?.addEventListener('click', () => {
            const row = tableBody.querySelector('.material-row').cloneNode(true);
            row.querySelectorAll('select').forEach((select) => {
                select.value = '';
            });
            row.querySelectorAll('input').forEach((input) => {
                input.value = input.classList.contains('material-qty') ? '1' : '0';
            });
            attachRowHandlers(row);
            tableBody.appendChild(row);
            recalc();
        });

        tableBody.querySelectorAll('.material-row').forEach(attachRowHandlers);
        recalc();
    })();
</script>
