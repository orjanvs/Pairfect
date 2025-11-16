<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Database;
use App\Repositories\ChatRepository;
use App\Services\ChatService;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Check if user is logged in
if (empty($_SESSION["user"]["is_logged_in"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["user"]["username"] ?? '';

// Load environment variables
if (!file_exists(dirname(__DIR__) . '/.env')) {
    throw new RuntimeException(".env file not found");
}
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $chatRepository = new ChatRepository($pdo);
    $chatService = new ChatService($chatRepository);

    $userId = (int)$_SESSION["user"]["userid"];

    $conversations = $chatService->getUserConversations($userId);
    if (!$conversations) {
        http_response_code(404);
        echo "Conversation not found.";
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Server error: " . $e->getMessage(); // Remove detailed error in production
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation History</title>
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/convoHistory.css">
    <?php include 'partials/header.php'; ?>
</head>

<body>
    <main class="convo-history">
        <h2>Your Conversations</h2>

        <?php if (empty($conversations)): ?>
            <p class="empty-state">No saved conversations yet.</p>
        <?php else: ?>
            <div class="convo-list">
            <?php foreach ($conversations as $c): ?>
                <div class="convo-card">
                    <div class="convo-title">
                        <?= htmlspecialchars($c['title'] ?: 'Untitled conversation', 
                        ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="convo-meta">
                        <?= htmlspecialchars(date('Y-m-d H:i', 
                        strtotime($c['started_at'])), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <a href="index.php?convoId=<?= (int) $c['convo_id'] ?>">Open in chat</a>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="assets/js/chatbot.js"></script>
</body>


</html>