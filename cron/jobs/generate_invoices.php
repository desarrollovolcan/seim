<?php
require __DIR__ . '/../../app/bootstrap.php';

$settings = new SettingsModel($db);
$prefix = $settings->get('invoice_prefix', 'FAC-');
$invoicesModel = new InvoicesModel($db);
$services = $db->fetchAll('SELECT * FROM services WHERE status = "activo" AND auto_invoice = 1 AND deleted_at IS NULL');

foreach ($services as $service) {
    $today = new DateTime();
    $start = new DateTime($service['start_date'] ?? date('Y-m-d'));
    $cycle = $service['billing_cycle'];

    $period = $cycle === 'mensual' ? $today->format('Y-m') : $today->format('Y');
    $exists = $db->fetch('SELECT id FROM invoices WHERE service_id = :service_id AND DATE_FORMAT(fecha_emision, :format) = :period', [
        'service_id' => $service['id'],
        'format' => $cycle === 'mensual' ? '%Y-%m' : '%Y',
        'period' => $period,
    ]);
    if ($exists) {
        continue;
    }

    $number = $invoicesModel->nextNumber($prefix);
    $invoiceId = $invoicesModel->create([
        'client_id' => $service['client_id'],
        'service_id' => $service['id'],
        'numero' => $number,
        'fecha_emision' => $today->format('Y-m-d'),
        'fecha_vencimiento' => $service['due_date'] ?? $today->format('Y-m-d'),
        'estado' => 'pendiente',
        'subtotal' => $service['cost'],
        'impuestos' => 0,
        'total' => $service['cost'],
        'notas' => 'Factura automática generada',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $itemsModel = new InvoiceItemsModel($db);
    $itemsModel->create([
        'invoice_id' => $invoiceId,
        'descripcion' => $service['name'],
        'cantidad' => 1,
        'precio_unitario' => $service['cost'],
        'total' => $service['cost'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
}
