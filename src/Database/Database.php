<?php

namespace App\Database;

use PDO;

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

        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
