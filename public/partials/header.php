
<header class="app-header">
  <div class="left">
    <h1>Pairfect - Your AI Wine Pairing Assistant</h1>
  </div>
  <div class="right">
    <button id="toggle-panel" class="sidepanel-btn" type="button">☰ Chat History</button>

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
