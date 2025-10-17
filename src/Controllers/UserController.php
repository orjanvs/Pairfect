<?php 

include_once __DIR__ . '/../../Repositories/UserRepository.php';

class UserController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // Register a new user
    public function registerUser(string $username, string $email, string $password): bool
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Hash password
        return $this->userRepository->createUser($username, $email, $passwordHash);
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
}
