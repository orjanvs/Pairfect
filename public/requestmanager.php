<?php
header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require __DIR__ . '/../src/Services/SpoonacularAPI.php';
require __DIR__ . '/../src/Services/GeminiAPI.php';

$input = json_decode(file_get_contents('php://input'), true);
$food = trim($input['message'] ?? '');

if ($food === '') {
    echo json_encode(['responseMessage' => 'Please provide a food item to get wine pairing suggestions, e.g., "steak".']);
    exit;
}

$data = getWinePairing($food);
$pairingText = $data['pairingText'] ?? "No pairing information available.";

$geminiEnhancedResponse = enhanceWithGemini($food, $pairingText);

echo json_encode(['responseMessage' => $geminiEnhancedResponse]);
