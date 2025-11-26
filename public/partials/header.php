<header class="app-header">
  <div class="left">
    <h1>Pairfect - Your AI Wine Pairing Assistant</h1>
  </div>

  <div class="right">
    <nav aria-label="Main navigation">
      <a class="home-btn header-btn" href="index.php">Home</a>
      <a class="history-btn header-btn" href="convoHistory.php">Chat History</a>
    </nav>

    <?php if (!empty($username)): ?>
      <a href="profile.php" class="profile-link">
        <?= htmlspecialchars($username, ENT_QUOTES, "UTF-8") ?>
      </a>
      <form method="post" action="logout.php" style="display:inline;">
        <button type="submit" class="logout-btn">Log out</button>
      </form>
    <?php endif; ?>
  </div>
</header>
