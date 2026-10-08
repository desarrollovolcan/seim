<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Inventario de productos</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=products/bulk" class="btn btn-success">Carga masiva</a>
            <a href="index.php?route=products/create" class="btn btn-primary">Nuevo producto</a>
        </div>
    </div>
    <div class="card-body">
        <form method="get" action="index.php" class="row g-2 mb-3">
            <input type="hidden" name="route" value="products">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre, SKU o descripción" value="<?php echo e($filters['search'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <select name="family_id" class="form-select form-select-sm">
                    <option value="0">Todas las familias</option>
                    <?php foreach (($families ?? []) as $family): ?>
                        <option value="<?php echo (int)$family['id']; ?>" <?php echo ((int)($filters['family_id'] ?? 0) === (int)$family['id']) ? 'selected' : ''; ?>>
                            <?php echo e($family['name'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="subfamily_id" class="form-select form-select-sm">
                    <option value="0">Todas las subfamilias</option>
                    <?php foreach (($subfamilies ?? []) as $subfamily): ?>
                        <option value="<?php echo (int)$subfamily['id']; ?>" <?php echo ((int)($filters['subfamily_id'] ?? 0) === (int)$subfamily['id']) ? 'selected' : ''; ?>>
                            <?php echo e($subfamily['name'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="0">Todos los proveedores</option>
                    <?php foreach (($suppliers ?? []) as $supplier): ?>
                        <option value="<?php echo (int)$supplier['id']; ?>" <?php echo ((int)($filters['supplier_id'] ?? 0) === (int)$supplier['id']) ? 'selected' : ''; ?>>
                            <?php echo e($supplier['name'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
                <a href="index.php?route=products" class="btn btn-sm btn-light w-100">Limpiar</a>
            </div>
        </form>

        <form method="post" action="index.php?route=products/bulk-assign">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="card border mb-3">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select name="bulk_family_id" class="form-select form-select-sm">
                                <option value="0">Asignar familia...</option>
                                <?php foreach (($families ?? []) as $family): ?>
                                    <option value="<?php echo (int)$family['id']; ?>"><?php echo e($family['name'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="bulk_subfamily_id" class="form-select form-select-sm">
                                <option value="0">Asignar subfamilia...</option>
                                <?php foreach (($subfamilies ?? []) as $subfamily): ?>
                                    <option value="<?php echo (int)$subfamily['id']; ?>"><?php echo e($subfamily['name'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="bulk_supplier_id" class="form-select form-select-sm">
                                <option value="0">Asignar proveedor...</option>
                                <?php foreach (($suppliers ?? []) as $supplier): ?>
                                    <option value="<?php echo (int)$supplier['id']; ?>"><?php echo e($supplier['name'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Aplicar a seleccionados</button>
                        </div>
                    </div>
                </div>
            </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 32px;"><input type="checkbox" id="selectAllProducts" class="form-check-input"></th>
                        <th style="width: 65px;">ID</th>
                        <th style="min-width: 140px; max-width: 200px;">Producto</th>
                        <th style="max-width: 180px;">Descripción</th>
                        <th style="width: 95px;">SKU</th>
                        <th style="max-width: 130px;">Familia</th>
                        <th style="max-width: 140px;">Subfamilia</th>
                        <th style="max-width: 130px;">Proveedor</th>
                        <th class="text-end" style="width: 85px;">Precio</th>
                        <th class="text-end" style="width: 70px;">Stock</th>
                        <th style="width: 75px;">Estado</th>
                        <th class="text-end" style="width: 80px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted">No se encontraron productos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $status = $product['status'] ?? 'activo';
                            $statusColor = match ($status) {
                                'activo' => 'success',
                                'inactivo' => 'secondary',
                                default => 'info',
                            };
                            ?>
                            <tr>
                                <td><input class="product-checkbox form-check-input" type="checkbox" name="product_ids[]" value="<?php echo (int)($product['id'] ?? 0); ?>"></td>
                                <td><?php echo render_id_badge($product['id'] ?? null); ?></td>
                                <td>
                                    <span class="fw-medium text-body d-inline-block text-truncate align-middle" style="max-width: 200px;" title="<?php echo e($product['name'] ?? ''); ?>">
                                        <?php echo e($product['name'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-muted fs-12 align-middle" style="max-width: 180px;" title="<?php echo e($product['description'] ?? ''); ?>">
                                        <?php echo e($product['description'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td class="nowrap">
                                    <span class="font-monospace text-secondary fs-12"><?php echo e($product['sku'] ?? '—'); ?></span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-secondary fs-12 align-middle" style="max-width: 130px;" title="<?php echo e($product['family_name'] ?? ''); ?>">
                                        <?php echo e($product['family_name'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-secondary fs-12 align-middle" style="max-width: 140px;" title="<?php echo e($product['subfamily_name'] ?? ''); ?>">
                                        <?php echo e($product['subfamily_name'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-secondary fs-12 align-middle" style="max-width: 130px;" title="<?php echo e($product['supplier_name'] ?? ''); ?>">
                                        <?php echo e($product['supplier_name'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td class="text-end nowrap">
                                    <span class="fw-semibold text-body fs-12"><?php echo e(format_currency((float)($product['price'] ?? 0), 0)); ?></span>
                                </td>
                                <td class="text-end nowrap">
                                    <span class="badge bg-light text-body fw-semibold fs-11">
                                        <?php echo (int)($product['stock'] ?? 0); ?>
                                        <?php if (!empty($product['stock_min']) && (int)$product['stock'] <= (int)$product['stock_min']): ?>
                                            <span class="text-danger ms-1" title="Stock bajo">●</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="nowrap">
                                    <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?> fs-11">
                                        <?php echo e(ucfirst($status)); ?>
                                    </span>
                                </td>
                                <td class="text-end nowrap">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm py-0 px-2 dropdown-toggle fs-12" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li><a class="dropdown-item" href="index.php?route=products/edit&id=<?php echo (int)$product['id']; ?>"><i class="ti ti-edit me-1"></i>Editar</a></li>
                                            <li>
                                                <form method="post" action="index.php?route=products/delete" onsubmit="return confirm('¿Eliminar este producto?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button text-danger"><i class="ti ti-trash me-1"></i>Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllProducts');
    if (!selectAll) return;
    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.product-checkbox').forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
    });
});
</script>
