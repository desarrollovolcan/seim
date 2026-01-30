<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Editar producto fabricado</h4>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=produced-products/update">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo (int)($product['id'] ?? 0); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($product['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="<?php echo e($product['sku'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Precio de venta</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?php echo e((float)($product['price'] ?? 0)); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Costo base</label>
                    <input type="number" name="cost" class="form-control" step="0.01" min="0" value="<?php echo e((float)($product['cost'] ?? 0)); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="activo" <?php echo ($product['status'] ?? 'activo') === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo ($product['status'] ?? '') === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" min="0" value="<?php echo e((int)($product['stock'] ?? 0)); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stock mínimo</label>
                    <input type="number" name="stock_min" class="form-control" min="0" value="<?php echo e((int)($product['stock_min'] ?? 0)); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($product['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <a href="index.php?route=produced-products" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar producto</button>
                </div>
            </div>
        </form>
    </div>
</div>
