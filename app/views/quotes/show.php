<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="card-title mb-0">Cotización <?php echo e($quote['numero']); ?></h4>
            <?php echo render_id_badge($quote['id'] ?? null); ?>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?route=quotes/print&id=<?php echo $quote['id']; ?>" class="btn btn-outline-primary btn-sm" target="_blank">Imprimir</a>
            <a href="index.php?route=quotes" class="btn btn-light btn-sm">Volver</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo render_id_badge($quote['id'] ?? null); ?></p>
                <p><strong>Cliente:</strong> <?php echo e($client['name'] ?? ''); ?></p>
                <p><strong>Emisión:</strong> <?php echo e($quote['fecha_emision']); ?></p>
                <p><strong>Estado:</strong> <?php echo e($quote['estado']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Subtotal:</strong> <?php echo e(format_currency((float)($quote['subtotal'] ?? 0))); ?></p>
                <p><strong>Impuestos:</strong> <?php echo e(format_currency((float)($quote['impuestos'] ?? 0))); ?></p>
                <p><strong>Total:</strong> <?php echo e(format_currency((float)($quote['total'] ?? 0))); ?></p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-uppercase text-muted mb-2">Datos tributarios (SII)</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Documento:</strong>
                            <?php
                            $docType = $quote['sii_document_type'] ?? '';
                            $docLabel = sii_document_types()[$docType] ?? $docType;
                            ?>
                            <?php echo e($docLabel ?: 'No informado'); ?>
                        </p>
                        <p class="mb-1"><strong>Folio:</strong> <?php echo e($quote['sii_document_number'] ?? ''); ?></p>
                        <p class="mb-1"><strong>RUT receptor:</strong> <?php echo e($quote['sii_receiver_rut'] ?? ''); ?></p>
                        <p class="mb-1"><strong>Razón social:</strong> <?php echo e($quote['sii_receiver_name'] ?? ''); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Giro:</strong> <?php echo e($quote['sii_receiver_giro'] ?? ''); ?></p>
                        <p class="mb-1"><strong>Código actividad:</strong> <?php echo e($quote['sii_receiver_activity_code'] ?? ''); ?></p>
                        <p class="mb-1"><strong>Dirección:</strong> <?php echo e($quote['sii_receiver_address'] ?? ''); ?></p>
                        <p class="mb-1"><strong>Comuna/Ciudad:</strong> <?php echo e(trim(($quote['sii_receiver_commune'] ?? '') . ' ' . ($quote['sii_receiver_city'] ?? ''))); ?></p>
                        <p class="mb-1"><strong>Tasa impuesto:</strong> <?php echo e(number_format((float)($quote['sii_tax_rate'] ?? 0), 2)); ?>%</p>
                        <p class="mb-1"><strong>Monto exento:</strong> <?php echo e(format_currency((float)($quote['sii_exempt_amount'] ?? 0))); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($quote['notas'])): ?>
            <p><strong>Notas:</strong> <?php echo e($quote['notas']); ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Items</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo e($item['descripcion']); ?></td>
                            <td><?php echo e($item['cantidad']); ?></td>
                            <td><?php echo e(format_currency((float)($item['precio_unitario'] ?? 0))); ?></td>
                            <td><?php echo e(format_currency((float)($item['total'] ?? 0))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
