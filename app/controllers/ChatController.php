<?php

class ChatController extends Controller
{
    public function messages(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $threadId = (int)($_GET['thread'] ?? 0);
        if ($threadId === 0) {
            echo json_encode(['messages' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        $chatModel = new ChatModel($this->db);
        $sinceId = (int)($_GET['since'] ?? 0);
        $thread = $chatModel->getThread($threadId, current_company_id());
        if (!$thread) {
            echo json_encode(['messages' => []], JSON_UNESCAPED_UNICODE);
            return;
        }
        $messages = $sinceId > 0
            ? $chatModel->getMessagesSince($threadId, $sinceId)
            : $chatModel->getMessages($threadId);

        echo json_encode(['messages' => $messages], JSON_UNESCAPED_UNICODE);
    }

    public function notifications(): void
    {
        $this->requireLogin();
        header('Content-Type: application/json; charset=utf-8');

        $chatModel = new ChatModel($this->db);
        $latestId = $chatModel->getLatestMessageIdForAdmin(current_company_id());
        echo json_encode(['latest_id' => $latestId], JSON_UNESCAPED_UNICODE);
    }
}
