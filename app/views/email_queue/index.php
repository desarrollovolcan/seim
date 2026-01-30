<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Cola de correos</h4>
        <a href="index.php?route=email-queue/compose" class="btn btn-primary">Nuevo correo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Asunto</th>
                        <th>Tipo</th>
                        <th>Destinatarios</th>
                        <th>Estado</th>
                        <th>Días faltantes</th>
                        <th>Programado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emails as $email): ?>
                        <?php
                        $scheduledAt = new DateTime($email['scheduled_at']);
                        $now = new DateTime();
                        $daysDiff = (int)$now->diff($scheduledAt)->format('%r%a');
                        $daysLabel = $daysDiff <= 0 ? 'Vencido' : $daysDiff . ' días';
                        if ($daysDiff <= 0) {
                            $daysBadge = 'danger';
                        } elseif ($daysDiff <= 15) {
                            $daysBadge = 'warning';
                        } else {
                            $daysBadge = 'secondary';
                        }
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($email['id'] ?? null); ?></td>
                            <td><?php echo e($email['client_name'] ?? ''); ?></td>
                            <td><?php echo e($email['subject']); ?></td>
                            <td><?php echo e($email['type']); ?></td>
                            <td>
                                <?php
                                $recipients = array_filter([
                                    $email['billing_email'] ?? null,
                                    $email['email'] ?? null,
                                ]);
                                ?>
                                <?php if (!empty($recipients)): ?>
                                    <div class="text-muted fs-xs">
                                        <?php echo e(implode(', ', $recipients)); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted fs-xs">Sin destinatarios</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $email['status'] === 'sent' ? 'success' : ($email['status'] === 'failed' ? 'danger' : 'warning'); ?>-subtle text-<?php echo $email['status'] === 'sent' ? 'success' : ($email['status'] === 'failed' ? 'danger' : 'warning'); ?>">
                                    <?php echo e($email['status']); ?>
                                </span>
                                <?php if ($email['status'] === 'failed' && !empty($email['last_error'])): ?>
                                    <div class="text-muted fs-xs mt-1">
                                        <?php echo e($email['last_error']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $daysBadge; ?>-subtle text-<?php echo $daysBadge; ?>">
                                    <?php echo e($daysLabel); ?>
                                </span>
                            </td>
                            <td><?php echo e($email['scheduled_at']); ?></td>
                            <td class="text-end">
                                <div class="dropdown actions-dropdown">
                                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if ($email['status'] !== 'sent'): ?>
                                            <li>
                                                <form method="post" action="index.php?route=email-queue/send">
                                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int)$email['id']; ?>">
                                                    <button type="submit" class="dropdown-item dropdown-item-button">Enviar ahora</button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <form method="post" action="index.php?route=email-queue/delete" onsubmit="return confirm('¿Eliminar este correo de la cola?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$email['id']; ?>">
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
    </div>
</div>
