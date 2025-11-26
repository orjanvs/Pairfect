<?php
require __DIR__ . "/bootstrap.php";

use App\Repositories\ChatRepository;
use App\Services\ChatService;  
use App\Services\GeminiAPI;

$chatRepository = new ChatRepository($pdo);
try {
    $geminiAPI = new GeminiAPI();
} catch (Throwable $e) {
    error_log($e->getMessage());
    $geminiAPI = null;
}  
    
$chatService = $geminiAPI ? new ChatService($chatRepository, $geminiAPI) : null;