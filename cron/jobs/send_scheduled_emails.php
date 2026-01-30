<?php
require __DIR__ . '/../../app/bootstrap.php';

$mailer = new Mailer($db);
$queue = new EmailQueueModel($db);
$pending = $queue->pending();

foreach ($pending as $email) {
    if ((int)$email['tries'] >= 3) {
        $db->execute('UPDATE email_queue SET status = "failed", last_error = "Máximo de intentos" WHERE id = :id', ['id' => $email['id']]);
        continue;
    }
    if (new DateTime($email['scheduled_at']) > new DateTime()) {
        continue;
    }

    $client = null;
    if (!empty($email['client_id'])) {
        $client = $db->fetch('SELECT * FROM clients WHERE id = :id', ['id' => $email['client_id']]);
    }

    $recipients = array_filter([
        $client['email'] ?? null,
        $client['billing_email'] ?? null,
    ], fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL));
    if (empty($recipients)) {
        $db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = "Sin email" WHERE id = :id', ['id' => $email['id']]);
        continue;
    }

    $sent = $mailer->send('info', $recipients, $email['subject'], $email['body_html']);

    if ($sent) {
        $db->execute('UPDATE email_queue SET status = "sent", updated_at = NOW() WHERE id = :id', ['id' => $email['id']]);
        $db->execute('INSERT INTO email_logs (client_id, type, subject, body_html, status, created_at, updated_at) VALUES (:client_id, :type, :subject, :body_html, :status, NOW(), NOW())', [
            'client_id' => $email['client_id'],
            'type' => $email['type'],
            'subject' => $email['subject'],
            'body_html' => $email['body_html'],
            'status' => 'sent',
        ]);
    } else {
        $errorDetail = $mailer->getLastError() ?: 'Error envío';
        $db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = :error WHERE id = :id', [
            'error' => $errorDetail,
            'id' => $email['id'],
        ]);
        $db->execute('INSERT INTO notifications (title, message, type, created_at, updated_at) VALUES (:title, :message, :type, NOW(), NOW())', [
            'title' => 'Correo fallido',
            'message' => 'Error al enviar correo programado ID ' . $email['id'],
            'type' => 'danger',
        ]);
    }
}
