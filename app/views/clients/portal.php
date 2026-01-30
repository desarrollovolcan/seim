<?php include('partials/html.php'); ?>

<head>
    <?php $title = $title ?? 'Portal Cliente'; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
    <style>
        body { background: #f4f5fb; }
        .portal-shell { min-height: 100vh; display: flex; }
        .portal-sidebar {
            width: 240px;
            background: #2f2e8b;
            color: #fff;
            padding: 20px 16px;
        }
        .portal-sidebar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .portal-sidebar .brand img { height: 32px; }
        .portal-sidebar .nav-link {
            color: #fff;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .portal-sidebar .nav-link:hover,
        .portal-sidebar .nav-link.active {
            background: rgba(255,255,255,0.12);
        }
        .portal-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .portal-topbar {
            background: linear-gradient(135deg, #4c3ade, #6a5ef7);
            color: #fff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .portal-main { padding: 20px; }
        .portal-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(24,39,75,0.08);
        }
    </style>
</head>

<?php
$companySettings = app_config('company', []);
$logoColor = $companySettings['logo_color'] ?? 'assets/images/logo.png';
$logoBlack = $companySettings['logo_black'] ?? $logoColor;
$portalLogo = $logoBlack ?: login_logo_src($companySettings);
$clientInitial = strtoupper(mb_substr_safe($client['name'] ?? 'C', 0, 1));
$upcomingTasks = array_values(array_filter($projectTasks ?? [], static fn(array $task): bool => empty($task['completed'])));
$projectsCount = count($projectsOverview ?? []);
$openTickets = count($supportTickets ?? []);
$pendingCount = count($pendingInvoices ?? []);
$paymentsCount = count($payments ?? []);
$activeSupportTicketId = (int)($activeSupportTicketId ?? 0);
$nextInvoice = $pendingInvoices[0] ?? null;
$latestPayment = $payments[0] ?? null;
$nextTask = $upcomingTasks[0] ?? null;
?>

<body>
<div class="portal-shell">
    <aside class="portal-sidebar">
        <div class="brand">
            <img src="<?php echo e($portalLogo); ?>" alt="logo">
            <span class="fw-semibold">Portal cliente</span>
        </div>
        <nav>
            <a class="nav-link" href="#resumen"><i class="ti ti-layout-grid"></i> Resumen</a>
            <a class="nav-link" href="#facturacion"><i class="ti ti-receipt-2"></i> Facturación</a>
            <a class="nav-link" href="#pagos"><i class="ti ti-cash"></i> Pagos</a>
            <a class="nav-link" href="#proyectos"><i class="ti ti-briefcase"></i> Proyectos</a>
            <a class="nav-link" href="#soporte"><i class="ti ti-headset"></i> Soporte</a>
            <a class="nav-link" href="#perfil"><i class="ti ti-user"></i> Perfil</a>
            <a class="nav-link" href="index.php?route=clients/portal/logout"><i class="ti ti-logout"></i> Cerrar sesión</a>
        </nav>
    </aside>

    <div class="portal-content">
        <div class="portal-topbar">
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold">Portal cliente</span>
                <span class="opacity-75 fs-12">index.php?route=clients/login</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fs-12 opacity-75">Cliente</div>
                    <div class="fw-semibold"><?php echo e($client['name'] ?? ''); ?></div>
                </div>
                <div class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <span class="fw-bold"><?php echo e($clientInitial); ?></span>
                </div>
            </div>
        </div>

        <div class="portal-main">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="portal-card p-3 mb-3 d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="#resumen">Resumen</a>
                <a class="btn btn-outline-success" href="#facturacion">Facturación</a>
                <a class="btn btn-outline-info" href="#pagos">Pagos</a>
                <a class="btn btn-outline-warning text-dark" href="#proyectos">Proyectos</a>
                <a class="btn btn-outline-secondary" href="#soporte">Soporte</a>
                <a class="btn btn-outline-dark" href="#perfil">Cuenta</a>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4" id="resumen">
                <div class="col">
                    <div class="portal-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-muted fs-xs mb-0 text-uppercase">Facturas pendientes</p>
                            <span class="badge bg-warning-subtle text-warning"><?php echo $pendingCount; ?></span>
                        </div>
                        <h3 class="fw-semibold mb-1"><?php echo e(format_currency((float)($pendingTotal ?? 0))); ?></h3>
                        <p class="text-muted mb-0"><?php echo $pendingCount; ?> documento(s)</p>
                        <?php if ($nextInvoice): ?>
                            <div class="mt-2 text-muted fs-sm">Próxima: <?php echo e($nextInvoice['fecha_vencimiento'] ?? '-'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col">
                    <div class="portal-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-muted fs-xs mb-0 text-uppercase">Pagos registrados</p>
                            <span class="badge bg-success-subtle text-success"><?php echo $paymentsCount; ?></span>
                        </div>
                        <h3 class="fw-semibold mb-1"><?php echo e(format_currency((float)($paidTotal ?? 0))); ?></h3>
                        <p class="text-muted mb-0"><?php echo $paymentsCount; ?> pago(s)</p>
                        <?php if ($latestPayment): ?>
                            <div class="mt-2 text-muted fs-sm">Último: <?php echo e($latestPayment['fecha_pago'] ?? '-'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col">
                    <div class="portal-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-muted fs-xs mb-0 text-uppercase">Proyectos</p>
                            <span class="badge bg-info-subtle text-info"><?php echo $projectsCount; ?></span>
                        </div>
                        <h3 class="fw-semibold mb-1"><?php echo $projectsCount; ?></h3>
                        <p class="text-muted mb-0"><?php echo count($projectTasks ?? []); ?> tareas totales</p>
                        <?php if ($nextTask): ?>
                            <div class="mt-2 text-muted fs-sm">Próxima tarea: <?php echo e($nextTask['title'] ?? $nextTask['name'] ?? ''); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col">
                    <div class="portal-card h-100 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="text-muted fs-xs mb-0 text-uppercase">Soporte</p>
                            <span class="badge bg-primary-subtle text-primary"><?php echo $openTickets; ?></span>
                        </div>
                        <h3 class="fw-semibold mb-1"><?php echo $openTickets; ?> ticket(s)</h3>
                        <p class="text-muted mb-0">Seguimiento de solicitudes y mensajes activos.</p>
                    </div>
                </div>
            </div>

            <?php if (!empty($pendingInvoices)): ?>
                <div class="portal-card p-3 mb-4" id="facturacion">
                    <h5 class="fw-semibold mb-3">Facturas pendientes</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>Número</th>
                                <th>Vence</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach (array_slice($pendingInvoices, 0, 6) as $invoice): ?>
                                <tr>
                                    <td class="fw-semibold">#<?php echo e($invoice['numero'] ?? $invoice['id']); ?></td>
                                    <td><?php echo e($invoice['fecha_vencimiento'] ?? '-'); ?></td>
                                    <td><?php echo e(format_currency((float)($invoice['total'] ?? 0))); ?></td>
                                    <td><span class="badge bg-warning-subtle text-warning text-capitalize"><?php echo e($invoice['estado'] ?? 'pendiente'); ?></span></td>
                                    <td class="text-end">
                                        <a class="btn btn-outline-primary btn-sm" href="index.php?route=clients/portal/invoice&id=<?php echo (int)($invoice['id'] ?? 0); ?>&token=<?php echo urlencode($client['portal_token'] ?? ''); ?>">Ver</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="portal-card p-3 mb-4" id="soporte">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <h5 class="fw-semibold mb-3">Crear ticket</h5>
                        <?php if (!empty($supportError)): ?>
                            <div class="alert alert-danger"><?php echo e($supportError); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($supportSuccess)): ?>
                            <div class="alert alert-success"><?php echo e($supportSuccess); ?></div>
                        <?php endif; ?>
                        <form method="post" action="index.php?route=clients/portal/tickets/create&token=<?php echo urlencode($client['portal_token'] ?? ''); ?>#soporte">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <div class="mb-2">
                                <label class="form-label">Asunto</label>
                                <input type="text" name="subject" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Prioridad</label>
                                <select name="priority" class="form-select">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Detalle</label>
                                <textarea name="message" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Enviar</button>
                        </form>
                    </div>
                    <div class="col-lg-7">
                        <h5 class="fw-semibold mb-3">Mis tickets</h5>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <?php if (!empty($supportTickets)): ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($supportTickets as $ticket): ?>
                                            <?php $isActiveTicket = (int)$ticket['id'] === $activeSupportTicketId; ?>
                                            <a href="index.php?route=clients/portal&token=<?php echo urlencode($client['portal_token'] ?? ''); ?>&ticket=<?php echo (int)$ticket['id']; ?>#soporte" class="list-group-item list-group-item-action <?php echo $isActiveTicket ? 'active' : ''; ?>">
                                                <div class="fw-semibold">#<?php echo (int)$ticket['id']; ?> · <?php echo e($ticket['subject'] ?? 'Ticket'); ?></div>
                                                <div class="fs-xxs <?php echo $isActiveTicket ? 'text-white-50' : 'text-muted'; ?>">Estado: <?php echo e(str_replace('_', ' ', $ticket['status'] ?? 'abierto')); ?></div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted">Aún no has generado tickets.</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <?php if (!empty($activeSupportTicket)): ?>
                                    <div class="portal-card p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="fw-semibold mb-1">#<?php echo (int)$activeSupportTicket['id']; ?> · <?php echo e($activeSupportTicket['subject'] ?? ''); ?></h6>
                                                <div class="text-muted fs-xxs text-uppercase">Estado: <?php echo e(str_replace('_', ' ', $activeSupportTicket['status'] ?? 'abierto')); ?></div>
                                            </div>
                                        </div>
                                        <?php if (!empty($supportMessages)): ?>
                                            <div class="list-group list-group-flush mb-3">
                                                <?php foreach ($supportMessages as $message): ?>
                                                    <div class="list-group-item px-0">
                                                        <div class="fw-semibold"><?php echo e($message['sender_type'] ?? ''); ?></div>
                                                        <div class="text-muted fs-sm"><?php echo e($message['message'] ?? ''); ?></div>
                                                        <div class="text-muted fs-xxs"><?php echo e($message['created_at'] ?? ''); ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <form method="post" action="index.php?route=clients/portal/tickets/message&token=<?php echo urlencode($client['portal_token'] ?? ''); ?>#soporte">
                                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                                            <input type="hidden" name="ticket_id" value="<?php echo (int)($activeSupportTicket['id'] ?? 0); ?>">
                                            <div class="mb-2">
                                                <label class="form-label">Responder</label>
                                                <textarea name="message" class="form-control" rows="2" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Enviar respuesta</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted">Selecciona un ticket para ver su detalle.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="portal-card p-3" id="perfil">
                    <h5 class="fw-semibold mb-3">Datos de contacto</h5>
                    <form method="post" action="index.php?route=clients/portal/update" enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="token" value="<?php echo e($client['portal_token'] ?? ''); ?>">
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e($client['email'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo e($client['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contacto</label>
                            <input type="text" name="contact" class="form-control" value="<?php echo e($client['contact'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="address" class="form-control" value="<?php echo e($client['address'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto de perfil</label>
                            <input type="file" name="avatar" class="form-control" accept="image/png,image/jpeg,image/webp">
                            <div class="form-text">Formatos permitidos: JPG, PNG o WEBP (máx 2MB).</div>
                            <?php if (!empty($client['avatar_path'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo e($client['avatar_path']); ?>" alt="Avatar cliente" class="rounded-3" style="width: 72px; height: 72px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>
