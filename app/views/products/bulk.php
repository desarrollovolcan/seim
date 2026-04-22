<div class="card">
    <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h4 class="card-title mb-1">Carga masiva de productos</h4>
            <p class="text-muted mb-0">Importa productos mediante un archivo CSV compatible con Excel.</p>
        </div>
        <a href="index.php?route=products/bulk-template" class="btn btn-success">Descargar plantilla de ejemplo</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=products/bulk-store" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="col-md-4">
                <label class="form-label">Empresa competencia (de la sesión activa)</label>
                <select name="default_competitor_company_id" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?php echo (int)$company['id']; ?>">
                            <?php echo e($company['name'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Se aplica a todas las filas importadas. No se leerá desde Excel.</div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Archivo CSV</label>
                <input type="file" name="bulk_file" class="form-control" accept=".csv,text/csv" required>
                <div class="form-text">Usa la plantilla y guarda en formato CSV UTF-8 desde Excel.</div>
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Procesar carga masiva</button>
            </div>
        </form>

        <hr class="my-4">

        <h5 class="mb-3">Columnas requeridas en el archivo</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Columna</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>name</code></td><td>Nombre del producto (obligatorio).</td></tr>
                    <tr><td><code>sku</code></td><td>Código SKU del producto (obligatorio).</td></tr>
                    <tr><td><code>supplier_code</code>, <code>family_code</code>, <code>subfamily_code</code></td><td>Opcionales. Si familia/subfamilia no existen, se crearán automáticamente durante la importación.</td></tr>
                    <tr><td><code>description</code>, <code>supplier_price</code>, <code>competition_price</code>, <code>price</code>, <code>cost</code>, <code>stock</code>, <code>stock_min</code>, <code>status</code></td><td>Campos opcionales.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3 mb-0">
            Los códigos <strong>competition_code</strong> y <strong>supplier_code</strong> se generan automáticamente con el mismo criterio de la creación manual.
        </div>
        <div class="alert alert-secondary mt-3 mb-0">
            También puedes cargar <strong>proveedor/familia/subfamilia por nombre</strong> usando columnas alternativas como
            <code>supplier_name</code>, <code>family_name</code> y <code>subfamily_name</code>.
        </div>
    </div>
</div>
