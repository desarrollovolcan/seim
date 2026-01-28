<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <!-- Brand Logo -->
    <?php $municipalidad = get_municipalidad(); ?>
    <?php
    $logoSidenavHeight = (int) ($municipalidad['logo_sidenav_height'] ?? 48);
    $logoSidenavHeightSm = (int) ($municipalidad['logo_sidenav_height_sm'] ?? 36);
    ?>
    <a href="index.php" class="logo">
        <span class="logo logo-light">
            <span class="logo-lg">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeight; ?>px;">
            </span>
            <span class="logo-sm">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeightSm; ?>px;">
            </span>
        </span>

        <span class="logo logo-dark">
            <span class="logo-lg">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeight; ?>px;">
            </span>
            <span class="logo-sm">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeightSm; ?>px;">
            </span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-on-hover">
        <i class="ti ti-menu-4 fs-22 align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-offcanvas">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div class="scrollbar" data-simplebar>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title mt-2" data-lang="menu-title">Menú principal del sistema</li>

            <li class="side-nav-item">
                <a href="inicio.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="home"></i></span>
                    <span class="menu-text">Inicio</span>
                </a>
            </li>

            <?php if (has_permission('dashboard', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-dashboard" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-dashboard">
                        <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="menu-text">Dashboard</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-dashboard">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="dashboard-resumen.php" class="side-nav-link">Resumen general</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="dashboard-alertas.php" class="side-nav-link">Alertas de stock</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('productos', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-productos" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-productos">
                        <span class="menu-icon"><i data-lucide="package"></i></span>
                        <span class="menu-text">Productos</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-productos">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="productos-listado.php" class="side-nav-link">Listado de productos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="productos-categorias.php" class="side-nav-link">Categorías</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="productos-unidades.php" class="side-nav-link">Unidades de medida</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="productos-precios-costos.php" class="side-nav-link">Precios y costos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="productos-estados.php" class="side-nav-link">Estados de producto</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('inventario', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-inventario" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-inventario">
                        <span class="menu-icon"><i data-lucide="boxes"></i></span>
                        <span class="menu-text">Inventario</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-inventario">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="inventario-entradas.php" class="side-nav-link">Entradas de productos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="inventario-stock-actual.php" class="side-nav-link">Stock actual</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="inventario-ajustes.php" class="side-nav-link">Ajustes de inventario</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="inventario-kardex.php" class="side-nav-link">Kardex de productos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="inventario-valorizado.php" class="side-nav-link">Inventario valorizado</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('traslados', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-traslados" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-traslados">
                        <span class="menu-icon"><i data-lucide="truck"></i></span>
                        <span class="menu-text">Traslados</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-traslados">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="traslados-registrar.php" class="side-nav-link">Registrar traslado</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="traslados-pendientes.php" class="side-nav-link">Traslados pendientes</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="traslados-historial.php" class="side-nav-link">Historial de traslados</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('ventas', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-ventas" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-ventas">
                        <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                        <span class="menu-text">Ventas</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-ventas">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="ventas-registrar.php" class="side-nav-link">Registrar venta</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="ventas-historial.php" class="side-nav-link">Historial de ventas</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="ventas-anuladas.php" class="side-nav-link">Ventas anuladas</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="ventas-devoluciones.php" class="side-nav-link">Devoluciones</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('clientes', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-clientes" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-clientes">
                        <span class="menu-icon"><i data-lucide="user-check"></i></span>
                        <span class="menu-text">Clientes</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-clientes">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="clientes-listado.php" class="side-nav-link">Listado de clientes</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="clientes-historial.php" class="side-nav-link">Historial de compras</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="clientes-cuentas-por-cobrar.php" class="side-nav-link">Cuentas por cobrar</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('costos', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-costos" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-costos">
                        <span class="menu-icon"><i data-lucide="line-chart"></i></span>
                        <span class="menu-text">Costos y Utilidades</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-costos">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="costos-promedio-producto.php" class="side-nav-link">Costo promedio por producto</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="costos-utilidad-venta.php" class="side-nav-link">Utilidad por venta</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="costos-utilidad-producto.php" class="side-nav-link">Utilidad por producto</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="costos-resumen-mensual.php" class="side-nav-link">Resumen mensual</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('reportes', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-reportes" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-reportes">
                        <span class="menu-icon"><i data-lucide="file-chart-line"></i></span>
                        <span class="menu-text">Reportes</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-reportes">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="reportes-inventario.php" class="side-nav-link">Reporte de inventario</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="reportes-ventas.php" class="side-nav-link">Reporte de ventas</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="reportes-utilidades.php" class="side-nav-link">Reporte de utilidades</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="reportes-productos-mas-vendidos.php" class="side-nav-link">Productos más vendidos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="reportes-productos-bajo-stock.php" class="side-nav-link">Productos con bajo stock</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="reportes-perdidas-mermas.php" class="side-nav-link">Pérdidas y mermas</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('usuarios', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-usuarios" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-usuarios">
                        <span class="menu-icon"><i data-lucide="users"></i></span>
                        <span class="menu-text">Usuarios</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-usuarios">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="usuarios-gestion.php" class="side-nav-link">Gestión de usuarios</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="usuarios-roles-permisos.php" class="side-nav-link">Roles y permisos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="usuarios-actividad.php" class="side-nav-link">Registro de actividades</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('configuracion', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-configuracion" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-configuracion">
                        <span class="menu-icon"><i data-lucide="settings"></i></span>
                        <span class="menu-text">Configuración</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-configuracion">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="config-empresa.php" class="side-nav-link">Datos de la empresa</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="config-bodegas.php" class="side-nav-link">Bodegas / sucursales</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="config-formas-pago.php" class="side-nav-link">Formas de pago</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="config-impuestos.php" class="side-nav-link">Impuestos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="config-parametros-inventario.php" class="side-nav-link">Parámetros de inventario</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="config-respaldos.php" class="side-nav-link">Respaldos del sistema</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
