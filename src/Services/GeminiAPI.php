<?php

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

class GeminiAPI
{
    private Client $client;
    private const MODEL = "gemini-2.5-flash";

    public function __construct()
    {
        if (!isset($_ENV['GEMINI_API_KEY']) || empty($_ENV['GEMINI_API_KEY'])) {
            throw new RuntimeException("Gemini API key not set in environment variables.");
        }
        $this->client = new Client($_ENV['GEMINI_API_KEY']);
    }

    /** 
     * Generate text using Gemini API
     * @param string $prompt The prompt to send to the Gemini API
     * @return string $resp The generated text response
     * @throws Exception if the API call fails
    */
    private function generateText(string $prompt): string
    {
        try {
        $resp = $this->client->generativeModel(self::MODEL)
            ->generateContent(new TextPart($prompt));
        return trim($resp->text()); 
        } catch (Exception $e) {
            return "The text could not be generated. Please try again.";
        }
    }

    /**
     * Extract keyword from user message
     * @param string $message The user input message
     * @return string $keyword The extracted keyword
     */
    public function extractKeyword(string $message): string
    {
        $prompt =
        <<<TXT
        INSTRUCTION:
        - When user is inputting a dish: 
        If ingredient or cuisine -> return exactly one word that best represents the main ingredient or cuisine.
        If specific dish name -> return the actual dish name as is.
        It has to make sense as a food item for wine pairing when viewing the dish as a whole.
        If not a food, cuisine, or ingredient -> return the string "none".
        - Always translate the keyword to English.
        
        USER INPUT:
        <<<USER $message 
        USER
        >>>
        TXT;

        $keyword = $this->generateText($prompt);
        return $keyword;
    }

    /**
     * Enhance wine pairing suggestion using Gemini API
     * @param string $message The user input message
     * @param string $pairingText The wine pairing text from Spoonacular API
     * @return string $response The enhanced wine pairing suggestion
     */
    public function enhanceWithGemini(string $message, string $pairingText): string
    {
        $prompt = <<<TXT
        INSTRUCTION: 
        - Enhance the following wine pairing suggestion by making it more engaging and informative from
        the provided FACTS. 
        - If no pairing information from the spoonacular api is available, 
        give a more general wine pairing advice for the food item.
        - Use a max of 100 words.
        - Don't include special characters or formatting such as *. Use plain text only.
        - Don't give specific wine brands or stores to buy from.
        - Don't mention the source of the information.
        - Respond in the same language as the USER INPUT.

        FACTS:
        <<<FACTS 
        {$pairingText} 
        FACTS
        >>>

         USER INPUT:
        <<<USER {$message} 
        USER
        >>>
        TXT;

        $response = $this->generateText($prompt);

        return $response;
    }
}
