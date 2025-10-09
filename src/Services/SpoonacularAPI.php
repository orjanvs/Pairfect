<?php

function getWinePairing(string $food)
{

$SPOONACULAR_API_ENDPOINT_URL = "https://api.spoonacular.com/food/wine/pairing";
$SPOONACULAR_API_KEY = $_ENV['SPOONACULAR_API_KEY'];

if (!$SPOONACULAR_API_KEY) {
    throw new Exception("Spoonacular API key not set in environment variables.");
};

// Builds the request URL
$spoonacularURL = $SPOONACULAR_API_ENDPOINT_URL 
. "?food=" 
. urlencode($food) 
. "&apiKey=" 
. $SPOONACULAR_API_KEY;

// Initialize cURL session
$ch = curl_init($spoonacularURL);
$param = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
);

// Set cURL options and execute the request
curl_setopt_array($ch, $param);
$spoonacularResponse = curl_exec($ch);
$spoonacularResInfo = curl_getinfo($ch);
curl_close($ch);

$spoonacularResponseJson = json_decode($spoonacularResponse, true);
$pairedWines = $spoonacularResponseJson['pairedWines'] ?? [];
$pairingText = $spoonacularResponseJson['pairingText'] ?? "No pairing information available.";

return [
    'pairedWines' => $pairedWines,
    'pairingText' => $pairingText
];
}
