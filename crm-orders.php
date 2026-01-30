<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Orders"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Órdenes de Venta"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="collapse" id="createOrderForm">
                            <div class="card mb-3">
                                <div class="card-header border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">Nueva Orden de Venta</h5>
                                            <p class="text-muted mb-0">Completa el formulario para registrar una nueva orden.</p>
                                        </div>
                                        <button type="button" class="btn btn-light btn-icon" data-bs-toggle="collapse" data-bs-target="#createOrderForm" aria-controls="createOrderForm" aria-expanded="true">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <form>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="orderClient">Cliente</label>
                                                <input type="text" class="form-control" id="orderClient" placeholder="Nombre del cliente">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="orderService">Servicio</label>
                                                <input type="text" class="form-control" id="orderService" placeholder="Servicio contratado">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="orderTotal">Total</label>
                                                <input type="number" class="form-control" id="orderTotal" placeholder="Ej. 1200000">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="orderStatus">Estado</label>
                                                <select class="form-select" id="orderStatus">
                                                    <option value="">Seleccionar estado</option>
                                                    <option>Pendiente</option>
                                                    <option>Confirmada</option>
                                                    <option>En ejecución</option>
                                                    <option>Finalizada</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="orderNotes">Notas</label>
                                                <textarea class="form-control" id="orderNotes" rows="3" placeholder="Condiciones, alcance, hitos"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex flex-column flex-sm-row gap-2">
                                        <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-toggle="collapse" data-bs-target="#createOrderForm" aria-controls="createOrderForm" aria-expanded="true">Cancelar</button>
                                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Guardar Orden</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div data-table data-table-rows-per-page="8" class="card">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input data-table-search type="search" class="form-control" placeholder="Buscar orden...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createOrderForm" aria-expanded="false" aria-controls="createOrderForm"><i class="ti ti-plus me-1"></i> Nueva Orden</button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Estado</option>
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="Confirmada">Confirmada</option>
                                            <option value="En ejecución">En ejecución</option>
                                            <option value="Finalizada">Finalizada</option>
                                        </select>
                                        <i data-lucide="filter" class="app-search-icon text-muted"></i>
                                    </div>

                                    <div>
                                        <select data-table-set-rows-per-page class="form-select form-control my-1 my-md-0">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                        <thead class="bg-light align-middle bg-opacity-25 thead-sm text-nowrap">
                                            <tr class="text-uppercase fs-xxs">
                                                <th class="ps-3" style="width: 1%;">
                                                    <input data-table-select-all class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                                </th>
                                                <th data-table-sort>Orden</th>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th data-table-sort data-column="total">Total</th>
                                                <th data-table-sort data-column="status">Estado</th>
                                                <th data-table-sort>Fecha</th>
                                                <th class="text-center" style="width: 1%;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#ORD-00087</a></td>
                                                <td>Nova Media</td>
                                                <td>SEO + Contenidos</td>
                                                <td>$2.150.000</td>
                                                <td><span class="badge bg-info-subtle text-info">En ejecución</span></td>
                                                <td>20 Jul, 2025</td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#ORD-00088</a></td>
                                                <td>Studio Alto</td>
                                                <td>Diseño UX/UI</td>
                                                <td>$980.000</td>
                                                <td><span class="badge bg-warning-subtle text-warning">Pendiente</span></td>
                                                <td>21 Jul, 2025</td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="Órdenes"></div>
                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

    <script src="assets/js/pages/custom-table.js"></script>

</body>

</html>
