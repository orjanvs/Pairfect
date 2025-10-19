<?php

interface IUserRepository
{
    public function createUser(string $username, string $email, string $passwordHash): bool;
    public function updateUser(int $userId, string $username, string $email): bool;
    public function updateUserPassword(int $userId, string $passwordHash): bool;
    public function deleteUser(int $userId): bool;
}
