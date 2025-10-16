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

            // Get keyword from user input then normalize it
            $keyword = $geminiAPI->extractKeyword($message);
            $keyword = mb_strtolower(trim($keyword));

            if ($keyword === "none" || $keyword === "") {
                return [
                    "responseMessage" => "Hi! Tell me a dish, ingredient, or cuisine and I'll suggest a wine pairing for it."
                ];
            }


            // Get wine pairing from Spoonacular API using the keyword
            $spoonacularData = getWinePairing($keyword);
            $pairingText = $spoonacularData['pairingText'] ?? "No pairing information available.";

            // Rewrite the answer using the Gemini API, if no pairing info, give general advice
            $geminiEnhancedResponse = $geminiAPI->enhanceWithGemini($message, $pairingText);

            return [
                "responseMessage" => $geminiEnhancedResponse,
                // debug info for development purpose, delete it in production
                "debug" => [
                    "keyword" => $keyword,
                    "spoonacularData" => $spoonacularData
                ]
            ];
        } catch (Throwable $e) {
            return [
                "responseMessage" => "An error occurred while processing your request. Please try again."
            ];
        }
    }
}