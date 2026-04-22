<div class="card">
    <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h4 class="card-title mb-1">Carga masiva de productos</h4>
            <p class="text-muted mb-0">Importa productos mediante un archivo CSV compatible con Excel.</p>
        </div>
        <a href="index.php?route=products/bulk-template" class="btn btn-success">Descargar plantilla de ejemplo</a>
    </div>
    <div class="card-body">
        <form method="post" action="index.php?route=products/bulk-start" enctype="multipart/form-data" class="row g-3">
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
                <input type="file" name="bulk_file" class="form-control" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                <div class="form-text">Puedes subir CSV o XLSX (primera hoja). Para evitar timeouts, recomendamos bloques de hasta 500 filas.</div>
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Procesar carga masiva</button>
            </div>
        </form>

        <?php if (!empty($bulkJobId ?? '')): ?>
            <hr class="my-4">
            <div class="card border">
                <div class="card-body">
                    <h6 class="mb-3">Progreso de carga masiva</h6>
                    <div class="progress mb-2" style="height: 18px;">
                        <div id="bulk-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                    </div>
                    <div id="bulk-progress-text" class="small text-muted">Iniciando...</div>
                </div>
            </div>
            <script>
                (function () {
                    const jobId = <?php echo json_encode($bulkJobId); ?>;
                    const csrf = <?php echo json_encode(csrf_token()); ?>;
                    const bar = document.getElementById('bulk-progress-bar');
                    const text = document.getElementById('bulk-progress-text');

                    async function step() {
                        const body = new URLSearchParams();
                        body.set('csrf_token', csrf);
                        body.set('job_id', jobId);
                        const response = await fetch('index.php?route=products/bulk-process', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                            body: body.toString()
                        });
                        const result = await response.json();
                        if (!result.ok) {
                            text.textContent = result.message || 'Error en carga masiva.';
                            return;
                        }
                        bar.style.width = `${result.progress}%`;
                        bar.textContent = `${result.progress}%`;
                        text.textContent = `Procesados: ${result.processed}/${result.total} - Creados: ${result.created}`;
                        if (result.done) {
                            bar.classList.remove('progress-bar-animated');
                            window.location.href = 'index.php?route=products/bulk';
                            return;
                        }
                        setTimeout(step, 250);
                    }

                    step().catch(() => {
                        text.textContent = 'No fue posible continuar el procesamiento.';
                    });
                })();
            </script>
        <?php endif; ?>

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
