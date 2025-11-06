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

    public function generateConversationTitle(string $initialMessage): string
    {
        $maxLength = 60; 
        $title = trim($initialMessage);
        if ($title === '') return 'New conversation';
        $title = mb_strimwidth($title, 0, $maxLength, '…');
        return $title;
    }

    public function createConversation(int $userId, string $title): int 
    {
        $sql = "INSERT INTO conversations (userid, title) VALUES (:userid, :title)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':userid' => $userId,
            ':title' => $title
        ]);
        return (int)$this->pdo->lastInsertId(); // Return the new conversation ID 
    }

    public function addMessage(int $convoId, string $role, string $content) 
    {
        $sql = "INSERT INTO messages (convo_id, role, content) 
                VALUES (:convo_id, :role, :content)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':convo_id' => $convoId,
            ':role' => $role,
            ':content' => $content
        ]);
    }

    public function getMessagesByConversationId(int $convoId): array
    {
        $sql = "SELECT * FROM messages WHERE convo_id = :convo_id ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':convo_id' => $convoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConversationsByUserId(int $userId): array
    {
        $sql = "SELECT * FROM conversations WHERE userid = :userid ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}