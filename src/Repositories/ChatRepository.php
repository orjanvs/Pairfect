<?php
namespace App\Repositories;
use PDO;

class ChatRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Generate a conversation title based on the initial message
     * @param string $initialMessage The initial message content
     * @return string The generated conversation title
     */
    public function generateConversationTitle(string $initialMessage): string
    {
        $maxLength = 60; 
        $title = trim($initialMessage);
        if ($title === "") return "New conversation";
        $title = mb_strimwidth($title, 0, $maxLength, "...");
        return $title;
    }

    /**
     * Create a new conversation
     * @param int $userId The ID of the user starting the conversation
     * @param string $title The title of the conversation
     * @return int The ID of the newly created conversation
     */
    public function createConversation(int $userId, string $title): int 
    {
        $sql = "INSERT INTO conversations (userid, title) VALUES (:userid, :title)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":userid" => $userId,
            ":title" => $title
        ]);
        return (int)$this->pdo->lastInsertId(); // Return the new conversation ID 
    }

    /**
     * Add a message to a conversation
     * @param int $convoId The ID of the conversation
     * @param string $role The role of the message sender (e.g., 'model', 'user')
     * @param string $content The content of the message
     * @return bool True if the message was successfully added, false otherwise
     */
    public function addMessage(int $convoId, string $role, string $content) 
    {
        $sql = "INSERT INTO messages (convo_id, role, content) 
                VALUES (:convo_id, :role, :content)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ":convo_id" => $convoId,
            ":role" => $role,
            ":content" => $content
        ]);
    }

    /**
     * Get conversations for a user
     * @param int $userId The ID of the user
     * @return array An array of conversations
     */
    public function getConversationsByUserId(int $userId): array
    {
        $sql = "SELECT convo_id, title, started_at 
                FROM conversations 
                WHERE userid = :userid 
                ORDER BY started_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":userid" => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get messages for a conversation and user
     * @param int $convoId The ID of the conversation
     * @param int $userId The ID of the user
     * @return array An array of messages
     */
    public function getMessagesByConversationIdForUser(int $convoId, int $userId): array
    {
        $sql = "SELECT m.msg_id, m.role, m.content, m.created_at  
                FROM messages m
                INNER JOIN conversations c ON m.convo_id = c.convo_id
                WHERE m.convo_id = :convo_id 
                AND c.userid = :userid  
                ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":convo_id" => $convoId, 
            ":userid" => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a conversation by ID for a specific user
     * @param int $convoId The ID of the conversation
     * @param int $userId The ID of the user
     * @return array|null The conversation data or null if not found
     */
    public function getConversationByIdForUser(int $convoId, int $userId): ?array
    {
        $sql = "SELECT convo_id, title, started_at, userid
                FROM conversations 
                WHERE convo_id = :convo_id AND userid = :userid 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":convo_id" => $convoId, 
            ":userid" => $userId
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}