<?php

class EmailQueueController extends Controller
{
    private EmailQueueModel $queue;
    private EmailTemplatesModel $templates;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->queue = new EmailQueueModel($db);
        $this->templates = new EmailTemplatesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $emails = $this->db->fetchAll(
            'SELECT email_queue.*, clients.name as client_name, clients.email, clients.billing_email FROM email_queue LEFT JOIN clients ON email_queue.client_id = clients.id WHERE email_queue.company_id = :company_id ORDER BY email_queue.id DESC',
            ['company_id' => current_company_id()]
        );
        $this->render('email_queue/index', [
            'title' => 'Cola de Correos',
            'pageTitle' => 'Cola de Correos',
            'emails' => $emails,
        ]);
    }

    public function compose(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $templates = $this->templates->all('deleted_at IS NULL AND company_id = :company_id', ['company_id' => $companyId]);
        $clients = $this->db->fetchAll('SELECT * FROM clients WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY name', ['company_id' => $companyId]);
        $this->render('email_queue/compose', [
            'title' => 'Nuevo Correo',
            'pageTitle' => 'Nuevo Correo',
            'templates' => $templates,
            'clients' => $clients,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $this->queue->create([
            'company_id' => current_company_id(),
            'client_id' => $_POST['client_id'] ?: null,
            'template_id' => $_POST['template_id'] ?: null,
            'subject' => trim($_POST['subject'] ?? ''),
            'body_html' => $_POST['body_html'] ?? '',
            'type' => $_POST['type'] ?? 'cobranza',
            'status' => $_POST['status'] ?? 'pending',
            'scheduled_at' => $_POST['scheduled_at'] ?? date('Y-m-d H:i:s'),
            'tries' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        audit($this->db, Auth::user()['id'], 'create', 'email_queue');
        flash('success', 'Correo agregado a la cola.');
        $this->redirect('index.php?route=email-queue');
    }

    public function sendNow(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $email = $this->db->fetch(
            'SELECT * FROM email_queue WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$email) {
            $this->redirect('index.php?route=email-queue');
        }

        if ($email['status'] === 'sent') {
            $this->createNotification('Correo enviado', 'El correo ya fue enviado previamente.', 'info');
            flash('info', 'El correo ya fue enviado previamente.');
            $this->redirect('index.php?route=email-queue');
        }

        if (empty($email['client_id'])) {
            $this->db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = "Sin cliente" WHERE id = :id', ['id' => $email['id']]);
            $this->createNotification('Correo fallido', 'No hay un cliente asociado a este correo.', 'danger');
            flash('error', 'No hay un cliente asociado a este correo.');
            $this->redirect('index.php?route=email-queue');
        }

        $client = $this->db->fetch(
            'SELECT * FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $email['client_id'], 'company_id' => current_company_id()]
        );
        if (!$client) {
            $this->db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = "Cliente no encontrado" WHERE id = :id', ['id' => $email['id']]);
            $this->createNotification('Correo fallido', 'No encontramos el cliente asociado al correo.', 'danger');
            flash('error', 'No encontramos el cliente asociado al correo.');
            $this->redirect('index.php?route=email-queue');
        }

        $recipients = array_filter([
            $client['email'] ?? null,
            $client['billing_email'] ?? null,
        ], fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL));
        if (empty($recipients)) {
            $this->db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = "Sin email" WHERE id = :id', ['id' => $email['id']]);
            $this->createNotification('Correo fallido', 'No hay email asociado al cliente para enviar.', 'danger');
            flash('error', 'No hay email asociado al cliente para enviar.');
            $this->redirect('index.php?route=email-queue');
        }

        try {
            $mailer = new Mailer($this->db);
            $bodyHtml = $email['body_html'];
            if (($email['type'] ?? '') === 'cobranza') {
                $context = $this->buildPendingInvoiceContext((int)$client['id']);
                if ($context) {
                    $bodyHtml = render_template_vars($bodyHtml, $context);
                }
            }
            $sent = $mailer->send('info', $recipients, $email['subject'], $bodyHtml);

            if ($sent) {
                $this->db->execute('UPDATE email_queue SET status = "sent", updated_at = NOW() WHERE id = :id', ['id' => $email['id']]);
                $email['body_html'] = $bodyHtml;
                $this->storeEmailLog($email, 'sent');
                $this->createNotification('Correo enviado', 'El correo se envió correctamente.', 'success');
                flash('success', 'El correo se envió correctamente.');
            } else {
                $errorDetail = $mailer->getLastError() ?: 'Error envío';
                $this->db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = :error WHERE id = :id', [
                    'error' => $errorDetail,
                    'id' => $email['id'],
                ]);
                $this->createNotification('Correo fallido', 'No se pudo enviar el correo.', 'danger');
                flash('error', 'No se pudo enviar el correo.');
            }
        } catch (Throwable $e) {
            $this->db->execute('UPDATE email_queue SET status = "failed", tries = tries + 1, last_error = :error WHERE id = :id', [
                'error' => $e->getMessage(),
                'id' => $email['id'],
            ]);
            log_message('error', 'Email send failed: ' . $e->getMessage());
            $this->createNotification('Correo fallido', 'No se pudo enviar el correo.', 'danger');
            flash('error', 'No se pudo enviar el correo.');
        }

        $this->redirect('index.php?route=email-queue');
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $email = $this->db->fetch(
            'SELECT id FROM email_queue WHERE id = :id AND company_id = :company_id',
            ['id' => $id, 'company_id' => current_company_id()]
        );
        if (!$email) {
            flash('error', 'Correo no encontrado.');
            $this->redirect('index.php?route=email-queue');
        }
        try {
            $this->db->execute(
                'DELETE FROM email_queue WHERE id = :id AND company_id = :company_id',
                ['id' => $id, 'company_id' => current_company_id()]
            );
            audit($this->db, Auth::user()['id'], 'delete', 'email_queue', $id);
            flash('success', 'Correo eliminado de la cola.');
        } catch (Throwable $e) {
            log_message('error', 'Failed to delete queued email: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar el correo.');
        }
        $this->redirect('index.php?route=email-queue');
    }

    private function createNotification(string $title, string $message, string $type): void
    {
        try {
            $this->db->execute('INSERT INTO notifications (company_id, title, message, type, created_at, updated_at) VALUES (:company_id, :title, :message, :type, NOW(), NOW())', [
                'company_id' => current_company_id(),
                'title' => $title,
                'message' => $message,
                'type' => $type,
            ]);
        } catch (PDOException $e) {
            log_message('error', 'Notification insert failed: ' . $e->getMessage());
        }
    }

    private function storeEmailLog(array $email, string $status): void
    {
        try {
            $this->db->execute('INSERT INTO email_logs (company_id, client_id, type, subject, body_html, status, created_at, updated_at) VALUES (:company_id, :client_id, :type, :subject, :body_html, :status, NOW(), NOW())', [
                'company_id' => current_company_id(),
                'client_id' => $email['client_id'],
                'type' => $email['type'],
                'subject' => $email['subject'],
                'body_html' => $email['body_html'],
                'status' => $status,
            ]);
        } catch (PDOException $e) {
            log_message('error', 'Email log insert failed: ' . $e->getMessage());
        }
    }

    private function buildPendingInvoiceContext(int $clientId): ?array
    {
        $companyId = current_company_id();
        $invoice = $this->db->fetch(
            'SELECT invoices.id,
                    invoices.numero,
                    invoices.total,
                    invoices.fecha_vencimiento,
                    COALESCE(SUM(payments.monto), 0) as paid_total,
                    MAX(invoices.total) - COALESCE(SUM(payments.monto), 0) as pending_total
             FROM invoices
             LEFT JOIN payments ON payments.invoice_id = invoices.id
             WHERE invoices.client_id = :client_id AND invoices.company_id = :company_id AND invoices.deleted_at IS NULL
             GROUP BY invoices.id
             HAVING pending_total > 0
             ORDER BY invoices.fecha_vencimiento ASC
             LIMIT 1',
            ['client_id' => $clientId, 'company_id' => $companyId]
        );

        if (!$invoice) {
            return null;
        }

        $client = $this->db->fetch(
            'SELECT name, email, billing_email FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            return null;
        }
        $paymentEmail = $client['billing_email'] ?? $client['email'] ?? '';
        if (!filter_var($paymentEmail, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $items = $this->db->fetchAll('SELECT descripcion FROM invoice_items WHERE invoice_id = :invoice_id', ['invoice_id' => $invoice['id']]);
        $detail = $items[0]['descripcion'] ?? 'Servicios';

        $settings = new SettingsModel($this->db);
        $flowConfig = $settings->get('flow_payment_config', []);
        $invoiceDefaults = $settings->get('invoice_defaults', []);
        $currency = $invoiceDefaults['currency'] ?? 'CLP';
        $pendingAmount = (float)($invoice['pending_total'] ?? 0);
        $flowLink = create_flow_payment_link($flowConfig, [
            'commerce_order' => (string)($invoice['numero'] ?? $invoice['id']),
            'subject' => 'Pago factura #' . ($invoice['numero'] ?? $invoice['id']) . ' - ' . ($client['name'] ?? ''),
            'currency' => (string)$currency,
            'amount' => number_format($pendingAmount, 0, '.', ''),
            'email' => $paymentEmail,
        ]);
        if ($flowLink === null) {
            return null;
        }

        return [
            'cliente_nombre' => $client['name'] ?? '',
            'monto_total' => format_currency($pendingAmount),
            'fecha_vencimiento' => $invoice['fecha_vencimiento'] ?? '',
            'servicio_nombre' => $detail,
            'link_pago' => $flowLink,
            'numero_factura' => $invoice['numero'] ?? '',
            'detalle_factura' => $detail,
        ];
    }
}
