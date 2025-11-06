<?php
header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\ChatService;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION["user"]["is_logged_in"])) {
    http_response_code(401);
    exit;
}


// Load environment variables
if (!file_exists(dirname(__DIR__) . '/.env')) {
    throw new RuntimeException(".env file not found");
}
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
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
    $chatService = new ChatService();
    $response = $chatService->handleMessage($message);

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
