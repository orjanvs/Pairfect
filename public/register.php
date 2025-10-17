<?php 
// Temp register page
require __DIR__ . "/../src/Controllers/UserController.php";

$userController = new UserController(new UserRepository($pdo));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['uname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['psw'] ?? '');

    try {
        $registered = $userController->registerUser($username, $email, $password);
        if ($registered) {
            echo "User registered successfully.";
        } else {
            echo "Failed to register user.";
        }
    } catch (Exception $e) {
        echo "Error registering user: " . $e->getMessage();
    }
}
?>
<html>
	<head>
		<title>Register as user</title>
	</head>
<body>
<pre>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	Username: <input type="text" name="uname" placeholder=
	"Username"><br>
	E-mail: <input type="email" name="email" placeholder=
	"E-mail"><br>
    Password: <input type="password" name="psw" placeholder=
	"Password"><br>
	<input type="submit" name="registrer" value="Registrer">
	<input type="hidden" name="dtstart" value=
	"<?php echo $dtstart->format("Y-m-d H:i:s.u"); ?>">
</form>
</pre>
</body>
</html>