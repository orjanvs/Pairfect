<?php
session_start();

require __DIR__ . "/../bootstrap.php";

$lockoutMessage = null;
$errorMessage = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validate empty fields
    if ($username === "" || $password === "") {
        $errorMessage = "Username and password are required.";
    } else {
        // Attempt to log in the user
        try {
            $loginResult = $userService->loginUser($username, $password);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo "An error occurred during login. Please try again later.";
            exit;
        }

        // Check for lockout
        if (is_array($loginResult) && $loginResult["status"] === "locked") {
            $remainingTime = null;
            if (!empty($loginResult["locked_until"])) {
                $lockedUntil = strtotime($loginResult["locked_until"]);
                $remainingTime = $lockedUntil - time();
            }
            $lockoutMessage = "Your account is temporarily locked due to multiple failed login attempts. Please try again in " . ceil($remainingTime / 60) . " minutes.";
        } elseif (is_object($loginResult)) {
            // Login successful, set session variables
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION["user"]["userid"] = $loginResult->userid;
            $_SESSION["user"]["username"] = $loginResult->username;
            $_SESSION["user"]["is_logged_in"] = true;

            // Redirect to index page
            header("Location: index.php");
            exit;
        } else {
            // Invalid credentials
            $errorMessage = "Invalid username or password.";
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Innlogging</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <h2>Log in</h2>

    <?php if ($lockoutMessage): ?>
        <div class="alert alert-error"><?= htmlspecialchars($lockoutMessage, ENT_QUOTES, "UTF-8") ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, "UTF-8") ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Log in</button>
        <button type="button" onclick="window.location.href='register.php'">Register</button>
    </form>

</body>

</html>