<?php
namespace App\Database;
use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $host = "localhost";
        $dbName = "pairfect";
        $user = "root";
        $pass = "";

        $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

}



/* define('DB_HOST', 'localhost');
define('DB_NAME', 'pairfect');
define('DB_USER', 'root');
define('DB_PASS', '');
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo "Connection failed";
} */

