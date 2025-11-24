<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Database;

use App\Repositories\ChatRepository;
use App\Repositories\UserRepository;

use App\Services\ChatService;
use App\Services\UserService;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {

    $db = new Database(); 
    $pdo = $db->getConnection();

    $chatRepository = new ChatRepository($pdo);
    $chatService = new ChatService($chatRepository);

    $userRepository = new UserRepository($pdo);
    $userService = new UserService($userRepository);

} catch (Throwable $e) {
    // Handle or log the error appropriately
    error_log($e->getMessage()); 
    exit('An error occurred while initializing the application.');
}
