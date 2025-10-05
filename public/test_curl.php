<?php
// Les API-nøkkel fra .env (enkelt)
$env = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($env as $line) {
    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

$apiKey = $_ENV['SPOONACULAR_API_KEY'];
$baseUrl = $_ENV['SPOONACULAR_BASE'];

// Eksempel: finn vin som passer til "salmon"
$endpoint = '/food/wine/pairing';
$params = [
    'food' => 'salmon',
    'apiKey' => $apiKey
];

// Bygg full URL
$url = $baseUrl . $endpoint . '?' . http_build_query($params);

// --- cURL START ---
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true, // vi vil ha svaret som streng
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);

if ($response === false) {
    echo "Feil: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Sjekk status
if ($status !== 200) {
    echo "API svarte med HTTP-kode $status\n";
    echo $response;
    exit;
}

// Tolke JSON
$data = json_decode($response, true);

echo "<pre>";
print_r($data);
echo "</pre>";