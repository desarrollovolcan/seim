<?php

class NotificationsController extends Controller
{
    private NotificationsModel $notificationsModel;

    public function __construct(array $config, Database $db)
    {
        parent::__construct($config, $db);
        $this->notificationsModel = new NotificationsModel($db);
    }

    public function index(): void
    {
        $this->requireLogin();
        $notifications = $this->notificationsModel->all('company_id = :company_id', [
            'company_id' => current_company_id(),
        ]);
        $this->render('notifications/index', [
            'title' => 'Notificaciones',
            'pageTitle' => 'Notificaciones',
            'notifications' => $notifications,
        ]);
    }
}
