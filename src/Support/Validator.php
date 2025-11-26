<?php
namespace App\Support;

class Validator 
{
    /**
     * Validate email format
     * @param string $email The email to validate
     * @return array An array of error messages, empty if valid
     */
    public static function validateEmail($email) {
        $email = trim($email);
        $emailErrors = [];
        if (empty($email)) {
            $emailErrors[] = "Email is required.";
            return $emailErrors;
        } 
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $emailErrors[] = "Invalid email format.";
        }
        return $emailErrors;
    }

    /**
     * Validate username format
     * @param string $username The username to validate
     * @return array An array of error messages, empty if valid
     */
    public static function validateUsername($username) {
        $username = trim($username);
        $usernameErrors = [];
        if (empty($username)) {
            $usernameErrors[] = "Username is required.";
            return $usernameErrors; 
        } 
        if (strlen($username) < 3 || strlen($username) > 20) {
            $usernameErrors[] = "Username must be between 3 and 20 characters.";
        } 
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $usernameErrors[] = "Username can only contain letters, numbers, and underscores.";
        }
        return $usernameErrors;
    }

    /**
     * Validate password strength
     * @param string $password The password to validate
     * @return array An array of error messages, empty if valid
     */
    public static function validatePassword($password) {
        $passwordErrors = [];
        if (empty($password)) {
            $passwordErrors[] = "Password is required.";
            return $passwordErrors;
        } 
        if (strlen($password) < 9) {
            $passwordErrors[] = "Password must be at least 9 characters long.";
        } 
        if (!preg_match('/[A-Z]/', $password)) {
            $passwordErrors[] = "Password must contain at least one uppercase letter.";
        } 
        if (!preg_match('/[a-z]/', $password)) {
            $passwordErrors[] = "Password must contain at least one lowercase letter.";
        } 
        if (preg_match_all('/\d/', $password, $matches) < 2) {
            $passwordErrors[] = "Password must contain at least two numbers.";
        } 
        if (!preg_match('/[\W_]/', $password)) {
            $passwordErrors[] = "Password must contain at least one special character.";
        }
        return $passwordErrors;
    }

    /**
     * Validate message content
     * @param string $message The message to validate
     * @return array An array of error messages, empty if valid
     */
    public static function validateMessage($message) {
        $messageErrors = [];
        $maxLength = 200;
        if (empty($message)) {
            $messageErrors[] = "Message cannot be empty.";
            return $messageErrors;
        } 
        if (mb_strlen($message) > $maxLength) {
            $messageErrors[] = "Message exceeds maximum length of $maxLength characters.";
        }
        return $messageErrors;
    }
}