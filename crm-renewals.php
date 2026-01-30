<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Renewals"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Renovaciones"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="collapse" id="createRenewalForm">
                            <div class="card mb-3">
                                <div class="card-header border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">Nueva Renovación</h5>
                                            <p class="text-muted mb-0">Completa la información para registrar una nueva renovación.</p>
                                        </div>
                                        <button type="button" class="btn btn-light btn-icon" data-bs-toggle="collapse" data-bs-target="#createRenewalForm" aria-controls="createRenewalForm" aria-expanded="true">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <form>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="renewalClient">Cliente</label>
                                                <input type="text" class="form-control" id="renewalClient" placeholder="Nombre del cliente">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="renewalService">Servicio</label>
                                                <input type="text" class="form-control" id="renewalService" placeholder="Servicio vigente">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="renewalAmount">Monto de renovación</label>
                                                <input type="number" class="form-control" id="renewalAmount" placeholder="Ej. 450000">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="renewalStatus">Estado</label>
                                                <select class="form-select" id="renewalStatus">
                                                    <option value="">Seleccionar estado</option>
                                                    <option>Pendiente</option>
                                                    <option>En negociación</option>
                                                    <option>Renovado</option>
                                                    <option>No renovado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="renewalDate">Fecha</label>
                                                <input type="date" class="form-control" id="renewalDate">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="renewalNotes">Notas</label>
                                                <textarea class="form-control" id="renewalNotes" rows="3" placeholder="Condiciones, alertas, responsables"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex flex-column flex-sm-row gap-2">
                                        <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-toggle="collapse" data-bs-target="#createRenewalForm" aria-controls="createRenewalForm" aria-expanded="true">Cancelar</button>
                                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Guardar Renovación</button>
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
                                        <input data-table-search type="search" class="form-control" placeholder="Buscar renovación...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createRenewalForm" aria-expanded="false" aria-controls="createRenewalForm"><i class="ti ti-plus me-1"></i> Nueva Renovación</button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Estado</option>
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="En negociación">En negociación</option>
                                            <option value="Renovado">Renovado</option>
                                            <option value="No renovado">No renovado</option>
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
                                                <th data-table-sort>Renovación</th>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th data-table-sort data-column="amount">Monto</th>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#REN-00314</a></td>
                                                <td>Digital Seven</td>
                                                <td>Mantenimiento Web</td>
                                                <td>$450.000</td>
                                                <td><span class="badge bg-info-subtle text-info">En negociación</span></td>
                                                <td>05 Ago, 2025</td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#REN-00315</a></td>
                                                <td>Oceanic Spa</td>
                                                <td>Ads Mensual</td>
                                                <td>$620.000</td>
                                                <td><span class="badge bg-success-subtle text-success">Renovado</span></td>
                                                <td>10 Ago, 2025</td>
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
                                    <div data-table-pagination-info="Renovaciones"></div>
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
