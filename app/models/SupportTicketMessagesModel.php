<?php

class SupportTicketMessagesModel extends Model
{
    protected string $table = 'support_ticket_messages';

    public function forTicket(int $ticketId): array
    {
        return $this->db->fetchAll(
            'SELECT support_ticket_messages.*,
                    CASE
                        WHEN support_ticket_messages.sender_type = "user" THEN users.name
                        ELSE clients.name
                    END AS sender_name,
                    CASE
                        WHEN support_ticket_messages.sender_type = "user" THEN users.avatar_path
                        ELSE clients.avatar_path
                    END AS sender_avatar
             FROM support_ticket_messages
             LEFT JOIN users ON support_ticket_messages.sender_type = "user" AND support_ticket_messages.sender_id = users.id
             LEFT JOIN clients ON support_ticket_messages.sender_type = "client" AND support_ticket_messages.sender_id = clients.id
             WHERE support_ticket_messages.ticket_id = :ticket_id
             ORDER BY support_ticket_messages.created_at ASC',
            ['ticket_id' => $ticketId]
        );
    }

    public function forTicketSince(int $ticketId, int $sinceId): array
    {
        return $this->db->fetchAll(
            'SELECT support_ticket_messages.*,
                    CASE
                        WHEN support_ticket_messages.sender_type = "user" THEN users.name
                        ELSE clients.name
                    END AS sender_name,
                    CASE
                        WHEN support_ticket_messages.sender_type = "user" THEN users.avatar_path
                        ELSE clients.avatar_path
                    END AS sender_avatar
             FROM support_ticket_messages
             LEFT JOIN users ON support_ticket_messages.sender_type = "user" AND support_ticket_messages.sender_id = users.id
             LEFT JOIN clients ON support_ticket_messages.sender_type = "client" AND support_ticket_messages.sender_id = clients.id
             WHERE support_ticket_messages.ticket_id = :ticket_id
               AND support_ticket_messages.id > :since_id
             ORDER BY support_ticket_messages.created_at ASC',
            ['ticket_id' => $ticketId, 'since_id' => $sinceId]
        );
    }
}
