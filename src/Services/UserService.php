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

    // Login user
    public function loginUser(string $username, string $password)
    {
        $user = $this->getUser($username);
        if (!$user) {
            return null;
        }

        // Check for lockout
        if ($user->locked_until && strtotime($user->locked_until) > time()) {
            return ["status" => "locked", "locked_until" => $user->locked_until];
        }

        if (!password_verify($password, $user->password_hash)) {
            $this->userRepository->recordFailedLoginAttempt($user->username, self::MAX_LOGIN_ATTEMPTS, self::LOCKOUT_DURATION);
            return null;
        }

        $this->userRepository->clearLockout($user->username);
        return $user;
    }

    // Get user
    public function getUser(string $username)
    {
        return $this->userRepository->getUserByUsername($username);
    }

    // Update user details
    public function updateUser(int $userId, string $username, string $email): bool
    {
        return $this->userRepository->updateUser($userId, $username, $email);
    }

    // Update user password
    public function updateUserPassword(int $userId, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT); // Hash new password
        return $this->userRepository->updateUserPassword($userId, $passwordHash);
    }

    // Delete user
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->deleteUser($userId);
    }

    // Check if email exists
    public function emailExists(string $email): bool
    {
        return $this->userRepository->emailExists($email);
    }

    // Check if username exists
    public function usernameExists(string $username): bool
    {
        return $this->userRepository->usernameExists($username);
    }
}
