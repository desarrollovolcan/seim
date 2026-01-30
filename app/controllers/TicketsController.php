<?php

class TicketsController extends Controller
{
    private SupportTicketsModel $tickets;
    private SupportTicketMessagesModel $messages;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->tickets = new SupportTicketsModel($db);
        $this->messages = new SupportTicketMessagesModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $tickets = $this->tickets->allWithClient(current_company_id());
        $this->render('tickets/index', [
            'title' => 'Tickets de soporte',
            'pageTitle' => 'Tickets de soporte',
            'tickets' => $tickets,
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $companyId = current_company_id();
        $clients = $this->db->fetchAll(
            'SELECT id, name, email FROM clients WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY name',
            ['company_id' => $companyId]
        );
        $users = $this->db->fetchAll(
            'SELECT id, name FROM users WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY name',
            ['company_id' => $companyId]
        );
        $selectedClientId = (int)($_GET['client_id'] ?? 0);
        $this->render('tickets/create', [
            'title' => 'Nuevo ticket',
            'pageTitle' => 'Nuevo ticket',
            'clients' => $clients,
            'users' => $users,
            'selectedClientId' => $selectedClientId,
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        verify_csrf();
        $clientId = (int)($_POST['client_id'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($clientId === 0 || $subject === '' || $description === '') {
            flash('error', 'Completa los campos obligatorios.');
            $this->redirect('index.php?route=tickets/create');
        }
        $companyId = current_company_id();
        $client = $this->db->fetch(
            'SELECT id FROM clients WHERE id = :id AND company_id = :company_id',
            ['id' => $clientId, 'company_id' => $companyId]
        );
        if (!$client) {
            flash('error', 'Cliente no encontrado para esta empresa.');
            $this->redirect('index.php?route=tickets/create');
        }
        $priority = $_POST['priority'] ?? 'media';
        $assignedUser = (int)($_POST['assigned_user_id'] ?? 0);
        $now = date('Y-m-d H:i:s');
        $ticketId = $this->tickets->create([
            'company_id' => $companyId,
            'client_id' => $clientId,
            'subject' => $subject,
            'description' => $description,
            'status' => 'abierto',
            'priority' => $priority,
            'assigned_user_id' => $assignedUser ?: null,
            'created_by_type' => 'user',
            'created_by_id' => (int)Auth::user()['id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->messages->create([
            'ticket_id' => $ticketId,
            'sender_type' => 'user',
            'sender_id' => (int)Auth::user()['id'],
            'message' => $description,
            'created_at' => $now,
        ]);
        flash('success', 'Ticket creado correctamente.');
        $this->redirect('index.php?route=tickets/show&id=' . $ticketId);
    }

    public function show(): void
    {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $ticket = $this->tickets->findWithClient($id, current_company_id());
        if (!$ticket) {
            $this->redirect('index.php?route=tickets');
        }
        $messages = $this->messages->forTicket($id);
        $users = $this->db->fetchAll(
            'SELECT id, name FROM users WHERE deleted_at IS NULL AND company_id = :company_id ORDER BY name',
            ['company_id' => current_company_id()]
        );
        $this->render('tickets/show', [
            'title' => 'Ticket #' . $id,
            'pageTitle' => 'Ticket #' . $id,
            'ticket' => $ticket,
            'messages' => $messages,
            'users' => $users,
        ]);
    }

    public function addMessage(): void
    {
        $this->requireLogin();
        verify_csrf();
        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($ticketId === 0 || $message === '') {
            flash('error', 'Escribe un mensaje antes de enviar.');
            $this->redirect('index.php?route=tickets/show&id=' . $ticketId);
        }
        $ticket = $this->tickets->findWithClient($ticketId, current_company_id());
        if (!$ticket) {
            $this->redirect('index.php?route=tickets');
        }
        $now = date('Y-m-d H:i:s');
        $this->messages->create([
            'ticket_id' => $ticketId,
            'sender_type' => 'user',
            'sender_id' => (int)Auth::user()['id'],
            'message' => $message,
            'created_at' => $now,
        ]);
        $this->tickets->update($ticketId, [
            'updated_at' => $now,
        ]);
        flash('success', 'Mensaje enviado correctamente.');
        $this->redirect('index.php?route=tickets/show&id=' . $ticketId);
    }

    public function updateStatus(): void
    {
        $this->requireLogin();
        verify_csrf();
        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $assignedUser = (int)($_POST['assigned_user_id'] ?? 0);
        $ticket = $this->tickets->findWithClient($ticketId, current_company_id());
        if (!$ticket) {
            $this->redirect('index.php?route=tickets');
        }
        $allowed = ['abierto', 'en_progreso', 'pendiente', 'resuelto', 'cerrado'];
        if (!in_array($newStatus, $allowed, true)) {
            flash('error', 'Estado inválido.');
            $this->redirect('index.php?route=tickets/show&id=' . $ticketId);
        }
        $now = date('Y-m-d H:i:s');
        $data = [
            'status' => $newStatus,
            'assigned_user_id' => $assignedUser ?: null,
            'updated_at' => $now,
        ];
        if ($newStatus === 'cerrado') {
            $data['closed_at'] = $now;
        }
        $this->tickets->update($ticketId, $data);

        if (($ticket['status'] ?? '') !== $newStatus) {
            $mailer = new Mailer($this->db);
            $subject = 'Actualización de ticket #' . $ticketId;
            $html = sprintf(
                '<p>Hola %s,</p><p>Tu ticket <strong>#%s</strong> cambió de estado a <strong>%s</strong>.</p><p>Asunto: %s</p>',
                e($ticket['client_name'] ?? 'Cliente'),
                $ticketId,
                e(ucfirst(str_replace('_', ' ', $newStatus))),
                e($ticket['subject'] ?? '')
            );
            $mailer->send('support_ticket_status', $ticket['client_email'] ?? '', $subject, $html);
        }

        flash('success', 'Estado actualizado.');
        $this->redirect('index.php?route=tickets/show&id=' . $ticketId);
    }

    public function delete(): void
    {
        $this->requireLogin();
        verify_csrf();
        $ticketId = (int)($_POST['id'] ?? 0);
        $ticket = $this->tickets->findWithClient($ticketId, current_company_id());
        if (!$ticket) {
            flash('error', 'Ticket no encontrado.');
            $this->redirect('index.php?route=tickets');
        }
        $pdo = $this->db->pdo();
        try {
            $pdo->beginTransaction();
            $this->db->execute(
                'DELETE FROM support_ticket_messages WHERE ticket_id = :ticket_id',
                ['ticket_id' => $ticketId]
            );
            $this->db->execute(
                'DELETE FROM support_tickets WHERE id = :id AND company_id = :company_id',
                ['id' => $ticketId, 'company_id' => current_company_id()]
            );
            audit($this->db, Auth::user()['id'], 'delete', 'support_tickets', $ticketId);
            $pdo->commit();
            flash('success', 'Ticket eliminado correctamente.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            log_message('error', 'Failed to delete ticket: ' . $e->getMessage());
            flash('error', 'No se pudo eliminar el ticket.');
        }
        $this->redirect('index.php?route=tickets');
    }

    public function messages(): void
    {
        $this->requireLogin();
        $ticketId = (int)($_GET['ticket_id'] ?? 0);
        $sinceId = (int)($_GET['since_id'] ?? 0);
        $ticket = $this->tickets->findWithClient($ticketId, current_company_id());
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['messages' => []]);
            return;
        }
        $messages = $sinceId > 0
            ? $this->messages->forTicketSince($ticketId, $sinceId)
            : $this->messages->forTicket($ticketId);

        header('Content-Type: application/json');
        echo json_encode(['messages' => $messages]);
    }
}
