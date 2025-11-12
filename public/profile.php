<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/../vendor/autoload.php';
use App\Database\Database;
use App\Repositories\UserRepository;
use App\Services\UserService;

// Simple HTML escaping helper
function htmlEscape(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Require user to be logged in
if (empty($_SESSION['user']['is_logged_in']) || empty($_SESSION['user']['userid']) || empty($_SESSION['user']['username'])) {
    header('Location: login.php');
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Initialize dependencies
$db = new Database();
$pdo = $db->getConnection();
$userRepository = new UserRepository($pdo);
$userService = new UserService($userRepository);

// Get current user from database
$username = (string)$_SESSION['user']['username'];
$user = $userRepository->getUserByUsername($username);

if (!$user || (int)$user->userid !== (int)$_SESSION['user']['userid']) {
    $_SESSION = [];
    header('Location: login.php');
    exit;
}

// Handle POST requests
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // CSRF check
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf'] ?? '')) {
        $message = 'Invalid request.';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {

            case 'update_profile':
                $newUsername = trim($_POST['username'] ?? '');
                $newEmail    = trim($_POST['email'] ?? '');

                if ($newUsername === '' || strlen($newUsername) < 2 || strlen($newUsername) > 80) {
                    $message = "Invalid username (must be 2–80 characters).";
                    break;
                }
                if ($newEmail === '' || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    $message = "Invalid email address.";
                    break;
                }

                $updated = $userService->updateUser((int)$user->userid, $newUsername, $newEmail);
                if ($updated) {
                    // Update session username and reload user from DB to show latest data
                    $_SESSION['user']['username'] = $newUsername;
                    $user = $userRepository->getUserByUsername($newUsername);
                    $message = "Profile updated successfully.";
                } else {
                    $message = "Failed to update profile.";
                }
                break;

            case 'change_password':
                $current = $_POST['current_password'] ?? '';
                $new     = $_POST['new_password'] ?? '';
                $confirm = $_POST['new_password_confirm'] ?? '';

                if ($new !== $confirm) {
                    $message = "Password confirmation does not match.";
                    break;
                }
                if (strlen($new) < 8) {
                    $message = "Password must be at least 8 characters long.";
                    break;
                }

                $check = $userService->loginUser($username, $current);
                if (!$check) {
                    $message = "Incorrect current password.";
                    break;
                }

                $hash = password_hash($new, PASSWORD_DEFAULT);
                $changed = $userRepository->updateUserPassword((int)$user->userid, $hash);

                $message = $changed ? "Password updated successfully." : "Failed to update password.";
                break;

            case 'delete_account':
                $deleted = $userService->deleteUser((int)$user->userid);
                if ($deleted) {
                    $_SESSION = [];
                    if (ini_get('session.use_cookies')) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                    }
                    session_destroy();
                    header('Location: index.php?deleted=1');
                    exit;
                } else {
                    $message = "Failed to delete account.";
                }
                break;

            default:
                $message = "Unknown action.";
                break;
        }

    } catch (Throwable $e) {
        $message = "Server error: " . $e->getMessage();
    }
}
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="assets/css/stylesheet.css">
</head>
<body>

<h1>My Profile</h1>

<?php if (!empty($message)): ?>
    <p><?php echo htmlEscape($message); ?></p>
<?php endif; ?>

<p>User ID: <?php echo htmlEscape((string)$user->userid); ?></p>
<p>Username: <?php echo htmlEscape($user->username); ?></p>
<p>Email: <?php echo htmlEscape($user->email); ?></p>

<h2>Update Profile</h2>
<form method="post">
    <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
    <input type="hidden" name="action" value="update_profile">
    <label>New username:
        <input type="text" name="username" value="<?php echo htmlEscape($user->username); ?>" required>
    </label><br>
    <label>New email:
        <input type="email" name="email" value="<?php echo htmlEscape($user->email); ?>" required>
    </label><br>
    <button type="submit">Save</button>
</form>

<h2>Change Password</h2>
<form method="post">
    <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
    <input type="hidden" name="action" value="change_password">
    <label>Current password:
        <input type="password" name="current_password" required>
    </label><br>
    <label>New password:
        <input type="password" name="new_password" required>
    </label><br>
    <label>Confirm new password:
        <input type="password" name="new_password_confirm" required>
    </label><br>
    <button type="submit">Update password</button>
</form>

<h2>Delete Account</h2>
<form method="post" onsubmit="return confirm('This cannot be undone.');">
    <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
    <input type="hidden" name="action" value="delete_account">
    <button type="submit">Delete account</button>
</form>

<p><a href="index.php">Back to home</a></p>

</body>
</html>
