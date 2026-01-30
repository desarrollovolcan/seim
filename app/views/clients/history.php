<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h4 class="card-title mb-1">Historial de actividades</h4>
                <p class="text-muted mb-0">Busca por nombre o RUT, selecciona el cliente y revisa todo su historial reciente.</p>
            </div>
        </div>

        <form class="row g-3 align-items-end flex-lg-nowrap" method="get" action="index.php">
            <input type="hidden" name="route" value="clients/history">
            <div class="col-12 col-md-5 col-lg-4">
                <label for="client-search" class="form-label fw-semibold small text-muted">Buscar cliente</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="search" id="client-search" class="form-control" placeholder="Ingresa nombre o RUT" autocomplete="off">
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-6">
                <label for="client-select" class="form-label fw-semibold small text-muted">Selecciona cliente</label>
                <select name="id" id="client-select" class="form-select" data-selected-id="<?php echo (int)$selectedClientId; ?>" required>
                    <option value="">Selecciona un cliente</option>
                    <?php foreach ($clients as $item): ?>
                        <option
                            value="<?php echo (int)$item['id']; ?>"
                            data-search="<?php echo e(strtolower(($item['name'] ?? '') . ' ' . ($item['rut'] ?? ''))); ?>"
                            <?php echo (int)$selectedClientId === (int)$item['id'] ? 'selected' : ''; ?>
                        >
                            <?php echo e(($item['name'] ?? '') . (!empty($item['rut']) ? ' · ' . $item['rut'] : '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-2 col-lg-2 d-grid">
                <label class="form-label fw-semibold small text-muted invisible">Buscar</label>
                <button type="submit" class="btn btn-primary w-100">Buscar historial</button>
            </div>
        </form>
    </div>
</div>

<?php if ($client): ?>
    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div>
                <h5 class="mb-1"><?php echo e($client['name'] ?? ''); ?></h5>
                <p class="mb-0 text-muted">
                    <?php echo e($client['email'] ?? ''); ?>
                    <?php if (!empty($client['phone'])): ?>
                        · <?php echo e($client['phone']); ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="index.php?route=clients/show&id=<?php echo (int)$client['id']; ?>" class="btn btn-soft-primary btn-sm">Ver cliente</a>
                <a href="index.php?route=clients/edit&id=<?php echo (int)$client['id']; ?>" class="btn btn-soft-secondary btn-sm">Editar</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($activities)): ?>
                <div class="text-center py-4 text-muted">Sin actividades recientes para este cliente.</div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($activities as $activity): ?>
                        <?php
                            $status = $activity['status'] ?? '';
                            $statusColor = match (strtolower($status)) {
                                'activo', 'abierto', 'pagado', 'en progreso', 'completado' => 'success',
                                'cerrado', 'vencido', 'cancelado', 'rechazado' => 'danger',
                                'pendiente', 'borrador', 'en revisión' => 'warning',
                                default => 'secondary',
                            };
                            $typeKey = strtolower($activity['type'] ?? '');
                            $typeIcons = [
                                'proyecto' => ['ti ti-layout-kanban', 'primary'],
                                'servicio' => ['ti ti-plug', 'info'],
                                'ticket' => ['ti ti-headset', 'warning'],
                                'factura' => ['ti ti-file-invoice', 'success'],
                                'renovación' => ['ti ti-rotate-clockwise', 'purple'],
                            ];
                            [$icon, $accent] = $typeIcons[$typeKey] ?? ['ti ti-dots', 'secondary'];
                            $rawDate = (string)($activity['date'] ?? '');
                            $formattedDate = $rawDate !== '' ? format_date(substr($rawDate, 0, 10)) : 'Fecha N/D';
                            if (strlen($rawDate) > 10) {
                                $formattedDate .= ' · ' . substr($rawDate, 11, 5);
                            }
                        ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="avatar-sm rounded-circle bg-<?php echo $accent; ?>-subtle text-<?php echo $accent; ?> d-inline-flex align-items-center justify-content-center">
                                        <i class="<?php echo $icon; ?>"></i>
                                    </span>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-light border text-muted text-uppercase"><?php echo e($activity['type'] ?? ''); ?></span>
                                            <span class="badge bg-<?php echo $statusColor; ?>-subtle text-<?php echo $statusColor; ?> text-capitalize"><?php echo e($status !== '' ? $status : 'N/A'); ?></span>
                                            <small class="text-muted"><?php echo e($formattedDate); ?></small>
                                        </div>
                                        <div class="fw-semibold"><?php echo e($activity['title'] ?? ''); ?></div>
                                        <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
                                            <?php if (!empty($activity['client'])): ?>
                                                <span><i class="ti ti-user"></i> <?php echo e($activity['client']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($activity['meta'])): ?>
                                                <span class="text-body-secondary"><?php echo e($activity['meta']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($activity['actions'])): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="dropdown actions-dropdown">
                                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <?php foreach ($activity['actions'] as $action): ?>
                                                    <li><a class="dropdown-item" href="<?php echo e($action['url'] ?? '#'); ?>"><?php echo e($action['label'] ?? 'Ver'); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php elseif (!empty($activity['url'])): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?php echo e($activity['url']); ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="ti ti-arrow-up-right"></i> Abrir detalle
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-2">Selecciona un cliente para ver su historial.</p>
            <p class="mb-0"><i class="ti ti-history fs-1 text-muted"></i></p>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('client-search');
    const select = document.getElementById('client-select');
    if (!searchInput || !select) {
        return;
    }

    const baseOptions = Array.from(select.options).map((option) => option.cloneNode(true));
    const selectedId = select.dataset.selectedId ?? '';

    const renderOptions = () => {
        const term = searchInput.value.trim().toLowerCase();
        select.innerHTML = '';
        let matches = 0;

        baseOptions.forEach((option) => {
            const text = (option.dataset.search || option.textContent || '').toLowerCase();
            if (term === '' || text.includes(term)) {
                select.appendChild(option.cloneNode(true));
                matches += 1;
            }
        });

        if (matches === 0) {
            const emptyOption = document.createElement('option');
            emptyOption.textContent = 'Sin coincidencias';
            emptyOption.disabled = true;
            select.appendChild(emptyOption);
        }

        if (selectedId !== '') {
            select.value = selectedId;
        }
    };

    renderOptions();
    searchInput.addEventListener('input', renderOptions);
});
</script>
