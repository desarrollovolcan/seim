<?php include __DIR__ . '/../../../partials/html.php'; ?>

<head>
    <?php $title = $title ?? 'Portal Cliente'; include __DIR__ . '/../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../partials/head-css.php'; ?>
</head>

<?php $logoColor = $companySettings['logo_color'] ?? 'assets/images/logo.png'; ?>

<body class="bg-body-tertiary">
    <?php $containerClass = !empty($hidePortalHeader) ? 'container-fluid p-0' : 'container-fluid py-4'; ?>
    <div class="<?php echo e($containerClass); ?>">
        <?php if (empty($hidePortalHeader)): ?>
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="<?php echo e($logoColor); ?>" alt="logo" style="height: 36px;">
                    <div>
                        <h4 class="mb-0"><?php echo e($pageTitle ?? 'Portal Cliente'); ?></h4>
                        <p class="text-muted mb-0">Información de actividades y pagos</p>
                    </div>
                </div>
                <?php if (!empty($client)): ?>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-md-end d-none d-md-block">
                            <div class="text-muted fs-sm">Cliente</div>
                            <div class="fw-semibold"><?php echo e($client['name'] ?? ''); ?></div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light border-0 d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (!empty($client['avatar_path'])): ?>
                                    <img src="<?php echo e($client['avatar_path']); ?>" alt="Avatar cliente" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                <?php else: ?>
                                    <span class="avatar-sm rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-semibold">
                                        <?php echo e(substr($client['name'] ?? 'C', 0, 1)); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="d-none d-sm-inline">Mi cuenta</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li class="dropdown-header text-muted">Acciones rápidas</li>
                                <li><a class="dropdown-item" href="#portal-profile" data-portal-tab="#portal-profile"><i class="ti ti-user me-2"></i>Editar perfil</a></li>
                                <li><a class="dropdown-item" href="#portal-invoices" data-portal-tab="#portal-invoices"><i class="ti ti-receipt me-2"></i>Ver facturas</a></li>
                                <li><a class="dropdown-item" href="#portal-projects" data-portal-tab="#portal-projects"><i class="ti ti-briefcase me-2"></i>Mis proyectos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?route=clients/portal/logout"><i class="ti ti-logout me-2"></i>Cerrar sesión</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php
        $viewPath = __DIR__ . '/../' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo '<div class="alert alert-danger">Vista no encontrada.</div>';
        }
        ?>
    </div>

    <?php include __DIR__ . '/../../../partials/footer-scripts.php'; ?>
</body>

</html>
