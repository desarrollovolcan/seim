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
            <?php if ($hasCompany && $canAccessAny(['purchase_orders_view', 'purchase_orders_edit', 'purchases_view', 'purchases_edit', 'suppliers_view', 'suppliers_edit'])): ?>
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
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['sales_view', 'sales_edit', 'clients_view', 'clients_edit', 'crm_view'])): ?>
                <li class="side-nav-title">Ventas</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarSales" aria-expanded="false" aria-controls="sidebarSales" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Ventas</span>
                            <span class="menu-caption">Órdenes y gestión comercial</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarSales">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('clients_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=clients" class="side-nav-link">
                                        <span class="menu-text">Clientes</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('crm_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=crm/orders" class="side-nav-link">
                                        <span class="menu-text">Órdenes de venta</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('quotes_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=quotes" class="side-nav-link">
                                        <span class="menu-text">Cotizaciones</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('sales_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=sales" class="side-nav-link">
                                        <span class="menu-text">Ventas</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=pos" class="side-nav-link">
                                        <span class="menu-text">Punto de venta</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $hasPermission('crm_view')): ?>
                <li class="side-nav-title">Comercial</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarCrm" aria-expanded="false" aria-controls="sidebarCrm" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="handshake"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">CRM Comercial</span>
                            <span class="menu-caption">Accesos rápidos por etapa</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCrm">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="index.php?route=crm/hub" class="side-nav-link">
                                    <span class="menu-text">Panel CRM</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarCrmProspeccion" aria-expanded="false" aria-controls="sidebarCrmProspeccion" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="radar"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Prospección</span>
                            <span class="menu-caption">Primer contacto y calificación</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCrmProspeccion">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="index.php?route=crm/briefs" class="side-nav-link">
                                    <span class="menu-text">Briefs Comerciales</span>
                                </a>
                            </li>
                            <?php if ($hasPermission('quotes_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=quotes" class="side-nav-link">
                                        <span class="menu-text">Cotizaciones</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarCrmVentas" aria-expanded="false" aria-controls="sidebarCrmVentas" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Ventas</span>
                            <span class="menu-caption">Funnel y oportunidades</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCrmVentas">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('clients_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=clients" class="side-nav-link">
                                        <span class="menu-text">Clientes</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('quotes_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=quotes" class="side-nav-link">
                                        <span class="menu-text">Cotizaciones</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('invoices_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=invoices" class="side-nav-link">
                                        <span class="menu-text">Facturas</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('payments_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=payments/buttons" class="side-nav-link">
                                        <span class="menu-text">Botones de pago</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=payments/paid" class="side-nav-link">
                                        <span class="menu-text">Pagos recibidos</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=payments/pending" class="side-nav-link">
                                        <span class="menu-text">Pagos pendientes</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarCrmPostventa" aria-expanded="false" aria-controls="sidebarCrmPostventa" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="handshake"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Postventa</span>
                            <span class="menu-caption">Soporte y fidelización</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCrmPostventa">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="index.php?route=crm/renewals" class="side-nav-link">
                                    <span class="menu-text">Renovaciones</span>
                                </a>
                            </li>
                            <?php if ($hasPermission('tickets_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=tickets" class="side-nav-link">
                                        <span class="menu-text">Service Desk</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="side-nav-item">
                                <a href="index.php?route=crm/reports" class="side-nav-link">
                                    <span class="menu-text">Reportes &amp; Insights</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['accounting_view', 'accounting_edit', 'taxes_view', 'taxes_edit', 'honorarios_view', 'honorarios_edit', 'fixed_assets_view', 'fixed_assets_edit', 'treasury_view', 'treasury_edit', 'inventory_view', 'inventory_edit'])): ?>
                <li class="side-nav-title">Contabilidad</li>
                <?php if ($canAccessAny(['accounting_view', 'accounting_edit'])): ?>
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAccounting" aria-expanded="false" aria-controls="sidebarAccounting" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="book-open"></i></span>
                            <span class="menu-label">
                                <span class="menu-text">Contabilidad general</span>
                                <span class="menu-caption">Libro diario y estados</span>
                            </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarAccounting">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="index.php?route=accounting/chart" class="side-nav-link">
                                        <span class="menu-text">Plan de cuentas</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=accounting/journals" class="side-nav-link">
                                        <span class="menu-text">Libro diario</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=accounting/ledger" class="side-nav-link">
                                        <span class="menu-text">Libro mayor</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=accounting/trial-balance" class="side-nav-link">
                                        <span class="menu-text">Balance de comprobación</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=accounting/financial-statements" class="side-nav-link">
                                        <span class="menu-text">Estados financieros</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=accounting/periods" class="side-nav-link">
                                        <span class="menu-text">Cierres contables</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if ($hasPermission('taxes_view') || $hasPermission('taxes_edit')): ?>
                    <li class="side-nav-item">
                        <a href="index.php?route=taxes" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="file-text"></i></span>
                            <span class="menu-text">Impuestos</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($hasPermission('honorarios_view') || $hasPermission('honorarios_edit')): ?>
                    <li class="side-nav-item">
                        <a href="index.php?route=honorarios" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="receipt"></i></span>
                            <span class="menu-text">Honorarios</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($hasPermission('fixed_assets_view') || $hasPermission('fixed_assets_edit')): ?>
                    <li class="side-nav-item">
                        <a href="index.php?route=fixed-assets" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="briefcase"></i></span>
                            <span class="menu-text">Activos fijos</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($hasPermission('treasury_view') || $hasPermission('treasury_edit')): ?>
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarTreasury" aria-expanded="false" aria-controls="sidebarTreasury" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="wallet"></i></span>
                            <span class="menu-text">Tesorería</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarTreasury">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="index.php?route=treasury/accounts" class="side-nav-link">
                                        <span class="menu-text">Cuentas bancarias</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="index.php?route=treasury/transactions" class="side-nav-link">
                                        <span class="menu-text">Movimientos</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if ($hasPermission('inventory_view') || $hasPermission('inventory_edit')): ?>
                    <li class="side-nav-item">
                        <a href="index.php?route=inventory/movements" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="layers"></i></span>
                            <span class="menu-text">Inventario</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['documents_view', 'calendar_view', 'projects_view', 'products_view', 'products_edit', 'suppliers_view', 'suppliers_edit', 'purchases_view', 'purchases_edit', 'sales_view', 'sales_edit', 'services_view', 'services_edit', 'system_services_view', 'system_services_edit', 'service_types_view', 'service_types_edit'])): ?>
                <li class="side-nav-title">Operaciones</li>
            <?php endif; ?>
            <?php if ($hasCompany && $hasPermission('documents_view')): ?>
                <li class="side-nav-item">
                    <a href="index.php?route=documents" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="folder-open"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Documentos</span>
                            <span class="menu-caption">Biblioteca de recursos</span>
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['calendar_view', 'calendar_edit'])): ?>
                <li class="side-nav-item">
                    <a href="index.php?route=calendar" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="calendar"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Calendario</span>
                            <span class="menu-caption">Reuniones y recordatorios</span>
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $hasPermission('projects_view')): ?>
                <li class="side-nav-item">
                    <a href="index.php?route=projects" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="folder"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Proyectos</span>
                            <span class="menu-caption">Ejecución de servicios</span>
                        </span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['services_view', 'services_edit', 'system_services_view', 'system_services_edit', 'service_types_view', 'service_types_edit'])): ?>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarServices" aria-expanded="false" aria-controls="sidebarServices" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="server"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Servicios</span>
                            <span class="menu-caption">Clientes y catálogo</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarServices">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('services_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=services" class="side-nav-link">
                                        <span class="menu-text">Listado servicios</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('services_edit')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=services/create" class="side-nav-link">
                                        <span class="menu-text">Asignar servicio a cliente</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('system_services_edit')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/services/create" class="side-nav-link">
                                        <span class="menu-text">Crear servicio</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('service_types_edit')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=maintainers/service-types/create" class="side-nav-link">
                                        <span class="menu-text">Crear tipo de servicio</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $canAccessAny(['hr_employees_view', 'hr_contracts_view', 'hr_attendance_view', 'hr_payrolls_view'])): ?>
                <li class="side-nav-title">Recursos Humanos</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarHr" aria-expanded="false" aria-controls="sidebarHr" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="id-card"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Gestión RRHH</span>
                            <span class="menu-caption">Contratos y asistencia</span>
                        </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarHr">
                        <ul class="sub-menu">
                            <?php if ($hasPermission('hr_employees_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=hr/employees" class="side-nav-link">
                                        <span class="menu-text">Trabajadores</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('hr_contracts_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=hr/contracts" class="side-nav-link">
                                        <span class="menu-text">Contratos</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('hr_contracts_edit')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=hr/contracts/bulk" class="side-nav-link">
                                        <span class="menu-text">Contratos masivos</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('hr_attendance_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=hr/attendance" class="side-nav-link">
                                        <span class="menu-text">Asistencia</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('hr_payrolls_view')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=hr/payrolls" class="side-nav-link">
                                        <span class="menu-text">Remuneraciones</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($hasPermission('hr_payrolls_edit')): ?>
                                <li class="side-nav-item">
                                    <a href="index.php?route=hr/payrolls/bulk" class="side-nav-link">
                                        <span class="menu-text">Liquidaciones masivas</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            <?php if ($hasCompany && $hasPermission('hr_attendance_edit')): ?>
                <li class="side-nav-title">Reloj Control</li>
                <li class="side-nav-item">
                    <a href="index.php?route=hr/clock" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="qr-code"></i></span>
                        <span class="menu-label">
                            <span class="menu-text">Marcación QR</span>
                            <span class="menu-caption">Control de asistencia</span>
                        </span>
                    </a>
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
            <?php if ($canAccessAny(['users_view', 'roles_view', 'users_companies_view', 'users_permissions_view', 'companies_view', 'settings_view', 'email_config_view', 'online_payments_config_view', 'system_services_view', 'service_types_view', 'chile_regions_view', 'hr_maintainers_view'])): ?>
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
