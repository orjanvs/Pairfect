<?php

require __DIR__ . "/../../chat_init.php";

use function App\Support\authenticateUserApi;
authenticateUserApi(); 

header('Content-Type: application/json');

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

$userId = $_SESSION["user"]["userid"];

try {
    // Handle the chat message
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
    http_response_code(500); // Internal Server Error
    error_log($e->getMessage());
    echo json_encode([
        'responseMessage' => 'Oh no! An error occurred while processing your request. Please try again.'
    ]);
}
