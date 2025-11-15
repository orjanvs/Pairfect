<?php
header('Content-Type: application/json');

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\ChatService;
use App\Database\Database;
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

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["responseMessage" => "Method Not Allowed."]);
    exit;
}

// Fetches user input from frontend 
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? ''); 
$convoId = isset($input['convoId']) ? (int)$input['convoId'] : null;

// Reuse conversation ID if it exists
if ($convoId === null && !empty($_SESSION['current_convo_id'])) {
    $convoId = (int)$_SESSION['current_convo_id'];
}

if ($message === '') {
    echo json_encode(['responseMessage' => 'Please enter a valid message.']);
    exit;
}


// Limit user input message length
$maxLength = 200;
if (mb_strlen($message) > $maxLength) {
    echo json_encode(['responseMessage' => "Message exceeds maximum length of $maxLength characters."]);
    exit;
}

try {
    // Handle the chat message
    $db = new Database();
    $pdo = $db->getConnection();
    $chatRepository = new ChatRepository($pdo);
    $chatService = new ChatService($chatRepository);

    $userId = $_SESSION["user"]["userid"];

    $response = $chatService->handleMessage($userId, $message, $convoId);

    // Remember current conversation ID in session
    if (!empty($response['convoId'])) {
    $_SESSION['current_convo_id'] = (int) $response['convoId'];
    }

    if (!isset($response['responseMessage'])) {
        $response['responseMessage'] = "Sorry, something went wrong. Please try again.";
    }

    // Return response as JSON
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode([
        'responseMessage' => 'Oh no! An error occurred while processing your request. Please try again.'
    ]);
}
