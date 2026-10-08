<?php
$rawLogoBlack = $currentCompany['logo_black'] ?? $companySettings['logo_black'] ?? '';
$logoBlack = (!empty($rawLogoBlack) && is_file(__DIR__ . '/../../../' . ltrim($rawLogoBlack, '/')))
    ? $rawLogoBlack
    : 'assets/images/seim-logo.png';
$companyName = $currentCompany['name'] ?? ($companySettings['name'] ?? 'SEIM Energía');
$userAvatar = $currentUser['avatar_path'] ?? '';
$userInitials = trim((string)($currentUser['name'] ?? 'U'));
$userInitials = $userInitials !== '' ? strtoupper(mb_substr_safe($userInitials, 0, 1)) : 'U';
$isAdmin = is_admin_user($currentUser);
$hasPermission = static function (string $key) use ($permissions, $isAdmin): bool {
    if ($isAdmin) {
        return true;
    }
    if (in_array($key, $permissions ?? [], true)) {
        return true;
    }
    $legacyKey = permission_legacy_key_for($key);
    return $legacyKey ? in_array($legacyKey, $permissions ?? [], true) : false;
};
$canSwitchCompany = $hasPermission('company_switch_view');
$canViewSettings = $hasPermission('settings_view');
$userCompanies = $canSwitchCompany ? user_company_ids($db, $currentUser) : [];
$hasMultipleCompanies = count($userCompanies) > 1;
$portalBaseUrl = rtrim($config['app']['base_url'] ?? '', '/');
$portalLoginPath = 'index.php?route=clients/login';
$portalLoginUrl = $portalBaseUrl !== '' ? $portalBaseUrl . '/' . $portalLoginPath : $portalLoginPath;
?>

<header class="app-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="button-toggle-menu" type="button" aria-label="Menú">
            <i class="ti ti-menu-2 fs-18"></i>
        </button>

        <div class="d-lg-none">
            <a href="index.php" class="d-inline-flex align-items-center">
                <img src="<?php echo e($logoBlack); ?>" alt="<?php echo e($companyName); ?>" style="max-height: 28px;" onerror="this.style.display='none';">
            </a>
        </div>

        <div class="app-search d-none d-md-block">
            <form method="get" action="index.php" class="position-relative">
                <input type="hidden" name="route" value="search">
                <input type="search" class="topbar-search" name="q" placeholder="Buscar en SEIM...">
                <i class="ti ti-search app-search-icon"></i>
            </form>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 gap-sm-3">
        <?php if (!empty($portalLoginUrl)): ?>
            <a href="<?php echo e($portalLoginUrl); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-none d-md-inline-flex align-items-center gap-1" target="_blank" rel="noopener">
                <i class="ti ti-external-link fs-14"></i>
                <span class="fs-12 fw-semibold">Portal Cliente</span>
            </a>
        <?php endif; ?>

        <!-- Notificaciones -->
        <div class="dropdown">
            <button class="topbar-btn" data-bs-toggle="dropdown" type="button" aria-label="Notificaciones" aria-expanded="false">
                <i class="ti ti-bell fs-18"></i>
                <?php if (!empty($notificationCount) && (int)$notificationCount > 0): ?>
                    <span class="topbar-badge"><?php echo (int)$notificationCount; ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 shadow-sm" style="min-width: 290px;">
                <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-semibold fs-13">Notificaciones</span>
                    <a href="index.php?route=notifications" class="badge bg-primary-subtle text-decoration-none">Ver todas</a>
                </div>
                <div style="max-height: 280px; overflow-y: auto;">
                    <?php if (empty($notifications)): ?>
                        <div class="p-3 text-center text-muted fs-13">Sin notificaciones nuevas</div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notification): ?>
                            <div class="px-3 py-2 border-bottom dropdown-item text-wrap">
                                <div class="fw-medium text-body fs-13"><?php echo e($notification['title']); ?></div>
                                <div class="text-muted fs-11"><?php echo e($notification['message']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Usuario -->
        <div class="dropdown">
            <button class="user-dropdown-btn d-flex align-items-center gap-2" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                <?php if (!empty($userAvatar)): ?>
                    <img src="<?php echo e($userAvatar); ?>" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                <?php else: ?>
                    <div class="avatar-user-sm"><?php echo e($userInitials); ?></div>
                <?php endif; ?>
                <div class="d-none d-sm-flex flex-column text-start">
                    <span class="user-header-name"><?php echo e($currentUser['name'] ?? 'Usuario'); ?></span>
                    <?php if ($companyName !== ''): ?>
                        <span class="user-header-sub text-truncate"><?php echo e($companyName); ?></span>
                    <?php endif; ?>
                </div>
                <i class="ti ti-chevron-down fs-12 text-muted ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-1">
                <li class="dropdown-header text-muted">
                    <div class="fw-semibold text-body"><?php echo e($currentUser['name'] ?? ''); ?></div>
                    <?php if ($companyName !== ''): ?>
                        <div class="fs-12 text-muted"><?php echo e($companyName); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($currentUser['role'])): ?>
                        <span class="badge bg-light text-dark border mt-1"><?php echo e(ucfirst($currentUser['role'])); ?></span>
                    <?php endif; ?>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="index.php?route=dashboard" class="dropdown-item"><i class="ti ti-layout-dashboard me-2"></i>Dashboard</a></li>
                <?php if ($canViewSettings): ?>
                    <li><a href="index.php?route=settings" class="dropdown-item"><i class="ti ti-settings me-2"></i>Configuración</a></li>
                <?php endif; ?>
                <?php if ($hasPermission('companies_view') || $isAdmin): ?>
                    <li><a href="index.php?route=companies" class="dropdown-item"><i class="ti ti-building me-2"></i>Empresas</a></li>
                <?php endif; ?>
                <?php if ($canSwitchCompany): ?>
                    <li><a href="index.php?route=auth/switch-company" class="dropdown-item"><i class="ti ti-arrows-left-right me-2"></i>Cambiar empresa</a></li>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                    <li><a href="index.php?route=users" class="dropdown-item"><i class="ti ti-users me-2"></i>Usuarios</a></li>
                <?php endif; ?>
                <li><a href="index.php?route=notifications" class="dropdown-item"><i class="ti ti-bell me-2"></i>Notificaciones</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="index.php?route=logout" class="dropdown-item text-danger"><i class="ti ti-logout me-2"></i>Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</header>
