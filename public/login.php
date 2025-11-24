<?php
session_start();

require __DIR__ . "/../bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Attempt to log in the user
    try {
        $loggedIn = $userService->loginUser($username, $password);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "An error occurred during login. Please try again later.";
        return;
    }

    if ($loggedIn) {
        // Login successful, set session variables
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION["user"]["userid"] = $loggedIn->userid;
        $_SESSION["user"]["username"] = $loggedIn->username;
        $_SESSION["user"]["is_logged_in"] = true;

        // Redirect to index page
        header("Location: index.php");
        exit;
    } else {
        // Invalid credentials
        echo "Invalid username or password.";
    }
}
?>

<!doctype html>
<html lang="no">

<head>
    <meta charset="utf-8">
    <title>Innlogging</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            padding: 40px;
            max-width: 400px;
            margin: auto;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input {
            padding: 8px;
            font-size: 1em;
        }

        button {
            padding: 8px;
            font-size: 1em;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h2>Log in</h2>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Log in</button>
        <button type ="button" onclick="window.location.href='register.php'">Register</button>
    </form>

</body>

</html>