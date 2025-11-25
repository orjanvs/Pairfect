<?php

require __DIR__ . "/../bootstrap.php";

use function App\Support\authenticateUserHtml;

authenticateUserHtml();

// Fetch user info
$username = $_SESSION["user"]["username"] ?? '';
$userId = (int)$_SESSION["user"]["userid"];

// Fetch conversations for the user
try {
    $conversations = $chatService->getUserConversations($userId);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "An error occurred while fetching conversations. Please try again later.";
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
</head>

<body>
    <?php include 'partials/header.php'; ?>

    <main class="convo-history">
        <h2>Your Conversations</h2>

        <?php if (empty($conversations)): ?>
            <p class="empty-state">No saved conversations yet.</p>
        <?php else: ?>
            <div class="convo-list">
                <?php foreach ($conversations as $c): ?>
                    <div class="convo-card">
                        <div class="convo-title">
                            <?= htmlspecialchars(
                                $c['title'] ?: 'Untitled conversation',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>
                        <div class="convo-meta">
                            <?= htmlspecialchars(date(
                                'Y-m-d H:i',
                                strtotime($c['started_at'])
                            ), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <a href="index.php?convoId=<?= (int) $c['convo_id'] ?>">Open in chat</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>