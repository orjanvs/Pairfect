<?php

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

require __DIR__ . '/../../vendor/autoload.php';


class GeminiAPI
{
    private Client $client;
    private string $modelName = "gemini-2.5-flash";

    public function __construct()
    {
        $this->client = new Client($_ENV['GEMINI_API_KEY']);
    }

    private function generateText(string $prompt): string
    {
        $resp = $this->client->generativeModel($this->modelName)
            ->generateContent(new TextPart($prompt));
        return trim($resp->text());
    }

    public function extractKeyword(string $message): string
    {
        $prompt =
            <<<TXT
        INSTRUCTION:
        - When user is inputting a dish: 
        extract the main ingredient, dish name, or cuisine type as one word.
        It has to make sense as a food item for wine pairing when viewing the dish as a whole.
        - Always translate the keyword to English.
        DATA:
        - User input: $message
        TXT;

        $keyword = $this->generateText($prompt);
        return $keyword;
    }

    public function enhanceWithGemini(string $message, string $pairingText): string
    {
        $prompt = <<<TXT
        INSTRUCTION: 
        - Enhance the following wine pairing suggestion by making it more engaging and informative.
        - Have to use spoonacular api response
        - If there is no response from the spoonacular api, 
        give a more general wine pairing advice for the food item.
        - Use a max of 100 words.
        - Don't include special characters or formatting such as *. Use plain text only.
        - Don't give specific wine brands or stores to buy from.
        - Don't mention the source of the information.
        - Respond in the same language as the input.

        DATA: 
        - Food item: $message
        - Source info: $pairingText
        TXT;

        $response = $this->generateText($prompt);

        return $response;
    }
}
