<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Caja Chica - Registro"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">

                <?php $subtitle = "Caja Chica"; $title = "Registro de Boletas"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Nueva boleta</h4>
                                <span class="text-muted fs-13">Agrega productos y observaciones por ítem.</span>
                            </div>
                            <div class="card-body">
                                <form id="pettyCashForm" class="row g-3">
                                    <div class="col-md-3">
                                        <label for="receiptNumber" class="form-label">N° Boleta</label>
                                        <input type="text" class="form-control" id="receiptNumber" placeholder="Ej. B-10025" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="receiptDate" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="receiptDate" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="receiptSupplier" class="form-label">Proveedor</label>
                                        <input type="text" class="form-control" id="receiptSupplier" placeholder="Nombre proveedor" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="receiptCurrency" class="form-label">Moneda</label>
                                        <select id="receiptCurrency" class="form-select" required>
                                            <option value="PEN" selected>PEN</option>
                                            <option value="USD">USD</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="mb-0">Productos de la boleta</h5>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                                                    <i class="ti ti-plus me-1"></i>Agregar producto rápido
                                                </button>
                                                <button type="button" class="btn btn-primary" id="addRowBtn">
                                                    <i class="ti ti-row-insert-bottom me-1"></i>Agregar fila
                                                </button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle mb-0" id="receiptItemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 28%;">Producto</th>
                                                        <th style="width: 12%;">Cantidad</th>
                                                        <th style="width: 14%;">Precio Unit.</th>
                                                        <th style="width: 14%;">Subtotal</th>
                                                        <th style="width: 24%;">Observación</th>
                                                        <th style="width: 8%;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="receiptItemsBody"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-4 ms-auto">
                                        <label class="form-label">Total boleta</label>
                                        <input type="text" class="form-control fw-bold" id="receiptTotal" value="0.00" readonly>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button type="reset" class="btn btn-outline-secondary" id="clearFormBtn">Limpiar</button>
                                            <button type="submit" class="btn btn-success">Guardar boleta</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php include('partials/footer.php'); ?>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar producto rápido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickProductForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quickProductName" class="form-label">Nombre del producto</label>
                            <input type="text" class="form-control" id="quickProductName" required>
                        </div>
                        <div class="mb-3">
                            <label for="quickProductCategory" class="form-label">Categoría</label>
                            <input type="text" class="form-control" id="quickProductCategory" placeholder="Librería, limpieza, etc.">
                        </div>
                        <div>
                            <label for="quickProductPrice" class="form-label">Precio sugerido</label>
                            <input type="number" class="form-control" id="quickProductPrice" min="0" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('partials/customizer.php'); ?>
    <?php include('partials/footer-scripts.php'); ?>

    <script src="assets/js/pages/caja-chica-registro.js"></script>
</body>

</html>
