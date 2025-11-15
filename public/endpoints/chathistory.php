<?php
header('Content-Type: application/json');

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Database;
use App\Services\ChatService;
use App\Repositories\ChatRepository;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if user is logged in
if (empty($_SESSION["user"]["is_logged_in"])) {
    http_response_code(401);
    exit;
}

// Load environment variables
if (!file_exists(dirname(__DIR__, 2) . '/.env')) {
    throw new RuntimeException(".env file not found");
}
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $chatRepository = new ChatRepository($pdo);
    $chatService = new ChatService($chatRepository);

    $userId = (int)$_SESSION["user"]["userid"];

    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method Not Allowed."]);
        exit;
    }

    // Fetch conversation ID from query parameters
    $convoId = isset($_GET['convoId']) ? (int)$_GET['convoId'] : 0;

    if ($convoId > 0) {
        $conversation = $chatService->getConversationWithMessages($convoId, $userId);
        if (!$conversation) {
            http_response_code(404);
            echo json_encode(["error" => "Conversation not found."]);
            exit;
        }
        echo json_encode($conversation, JSON_UNESCAPED_UNICODE);
    } else {
        $convos = $chatService->getUserConversations($userId);
        echo json_encode(['conversations' => $convos], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]); // Remove detailed error in production
    exit;
}