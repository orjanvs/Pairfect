<?php

require __DIR__ . "/../bootstrap.php";

use function App\Support\authenticateUserHtml;
use App\Support\Validator;

authenticateUserHtml();

// Get current user from database
$username = (string)$_SESSION['user']['username'];
$userId = (int)$_SESSION['user']['userid'];
$user = $userService->getUser($username);

if (!$user || (int)$user->userid !== $userId) {
    $_SESSION = [];
    header('Location: login.php');
    exit;
}

// Handle POST requests
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {

            case 'update_profile':
                $updateErrors = [];
                $newUsername = trim($_POST['username'] ?? '');
                $newEmail    = trim($_POST['email'] ?? '');

                // Validate inputs
                $errors = array_merge(
                    Validator::validateUsername($newUsername),
                    Validator::validateEmail($newEmail)
                );

                if (empty($errors)) {

                    // Check for uniqueness
                    if (
                        $userService->usernameExists($newUsername) &&
                        $newUsername !== $user->username
                    ) {
                        $updateErrors[] = "Username already taken.";
                    }

                    if (
                        $userService->emailExists($newEmail) &&
                        $newEmail !== $user->email
                    ) {
                        $updateErrors[] = "E-mail already registered.";
                    }

                    if (!empty($updateErrors)) {
                        break;
                    }

                    // Update user details
                    $updated = $userService->updateUser($userId, $newUsername, $newEmail);

                    if ($updated) {
                        $_SESSION['user']['username'] = $newUsername; // Update session username
                        $user = $userService->getUser($newUsername); // Refresh user data
                        $message = "Profile updated successfully.";
                    } else {
                        $message = "Failed to update profile.";
                    }
                }
                break;

            case 'change_password':
                $passwordErrors = [];
                $current = $_POST['current_password'] ?? '';
                $new     = $_POST['new_password'] ?? '';
                $confirm = $_POST['new_password_confirm'] ?? '';

                // Validate new password
                $passwordErrors = Validator::validatePassword($new);

                if ($new !== $confirm) {
                    $passwordErrors[] = "Password confirmation does not match.";
                }

                // Verify current password
                if (!password_verify($current, $user->password_hash)) {
                    $passwordErrors[] = "Current password is incorrect.";
                }

                if (!empty($passwordErrors)) {
                    break;
                }

                // Update to new password
                $changed = $userService->updateUserPassword($userId, $new);

                // Verify update success
                $message = $changed ? "Password updated successfully." : "Failed to update password.";
                break;

            case 'delete_account':
                $deleted = $userService->deleteUser($userId);
                if ($deleted) {
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
        error_log($e->getMessage());
        $message = "An error occurred. Please try again later.";
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>

<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <h1>My Profile</h1>

    <?php if ($message): ?>
        <div class="message">
            <?php echo htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <h2>Profile Information</h2>
    <div class="profile-info">
        <p>Username: <?php echo htmlspecialchars($user->username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
        <p>Email: <?php echo htmlspecialchars($user->email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
    </div>

    <h2>Update Profile</h2>

    <?php if (!empty($updateErrors)): ?>
        <div class="errors" role="alert" aria-live="assertive">
            <ul>
                <?php foreach ($updateErrors as $e): ?>
                    <li><?php echo htmlspecialchars($e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="update_profile">
        <label>New username:
            <input type="text" name="username" value="<?php echo htmlspecialchars($user->username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" required>
        </label><br>
        <label>New email:
            <input type="email" name="email" value="<?php echo htmlspecialchars($user->email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" required>
        </label><br>
        <button type="submit">Save</button>
    </form>

    <h2>Change Password</h2>

    <?php if (!empty($passwordErrors)): ?>
        <div class="errors" role="alert" aria-live="assertive">
            <ul>
                <?php foreach ($passwordErrors as $e): ?>
                    <li><?php echo htmlspecialchars($e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
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
        <input type="hidden" name="action" value="delete_account">
        <button class="delete-account-btn" type="submit">Delete account</button>
    </form>

    <p><a href="index.php">Back to home</a></p>

</body>

</html>