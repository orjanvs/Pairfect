<?php

require __DIR__ . '/../Services/GeminiAPI.php';
require __DIR__ . '/../Services/SpoonacularAPI.php';

class ChatController
{
    
    public function handleMessage(string $message)
    {
        if ($message === '') {
            return [
                "responseMessage" => "Please enter a valid message."
            ];
        }
        try {
            $geminiAPI = new GeminiAPI();
        } catch (Throwable $e) {
            return [
                "responseMessage" => "An error occurred while processing your request. Please try again."
            ];
        }
    }
}