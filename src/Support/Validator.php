<?php
namespace App\Support;

class Validator 
{
    public static function validateEmail($email) {
        $email = trim($email);
        $emailErrors = [];
        if (empty($email)) {
            $emailErrors[] = "Email is required.";
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $emailErrors[] = "Invalid email format.";
        }
        return $emailErrors;
    }

    public static function validateUsername($username) {
        $username = trim($username);
        $usernameErrors = [];
        if (empty($username)) {
            $usernameErrors[] = "Username is required.";
        } elseif (strlen($username) < 3 || strlen($username) > 20) {
            $usernameErrors[] = "Username must be between 3 and 20 characters.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $usernameErrors[] = "Username can only contain letters, numbers, and underscores.";
        }
        return $usernameErrors;
    }

    public static function validatePassword($password) {
        $password = trim($password);
        $passwordErrors = [];
        if (empty($password)) {
            $passwordErrors[] = "Password is required.";
        } elseif (strlen($password) < 9) {
            $passwordErrors[] = "Password must be at least 9 characters long.";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $passwordErrors[] = "Password must contain at least one uppercase letter.";
        } elseif (!preg_match('/[a-z]/', $password)) {
            $passwordErrors[] = "Password must contain at least one lowercase letter.";
        } elseif (!preg_match('/(\D*\d){2,}/', $password)) {
            $passwordErrors[] = "Password must contain at least two numbers.";
        } elseif (!preg_match('/[\W_]/', $password)) {
            $passwordErrors[] = "Password must contain at least one special character.";
        }
        return $passwordErrors;
    }

    public static function validateMessage($message) {
        $messageErrors = [];
        $maxLength = 200;
        if (empty($message)) {
            $messageErrors[] = "Message cannot be empty.";
        } elseif (mb_strlen($message) > $maxLength) {
            $messageErrors[] = "Message exceeds maximum length of $maxLength characters.";
        }
        return $messageErrors;
    }
}