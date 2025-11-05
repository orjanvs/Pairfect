<?php

session_start();
if (!$_SESSION["user"]["is_logged_in"]) {
  header("Location: login.php");
  exit;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Services/GeminiAPI.php';


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
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

// 2) Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $userMessage = trim((string)$_POST['message']);
    if ($userMessage !== '') {
        // Keep session history lean (last 20 turns)
        $_SESSION['messages'] = array_slice($_SESSION['messages'], -40);

        // Save user turn
        $_SESSION['messages'][] = ['role' => 'user', 'content' => $userMessage];

        
        try {
          // Call model
          $svc = new GeminiAPI();


          // Call REST API and get reply
          $reply = $svc->geminiChat($userMessage);
          if (empty($reply)) {
            $reply = "Sorry, I couldn't generate a response. Please try again.";
          }
        } catch (Throwable $e) {
            $reply = "Sorry, something went wrong while processing your request. Please try again.";
        }

        // Save model turn
        $_SESSION['messages'][] = ['role' => 'model', 'content' => $reply];

        header("Location: " . $_SERVER["REQUEST_URI"]); 
        exit;
    }
}

?>
<!doctype html>
<html lang="no">
<head>
  <meta charset="utf-8">
  <title>Pairfect - Your AI Wine Pairing Assistant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, sans-serif; max-width: 700px; margin: 2rem auto; padding: 0 1rem; }
    .msg { padding: .75rem 1rem; border-radius: 10px; margin-bottom: .5rem; }
    .user { background: #f3f3f3; text-align: right; }
    .model { background: #ffffff; border: 1px solid #e5e5e5; }
    form { display: flex; gap: .5rem; margin-top: 1rem; }
    input[type=text]{ flex:1; padding:.75rem; border:1px solid #ddd; border-radius:8px; }
    button{ padding:.75rem 1rem; border:0; border-radius:8px; background:#111; color:#fff; cursor:pointer; }
  </style>
</head>
<body>
  <?php $username = $_SESSION['user']['username'] ?? null; ?>

  <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <div>
      <h1 style="margin:0;font-size:1.25rem;">Pairfect - Your AI Wine Pairing Assistant</h1>
    </div>
    <div>
      <?php if ($username): ?>
        <a href="profile.php" style="margin-right:.5rem;padding:.5rem .75rem;border-radius:6px;border:1px solid #ddd;text-decoration:none;color:#111;"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></a>
        <form method="post" style="display:inline;">
          <button type="submit" name="logout" value="1" style="padding:.5rem .75rem;border-radius:6px;border:0;background:#e74c3c;color:#fff;">Log out</button>
        </form>
      <?php endif; ?>
    </div>
  </header>

  <div id="chat">
    <?php foreach ($_SESSION['messages'] as $m): ?>
      <div class="msg <?= $m['role'] === 'user' ? 'user' : 'model' ?>">
        <?= htmlspecialchars($m['content'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form method="post" autocomplete="off">
    <input type="text" name="message" placeholder="Example: 'Pasta with tomato sauce'" autofocus>
    <button type="submit">Send</button>
  </form>
</body>
</html>
