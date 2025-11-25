<?php
require __DIR__ . '/bootstrap.php';

use App\Repositories\ChatRepository;
use App\Services\ChatService;  
use App\Services\GeminiAPI;

$chatRepository = new ChatRepository($pdo);
$geminiAPI = new GeminiAPI();
$chatService = new ChatService($chatRepository, $geminiAPI);