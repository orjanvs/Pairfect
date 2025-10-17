<?php

include_once __DIR__ . '/../Database/db.inc.php';

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** 
     * Create a new user
     * @param string $username The username of the new user
     * @param string $email The email of the new user
     * @param string $passwordHash The hashed password of the new user
     * @return bool True if user creation was successful, false otherwise
     */
    public function createUser(string $username, string $email, string $passwordHash): bool
    {
        $sql = "INSERT INTO users (username, email, password_hash) 
                VALUES (:username, :email, :password_hash)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash
        ]);
    }

    /**
     * Update user details
     * @param int $userId The ID of the user to update
     * @param string $username The new username
     * @param string $email The new email
     * @return bool True if update was successful, false otherwise
     */
    public function updateUser(int $userId, string $username, string $email): bool
    {
        $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':id' => $userId
        ]);
    }

    /** 
     * Update user password
     * @param int $userId The ID of the user to update
     * @param string $passwordHash The new hashed password
     * @return bool True if password update was successful, false otherwise
     */
    public function updateUserPassword(int $userId, string $passwordHash): bool
    {
        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':password_hash' => $passwordHash,
            ':id' => $userId
        ]);
    }

    /**
     * Delete a user
     * @param int $userId The ID of the user to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteUser(int $userId): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }
}