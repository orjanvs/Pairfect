<?php
header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Controllers/ChatController.php';

if (!file_exists(dirname(__DIR__) . '/.env')) {
    throw new RuntimeException(".env file not found");
}
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
if ($message === '') {
    echo json_encode(['responseMessage' => 'Please enter a valid message.']);
    exit;
}

// Sanitize input to prevent XSS attacks
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$message = strip_tags($message);
$message = trim($message);

$maxLength = 200;
if (mb_strlen($message) > $maxLength) {
    echo json_encode(['responseMessage' => "Message exceeds maximum length of $maxLength characters."]);
    exit;
}

try {
    $controller = new ChatController();
    $response = $controller->handleMessage($message);

    if (!isset($response['responseMessage'])) {
        $response['responseMessage'] = "Sorry, something went wrong. Please try again.";
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode([
        'responseMessage' => 'An error occurred while processing your request. Please try again.'
    ]);
}
/* $geminiAPI = new GeminiAPI();
$keyword = $geminiAPI->extractKeyword($message);

$data = getWinePairing($keyword);
$pairingText = $data['pairingText'] ?? "No pairing information available.";


$geminiEnhancedResponse = $geminiAPI->enhanceWithGemini($message, $pairingText);

echo json_encode(['responseMessage' => $geminiEnhancedResponse]); */
