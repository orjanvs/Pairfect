<?php

require __DIR__ . "/../bootstrap.php";

use function App\Support\authenticateUserHtml;
authenticateUserHtml();


// Fetch user info
$userId = (int)$_SESSION["user"]["userid"];
$username = $_SESSION["user"]["username"] ?? '';
$convoId = isset($_GET["convoId"]) ? (int)$_GET["convoId"] : null;

// Clear current conversation ID from session if no convoId is provided
if ($convoId === null && isset($_SESSION["current_convo_id"])) {
  unset($_SESSION["current_convo_id"]);
}

$messages = []; 

// If a conversation ID is provided, fetch its messages
if ($convoId) {
  try {
    $convo = $chatService->getConversationWithMessages($convoId, $userId);
    if ($convo === null) {
      http_response_code(404);
      echo "Conversation not found.";
      exit;
    }
  } catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo "Server error occurred.";
    exit;
  }

  // Extract messages
  $messages = $convo["messages"] ?? [];
  $_SESSION["current_convo_id"] = $convoId; // Remember current conversation ID in session
}

?>

<!doctype html>
<html lang="no">

<head>
  <meta charset="utf-8">
  <title>Pairfect - Your AI Wine Pairing Assistant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/chatbot.css">
  <link rel="stylesheet" href="assets/css/header.css">
</head>

<body>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <div id="chat" aria-live="polite" aria-busy="false">
    <?php if ($messages): ?>
      <?php foreach ($messages as $m): ?>
        <div class="msg <?= $m["role"] === "user" ? "user" : "model" ?>">
          <?= htmlspecialchars($m["content"], ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="msg model">
        Hello! I'm your personal wine pairing assistant.
        Tell me about a dish, ingredient, or cuisine, and I'll suggest the perfect wine to accompany it!
      </div>
    <?php endif; ?>
  </div>

  <form id="chat-form" autocomplete="off">
    <input type="hidden" id="convo-id" name="convoId" value="<?= isset($_GET['convoId']) ? (int)$_GET['convoId'] : '' ?>">
    <input type="text" id="chat-input" name="message"
      placeholder="Example: 'Pasta with tomato sauce'" autofocus maxlength="200">
    <button id="chat-send" type="submit">Send</button>
  </form>

  <script src="assets/js/chatbot.js"></script>
</body>

</html>