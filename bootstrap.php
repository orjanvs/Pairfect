<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Support/Auth.php';

use Dotenv\Dotenv;
use App\Database\Database;


use App\Repositories\UserRepository;
use App\Services\UserService;



// Configure exception logging 
// Stores logs inside the web root, only done for school project purposes
// In production, logs should be stored outside the web root for security
$logDir = __DIR__ . "/logs"; 
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logFile = $logDir . "/app-" . date("Y-m-d") . ".log";

ini_set('log_errors', '1');
ini_set('error_log', $logFile);

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {

    $db = new Database(); 
    $pdo = $db->getConnection();

    $userRepository = new UserRepository($pdo);
    $userService = new UserService($userRepository);

} catch (Throwable $e) {
    // Handle or log the error appropriately
    error_log($e->getMessage()); 
    exit('An error occurred while initializing the application.');
}
