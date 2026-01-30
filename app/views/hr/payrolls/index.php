<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Remuneraciones y nómina</h4>
        <div class="d-flex gap-2">
            <a href="index.php?route=hr/payrolls/bulk" class="btn btn-outline-primary">Liquidaciones masivas</a>
            <a href="index.php?route=hr/payrolls/create" class="btn btn-primary">Nueva remuneración</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Período</th>
                        <th class="text-end">Sueldo base</th>
                        <th class="text-end">Haberes</th>
                        <th class="text-end">Cotizaciones</th>
                        <th class="text-end">Otros descuentos</th>
                        <th class="text-end">Líquido</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payrolls as $payroll): ?>
                        <?php
                        $status = $payroll['status'] ?? 'borrador';
                        $statusColor = match ($status) {
                            'procesado' => 'success',
                            'pagado' => 'primary',
                            'borrador' => 'secondary',
                            default => 'info',
                        };
                        ?>
                        <tr>
                            <td class="text-muted"><?php echo render_id_badge($payroll['id'] ?? null); ?></td>
                            <td>
                                <?php echo e(trim(($payroll['first_name'] ?? '') . ' ' . ($payroll['last_name'] ?? ''))); ?>
                                <div class="text-muted fs-12"><?php echo e($payroll['rut'] ?? ''); ?></div>
                            </td>
                            <td><?php echo e(format_date($payroll['period_start'] ?? null)); ?> - <?php echo e(format_date($payroll['period_end'] ?? null)); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($payroll['base_salary'] ?? 0), 0)); ?></td>
                            <td class="text-end"><?php echo e(format_currency((float)($payroll['bonuses'] ?? 0), 0)); ?></td>
                            <td class="text-end">
                                <?php
                                $social = (float)($payroll['pension_deduction'] ?? 0)
                                    + (float)($payroll['health_deduction'] ?? 0)
                                    + (float)($payroll['unemployment_deduction'] ?? 0);
                                ?>
                                <?php echo e(format_currency($social, 0)); ?>
                            </td>
                            <td class="text-end"><?php echo e(format_currency((float)($payroll['other_deductions'] ?? 0), 0)); ?></td>
                            <td class="text-end fw-semibold"><?php echo e(format_currency((float)($payroll['net_pay'] ?? 0), 0)); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?>">
                                    <?php echo e($status); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <form method="post" action="index.php?route=hr/payrolls/delete" onsubmit="return confirm('¿Eliminar esta remuneración?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$payroll['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-soft-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
