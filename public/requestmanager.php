<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Controllers/ChatController.php';

if (!file_exists(dirname(__DIR__) . '/.env')) {
    throw new RuntimeException(".env file not found");
}
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Fetches user input from frontend 
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? ''); // Trim whitespace
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
    $controller = new ChatController();
    $response = $controller->handleMessage($message);

    // Check if responseMessage is set
    if (!isset($response['responseMessage'])) {
        $response['responseMessage'] = "Sorry, something went wrong. Please try again.";
    }

    // Return the response as JSON
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode([
        'responseMessage' => 'Oh no! An error occurred while processing your request. Please try again.'
    ]);
}
