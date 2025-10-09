<?php

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

require __DIR__ . '/../../vendor/autoload.php';

function enhanceWithGemini(string $food, string $pairingText): string
{
$client = new Client($_ENV['GEMINI_API_KEY']);

$prompt = <<<TXT
    INSTRUCTION: 
    - Enhance the following wine pairing suggestion by making it more engaging and informative.
    - Have to use spoonacular api response
    - Use a max of 100 words.
    - Don't include special characters or formatting such as *. Use plain text only.
    - Don't give specific wine brands or stores to buy from.
    - Don't mention the source of the information.
    - Respond in the same language as the input food item.

    DATA: 
    - Food item: $food 
    - Source info: $pairingText
    TXT;

$response = $client->generativeModel("gemini-2.5-flash")
    ->generateContent(
        new TextPart($prompt)
    );

return $response->text();
}
