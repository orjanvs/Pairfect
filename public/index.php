<?php
session_start();

require __DIR__ . "/../bootstrap.php";

if (!$_SESSION["user"]["is_logged_in"]) {
  header("Location: login.php");
  exit;
}

$userId = (int)$_SESSION["user"]["userid"];
$convoId = isset($_GET['convoId']) ? (int)$_GET['convoId'] : null;

// Clear current conversation ID when loading index.php without convoId
if ($convoId === null && isset($_SESSION['current_convo_id'])) {
  unset($_SESSION['current_convo_id']);
}

$messages = [];

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

  $messages = $convo['messages'] ?? [];
}



// Handle logout request 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  // Clear and destroy session then redirect to login
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params['path'],
      $params['domain'],
      $params['secure'],
      $params['httponly']
    );
  }
  session_destroy();
  header('Location: login.php');
  exit;
}

$username = $_SESSION["user"]["username"] ?? '';

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
  <?php
  include __DIR__ . '/partials/header.php';
  ?>

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