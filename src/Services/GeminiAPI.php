<?php
namespace App\Services;

use Exception;

class GeminiAPI
{
    private $apiKey;
    private $instruction;

    public function __construct()
    {
        // Check for API key in environment variables, then load. 
        if (!isset($_ENV["GEMINI_API_KEY"])) {
            throw new Exception("GEMINI_API_KEY is not set in environment variables.");
        }
        $this->apiKey = $_ENV["GEMINI_API_KEY"];
        $this->instruction =
            <<<'SYS'
        You are a professional sommelier.
        - Provide expert wine pairing suggestions based on the user's input about dishes, ingredients, or cuisines.
        - Always respond in a friendly and informative manner.
        - Keep answer concise and relevant to wine pairing, but allow simple greetings.
        - If the input is unclear or lacks context, politely ask for more details about the dish, ingredient, or cuisine.
        - Answer in a maximum of 100 words.
        - Don't use any special formatting or characters, such as * etc. 
        - Always respond in the same language as the user's input.
        SYS;
    }

    /**
     * Send chat messages to Gemini API and get a response
     * @param array $messages An array of messages with 'role' and 'content'
     * @return string The response from Gemini API
     * @throws Exception If there is an error with the API request
     */
    public function geminiChat(array $messages): string
    {
        $contents = [];
        foreach ($messages as $m) {
            $contents[] = [
                "role" => $m["role"] === "model" ? "model" : "user",
                "parts" => [
                    ["text" => $m["content"]],
                ],
            ];
        }

        // Pass system instruction to API along with user message
        $payload = [
            "system_instruction" => [
                "parts" => [
                    ["text" => $this->instruction],
                ],
            ],
            "contents" => $contents,
        ];

        $data = $this->apiRequest($payload);
        return $this->extractText($data);
    }

    // Make the API request
    private function apiRequest(array $payload)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            // Setting url headers
            CURLOPT_HTTPHEADER => [
                "x-goog-api-key: {$this->apiKey}",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true, // Defining request as POST.
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE), // Attaches payload and encodes to JSON
            CURLOPT_RETURNTRANSFER => true, // Return response as string
        ]);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close($ch); Commented out because deprecated in PHP 8.5. Left here for exam evaluation. 

        if ($response === false) {
            throw new Exception("cURL Error: " . $curl_error);
        }

        if ($httpCode >= 400) {
            throw new Exception("HTTP Error: " . $httpCode . " - Response: " . $response);
        }

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new Exception("Invalid JSON response from Gemini API.");
        }

        return $data;
    }

    // Extract text from API response
    private function extractText(array $data)
    {
        return trim($data["candidates"][0]["content"]["parts"][0]["text"] ?? "");
    }
}
