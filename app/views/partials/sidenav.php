<?php
$logoColor = $companySettings['logo_color'] ?? 'assets/images/logo.png';
$logoBlack = $companySettings['logo_black'] ?? 'assets/images/logo-black.png';
$logoSmallColor = $companySettings['logo_color'] ?? 'assets/images/logo-sm.png';
$logoSmallBlack = $companySettings['logo_black'] ?? 'assets/images/logo-sm.png';
?>

<div class="sidenav-menu">
    <a href="index.php" class="logo">
        <span class="logo logo-light">
            <span class="logo-lg"><img src="<?php echo e($logoColor); ?>" alt="logo"></span>
            <span class="logo-sm"><img src="<?php echo e($logoSmallColor); ?>" alt="small logo"></span>
        </span>
        <span class="logo logo-dark">
            <span class="logo-lg"><img src="<?php echo e($logoBlack); ?>" alt="dark logo"></span>
            <span class="logo-sm"><img src="<?php echo e($logoSmallBlack); ?>" alt="small logo"></span>
        </span>
    </a>
    <button class="button-on-hover">
        <i class="ti ti-menu-4 fs-22 align-middle"></i>
    </button>
    <button class="button-close-offcanvas">
        <i class="ti ti-x align-middle"></i>
    </button>
    <div class="scrollbar" data-simplebar>
        <div class="sidenav-user">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="sidenav-user-name fw-bold"><?php echo e($currentUser['name'] ?? 'Usuario'); ?></span>
                    <span class="fs-12 fw-semibold"><?php echo e($currentUser['role'] ?? ''); ?></span>
                </div>
            </div>
        </div>
        <?php
        $isAdmin = ($currentUser['role'] ?? '') === 'admin';
        $hasCompany = !empty($currentCompany['id']);
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
        $canAccessAny = static function (array $keys) use ($hasPermission): bool {
            foreach ($keys as $key) {
                if ($hasPermission($key)) {
                    return true;
                }
            }
            return false;
        };
        ?>
        <ul class="side-nav">
            <li class="side-nav-title mt-2">Menú</li>
            <?php if ($hasCompany && $hasPermission('dashboard_view')): ?>
                <li class="side-nav-item">
                    <a href="index.php?route=dashboard" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="circle-gauge"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Dashboard</span>
                            <span class="menu-caption">Visión general de indicadores</span>
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['sales_view', 'sales_edit'])): ?>
                <li class="side-nav-title">Ventas</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarSales" aria-expanded="false" aria-controls="sidebarSales" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Ventas</span>
                            <span class="menu-caption">Registros y análisis</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarSales">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('sales_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=sales" class="side-nav-link">
                                        <span class="menu-text">Listado ventas</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=sales/create" class="side-nav-link">
                                        <span class="menu-text">Registrar venta</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=sales/profit-analysis" class="side-nav-link">
                                        <span class="menu-text">Análisis ganancias</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['products_view', 'products_edit', 'produced_products_view', 'produced_products_edit', 'product_families_view', 'product_subfamilies_view', 'production_view', 'production_edit'])): ?>
                <li class="side-nav-title">Productos</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarInventory" aria-expanded="false" aria-controls="sidebarInventory" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="package"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Productos</span>
                            <span class="menu-caption">Compras y gestión productiva</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarInventory">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('products_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=products" class="side-nav-link">
                                        <span class="menu-text">Listado productos</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('production_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=production/inputs" class="side-nav-link">
                                        <span class="menu-text">Consumos para producción</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=production/expenses" class="side-nav-link">
                                        <span class="menu-text">Gastos para producción</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('product_families_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/product-families" class="side-nav-link">
                                        <span class="menu-text">Familias</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('product_subfamilies_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/product-subfamilies" class="side-nav-link">
                                        <span class="menu-text">Subfamilias</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['production_view', 'production_edit'])): ?>
                <li class="side-nav-title">Producción</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarProduction" aria-expanded="false" aria-controls="sidebarProduction" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="factory"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Producción</span>
                            <span class="menu-caption">Costos y stock final</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarProduction">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('production_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=production" class="side-nav-link">
                                        <span class="menu-text">Órdenes de producción</span>
                                    </a>
                                </li>
                                <?php if ($hasPermission('produced_products_view')): ?>
                                    <li class="side-nav-item">
                                        <a href="index.php?route=produced-products" class="side-nav-link">
                                            <span class="menu-text">Productos fabricados</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=production/stock" class="side-nav-link">
                                        <span class="menu-text">Stock producido</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['purchase_orders_view', 'purchase_orders_edit', 'purchases_view', 'purchases_edit', 'suppliers_view', 'suppliers_edit', 'competitor_companies_view'])): ?>
                <li class="side-nav-title">Compras</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarPurchases" aria-expanded="false" aria-controls="sidebarPurchases" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="shopping-bag"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Compras</span>
                            <span class="menu-caption">Órdenes, proveedores y recepciones</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarPurchases">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('purchase_orders_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=purchase-orders" class="side-nav-link">
                                        <span class="menu-text">Órdenes de compra</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('purchases_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=purchases" class="side-nav-link">
                                        <span class="menu-text">Compras</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('suppliers_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=suppliers" class="side-nav-link">
                                        <span class="menu-text">Proveedores</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('competitor_companies_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/competitor-companies/create" class="side-nav-link">
                                        <span class="menu-text">Empresas competencia</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['email_templates_view', 'email_queue_view'])): ?>
                <li class="side-nav-title">Comunicaciones</li>
                <?php if ($hasPermission('email_templates_view')): ?>
                    <li class="side-nav-item">
                        <a href="index.php?route=email-templates" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="mail"></i></span>
                            <span class="menu-label">
                                <span class="menu-text">Plantillas Email</span>
                                <span class="menu-caption">Mensajes y diseños</span>
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($hasPermission('email_queue_view')): ?>
                    <li class="side-nav-item">
                        <a href="index.php?route=email-queue" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="send"></i></span>
                            <span class="menu-label">
                                <span class="menu-text">Cola de Correos</span>
                                <span class="menu-caption">Envíos programados</span>
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($canAccessAny(['users_view', 'roles_view', 'users_companies_view', 'users_permissions_view', 'companies_view', 'settings_view', 'email_config_view', 'online_payments_config_view', 'system_services_view', 'service_types_view', 'chile_regions_view', 'competitor_companies_view', 'hr_maintainers_view'])): ?>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarMaintainers" aria-expanded="false" aria-controls="sidebarMaintainers" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="database"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Mantenedores</span>
                            <span class="menu-caption">Configuración y catálogos</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarMaintainers">
                        <ul class="sub-menu">
                            <?php if ($canAccessAny(['users_view', 'users_edit', 'roles_view', 'users_companies_view', 'users_permissions_view'])): ?>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#sidebarMaintainersUsers" aria-expanded="false" aria-controls="sidebarMaintainersUsers" class="side-nav-link">
                                        <span class="menu-text">Usuarios</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="sidebarMaintainersUsers">
                                        <ul class="sub-menu">
                                            <?php if ($hasPermission('users_view')): ?>
                                                <li class="side-nav-item">
                                                    <a href="index.php?route=users" class="side-nav-link">
                                                        <span class="menu-text">Listado usuarios</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($hasPermission('users_permissions_view')): ?>
                                                <li class="side-nav-item">
                                                    <a href="index.php?route=users/permissions" class="side-nav-link">
                                                        <span class="menu-text">Roles y permisos</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($hasPermission('roles_view')): ?>
                                                <li class="side-nav-item">
                                                    <a href="index.php?route=roles" class="side-nav-link">
                                                        <span class="menu-text">Roles</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($hasPermission('users_companies_view')): ?>
                                                <li class="side-nav-item">
                                                    <a href="index.php?route=users/assign-company" class="side-nav-link">
                                                        <span class="menu-text">Asignar empresa</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('companies_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=companies" class="side-nav-link">
                                        <span class="menu-text">Empresas</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasCompany && $hasPermission('competitor_companies_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/competitor-companies" class="side-nav-link">
                                        <span class="menu-text">Empresas competencia</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasCompany && $hasPermission('settings_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=settings" class="side-nav-link">
                                        <span class="menu-text">Configuraciones</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/form-audit" class="side-nav-link">
                                        <span class="menu-text">Auditoría formularios</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasCompany && $hasPermission('email_config_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/email-config" class="side-nav-link">
                                        <span class="menu-text">Configuración de correo</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasCompany && $hasPermission('online_payments_config_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/online-payments" class="side-nav-link">
                                        <span class="menu-text">Pagos en línea</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('chile_regions_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/chile-regions" class="side-nav-link">
                                        <span class="menu-text">Regiones</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasCompany && $hasPermission('hr_maintainers_view')): ?>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#sidebarMaintainersHr" aria-expanded="false" aria-controls="sidebarMaintainersHr" class="side-nav-link">
                                        <span class="menu-text">Recursos Humanos</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="sidebarMaintainersHr">
                                        <ul class="sub-menu">
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-departments" class="side-nav-link">
                                                    <span class="menu-text">Departamentos</span>
                                                </a>
                                            </li>
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-positions" class="side-nav-link">
                                                    <span class="menu-text">Cargos</span>
                                                </a>
                                            </li>
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-contract-types" class="side-nav-link">
                                                    <span class="menu-text">Tipos de contrato</span>
                                                </a>
                                            </li>
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-work-schedules" class="side-nav-link">
                                                    <span class="menu-text">Jornadas</span>
                                                </a>
                                            </li>
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-health-providers" class="side-nav-link">
                                                    <span class="menu-text">Instituciones de salud</span>
                                                </a>
                                            </li>
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-pension-funds" class="side-nav-link">
                                                    <span class="menu-text">AFP</span>
                                                </a>
                                            </li>
                                            <li class="side-nav-item">
                                                <a href="index.php?route=maintainers/hr-payroll-items" class="side-nav-link">
                                                    <span class="menu-text">Ítems remuneración</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
    (function () {
        const currentRoute = new URLSearchParams(window.location.search).get('route') || 'dashboard';
        const links = document.querySelectorAll('.side-nav-link[href*="route="]');
        links.forEach((link) => {
            let linkRoute = '';
            try {
                linkRoute = new URL(link.href, window.location.origin).searchParams.get('route') || '';
            } catch (error) {
                linkRoute = '';
            }
            if (!linkRoute) {
                return;
            }
            const isActive = currentRoute === linkRoute || currentRoute.startsWith(`${linkRoute}/`);
            if (!isActive) {
                return;
            }
            link.classList.add('active');
            link.closest('.side-nav-item')?.classList.add('active');
            const collapse = link.closest('.collapse');
            if (collapse) {
                collapse.classList.add('show');
                const toggle = collapse.previousElementSibling;
                if (toggle && toggle.classList.contains('side-nav-link')) {
                    toggle.classList.add('active');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            }
        });
    })();
</script>
