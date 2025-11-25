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

        try {
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new \Exception("Database connection failed.");
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
