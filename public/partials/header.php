
<header class="app-header">
  <div class="left">
    <h1>Pairfect - Your AI Wine Pairing Assistant</h1>
  </div>
  <div class="right">
    <a class="home-btn" href="index.php">Home</a>
    <a class="history-btn" href="convoHistory.php">Chat History</a>

    <?php if (!empty($username)): ?>
      <a href="profile.php" class="profile-link">
        <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
      </a>
      <form method="post" style="display:inline;">
        <button type="submit" name="logout" value="1" class="logout-btn">Log out</button>
      </form>
    <?php endif; ?>
  </div>
</header>
