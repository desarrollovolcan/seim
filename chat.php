<?php
require __DIR__ . '/app/bootstrap.php';

if (!Auth::check()) {
    header('Location: login.php');
    exit;
}

$chatModel = new ChatModel($db);
$currentUser = Auth::user();
$permissions = [];
if ($currentUser && ($currentUser['role'] ?? '') !== 'admin') {
    $roleId = (int)($currentUser['role_id'] ?? 0);
    if ($roleId === 0 && !empty($currentUser['role'])) {
        $roleRow = $db->fetch('SELECT id FROM roles WHERE name = :name', ['name' => $currentUser['role']]);
        $roleId = (int)($roleRow['id'] ?? 0);
    }
    if ($roleId) {
        $permissions = role_permissions($db, $roleId);
    }
}
try {
    $notifications = $db->fetchAll("SELECT * FROM notifications WHERE read_at IS NULL ORDER BY created_at DESC LIMIT 5");
} catch (PDOException $e) {
    log_message('error', 'Failed to load notifications: ' . $e->getMessage());
    $notifications = [];
}
$notificationCount = count($notifications);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create_thread') {
        $clientId = (int)($_POST['client_id'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($clientId === 0 || $subject === '' || $message === '') {
            $_SESSION['chat_error'] = 'Completa el cliente, asunto y mensaje.';
        } else {
            $threadId = $chatModel->createThread($clientId, $subject);
            $chatModel->addMessage($threadId, 'user', (int)$currentUser['id'], $message);
            $_SESSION['chat_success'] = 'Conversación creada correctamente.';
            header('Location: chat.php?thread=' . $threadId);
            exit;
        }
    }

    if ($action === 'send_message') {
        $threadId = (int)($_POST['thread_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($threadId === 0 || $message === '') {
            $_SESSION['chat_error'] = 'Escribe un mensaje antes de enviar.';
        } else {
            $thread = $chatModel->getThread($threadId);
            if (!$thread) {
                $_SESSION['chat_error'] = 'No encontramos la conversación seleccionada.';
            } else {
                $chatModel->addMessage($threadId, 'user', (int)$currentUser['id'], $message);
                $_SESSION['chat_success'] = 'Mensaje enviado.';
                header('Location: chat.php?thread=' . $threadId);
                exit;
            }
        }
    }
}

$chatThreads = $chatModel->getThreadsForAdmin();
$activeThreadId = (int)($_GET['thread'] ?? 0);
if ($activeThreadId === 0 && !empty($chatThreads)) {
    $activeThreadId = (int)$chatThreads[0]['id'];
}

$activeThread = $activeThreadId ? $chatModel->getThread($activeThreadId) : null;
$chatMessages = $activeThread ? $chatModel->getMessages($activeThreadId) : [];
$clients = $db->fetchAll('SELECT id, name, email FROM clients WHERE deleted_at IS NULL ORDER BY name');
$chatSuccess = $_SESSION['chat_success'] ?? null;
$chatError = $_SESSION['chat_error'] ?? null;
unset($_SESSION['chat_success'], $_SESSION['chat_error']);

$title = 'Chat';
$pageTitle = 'Chat';
$subtitle = 'Apps';
$view = 'chat';
include __DIR__ . '/app/views/layouts/main.php';
