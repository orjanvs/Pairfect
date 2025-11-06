<?php

session_start();
if (!$_SESSION["user"]["is_logged_in"]) {
  header("Location: login.php");
  exit;
}

require __DIR__ . '/../vendor/autoload.php';
use App\Services\GeminiAPI;
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// 1) Ensure message array exists in session
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [[
        'role' => 'model',
        'content' => "Hello! I'm your personal wine pairing assistant. Tell me about a dish, ingredient, or cuisine, and I'll suggest the perfect wine to accompany it!"
    ]];
}

// Handle logout request (form on this page)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  // Clear and destroy session then redirect to login
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params['path'], $params['domain'], $params['secure'], $params['httponly']);
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
</head>
<body>
  <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <div>
      <h1 style="margin:0;font-size:1.25rem;">Pairfect - Your AI Wine Pairing Assistant</h1>
    </div>
    <div>
      <?php if ($username): ?>
        <a href="profile.php" style="margin-right:.5rem;padding:.5rem .75rem;border-radius:6px;border:1px solid #ddd;
        text-decoration:none;color:#111;"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></a>
        <form method="post" style="display:inline;">
          <button type="submit" name="logout" value="1" style="padding:.5rem .75rem;border-radius:6px;border:0;
          background:#e74c3c;color:#fff;">Log out</button>
        </form>
      <?php endif; ?>
    </div>
  </header>

  <div id="chat" aria-live="polite" aria-busy="false">
    <?php foreach ($_SESSION['messages'] as $m): ?>
      <div class="msg <?= $m['role'] === 'user' ? 'user' : 'model' ?>">
        <?= htmlspecialchars($m['content'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="chat-form" autocomplete="off">
    <input type="text" id="chat-input" name="message" placeholder="Example: 'Pasta with tomato sauce'" autofocus>
    <button id="chat-send" type="submit">Send</button>
  </form>

  <script src="assets/js/chatbot.js"></script>
</body>
</html>
