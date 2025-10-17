<?php 

include_once __DIR__ . '/../../Repositories/UserRepository.php';
include_once __DIR__ . '/../Services/Auth/PasswordHasher.php';

class UserController
{
    private UserRepository $userRepository;

    public function __construct(PDO $pdo)
    {
        $this->userRepository = new UserRepository($pdo);
    }

    // Register a new user
    public function registerUser(string $username, string $email, string $password): bool
    {
        $passwordHash = passwordHasher($password); // Use simple password hashing function
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
        $passwordHash = passwordHasher($newPassword);
        return $this->userRepository->updateUserPassword($userId, $passwordHash);
    }

    // Delete user
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->deleteUser($userId);
    }
}


// register user for test

function testRegisterUser($pdo) {
$userController = new UserController($pdo);
try {
    $user = $userController->registerUser('testuser1', 'test1@example.com', 'password123');
    if ($user) { 
    echo "User registered successfully.";
    } else {
    echo "Failed to register user.";
    }
} catch (Exception $e) {
    echo "Error registering user: " . $e->getMessage();
}
}

testRegisterUser($pdo);