<?php
namespace App\Services;
use Throwable;

class ChatService
{
    private GeminiAPI $gemini;

    public function __construct()
    {
        $this->gemini = new GeminiAPI();
    }

    public function handleMessage(string $message) : array
    {
        if ($message === "") {
            return [
                "responseMessage" => "Please enter a valid message."
            ];
        }

        try {
            $reply = $this->gemini->geminiChat($message);
            if (!$reply) {
                $reply = "Sorry! Response could not be generated. Please try again.";
            }
        } catch (Throwable $e) {
            $reply = "An error occurred while processing your request. Please try again.";
        }
        return [
            "responseMessage" => $reply
        ];
    }



    
}