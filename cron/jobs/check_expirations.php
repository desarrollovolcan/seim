<?php
require __DIR__ . '/../../app/bootstrap.php';

$services = $db->fetchAll('SELECT services.*, clients.name as client_name, clients.email, clients.billing_email FROM services JOIN clients ON services.client_id = clients.id WHERE services.status = "activo" AND services.deleted_at IS NULL AND services.due_date IS NOT NULL');

foreach ($services as $service) {
    $daysToExpire = (new DateTime($service['due_date']))->diff(new DateTime())->days;
    if ($service['due_date'] < date('Y-m-d')) {
        $db->execute('INSERT INTO notifications (title, message, type, created_at, updated_at) VALUES (:title, :message, :type, NOW(), NOW())', [
            'title' => 'Servicio vencido',
            'message' => $service['name'] . ' está vencido para ' . $service['client_name'],
            'type' => 'danger',
        ]);
        continue;
    }

    if ((int)$service['notice_days_1'] === $daysToExpire || (int)$service['notice_days_2'] === $daysToExpire) {
        $db->execute('INSERT INTO notifications (title, message, type, created_at, updated_at) VALUES (:title, :message, :type, NOW(), NOW())', [
            'title' => 'Servicio por vencer',
            'message' => $service['name'] . ' vence en ' . $daysToExpire . ' días.',
            'type' => 'warning',
        ]);
    }
}
