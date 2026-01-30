<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Botones de pago</h4>
        <span class="text-muted">Comparte estos enlaces con tus clientes</span>
    </div>
    <div class="card-body">
        <?php if (empty($invoices)): ?>
            <div class="text-muted">No hay facturas con saldo pendiente.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-centered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Emisión</th>
                            <th>Vencimiento</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Pendiente</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <?php
                            $status = $invoice['estado'] ?? 'pendiente';
                            $badgeClass = $status === 'pagada' ? 'success' : ($status === 'vencida' ? 'danger' : 'warning');
                            $detailUrl = 'index.php?route=invoices/details&id=' . (int)$invoice['id'];
                            ?>
                            <tr>
                                <td>#<?php echo e($invoice['numero'] ?? ''); ?></td>
                                <td><?php echo e($invoice['client_name'] ?? ''); ?></td>
                                <td><?php echo e($invoice['fecha_emision'] ?? ''); ?></td>
                                <td><?php echo e($invoice['fecha_vencimiento'] ?? ''); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($invoice['total'] ?? 0))); ?></td>
                                <td class="text-end"><?php echo e(format_currency((float)($invoice['pending_total'] ?? 0))); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $badgeClass; ?>-subtle text-<?php echo $badgeClass; ?>">
                                        <?php echo e(ucfirst($status)); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown actions-dropdown">
                                        <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="<?php echo e($detailUrl); ?>" target="_blank">Ver factura</a></li>
                                            <li>
                                                <button class="dropdown-item dropdown-item-button" type="button" data-copy-link="<?php echo e($detailUrl); ?>">
                                                    Copiar link
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('[data-copy-link]').forEach((button) => {
        button.addEventListener('click', async () => {
            const link = button.getAttribute('data-copy-link') || '';
            if (!link) {
                return;
            }
            try {
                await navigator.clipboard.writeText(window.location.origin + '/' + link);
                button.textContent = 'Copiado';
                setTimeout(() => {
                    button.textContent = 'Copiar link';
                }, 1500);
            } catch (error) {
                console.error('No se pudo copiar el enlace.', error);
            }
        });
    });
</script>
