<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Nueva producción</h4>
                <div class="d-flex gap-2">
                    <a href="index.php?route=produced-products/create" class="btn btn-soft-primary btn-sm">Crear producto fabricado</a>
                    <a href="index.php?route=products/create" class="btn btn-soft-secondary btn-sm">Crear producto normal</a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?route=production/store" id="production-form">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha de producción</label>
                            <input type="date" name="production_date" class="form-control" value="<?php echo e($today); ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Notas internas</label>
                            <input type="text" name="notes" class="form-control" placeholder="Observaciones de la producción">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Productos finales</label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="output-items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60%;">Producto</th>
                                            <th style="width: 25%;">Cantidad</th>
                                            <th style="width: 15%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="output-row">
                                            <td>
                                                <select name="output_product_id[]" class="form-select form-select-sm">
                                                    <option value="">Selecciona</option>
                                                    <?php foreach ($producedProducts as $product): ?>
                                                        <option value="<?php echo (int)$product['id']; ?>"><?php echo e($product['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="output_quantity[]" class="form-control form-control-sm output-qty" min="1" value="1"></td>
                                            <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-output">Agregar producto final</button>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Materias primas / insumos</label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="input-items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 45%;">Producto</th>
                                            <th style="width: 15%;">Cantidad</th>
                                            <th style="width: 20%;">Costo unitario</th>
                                            <th class="text-end" style="width: 15%;">Subtotal</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="input-row">
                                            <td>
                                                <select name="input_product_id[]" class="form-select form-select-sm input-product">
                                                    <option value="">Selecciona</option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?php echo (int)$product['id']; ?>" data-cost="<?php echo e((float)($product['cost'] ?? 0)); ?>">
                                                            <?php echo e($product['name']); ?> (Stock: <?php echo (int)($product['stock'] ?? 0); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="input_quantity[]" class="form-control form-control-sm input-qty" min="1" value="1"></td>
                                            <td><input type="number" name="input_unit_cost[]" class="form-control form-control-sm input-cost" step="0.01" min="0" value="0"></td>
                                            <td class="text-end input-subtotal fw-semibold">0</td>
                                            <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-input">Agregar insumo</button>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gastos adicionales</label>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="expense-items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 70%;">Descripción</th>
                                            <th class="text-end" style="width: 25%;">Monto</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="expense-row">
                                            <td><input type="text" name="expense_description[]" class="form-control form-control-sm" placeholder="Fletes, energía, etc."></td>
                                            <td><input type="number" name="expense_amount[]" class="form-control form-control-sm expense-amount" step="0.01" min="0" value="0"></td>
                                            <td><button type="button" class="btn btn-link text-danger p-0 remove-row">✕</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-expense">Agregar gasto</button>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <span>Costo de insumos</span>
                                        <strong id="materials-total"><?php echo format_currency(0); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Gastos adicionales</span>
                                        <strong id="expenses-total"><?php echo format_currency(0); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Costo total</span>
                                        <strong id="total-cost"><?php echo format_currency(0); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span>Cantidad total producida</span>
                                        <strong id="total-output">0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Costo unitario estimado</span>
                                        <strong id="unit-cost"><?php echo format_currency(0); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-actions">
                                <a href="index.php?route=production" class="btn btn-light">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar producción</button>
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
        const outputTable = document.querySelector('#output-items-table tbody');
        const inputTable = document.querySelector('#input-items-table tbody');
        const expenseTable = document.querySelector('#expense-items-table tbody');
        const addOutput = document.getElementById('add-output');
        const addInput = document.getElementById('add-input');
        const addExpense = document.getElementById('add-expense');
        const materialsTotal = document.getElementById('materials-total');
        const expensesTotal = document.getElementById('expenses-total');
        const totalCost = document.getElementById('total-cost');
        const totalOutput = document.getElementById('total-output');
        const unitCost = document.getElementById('unit-cost');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }).format(amount || 0);
        }

        function recalc() {
            let materials = 0;
            inputTable.querySelectorAll('.input-row').forEach((row) => {
                const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                const cost = parseFloat(row.querySelector('.input-cost').value) || 0;
                const subtotal = qty * cost;
                materials += subtotal;
                row.querySelector('.input-subtotal').innerText = formatCurrency(subtotal);
            });

            let expenses = 0;
            expenseTable.querySelectorAll('.expense-row').forEach((row) => {
                const amount = parseFloat(row.querySelector('.expense-amount').value) || 0;
                expenses += amount;
            });

            let outputs = 0;
            outputTable.querySelectorAll('.output-row').forEach((row) => {
                outputs += parseFloat(row.querySelector('.output-qty').value) || 0;
            });

            const total = materials + expenses;
            const unit = outputs > 0 ? total / outputs : 0;
            materialsTotal.innerText = formatCurrency(materials);
            expensesTotal.innerText = formatCurrency(expenses);
            totalCost.innerText = formatCurrency(total);
            totalOutput.innerText = outputs;
            unitCost.innerText = formatCurrency(unit);
        }

        function attachRowHandlers(row) {
            row.querySelectorAll('input, select').forEach((input) => {
                input.addEventListener('input', recalc);
                input.addEventListener('change', () => {
                    if (input.classList.contains('input-product')) {
                        const cost = parseFloat(input.selectedOptions[0]?.dataset?.cost || 0);
                        const costInput = row.querySelector('.input-cost');
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

        addOutput?.addEventListener('click', () => {
            const row = outputTable.querySelector('.output-row').cloneNode(true);
            row.querySelectorAll('select, input').forEach((input) => {
                input.value = input.tagName === 'INPUT' ? '1' : '';
            });
            attachRowHandlers(row);
            outputTable.appendChild(row);
            recalc();
        });

        addInput?.addEventListener('click', () => {
            const row = inputTable.querySelector('.input-row').cloneNode(true);
            row.querySelectorAll('select').forEach((select) => {
                select.value = '';
            });
            row.querySelectorAll('input').forEach((input) => {
                input.value = input.classList.contains('input-qty') ? '1' : '0';
            });
            attachRowHandlers(row);
            inputTable.appendChild(row);
            recalc();
        });

        addExpense?.addEventListener('click', () => {
            const row = expenseTable.querySelector('.expense-row').cloneNode(true);
            row.querySelectorAll('input').forEach((input) => {
                input.value = input.type === 'text' ? '' : '0';
            });
            attachRowHandlers(row);
            expenseTable.appendChild(row);
            recalc();
        });

        document.querySelectorAll('.output-row, .input-row, .expense-row').forEach(attachRowHandlers);
        recalc();
    })();
</script>
