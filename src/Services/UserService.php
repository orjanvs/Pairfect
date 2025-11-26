<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepository;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_DURATION = 30; // in minutes

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     * @param string $username The username of the new user
     * @param string $email The email of the new user
     * @param string $password The password of the new user
     * @return bool True if the user was successfully registered, false otherwise
     */
    public function registerUser(string $username, string $email, string $password): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Hash password
        return $this->userRepository->createUser($username, $email, $passwordHash);
    }

    /** 
     * Login user
     * @param string $username The username of the user
     * @param string $password The password of the user
     * @return mixed The user object if login is successful, null if not, or an array if locked out
     */
    public function loginUser(string $username, string $password)
    {
        $user = $this->getUser($username);
        if (!$user) {
            return null;
        }

        // If lock is set, but expired, clear it
        if ($user->locked_until && strtotime($user->locked_until) <= time()) {
            $this->userRepository->clearLockout($user->username);
            $user = $this->getUser($username); // Refresh user data
        }

        // Check for lockout
        if ($user->locked_until && strtotime($user->locked_until) > time()) {
            return ["status" => "locked", "locked_until" => $user->locked_until];
        }

        if (!password_verify($password, $user->password_hash)) {
            $this->userRepository->recordFailedLoginAttempt(
                $user->username,
                self::MAX_LOGIN_ATTEMPTS,
                self::LOCKOUT_DURATION
            );
            return null;
        }

        $this->userRepository->clearLockout($user->username);
        return $user;
    }

    /** 
     * Get user by username
     * @param string $username The username of the user
     * @return mixed The user object or null if not found
     */
    public function getUser(string $username)
    {
        return $this->userRepository->getUserByUsername($username);
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
        return $this->userRepository->updateUser($userId, $username, $email);
    }

    /** 
     * Update user password
     * @param int $userId The ID of the user to update
     * @param string $newPassword The new password
     * @return bool True if password update was successful, false otherwise
     */
    public function updateUserPassword(int $userId, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT); // Hash new password
        return $this->userRepository->updateUserPassword($userId, $passwordHash);
    }

    /** 
     * Delete a user
     * @param int $userId The ID of the user to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->deleteUser($userId);
    }

    /** 
     * Check if email exists
     * @param string $email The email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists(string $email): bool
    {
        return $this->userRepository->emailExists($email);
    }

    /** 
     * Check if username exists
     * @param string $username The username to check
     * @return bool True if username exists, false otherwise
     */
    public function usernameExists(string $username): bool
    {
        return $this->userRepository->usernameExists($username);
    }
}
