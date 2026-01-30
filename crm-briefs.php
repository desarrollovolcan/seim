<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Briefs"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Briefs Comerciales"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="collapse" id="createBriefForm">
                            <div class="card mb-3">
                                <div class="card-header border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">Nuevo Brief Comercial</h5>
                                            <p class="text-muted mb-0">Completa el brief para registrar una nueva oportunidad.</p>
                                        </div>
                                        <button type="button" class="btn btn-light btn-icon" data-bs-toggle="collapse" data-bs-target="#createBriefForm" aria-controls="createBriefForm" aria-expanded="true">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <form>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="briefClient">Cliente</label>
                                                <input type="text" class="form-control" id="briefClient" placeholder="Nombre del cliente">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="briefService">Servicio solicitado</label>
                                                <input type="text" class="form-control" id="briefService" placeholder="Ej. Branding, Ads">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="briefBudget">Presupuesto estimado</label>
                                                <input type="number" class="form-control" id="briefBudget" placeholder="Ej. 1500000">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="briefStatus">Estado</label>
                                                <select class="form-select" id="briefStatus">
                                                    <option value="">Seleccionar estado</option>
                                                    <option>Nuevo</option>
                                                    <option>En revisión</option>
                                                    <option>Aprobado</option>
                                                    <option>Descartado</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="briefNotes">Necesidades del cliente</label>
                                                <textarea class="form-control" id="briefNotes" rows="3" placeholder="Contexto, objetivos, requerimientos"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex flex-column flex-sm-row gap-2">
                                        <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-toggle="collapse" data-bs-target="#createBriefForm" aria-controls="createBriefForm" aria-expanded="true">Cancelar</button>
                                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Guardar Brief</button>
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
                                        <input data-table-search type="search" class="form-control" placeholder="Buscar brief...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createBriefForm" aria-expanded="false" aria-controls="createBriefForm"><i class="ti ti-plus me-1"></i> Nuevo Brief</button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Estado</option>
                                            <option value="Nuevo">Nuevo</option>
                                            <option value="En revisión">En revisión</option>
                                            <option value="Aprobado">Aprobado</option>
                                            <option value="Descartado">Descartado</option>
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
                                                <th data-table-sort>Brief ID</th>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th data-table-sort data-column="budget">Presupuesto</th>
                                                <th data-table-sort data-column="status">Estado</th>
                                                <th data-table-sort>Creado</th>
                                                <th class="text-center" style="width: 1%;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#BRF-00021</a></td>
                                                <td>Innova Labs</td>
                                                <td>Landing + Ads</td>
                                                <td>$1.200.000</td>
                                                <td><span class="badge bg-warning-subtle text-warning">En revisión</span></td>
                                                <td>12 Jul, 2025</td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#BRF-00022</a></td>
                                                <td>Grupo Norte</td>
                                                <td>Branding</td>
                                                <td>$850.000</td>
                                                <td><span class="badge bg-success-subtle text-success">Aprobado</span></td>
                                                <td>15 Jul, 2025</td>
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
                                    <div data-table-pagination-info="Briefs"></div>
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
