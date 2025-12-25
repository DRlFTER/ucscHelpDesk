<?php
require_once __DIR__ . '/../core/config.php';

class TicketChat
{
    private static function getConnection()
    {
        $conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);
        if ($conn->connect_error) {
            die("DB Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    public function getChatByTicketId($ticketId)
    {
        $conn = self::getConnection();
        $sql = "SELECT * FROM ticket_chat WHERE ticket_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ticketId);
        $stmt->execute();
        $result = $stmt->get_result();
        $chat = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $chat;
    }

    public function createChat($ticketId, $u1Id, $u2Id)
    {
        $conn = self::getConnection();
        $sql = "INSERT INTO ticket_chat (ticket_id, u1_id, u2_id, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $ticketId, $u1Id, $u2Id);
        
        if ($stmt->execute()) {
            $chatId = $stmt->insert_id;
            $stmt->close();
            $conn->close();
            return $chatId;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    public function getMessages($chatId)
    {
        $conn = self::getConnection();
        $sql = "SELECT m.*, u.name as sender_name, u.role as sender_role 
                FROM ticket_messages m 
                JOIN users u ON m.sender_id = u.u_id 
                WHERE m.chat_id = ? 
                ORDER BY m.created_at ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $messages;
    }

    public function sendMessage($chatId, $senderId, $message, $messageType = 'text', $replyToId = null)
    {
        $conn = self::getConnection();
        $sql = "INSERT INTO ticket_messages (chat_id, sender_id, message, message_type, reply_to_id, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $chatId, $senderId, $message, $messageType, $replyToId);
        
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public function markMessagesAsRead($chatId, $userId)
    {
        $conn = self::getConnection();
        // Mark messages as read where the sender is NOT the current user
        $sql = "UPDATE ticket_messages SET is_read = 1 WHERE chat_id = ? AND sender_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $chatId, $userId);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}
