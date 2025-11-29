<?php
session_start();

require __DIR__ . "/../bootstrap.php";

use App\Support\Validator;

$errors = [];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = trim($_POST["username"] ?? "");
        $email    = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        // Validate inputs
        $errors = array_merge(
            Validator::validateUsername($username),
            Validator::validateEmail($email),
            Validator::validatePassword($password)
        );

        // If no validation errors, proceed to register user
        if (empty($errors)) {

            // Check if email already exists
            if ($userService->emailExists($email)) {
                $errors[] = "E-mail already registered.";
            }
            // Check if username already exists
            if ($userService->usernameExists($username)) {
                $errors[] = "Username already taken.";
            }

            if (empty($errors)) {
                // Register the user
                $registered = $userService->registerUser($username, $email, $password);

                // If registration successful, log in the user
                if ($registered) {
                    $logIn = $userService->loginUser($username, $password);
                    if (is_object($logIn)) {
                        session_regenerate_id(true); // Prevent session fixation
                        $_SESSION["user"]["userid"] = $logIn->userid;
                        $_SESSION["user"]["username"] = $logIn->username;
                        $_SESSION["user"]["is_logged_in"] = true;

                        header("Location: index.php");
                        exit;
                    } else {
                        $errors[] = "Login failed after registration.";
                    } 
                } else {
                    $errors[] = "Failed to register user.";
                }
            }
        }
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "An error occurred during registration. Please try again later.";
    exit; 
}

?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body>
    <h2>Register User</h2>

    <?php if (!empty($errors)): ?>
        <ul class="errors" role="alert" aria-live="assertive">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES, "UTF-8") ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "UTF-8"); ?>">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required placeholder="Username">

        <label for="email">E-post:</label>
        <input type="email" name="email" id="email" required placeholder="E-post">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required placeholder="Password">

        <button type="submit">Register</button>
        <button type="button" onclick="window.location.href='login.php'">Log in</button>
    </form>
</body>

</html>