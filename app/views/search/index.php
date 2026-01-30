<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="index.php">
            <input type="hidden" name="route" value="search">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Buscar clientes, proyectos, servicios o facturas" value="<?php echo e($term); ?>">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Clientes</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($clients as $client): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo e($client['name']); ?>
                            <span class="text-muted fs-xs"><?php echo e($client['email']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Proyectos</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($projects as $project): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo e($project['name']); ?>
                            <span class="badge bg-light text-dark"><?php echo e($project['status']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Servicios</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($services as $service): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo e($service['name']); ?>
                            <span class="badge bg-info-subtle text-info"><?php echo e($service['service_type']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Facturas</h4></div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($invoices as $invoice): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo e($invoice['numero']); ?>
                            <span class="badge bg-secondary-subtle text-secondary"><?php echo e($invoice['estado']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
