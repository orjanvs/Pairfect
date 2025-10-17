<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'pairfect');
define('DB_USER', 'root');
define('DB_PASS', '');
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo "Connection failed";
}

