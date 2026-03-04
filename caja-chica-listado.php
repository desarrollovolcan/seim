<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Caja Chica - Listado"; include('partials/title-meta.php'); ?>

    <link href="assets/plugins/datatables/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/datatables/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css">

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">

                <?php $subtitle = "Caja Chica"; $title = "Listado de Registros"; include('partials/page-title.php'); ?>

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                        <h4 class="card-title mb-0">Boletas registradas</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="date" class="form-control" id="filterFromDate" title="Desde">
                            <input type="date" class="form-control" id="filterToDate" title="Hasta">
                            <input type="text" class="form-control" id="filterSupplier" placeholder="Filtrar proveedor">
                            <button class="btn btn-outline-secondary" id="clearFiltersBtn">Limpiar filtros</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pettyCashTable" class="table table-striped align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>N° Boleta</th>
                                        <th>Fecha</th>
                                        <th>Proveedor</th>
                                        <th>Moneda</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <?php include('partials/footer.php'); ?>
        </div>
    </div>

    <?php include('partials/customizer.php'); ?>
    <?php include('partials/footer-scripts.php'); ?>

    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="assets/plugins/datatables/responsive.bootstrap5.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="assets/plugins/datatables/buttons.bootstrap5.min.js"></script>
    <script src="assets/plugins/datatables/jszip.min.js"></script>
    <script src="assets/plugins/datatables/buttons.html5.min.js"></script>

    <script src="assets/js/pages/caja-chica-listado.js"></script>
</body>

</html>
