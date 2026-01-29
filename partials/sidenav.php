<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <!-- Brand Logo -->
    <?php $municipalidad = get_municipalidad(); ?>
    <?php
    $logoSidenavHeight = (int) ($municipalidad['logo_sidenav_height'] ?? 48);
    $logoSidenavHeightSm = (int) ($municipalidad['logo_sidenav_height_sm'] ?? 36);
    $canDashboard = has_permission('dashboard', 'view');
    $canCompras = has_permission('compras', 'view');
    $canVentas = has_permission('ventas', 'view');
    $canProductos = has_permission('productos', 'view');
    $canCategorias = has_permission('categorias', 'view');
    $canSubfamilias = has_permission('subfamilias', 'view');
    $canUnidades = has_permission('unidades', 'view');
    $canStock = has_permission('stock', 'view');
    $canMovimientos = has_permission('movimientos', 'view');
    $canEmpresas = has_permission('empresas', 'view');
    $canUsuarios = has_permission('usuarios', 'view');
    $canRoles = has_permission('roles', 'view');
    $canPermisos = has_permission('permisos', 'view');
    $canClientes = has_permission('clientes', 'view');
    $canProveedores = has_permission('proveedores', 'view');
    $canUsuariosEmpresas = has_permission('usuarios_empresas', 'view');
    $canNotificacionesImap = has_permission('notificaciones_imap', 'view');

    $showEntradas = $canCompras;
    $showSalidas = $canVentas;
    $showInventario = $canCategorias || $canSubfamilias || $canProductos || $canStock || $canMovimientos || $canUnidades;
    $showComercial = $canClientes || $canProveedores;
    $showAdministracion = $canEmpresas || $canUsuarios || $canUsuariosEmpresas || $canRoles || $canPermisos;
    $showMantenedores = $canNotificacionesImap;
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
            <?php if ($canDashboard) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarGeneral" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarGeneral" class="side-nav-link">
                        <span class="menu-text">General</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarGeneral">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="dashboard.php" class="side-nav-link">Dashboard</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showEntradas) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarCompras" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarCompras" class="side-nav-link">
                        <span class="menu-text">Compras</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCompras">
                        <ul class="sub-menu">
                            <?php if ($canCompras) : ?>
                                <li class="side-nav-item">
                                    <a href="compras.php" class="side-nav-link">Compras</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showSalidas) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarVentas" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarVentas" class="side-nav-link">
                        <span class="menu-text">Ventas</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarVentas">
                        <ul class="sub-menu">
                            <?php if ($canVentas) : ?>
                                <li class="side-nav-item">
                                    <a href="ventas.php" class="side-nav-link">Ventas</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showComercial) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarContactos" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarContactos" class="side-nav-link">
                        <span class="menu-text">Contactos</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarContactos">
                        <ul class="sub-menu">
                            <?php if ($canClientes) : ?>
                                <li class="side-nav-item">
                                    <a href="clientes.php" class="side-nav-link">Clientes</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canProveedores) : ?>
                                <li class="side-nav-item">
                                    <a href="proveedores.php" class="side-nav-link">Proveedores</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showInventario) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarInventario" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarInventario" class="side-nav-link">
                        <span class="menu-text">Inventario</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarInventario">
                        <ul class="sub-menu">
                            <?php if ($canCategorias) : ?>
                                <li class="side-nav-item">
                                    <a href="inventario-categorias.php" class="side-nav-link">Familias</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canSubfamilias) : ?>
                                <li class="side-nav-item">
                                    <a href="inventario-subfamilias.php" class="side-nav-link">Subfamilias</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canProductos) : ?>
                                <li class="side-nav-item">
                                    <a href="inventario-productos.php" class="side-nav-link">Productos</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canUnidades) : ?>
                                <li class="side-nav-item">
                                    <a href="inventario-unidades.php" class="side-nav-link">Unidades de medida</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canStock) : ?>
                                <li class="side-nav-item">
                                    <a href="inventario-stock.php" class="side-nav-link">Stock actual</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canMovimientos) : ?>
                                <li class="side-nav-item">
                                    <a href="inventario-movimientos.php" class="side-nav-link">Movimientos</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showAdministracion) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarAdministracion" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarAdministracion" class="side-nav-link">
                        <span class="menu-text">Administración</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAdministracion">
                        <ul class="sub-menu">
                            <?php if ($canEmpresas) : ?>
                                <li class="side-nav-item">
                                    <a href="empresa.php" class="side-nav-link">Empresas</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canUsuarios) : ?>
                                <li class="side-nav-item">
                                    <a href="usuario.php" class="side-nav-link">Usuarios</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canUsuariosEmpresas) : ?>
                                <li class="side-nav-item">
                                    <a href="usuarios-empresas.php" class="side-nav-link">Usuarios por empresa</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canRoles) : ?>
                                <li class="side-nav-item">
                                    <a href="roles.php" class="side-nav-link">Roles</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($canPermisos) : ?>
                                <li class="side-nav-item">
                                    <a href="roles-permisos.php" class="side-nav-link">Permisos</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showMantenedores) : ?>
                <li class="side-nav-item">
                    <a href="#sidebarConfiguracion" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarConfiguracion" class="side-nav-link">
                        <span class="menu-text">Configuración</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarConfiguracion">
                        <ul class="sub-menu">
                            <?php if ($canNotificacionesImap) : ?>
                                <li class="side-nav-item">
                                    <a href="configuracion-imap.php" class="side-nav-link">Configuración IMAP</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($showMantenedores) : ?>
                <li class="side-nav-title mt-3" data-lang="menu-title">Configuración</li>

                <?php if ($canNotificacionesImap) : ?>
                    <li class="side-nav-item">
                        <a href="configuracion-imap.php" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="mail"></i></span>
                            <span class="menu-text">Configuración IMAP</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
