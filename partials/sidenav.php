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
            <li class="side-nav-title mt-2" data-lang="menu-title">Menú principal</li>

            <li class="side-nav-item">
                <a href="dashboard.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <li class="side-nav-title mt-3" data-lang="menu-title">Entradas</li>

            <li class="side-nav-item">
                <a href="compras.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="truck"></i></span>
                    <span class="menu-text">Compras</span>
                </a>
            </li>

            <li class="side-nav-title mt-3" data-lang="menu-title">Salidas</li>

            <li class="side-nav-item">
                <a href="ventas.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="shopping-cart"></i></span>
                    <span class="menu-text">Ventas</span>
                </a>
            </li>

            <li class="side-nav-title mt-3" data-lang="menu-title">Productos</li>

            <li class="side-nav-item">
                <a href="inventario-categorias.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="list"></i></span>
                    <span class="menu-text">Familia</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="inventario-subfamilias.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="list-tree"></i></span>
                    <span class="menu-text">Sub familia</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="inventario-productos.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="package"></i></span>
                    <span class="menu-text">Producto</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="inventario-stock.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="layers"></i></span>
                    <span class="menu-text">Stock actual</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="inventario-movimientos.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="arrow-left-right"></i></span>
                    <span class="menu-text">Movimientos</span>
                </a>
            </li>

            <li class="side-nav-title mt-3" data-lang="menu-title">Mantenedores</li>

            <li class="side-nav-item">
                <a href="empresa.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="building-2"></i></span>
                    <span class="menu-text">Empresa</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="usuario.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="user"></i></span>
                    <span class="menu-text">Usuarios</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="clientes.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="users"></i></span>
                    <span class="menu-text">Clientes</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="proveedores.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="building"></i></span>
                    <span class="menu-text">Proveedores</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="inventario-unidades.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="ruler"></i></span>
                    <span class="menu-text">Unidades de medida</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
