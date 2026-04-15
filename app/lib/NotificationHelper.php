<?php

/**
 * NotificationHelper — static convenience methods for creating notifications
 * from various trigger points across controllers.
 *
 * Usage:
 *   require_once __DIR__ . '/../lib/NotificationHelper.php';
 *   NotificationHelper::notifyTicketMessage($ticketId, $senderId, $senderName);
 */
class NotificationHelper
{
    /**
     * Get or create a Notification model instance.
     */
    private static function model(): Notification
    {
        if (!class_exists('Notification', false)) {
            require_once __DIR__ . '/../models/Notification.php';
        }
        return new Notification();
    }

    /**
     * Notify the other party in a ticket chat when a new message is sent.
     * Determines the recipient by looking at the ticket owner vs sender.
     */
    public static function notifyTicketMessage(int $ticketId, int $senderId, string $senderName): void
    {
        try {
            $db = Database::getInstance();

            // Get ticket owner and assigned staff
            $stmt = $db->prepare("SELECT u_id, assigned_to, title FROM tickets WHERE ticket_id = ? LIMIT 1");
            if (!$stmt) return;
            $stmt->bind_param('i', $ticketId);
            $stmt->execute();
            $result = $stmt->get_result();
            $ticket = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$ticket) return;

            $ownerId = (int)($ticket['u_id'] ?? 0);
            $staffId = (int)($ticket['assigned_to'] ?? 0);
            $title = $ticket['title'] ?? 'Ticket';

            // Determine recipient: if sender is owner, notify staff; otherwise notify owner
            $recipientId = ($senderId === $ownerId) ? $staffId : $ownerId;
            if ($recipientId <= 0) return;

            $msg = $senderName . ' sent a message on "' . self::truncate($title, 40) . '"';
            self::model()->create($recipientId, 'ticket_message', $msg, 'ticket', $ticketId);
        } catch (Throwable $e) {
            error_log('NotificationHelper::notifyTicketMessage error: ' . $e->getMessage());
        }
    }

    /**
     * Notify the ticket owner when their ticket is assigned to a staff member.
     */
    public static function notifyTicketAssigned(int $ticketId, int $staffId): void
    {
        try {
            $db = Database::getInstance();

            $stmt = $db->prepare("SELECT t.u_id, t.title, u.name AS staff_name FROM tickets t LEFT JOIN users u ON u.u_id = ? WHERE t.ticket_id = ? LIMIT 1");
            if (!$stmt) return;
            $stmt->bind_param('ii', $staffId, $ticketId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$row) return;

            $ownerId = (int)($row['u_id'] ?? 0);
            $staffName = $row['staff_name'] ?? 'A staff member';
            $title = $row['title'] ?? 'Your ticket';

            if ($ownerId <= 0) return;

            $msg = $staffName . ' has been assigned to "' . self::truncate($title, 40) . '"';
            self::model()->create($ownerId, 'ticket_assigned', $msg, 'ticket', $ticketId);
        } catch (Throwable $e) {
            error_log('NotificationHelper::notifyTicketAssigned error: ' . $e->getMessage());
        }
    }

    /**
     * Notify the ticket owner when their ticket status changes.
     */
    public static function notifyTicketStatusChange(int $ticketId, string $newStatus, ?int $actorId = null): void
    {
        try {
            $db = Database::getInstance();

            $stmt = $db->prepare("SELECT u_id, title, assigned_to FROM tickets WHERE ticket_id = ? LIMIT 1");
            if (!$stmt) return;
            $stmt->bind_param('i', $ticketId);
            $stmt->execute();
            $result = $stmt->get_result();
            $ticket = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$ticket) return;

            $ownerId = (int)($ticket['u_id'] ?? 0);
            $staffId = (int)($ticket['assigned_to'] ?? 0);
            $title = $ticket['title'] ?? 'Ticket';

            $statusLabel = ucfirst(str_replace(['-', '_'], ' ', $newStatus));
            $msg = 'Your ticket "' . self::truncate($title, 40) . '" is now ' . $statusLabel;

            // Notify owner (unless they are the actor)
            if ($ownerId > 0 && $ownerId !== $actorId) {
                self::model()->create($ownerId, 'ticket_status', $msg, 'ticket', $ticketId);
            }

            // If student resolved, also notify the assigned staff
            if ($staffId > 0 && $staffId !== $actorId) {
                $staffMsg = 'Ticket "' . self::truncate($title, 40) . '" has been marked as ' . $statusLabel;
                self::model()->create($staffId, 'ticket_status', $staffMsg, 'ticket', $ticketId);
            }
        } catch (Throwable $e) {
            error_log('NotificationHelper::notifyTicketStatusChange error: ' . $e->getMessage());
        }
    }

    /**
     * Notify new staff member when a ticket is forwarded/escalated to them.
     */
    public static function notifyTicketForward(int $ticketId, int $newStaffId, ?string $forwarderName = null): void
    {
        try {
            $db = Database::getInstance();

            $stmt = $db->prepare("SELECT title FROM tickets WHERE ticket_id = ? LIMIT 1");
            if (!$stmt) return;
            $stmt->bind_param('i', $ticketId);
            $stmt->execute();
            $result = $stmt->get_result();
            $ticket = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$ticket) return;

            $title = $ticket['title'] ?? 'A ticket';
            $from = $forwarderName ? " by $forwarderName" : '';
            $msg = 'Ticket "' . self::truncate($title, 40) . '" has been forwarded to you' . $from;

            self::model()->create($newStaffId, 'ticket_assigned', $msg, 'ticket', $ticketId);
        } catch (Throwable $e) {
            error_log('NotificationHelper::notifyTicketForward error: ' . $e->getMessage());
        }
    }

    /**
     * Truncate a string to a given length with ellipsis.
     */
    private static function truncate(string $str, int $len): string
    {
        return (mb_strlen($str) > $len) ? mb_substr($str, 0, $len) . '…' : $str;
    }
}
