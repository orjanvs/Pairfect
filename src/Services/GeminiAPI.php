<?php

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

require __DIR__ . '/../../vendor/autoload.php';

function enhanceWithGemini(string $food, string $pairingText): string
{
$client = new Client($_ENV['GEMINI_API_KEY']);

$prompt = 
    "You're a sommelier that provides wine pairing suggestions based on food items.
    The user suggests a food item, then you use the response from the Spoonacular API to provide a better and more friendly
    answer. Don't give a specific wine brand, just the type of wine that pairs well with the food item. 
    If you don't know the answer, just say that you don't know. Do not try to make up an answer.
    If the the user asks for something that is not a food item, 
    politely inform them that you can only provide wine pairings for food items.
    If the question is empty, ask the user to provide a food item.
    Keep the response under 100 words.
    Also do short responses to everyday talk like 'hello', 'how are you', 'what's your name' etc.

    DATA: Food item: $food, Source info: $pairingText";

$response = $client->generativeModel("gemini-2.5-flash")
    ->generateContent(
        new TextPart($prompt)
    );

return $response->text();
}
