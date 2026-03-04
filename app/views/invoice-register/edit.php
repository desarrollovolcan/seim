<?php
$record = $record ?? [];
$items = !empty($items) ? $items : [['item_type' => 'producto', 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'observation' => '']];
?>
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar factura</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=invoice-register/update" id="invoiceRegisterForm">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)$record['id']; ?>">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Tipo documento</label><select name="document_type" class="form-select" required><option value="factura" <?php echo ($record['document_type'] ?? '') === 'factura' ? 'selected' : ''; ?>>Factura</option><option value="boleta" <?php echo ($record['document_type'] ?? '') === 'boleta' ? 'selected' : ''; ?>>Boleta</option><option value="servicio" <?php echo ($record['document_type'] ?? '') === 'servicio' ? 'selected' : ''; ?>>Servicio</option></select></div>
                <div class="col-md-3"><label class="form-label">N° Factura</label><input type="text" name="invoice_number" class="form-control" value="<?php echo e($record['invoice_number'] ?? ''); ?>" required></div>
                <div class="col-md-3"><label class="form-label">Fecha emisión</label><input type="date" name="invoice_date" class="form-control" value="<?php echo e($record['invoice_date'] ?? date('Y-m-d')); ?>" required></div>
                <div class="col-md-3"><label class="form-label">Vencimiento</label><input type="date" name="due_date" class="form-control" value="<?php echo e($record['due_date'] ?? ''); ?>"></div>
                <div class="col-md-6"><label class="form-label">Proveedor</label><select class="form-select" id="supplierSelect"><option value="">Selecciona proveedor</option><?php foreach (($suppliers ?? []) as $supplier): ?><option value="<?php echo (int)$supplier['id']; ?>" data-name="<?php echo e($supplier['name'] ?? ''); ?>" data-rut="<?php echo e($supplier['tax_id'] ?? ''); ?>"><?php echo e($supplier['name'] ?? ''); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4"><label class="form-label">Nombre proveedor</label><input type="text" name="supplier_name" id="supplierName" class="form-control" value="<?php echo e($record['supplier_name'] ?? ''); ?>" required></div>
                <div class="col-md-2"><label class="form-label">RUT proveedor</label><input type="text" name="supplier_tax_id" id="supplierTaxId" class="form-control" value="<?php echo e($record['supplier_tax_id'] ?? ''); ?>"></div>
                <div class="col-md-2"><label class="form-label">Moneda</label><select name="currency" class="form-select" required><?php foreach (['CLP', 'USD', 'PEN'] as $currency): ?><option value="<?php echo $currency; ?>" <?php echo ($record['currency'] ?? 'CLP') === $currency ? 'selected' : ''; ?>><?php echo $currency; ?></option><?php endforeach; ?></select></div>
            </div>

            <div class="table-responsive mt-3"><table class="table table-bordered align-middle" id="itemsTable"><thead><tr><th style="width:14%">Catálogo</th><th style="width:10%">Tipo</th><th style="width:24%">Descripción</th><th style="width:10%">Cantidad</th><th style="width:14%">Precio unitario</th><th style="width:14%">Subtotal</th><th style="width:14%">Observación</th><th style="width:6%"></th></tr></thead><tbody id="itemsBody">
                <?php foreach ($items as $item): ?>
                    <tr class="item-row">
                        <td><select class="form-select catalog-select"><option value="">Seleccionar...</option><?php foreach (($catalogProducts ?? []) as $product): ?><?php $classification = ($product['classification'] ?? '') === 'producto' ? 'producto' : 'servicio'; ?><option value="<?php echo (int)$product['id']; ?>" data-name="<?php echo e($product['name'] ?? ''); ?>" data-type="<?php echo e($classification); ?>" data-price="<?php echo e((string)((float)($product['suggested_price'] ?? 0))); ?>"><?php echo e($product['name'] ?? ''); ?></option><?php endforeach; ?></select></td>
                        <td><select name="item_type[]" class="form-select item-type-select"><option value="producto" <?php echo ($item['item_type'] ?? '') === 'producto' ? 'selected' : ''; ?>>Producto</option><option value="servicio" <?php echo ($item['item_type'] ?? '') === 'servicio' ? 'selected' : ''; ?>>Servicio</option></select></td>
                        <td><input type="text" name="item_description[]" class="form-control description-input" value="<?php echo e($item['description'] ?? ''); ?>" required></td>
                        <td><input type="number" min="0.01" step="0.01" name="item_quantity[]" class="form-control qty-input" value="<?php echo e((string)($item['quantity'] ?? '1.00')); ?>" required></td>
                        <td><input type="number" min="0" step="0.01" name="item_unit_price[]" class="form-control price-input" value="<?php echo e((string)($item['unit_price'] ?? '0')); ?>" required></td>
                        <td><input type="text" class="form-control subtotal-input" value="0.00" readonly></td>
                        <td><input type="text" name="item_observation[]" class="form-control" value="<?php echo e($item['observation'] ?? ''); ?>"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">✕</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody></table></div>

            <div class="row g-3"><div class="col-md-6"><label class="form-label">Observación general</label><textarea name="notes" class="form-control" rows="2"><?php echo e($record['notes'] ?? ''); ?></textarea></div><div class="col-md-2"><label class="form-label">Neto</label><input type="text" id="netAmount" class="form-control" value="0.00" readonly></div><div class="col-md-2"><label class="form-label">IVA (19%)</label><input type="text" id="taxAmount" class="form-control" value="0.00" readonly></div><div class="col-md-2"><label class="form-label">Total</label><input type="text" id="totalAmount" class="form-control fw-bold" value="0.00" readonly></div></div>
            <div class="mt-3 d-flex gap-2 justify-content-end"><a href="index.php?route=invoice-register" class="btn btn-light">Cancelar</a><button type="button" class="btn btn-outline-primary" id="addRowBtn">Agregar fila</button><button type="submit" class="btn btn-primary">Guardar cambios</button></div>
        </form>
    </div>
</div>
<script>
(() => {
    const body = document.getElementById('itemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const supplierSelect = document.getElementById('supplierSelect');
    const supplierName = document.getElementById('supplierName');
    const supplierTaxId = document.getElementById('supplierTaxId');
    const recalc = () => {
        let net = 0;
        body.querySelectorAll('.item-row').forEach((row) => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value || '0');
            const price = parseFloat(row.querySelector('.price-input')?.value || '0');
            const subtotal = qty * price;
            row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
            net += subtotal;
        });
        const tax = net * 0.19;
        document.getElementById('netAmount').value = net.toFixed(2);
        document.getElementById('taxAmount').value = tax.toFixed(2);
        document.getElementById('totalAmount').value = (net + tax).toFixed(2);
    };
    const bindRow = (row) => {
        const catalogSelect = row.querySelector('.catalog-select');
        const typeSelect = row.querySelector('.item-type-select');
        const descriptionInput = row.querySelector('.description-input');
        const priceInput = row.querySelector('.price-input');
        catalogSelect?.addEventListener('change', () => {
            const selected = catalogSelect.options[catalogSelect.selectedIndex];
            if (!selected || !selected.value) return;
            if (!descriptionInput.value.trim()) descriptionInput.value = selected.dataset.name || '';
            if (!parseFloat(priceInput.value || '0')) priceInput.value = selected.dataset.price || '0';
            if (selected.dataset.type === 'producto' || selected.dataset.type === 'servicio') typeSelect.value = selected.dataset.type;
            recalc();
        });
        row.querySelectorAll('.qty-input, .price-input').forEach((input) => input.addEventListener('input', recalc));
        row.querySelector('.remove-row')?.addEventListener('click', () => {
            if (body.querySelectorAll('.item-row').length === 1) return;
            row.remove();
            recalc();
        });
    };
    addRowBtn.addEventListener('click', () => {
        const row = body.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach((input) => {
            if (input.classList.contains('qty-input')) input.value = '1.00';
            else if (input.classList.contains('subtotal-input')) input.value = '0.00';
            else if (input.classList.contains('price-input')) input.value = '0';
            else input.value = '';
        });
        row.querySelector('.catalog-select').selectedIndex = 0;
        row.querySelector('.item-type-select').value = 'producto';
        body.appendChild(row);
        bindRow(row);
        recalc();
    });
    supplierSelect?.addEventListener('change', () => {
        const selected = supplierSelect.options[supplierSelect.selectedIndex];
        if (!selected || !selected.value) return;
        supplierName.value = selected.dataset.name || '';
        supplierTaxId.value = selected.dataset.rut || '';
    });
    body.querySelectorAll('.item-row').forEach(bindRow);
    recalc();
})();
</script>
