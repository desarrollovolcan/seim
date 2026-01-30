<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Auditoría de formularios</h4>
                <p class="text-muted mb-0">Seguimiento de validaciones, homologación de datos y mejoras por módulo.</p>
            </div>
            <div class="card-body">
                <?php if (!empty($auditMissing)): ?>
                    <div class="alert alert-warning">No se encontró el checklist de formularios.</div>
                <?php endif; ?>
                <?php if (!empty($statusCounts)): ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ($statusCounts as $status => $count): ?>
                            <span class="badge bg-light text-dark border">
                                <?php echo e($status); ?>: <?php echo (int)$count; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th>Archivo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?php echo e($row['module']); ?></td>
                                        <td><code><?php echo e($row['file']); ?></code></td>
                                        <td><?php echo e($row['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Sin registros disponibles.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
