<?php

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

require __DIR__ . '/../../vendor/autoload.php';

function enhanceWithGemini(string $food, string $pairingText): string
{
$client = new Client($_ENV['GEMINI_API_KEY']);

$prompt = <<<TXT
    INSTRUCTION: 
    - Enhance the following wine pairing suggestion by making it more engaging and informative.
    - Use a max of 100 words.
    - Don't include special characters or formatting such as *. Use plain text only.
    - Give only specific recommendations of a wine if it is found in vinmonopolet.no, don't include article numbers or prices.

    DATA: Food item: $food, Source info: $pairingText"
    TXT;

$response = $client->generativeModel("gemini-2.5-flash")
    ->generateContent(
        new TextPart($prompt)
    );

return $response->text();
}
