<?php

use function App\Support\logoutAndRedirect;
require __DIR__ . "/../src/Support/Auth.php";

logoutAndRedirect("login.php");