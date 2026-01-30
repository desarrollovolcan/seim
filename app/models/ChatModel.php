<?php

class ChatModel
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getThreadsForAdmin(?int $companyId = null): array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetchAll(
            'SELECT chat_threads.*,
                    clients.name AS client_name,
                    clients.email AS client_email,
                    clients.avatar_path AS client_avatar,
                    latest.message AS last_message,
                    latest.created_at AS last_message_at
             FROM chat_threads
             JOIN clients ON chat_threads.client_id = clients.id
             LEFT JOIN (
                 SELECT thread_id, message, created_at
                 FROM chat_messages
                 WHERE id IN (
                     SELECT MAX(id) FROM chat_messages GROUP BY thread_id
                 )
             ) AS latest ON latest.thread_id = chat_threads.id
             WHERE chat_threads.company_id = :company_id
             ORDER BY chat_threads.updated_at DESC',
            ['company_id' => $companyId]
        );
    }

    public function getThreadsForClient(int $clientId): array
    {
        return $this->db->fetchAll(
            'SELECT chat_threads.*,
                    latest.message AS last_message,
                    latest.created_at AS last_message_at
             FROM chat_threads
             LEFT JOIN (
                 SELECT thread_id, message, created_at
                 FROM chat_messages
                 WHERE id IN (
                     SELECT MAX(id) FROM chat_messages GROUP BY thread_id
                 )
             ) AS latest ON latest.thread_id = chat_threads.id
             WHERE chat_threads.client_id = :client_id
             ORDER BY chat_threads.updated_at DESC',
            ['client_id' => $clientId]
        );
    }

    public function getThread(int $threadId, ?int $companyId = null): ?array
    {
        $companyId = $companyId ?? current_company_id();
        return $this->db->fetch(
            'SELECT chat_threads.*, clients.name AS client_name, clients.email AS client_email
             FROM chat_threads
             JOIN clients ON chat_threads.client_id = clients.id
             WHERE chat_threads.id = :id AND chat_threads.company_id = :company_id',
            ['id' => $threadId, 'company_id' => $companyId]
        );
    }

    public function getThreadForClient(int $threadId, int $clientId): ?array
    {
        return $this->db->fetch(
            'SELECT chat_threads.*
             FROM chat_threads
             WHERE chat_threads.id = :id AND chat_threads.client_id = :client_id',
            ['id' => $threadId, 'client_id' => $clientId]
        );
    }

    public function getMessages(int $threadId): array
    {
        return $this->db->fetchAll(
            'SELECT chat_messages.id,
                    chat_messages.thread_id,
                    chat_messages.sender_type,
                    chat_messages.sender_id,
                    chat_messages.message,
                    chat_messages.created_at,
                    CASE
                        WHEN chat_messages.sender_type = "user" THEN users.name
                        ELSE clients.name
                    END AS sender_name,
                    CASE
                        WHEN chat_messages.sender_type = "user" THEN users.avatar_path
                        ELSE clients.avatar_path
                    END AS sender_avatar
             FROM chat_messages
             LEFT JOIN users ON chat_messages.sender_type = "user" AND chat_messages.sender_id = users.id
             LEFT JOIN clients ON chat_messages.sender_type = "client" AND chat_messages.sender_id = clients.id
             WHERE chat_messages.thread_id = :thread_id
             ORDER BY chat_messages.created_at ASC',
            ['thread_id' => $threadId]
        );
    }

    public function getMessagesSince(int $threadId, int $sinceId): array
    {
        return $this->db->fetchAll(
            'SELECT chat_messages.id,
                    chat_messages.thread_id,
                    chat_messages.sender_type,
                    chat_messages.sender_id,
                    chat_messages.message,
                    chat_messages.created_at,
                    CASE
                        WHEN chat_messages.sender_type = "user" THEN users.name
                        ELSE clients.name
                    END AS sender_name,
                    CASE
                        WHEN chat_messages.sender_type = "user" THEN users.avatar_path
                        ELSE clients.avatar_path
                    END AS sender_avatar
             FROM chat_messages
             LEFT JOIN users ON chat_messages.sender_type = "user" AND chat_messages.sender_id = users.id
             LEFT JOIN clients ON chat_messages.sender_type = "client" AND chat_messages.sender_id = clients.id
             WHERE chat_messages.thread_id = :thread_id AND chat_messages.id > :since_id
             ORDER BY chat_messages.created_at ASC',
            ['thread_id' => $threadId, 'since_id' => $sinceId]
        );
    }

    public function createThread(int $clientId, string $subject, ?int $companyId = null): int
    {
        $companyId = $companyId ?? current_company_id();
        $now = date('Y-m-d H:i:s');
        $this->db->execute(
            'INSERT INTO chat_threads (company_id, client_id, subject, status, created_at, updated_at)
             VALUES (:company_id, :client_id, :subject, :status, :created_at, :updated_at)',
            [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'subject' => $subject,
                'status' => 'abierto',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        return (int)$this->db->lastInsertId();
    }

    public function addMessage(int $threadId, string $senderType, int $senderId, string $message): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->execute(
            'INSERT INTO chat_messages (thread_id, sender_type, sender_id, message, created_at)
             VALUES (:thread_id, :sender_type, :sender_id, :message, :created_at)',
            [
                'thread_id' => $threadId,
                'sender_type' => $senderType,
                'sender_id' => $senderId,
                'message' => $message,
                'created_at' => $now,
            ]
        );
        $this->db->execute(
            'UPDATE chat_threads SET updated_at = :updated_at WHERE id = :id',
            ['updated_at' => $now, 'id' => $threadId]
        );
    }

    public function getLatestMessageIdForAdmin(?int $companyId = null): int
    {
        $companyId = $companyId ?? current_company_id();
        $row = $this->db->fetch(
            'SELECT COALESCE(MAX(chat_messages.id), 0) AS latest_id
             FROM chat_messages
             JOIN chat_threads ON chat_messages.thread_id = chat_threads.id
             WHERE chat_threads.company_id = :company_id',
            ['company_id' => $companyId]
        );
        return (int)($row['latest_id'] ?? 0);
    }

    public function getLatestMessageIdForClient(int $clientId): int
    {
        $row = $this->db->fetch(
            'SELECT COALESCE(MAX(chat_messages.id), 0) AS latest_id
             FROM chat_messages
             JOIN chat_threads ON chat_messages.thread_id = chat_threads.id
             WHERE chat_threads.client_id = :client_id',
            ['client_id' => $clientId]
        );
        return (int)($row['latest_id'] ?? 0);
    }
}
