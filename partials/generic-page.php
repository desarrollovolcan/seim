<?php include('partials/html.php'); ?>

<head>
    <?php $title = $pageTitle ?? 'Módulo'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = $pageSubtitle ?? 'Módulo'; $title = $pageTitle ?? 'Módulo'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($pageTitle ?? 'Módulo', ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($pageDescription ?? 'Vista informativa del módulo.', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">Esta vista está lista para conectar con los flujos y datos reales del sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
