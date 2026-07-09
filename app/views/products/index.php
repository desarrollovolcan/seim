<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Inventario de productos</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=products/bulk" class="btn btn-success">Carga masiva</a>
            <a href="index.php?route=products/create" class="btn btn-primary">Nuevo producto</a>
        </div>
    </div>
    <div class="card-body">
        <form method="get" action="apps-productos.php" class="row g-2 mb-3">
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
                <a href="apps-productos.php" class="btn btn-sm btn-light w-100">Limpiar</a>
            </div>
        </form>

        <form method="post" action="index.php?route=products/bulk-assign">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="filter_search" value="<?php echo e($filters['search'] ?? ''); ?>">
            <input type="hidden" name="filter_family_id" value="<?php echo (int)($filters['family_id'] ?? 0); ?>">
            <input type="hidden" name="filter_subfamily_id" value="<?php echo (int)($filters['subfamily_id'] ?? 0); ?>">
            <input type="hidden" name="filter_supplier_id" value="<?php echo (int)($filters['supplier_id'] ?? 0); ?>">
            <div class="card border mb-3">
                <div class="card-body py-2">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-2">
                        <div>
                            <div class="fw-semibold small">Asignación por lotes</div>
                            <div class="text-muted small">Asocia categorías (familias), subcategorías y proveedores a varios productos en una sola acción.</div>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Alcance de asignación por lote">
                            <input type="radio" class="btn-check" name="bulk_scope" id="bulkScopeSelected" value="selected" checked>
                            <label class="btn btn-outline-secondary" for="bulkScopeSelected">Seleccionados</label>
                            <input type="radio" class="btn-check" name="bulk_scope" id="bulkScopeFiltered" value="filtered">
                            <label class="btn btn-outline-secondary" for="bulkScopeFiltered">Todos los filtrados (<?php echo count($products ?? []); ?>)</label>
                        </div>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select name="bulk_family_id" class="form-select form-select-sm">
                                <option value="0">Asignar categoría/familia...</option>
                                <?php foreach (($families ?? []) as $family): ?>
                                    <option value="<?php echo (int)$family['id']; ?>"><?php echo e($family['name'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="bulk_subfamily_id" class="form-select form-select-sm">
                                <option value="0">Asignar subcategoría/subfamilia...</option>
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
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Aplicar por lote</button>
                        </div>
                    </div>
                </div>
            </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm align-middle small">
                <thead>
                    <tr>
                        <th style="width:1%;"><input type="checkbox" id="selectAllProducts"></th>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>SKU</th>
                        <th>Familia</th>
                        <th>Subfamilia</th>
                        <th>Proveedor</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Stock</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
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
                            <td><input class="product-checkbox" type="checkbox" name="product_ids[]" value="<?php echo (int)($product['id'] ?? 0); ?>"></td>
                            <td class="text-muted"><?php echo render_id_badge($product['id'] ?? null); ?></td>
                            <td><?php echo e($product['name'] ?? ''); ?></td>
                            <td class="text-muted" style="max-width: 320px;">
                                <span class="d-inline-block text-truncate align-middle" style="max-width: 320px;" title="<?php echo e($product['description'] ?? ''); ?>">
                                    <?php echo e($product['description'] ?? ''); ?>
                                </span>
                            </td>
                            <td><?php echo e($product['sku'] ?? ''); ?></td>
                            <td><?php echo e($product['family_name'] ?? ''); ?></td>
                            <td><?php echo e($product['subfamily_name'] ?? ''); ?></td>
                            <td><?php echo e($product['supplier_name'] ?? ''); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($product['price'] ?? 0), 0)); ?></td>
                            <td class="text-end">
                                <span class="badge bg-light text-body fw-semibold">
                                    <?php echo (int)($product['stock'] ?? 0); ?>
                                    <?php if (!empty($product['stock_min']) && (int)$product['stock'] <= (int)$product['stock_min']): ?>
                                        <span class="text-danger ms-1">(Bajo)</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm py-0 px-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="index.php?route=products/edit&id=<?php echo (int)$product['id']; ?>">Editar</a></li>
                                        <li>
                                            <form method="post" action="index.php?route=products/delete" onsubmit="return confirm('¿Eliminar este producto?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
                                                <button type="submit" class="dropdown-item dropdown-item-button text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
