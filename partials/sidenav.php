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
            <li class="side-nav-title mt-2" data-lang="menu-title">Menú principal</li>

            <?php if ($canDashboard) : ?>
                <li class="side-nav-item">
                    <a href="dashboard.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="side-nav-title mt-3" data-lang="menu-title">Entradas</li>

            <?php if ($canCompras) : ?>
                <li class="side-nav-item">
                    <a href="compras.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="truck"></i></span>
                        <span class="menu-text">Compras</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="side-nav-title mt-3" data-lang="menu-title">Salidas</li>

            <?php if ($canVentas) : ?>
                <li class="side-nav-item">
                    <a href="ventas.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                        <span class="menu-text">Ventas</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="side-nav-title mt-3" data-lang="menu-title">Productos</li>

            <?php if ($canCategorias) : ?>
                <li class="side-nav-item">
                    <a href="inventario-categorias.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="list"></i></span>
                        <span class="menu-text">Familia</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($canSubfamilias) : ?>
                <li class="side-nav-item">
                    <a href="inventario-subfamilias.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="list-tree"></i></span>
                        <span class="menu-text">Sub familia</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($canProductos) : ?>
                <li class="side-nav-item">
                    <a href="inventario-productos.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="package"></i></span>
                        <span class="menu-text">Producto</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($canStock) : ?>
                <li class="side-nav-item">
                    <a href="inventario-stock.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="layers"></i></span>
                        <span class="menu-text">Stock actual</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($canMovimientos) : ?>
                <li class="side-nav-item">
                    <a href="inventario-movimientos.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="arrow-left-right"></i></span>
                        <span class="menu-text">Movimientos</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="side-nav-title mt-3" data-lang="menu-title">Mantenedores</li>

            <?php if ($canEmpresas) : ?>
                <li class="side-nav-item">
                    <a href="empresa.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="building-2"></i></span>
                        <span class="menu-text">Empresas</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canUsuarios) : ?>
                <li class="side-nav-item">
                    <a href="usuario.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="user"></i></span>
                        <span class="menu-text">Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canUsuariosEmpresas) : ?>
                <li class="side-nav-item">
                    <a href="usuarios-empresas.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="building"></i></span>
                        <span class="menu-text">Usuarios por empresa</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canRoles) : ?>
                <li class="side-nav-item">
                    <a href="roles.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="shield"></i></span>
                        <span class="menu-text">Roles</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canPermisos) : ?>
                <li class="side-nav-item">
                    <a href="roles-permisos.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="key-round"></i></span>
                        <span class="menu-text">Permisos</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canClientes) : ?>
                <li class="side-nav-item">
                    <a href="clientes.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="users"></i></span>
                        <span class="menu-text">Clientes</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canProveedores) : ?>
                <li class="side-nav-item">
                    <a href="proveedores.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="building"></i></span>
                        <span class="menu-text">Proveedores</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canUnidades) : ?>
                <li class="side-nav-item">
                    <a href="inventario-unidades.php" class="side-nav-link">
                        <span class="menu-icon"><i data-lucide="ruler"></i></span>
                        <span class="menu-text">Unidades de medida</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
