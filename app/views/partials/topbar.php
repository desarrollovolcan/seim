<?php
$logoColor = $companySettings['logo_color'] ?? 'assets/images/logo.png';
$logoBlack = $companySettings['logo_black'] ?? 'assets/images/logo-black.png';
$logoSmallColor = $companySettings['logo_color'] ?? 'assets/images/logo-sm.png';
$logoSmallBlack = $companySettings['logo_black'] ?? 'assets/images/logo-sm.png';
$companyName = $currentCompany['name'] ?? ($companySettings['name'] ?? '');
$userAvatar = $currentUser['avatar_path'] ?? '';
$userInitials = trim((string)($currentUser['name'] ?? 'U'));
$userInitials = $userInitials !== '' ? strtoupper(mb_substr($userInitials, 0, 1)) : 'U';
$isAdmin = ($currentUser['role'] ?? '') === 'admin';
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
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-2">
            <div class="logo-topbar">
                <a href="index.php" class="logo-light">
                    <span class="logo-lg">
                        <img src="<?php echo e($logoColor); ?>" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="<?php echo e($logoSmallColor); ?>" alt="small logo">
                    </span>
                </a>
                <a href="index.php" class="logo-dark">
                    <span class="logo-lg">
                        <img src="<?php echo e($logoBlack); ?>" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="<?php echo e($logoSmallBlack); ?>" alt="small logo">
                    </span>
                </a>
            </div>
            <button class="sidenav-toggle-button btn btn-default btn-icon">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="app-search d-none d-xl-flex me-2">
                <form method="get" action="index.php" class="position-relative">
                    <input type="hidden" name="route" value="search">
                    <input type="search" class="form-control topbar-search rounded-pill" name="q" placeholder="Buscar...">
                    <i data-lucide="search" class="app-search-icon text-muted"></i>
                </form>
            </div>
            <div class="d-none d-lg-flex align-items-center me-2 topbar-portal">
                <span class="text-white-50 fs-12 me-2">Portal cliente:</span>
                <a href="<?php echo e($portalLoginUrl); ?>" class="topbar-portal-link" target="_blank" rel="noopener">
                    <?php echo e($portalLoginUrl); ?>
                </a>
            </div>

            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" data-bs-auto-close="outside" aria-haspopup="false" aria-expanded="false">
                        <i data-lucide="bell" class="fs-xxl"></i>
                        <span class="badge text-bg-danger badge-circle topbar-badge"><?php echo $notificationCount; ?></span>
                    </button>
                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                        <div class="px-3 py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-md fw-semibold">Notificaciones</h6>
                                </div>
                                <div class="col text-end">
                                    <a href="index.php?route=notifications" class="badge badge-soft-success badge-label py-1">Ver todas</a>
                                </div>
                            </div>
                        </div>
                        <div style="max-height: 300px;" data-simplebar>
                            <?php foreach ($notifications as $notification): ?>
                                <div class="dropdown-item notification-item py-2 text-wrap">
                                    <span class="d-flex align-items-center gap-3">
                                        <span class="flex-grow-1 text-muted">
                                            <span class="fw-medium text-body"><?php echo e($notification['title']); ?></span><br>
                                            <span class="fs-xs"><?php echo e($notification['message']); ?></span>
                                        </span>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link d-flex align-items-center gap-2" data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" aria-haspopup="false" aria-expanded="false">
                        <?php if (!empty($userAvatar)): ?>
                            <img src="<?php echo e($userAvatar); ?>" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        <?php else: ?>
                            <span class="avatar-sm rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-semibold"><?php echo e($userInitials); ?></span>
                        <?php endif; ?>
                        <span class="d-none d-sm-flex flex-column text-start">
                            <span class="fw-semibold"><?php echo e($currentUser['name'] ?? ''); ?></span>
                            <?php if ($companyName !== ''): ?>
                                <span class="text-muted fs-12"><?php echo e($companyName); ?></span>
                            <?php endif; ?>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header text-muted">
                            <div class="fw-semibold"><?php echo e($currentUser['name'] ?? ''); ?></div>
                            <?php if ($companyName !== ''): ?>
                                <div class="fs-12"><?php echo e($companyName); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($currentUser['role'])): ?>
                                <span class="badge text-bg-light mt-1"><?php echo e(ucfirst($currentUser['role'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="index.php?route=dashboard" class="dropdown-item">
                            <i class="ti ti-layout-dashboard me-2"></i> Dashboard
                        </a>
                        <?php if ($canViewSettings): ?>
                            <a href="index.php?route=settings" class="dropdown-item">
                                <i class="ti ti-settings me-2"></i> Configuración
                            </a>
                        <?php endif; ?>
                        <?php if ($canSwitchCompany && $hasMultipleCompanies): ?>
                            <a href="index.php?route=auth/switch-company" class="dropdown-item">
                                <i class="ti ti-building me-2"></i> Cambiar empresa
                            </a>
                        <?php endif; ?>
                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <a href="index.php?route=users" class="dropdown-item">
                                <i class="ti ti-users me-2"></i> Usuarios
                            </a>
                        <?php endif; ?>
                        <a href="index.php?route=notifications" class="dropdown-item">
                            <i class="ti ti-bell me-2"></i> Notificaciones
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="index.php?route=logout" class="dropdown-item text-danger">
                            <i class="ti ti-logout me-2"></i> Salir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
