<?php

require __DIR__ . '/../vendor/autoload.php';
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Database\Database;

$db = new Database();
$pdo = $db->getConnection();
$userRepository = new UserRepository($pdo);
$userService = new UserService($userRepository);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $registered = $userService->registerUser($username, $email, $password);
        if ($registered) {
            echo "User registered successfully.";
            $logIn = $userService->loginUser($username, $password);
            session_start();
            $_SESSION["user"]["userid"] = $logIn->userid;
            $_SESSION["user"]["username"] = $logIn->username;
            $_SESSION["user"]["is_logged_in"] = true;
            header("Refresh: 3; url=index.php");

            exit;
        } else {
            echo "Failed to register user.";
        }
    } catch (Exception $e) {
        echo "Error registering user: " . $e->getMessage();
    }
}
?>
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
    <h2>Register User</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
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