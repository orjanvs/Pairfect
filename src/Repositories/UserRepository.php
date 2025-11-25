<?php
namespace App\Repositories;
use PDO;


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

    public function getUserByUsername(string $username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        return $user; 
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
        $sql = "UPDATE users SET username = :username, email = :email WHERE userid = :userid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':userid' => $userId
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
        $sql = "UPDATE users SET password_hash = :password_hash WHERE userid = :userid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':password_hash' => $passwordHash,
            ':userid' => $userId
        ]);
    }

    /**
     * Delete a user
     * @param int $userId The ID of the user to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteUser(int $userId): bool
    {
        $sql = "DELETE FROM users WHERE userid = :userid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':userid' => $userId]);
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function usernameExists(string $username): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn() > 0;
    }


    // Account lockout methods
    public function incrementLoginAttempts(string $username): void
    {
        $sql = "UPDATE users SET login_attempts = login_attempts + 1 
        WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
    }

    public function resetLoginAttempts(string $username): void
    {
        $sql = "UPDATE users SET login_attempts = 0 WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
    }

    public function getLoginAttempts(string $username): int
    {
        $sql = "SELECT login_attempts FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        return (int)$stmt->fetchColumn();
    }

    public function lockAccount(string $username): void
    {
        $sql = "UPDATE users SET is_locked = 1 WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
    }
    public function isAccountLocked(string $username): bool
    {
        $sql = "SELECT is_locked FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        return (bool)$stmt->fetchColumn();
    }
}