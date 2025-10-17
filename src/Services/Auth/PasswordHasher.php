<?php

function passwordHasher(string $password): string
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    return $passwordHash;
}