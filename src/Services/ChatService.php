<?php
namespace App\Services;
use App\Repositories\ChatRepository;
use Throwable;

class ChatService
{
    private ?GeminiAPI $gemini = null;
    private ChatRepository $chatRepository;

    public function __construct(ChatRepository $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    public function handleMessage(int $userId, string $message, ?int $convoId = null) : array
    {
        if ($message === "") {
            return [
                "responseMessage" => "Please enter a valid message."
            ];
        }

        // Start or validate conversation
        if ($convoId === null) {
            $convoTitle = $this->chatRepository->generateConversationTitle($message);
            $convoId = $this->chatRepository->createConversation($userId, $convoTitle);
        } else {
            $conversation = $this->chatRepository->getConversationByIdForUser($convoId, $userId);
            if (!$conversation) {
                return [
                    "responseMessage" => "Conversation not found."
                ];
            }
        }

        // Save user message
        $this->chatRepository->addMessage($convoId, "user", $message);

        // Pass convo history to Gemini for context
        $history = $this->chatRepository->getMessagesByConversationIdForUser($convoId, $userId);
        $context = array_slice($history, -10); // limit to last 10 messages for context

         // Get response from Gemini API
        try {
            if ($this->gemini === null) {
                $this->gemini = new GeminiAPI();
            }
            $reply = $this->gemini->geminiChat($context);
            if (!$reply) {
                $reply = "Sorry! Response could not be generated. Please try again.";
            }
        } catch (Throwable $e) {
            error_log("Gemini API error: " . $e->getMessage());
            $reply = "An error occurred while processing your request. Please try again.";
        }
        // Save Gemini response
        $this->chatRepository->addMessage($convoId, "model", $reply);
        
        return [
            "responseMessage" => $reply,
            "convoId" => $convoId
        ];
    }

    // Get all conversations for a user to display in chat history page
    public function getUserConversations(int $userId): array
    {
        return $this->chatRepository->getConversationsByUserId($userId);
    }

    // Get specific conversation with messages
    public function getConversationWithMessages(int $convoId, int $userId): ?array
    {
        // Fetch conversation with ownership check
        $conversation = $this->chatRepository->getConversationByIdForUser($convoId, $userId);
        if (!$conversation) {
            return null; // Conversation not found or does not belong to user
        }
        // Fetch messages for the conversation
        $messages = $this->chatRepository->getMessagesByConversationIdForUser($convoId, $userId);
        return [
            "conversation" => $conversation,
            "messages" => $messages
        ];
    }


    
}